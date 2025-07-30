<?php
namespace App\Exports;

use App\Models\Affiliate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AffiliatesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Affiliate::with('user')->get()->map(function ($affiliate) {
            return [
                'Name'       => $affiliate->user->name ?? 'N/A',
                'Email'      => $affiliate->user->email ?? 'N/A',
                'Referral Code' => $affiliate->referral_code,
                'Clicks'     => $affiliate->clicks,
                'Signups'    => $affiliate->signups,
                'Total Commissions' => number_format($affiliate->totalCommission(), 2),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Referral Code',
            'Clicks',
            'Signups',
            'Total Commissions (₦)',
        ];
    }
}
