@extends('layouts.admin')

@section('title', 'Room Allotment')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => url('/warden/dashboard')],
                ['name' => 'Room Allotment', 'url' => '']
            ]
        ])
    </div>
    <div>
        <a href="{{ route('warden.applications.index') }}" class="btn btn-primary">
            <i class="fas fa-list"></i> View All Applications
        </a>
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Room Allotment</h5>
</div>

{{-- Removed Add Room Type Button and Modal --}}

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="allotment-success-alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <script>
        setTimeout(function() {
            var alert = document.getElementById('allotment-success-alert');
            if(alert) { alert.classList.remove('show'); alert.classList.add('fade'); alert.style.display = 'none'; }
        }, 3000);
    </script>
@endif

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pending Student Applications</h6>
            </div>
            <form method="POST" action="{{ route('warden.room-allotment.bulk_reject') }}" id="bulkRejectForm">
                @csrf
                <div class="table-responsive" id="pending-applications-table">
                    @include('warden.room_allotment._table', ['pendingApplications' => $pendingApplications])
                </div>
                <div class="mb-3 d-flex justify-content-end align-items-center">
                    <button type="submit" class="btn btn-danger btn-sm" id="bulk-reject-btn">
                        <i class="fas fa-times"></i> Reject Selected
                    </button>
                </div>
            </form>
            <div class="d-flex justify-content-end">
                <!-- Pagination removed for debugging -->
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Application Summary</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-warning">{{ $pendingApplications->count() }}</div>
                            <div class="text-muted">Pending Applications</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-info">{{ $pendingApplications->groupBy('hostel_id')->count() }}</div>
                            <div class="text-muted">Hostels with Pending Apps</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="h4 text-primary">{{ $pendingApplications->groupBy('room_type_id')->count() }}</div>
                            <div class="text-muted">Room Types Requested</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.student-profile-modal')
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[ 4, "desc" ]],
            "pageLength": 10,
            "language": {
                "search": "Search applications:",
                "lengthMenu": "Show _MENU_ applications per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ applications"
            }
        });

        document.getElementById('selectAllReject').addEventListener('change', function() {
            const checked = this.checked;
            document.querySelectorAll('input[name="application_ids[]"]').forEach(cb => cb.checked = checked);
        });

        function refreshPendingApplications() {
            var url = window.location.pathname + '?page=1&_t=' + new Date().getTime();
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(data) {
                    $('#pending-applications-table').html(data);
                }
            });
        }
        setInterval(refreshPendingApplications, 10000); // 10 seconds

        // Handle bulk reject form submission
        const bulkRejectForm = document.getElementById('bulkRejectForm');
        const bulkRejectBtn = document.getElementById('bulk-reject-btn');
        
        if (bulkRejectForm && bulkRejectBtn) {
            bulkRejectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check if any applications are selected
                const selectedApplications = document.querySelectorAll('input[name="application_ids[]"]:checked');
                if (selectedApplications.length === 0) {
                    alert('Please select at least one application to reject.');
                    return;
                }
                
                // Show confirmation dialog
                if (confirm('Are you sure you want to reject the selected applications? This action will require additional confirmation.')) {
                    // Submit the form
                    bulkRejectForm.submit();
                }
            });
        }
    });
</script>
@endpush 