<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Affiliate;
use App\Models\AffiliateCampaign;
use App\Models\AffiliateCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $affiliate = Affiliate::firstOrCreate(['user_id' => $user->id]);

        $campaigns = AffiliateCampaign::where('affiliate_id', $affiliate->id)
            ->latest()
            ->paginate(12);

        // Totals per campaign (commissions earned by this affiliate)
        $commissionTotals = AffiliateCommission::selectRaw('campaign_id, SUM(amount) as total')
            ->where('affiliate_id', $affiliate->id)
            ->groupBy('campaign_id')
            ->pluck('total', 'campaign_id');

        return view('affiliate.campaigns.index', compact('affiliate', 'campaigns', 'commissionTotals'));
    }

    public function store(StoreCampaignRequest $request)
    {
        $user = Auth::user();
        $affiliate = Affiliate::firstOrCreate(['user_id' => $user->id]);

        $data = $request->validated();

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name'] . '-' . Str::random(4));
        }

        // Generate code if not provided
        if (empty($data['code'])) {
            $data['code'] = $this->generateUniqueCode();
        }

        $data['affiliate_id'] = $affiliate->id;

        AffiliateCampaign::create($data);

        return back()->with('success', 'Campaign created successfully.');
    }

    public function update(UpdateCampaignRequest $request, AffiliateCampaign $campaign)
    {
        $user = Auth::user();
        $affiliate = Affiliate::firstOrCreate(['user_id' => $user->id]);

        // Own campaign guard
        abort_unless($campaign->affiliate_id === $affiliate->id, 403);

        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name'] . '-' . Str::random(4));
        }

        $campaign->update($data);

        return back()->with('success', 'Campaign updated successfully.');
    }

    public function destroy(AffiliateCampaign $campaign)
    {
        $user = Auth::user();
        $affiliate = Affiliate::firstOrCreate(['user_id' => $user->id]);

        abort_unless($campaign->affiliate_id === $affiliate->id, 403);

        $campaign->delete();

        return back()->with('success', 'Campaign deleted.');
    }

    public function regenerateCode(AffiliateCampaign $campaign)
    {
        $user = Auth::user();
        $affiliate = Affiliate::firstOrCreate(['user_id' => $user->id]);

        abort_unless($campaign->affiliate_id === $affiliate->id, 403);

        $campaign->referral_code = $this->generateUniqueCode();
        $campaign->save();

        return back()->with('success', 'Campaign code regenerated.');
    }

    protected function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (AffiliateCampaign::where('referral_code', $code)->exists());

        return $code;
    }
}
