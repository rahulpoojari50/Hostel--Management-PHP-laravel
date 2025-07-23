@extends('layouts.admin')

@section('title', 'Fee Receipt')

@section('content')
<div class="container py-4" id="receipt-content">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Fee Receipt</h5>
            <div>
                <a href="{{ route('student.fees.receipt.download', $fee->id) }}" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Download PDF</a>
                <button class="btn btn-secondary btn-sm" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-2"><strong>Student Name:</strong> {{ $fee->student->name }}</div>
            <div class="mb-2"><strong>Email:</strong> {{ $fee->student->email }}</div>
            <div class="mb-2"><strong>Hostel:</strong> {{ $fee->hostel->name ?? '-' }}</div>
            <div class="mb-2"><strong>Fee Type:</strong> {{ $fee->fee_type }}</div>
            <div class="mb-2"><strong>Amount Paid:</strong> ₹{{ number_format($fee->amount, 2) }}</div>
            <div class="mb-2"><strong>Paid On:</strong> {{ $fee->paid_at ? $fee->paid_at->format('Y-m-d H:i') : '-' }}</div>
            @if($fee->application)
            <div class="mb-2"><strong>Application ID:</strong> {{ $fee->application->id }}</div>
            @endif
            <div class="mb-2"><strong>Receipt ID:</strong> {{ $fee->id }}</div>
        </div>
    </div>
</div>
@endsection 