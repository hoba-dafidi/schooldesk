<div class="btn-group btn-group-sm">
    <button class="btn btn-outline-danger" onclick="deleteCampaign({{ $row->id }})">Delete</button>
</div>

<script>
    function deleteCampaign(id) {
        if (!confirm('Are you sure you want to delete this campaign?')) return;

        $.ajax({
            url: "{{ url('admin/campaigns') }}/" + id,
            type: "DELETE",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function() {
                $('#campaignsTable').DataTable().ajax.reload();
            }
        });
    }
</script>
