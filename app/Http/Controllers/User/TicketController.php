<?php

namespace App\Http\Controllers\User;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Events\TicketMessageCreated;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class TicketController extends Controller
{
    /**
     * Display a list of the user's tickets.
     */
    public function index()
    {
        $tickets = Auth::user()
            ->tickets()
            ->latest()
            ->paginate(10);

        return view('user.tickets.index', compact('tickets'));
    }

    /**
     * Show form to create a new ticket.
     */
    public function create()
    {
        return view('user.tickets.create');
    }

    /**
     * Store a new ticket from the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:' . implode(',', array_column(TicketPriority::cases(), 'value')),
        ]);

        $ticket = $request->user()->tickets()->create([
            ...$validated,
            'status' => TicketStatus::Open,
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket submitted successfully.');
    }

    /**
     * Show the ticket detail and conversation thread.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['messages.user', 'messages.attachments']);

        return view('user.tickets.show', compact('ticket'));
    }

    /**
     * Post a reply from the user.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorize('reply', $ticket);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $message = $ticket->messages()->create([
            'user_id' => auth()->id(),
            'content' => $validated['message'],
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

        $html = View::make('partials.ticket-message', ['msg' => $message])->render();
        event(new TicketMessageCreated($message, $html));

        return back()->with('success', 'Your reply has been sent.');
    }
}
