<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Events\TicketMessageCreated;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class TicketController extends Controller
{
    /**
     * Display all tickets with filters.
     */
    public function index(Request $request)
    {
        $query = Ticket::query()->with(['user', 'assignedAgent']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        $tickets = $query->latest()->paginate(10);
        // ✅ Only users with admin, support sales or developer role
        $agents = User::role(['admin', 'support', 'sales', 'developer'])->get();

        return view('admin.tickets.index', compact('tickets', 'agents'));
    }

    /**
     * Show form to edit a ticket.
     */
    public function edit(Ticket $ticket)
    {
        return view('admin.tickets.edit', compact('ticket'));
    }

    /**
     * Update a ticket.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'status'  => ['required', new Enum(TicketStatus::class)],
            'priority'=> 'required|in:low,medium,high',
        ]);

        $ticket->update([
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => $request->status,
            'priority'=> $request->priority,
        ]);

        return response()->json([
            'message' => 'Ticket updated successfully',
            'ticket' => $ticket,
        ]);
    }


    /**
     * Store a new ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
            'attachments.*' => 'file|max:5120', // max 5MB each
        ]);

        // 1. Create ticket
        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => TicketStatus::Open,
            'description' => $request->message, // Set initial message as description
        ]);

        // 2. Create first message
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // 3. Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket_attachments');

                TicketAttachment::create([
                    'ticket_message_id' => $message->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Ticket created successfully with message and attachments.');
    }

    /**
     * Post a reply to the ticket.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:10240', // 10MB
        ]);

        $message = $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments/tickets', 'public');

            $message->attachments()->create([
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        $ticket->update(['status' => TicketStatus::InProgress]);

        $html = View::make('admin.tickets.partials.ticket-message', ['message' => $message])->render();
        event(new TicketMessageCreated($message, $html));

        return response()->json(['success' => true, 'ticket_id' => $ticket->id]);
    }

    /**
     * Get all messages for a ticket (AJAX).
     */
    public function messages(Ticket $ticket)
    {
        // return response()->json(
        //     $ticket->messages()
        //         ->with(['user', 'attachments'])
        //         ->latest()
        //         ->get()
        //         // ->map(fn($msg) => [
        //         //     'user' => [
        //         //         'name' => $msg->user->name,
        //         //         'is_admin' => $msg->user->hasRole('admin'),
        //         //         'avatar' => $msg->user->avatar_url ?? asset('default-avatar.png'),
        //         //     ],
        //         //     'content' => $msg->content,
        //         //     'time' => $msg->created_at->diffForHumans(),
        //         //     'attachments' => $msg->attachments->map(fn($a) => [
        //         //         'url' => Storage::disk('public')->url($a->file_path),
        //         //         'name' => $a->original_name,
        //         //     ]),
        //         // ])
        // );

        $messages = $ticket->messages()->with('user', 'attachments')->latest()->get();

        return view('admin.tickets.partials.thread', compact('messages'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', Rule::in(['open', 'pending', 'closed', 'escalated'])]
        ]);

        $ticket->status = $request->status;
        $ticket->save();

        return response()->json(['success' => true]);
    }


    public function show(Ticket $ticket)
    {
        // return response()->json($ticket->messages()->with('attachments')->get());
        return response()->json($ticket->load(['messages.attachments']));
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);


        $ticket->assigned_to = $request->assigned_to;
        $ticket->save();

        return response()->json(['success' => 'Ticket assigned successfully.']);
        // return back()->with('success', 'Ticket assigned successfully.');
    }


    /**
     * Delete a ticket (optional).
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return back()->with('success', 'Ticket deleted.');
    }
}
