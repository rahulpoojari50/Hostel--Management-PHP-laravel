@extends('layouts.admin')

@section('title', 'Application Receipt')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow mb-4" id="receipt-content">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Application Receipt</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Student:</strong> {{ $student->name }} ({{ $student->email }})</div>
                    <div class="mb-2"><strong>Hostel:</strong> {{ $application->hostel->name }}</div>
                    <div class="mb-2"><strong>Room Type:</strong> {{ $application->roomType->type }}</div>
                    <div class="mb-2"><strong>Amount Paid:</strong> ₹{{ $application->amount }}</div>
                    <div class="mb-2"><strong>Status:</strong> <span class="badge badge-info text-uppercase">{{ $application->status }}</span></div>
                    <div class="mb-2"><strong>Application ID:</strong> {{ $application->id }}</div>
                    <div class="mb-2"><strong>Date:</strong> {{ $application->created_at->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- html2canvas CDN -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        html2canvas(document.getElementById('receipt-content')).then(function(canvas) {
            var link = document.createElement('a');
            link.download = 'hostel-application-receipt.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }, 1000);
});
</script>
@endsection 