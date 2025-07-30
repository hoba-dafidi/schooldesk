<?php

namespace App\Exports;

use App\Models\AffiliateCommission;
use App\Models\Commission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommissionExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return AffiliateCommission::with('user')->get()->map(function ($c) {
            return [
                'User' => $c->user->name,
                'Amount' => $c->amount,
                'Status' => $c->status,
                'Paid' => $c->is_paid ? 'Yes' : 'No',
                'Date' => $c->created_at->toDateString(),
            ];
        });
    }

    public function headings(): array
    {
        return ['User', 'Amount', 'Status', 'Paid', 'Date'];
    }
}
