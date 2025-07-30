<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:120',
            'slug'  => 'nullable|string|max:160',
            'code'  => 'nullable|string|max:40|unique:affiliate_campaigns,code',
            'custom_commission_rate' => 'nullable|numeric|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at'   => 'nullable|date|after_or_equal:starts_at',
        ];
    }
}
