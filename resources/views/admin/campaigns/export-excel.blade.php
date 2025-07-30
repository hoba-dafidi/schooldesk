<table>
    <thead>
        <tr>
            <th>Campaign Name</th>
            <th>Affiliate</th>
            <th>Code</th>
            <th>Clicks</th>
            <th>Signups</th>
            <th>Commission Rate</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->name }}</td>
                <td>{{ $campaign->affiliate->user->name ?? '—' }}</td>
                <td>{{ $campaign->code }}</td>
                <td>{{ $campaign->clicks }}</td>
                <td>{{ $campaign->unique_signups }}</td>
                <td>{{ $campaign->custom_commission_rate ? $campaign->custom_commission_rate . '%' : '—' }}</td>
                <td>{{ $campaign->isActive() ? 'Active' : 'Inactive' }}</td>
                <td>{{ $campaign->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
