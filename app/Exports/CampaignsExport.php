<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CampaignsExport implements FromView
{
    public $campaigns;

    public function __construct($campaigns)
    {
        $this->campaigns = $campaigns;
    }

    public function view(): View
    {
        return view('admin.campaigns.export-excel', [
            'campaigns' => $this->campaigns
        ]);
    }
}
