@extends('layouts.admin')

@section('title', 'Paid Fees')

@section('content')
<div class="container-fluid py-4">
    <h3>Paid Fees</h3>
    @if(count($paidFees))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fee Type</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paidFees as $fee)
                <tr>
                    <td>{{ $fee->fee_type }}</td>
                    <td>₹{{ number_format($fee->amount, 2) }}</td>
                    <td><a href="{{ route('student.fees.receipt', $fee->id) }}" class="btn btn-primary btn-sm" target="_blank">Generate Receipt</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No paid fees found.</div>
    @endif
</div>
@endsection 