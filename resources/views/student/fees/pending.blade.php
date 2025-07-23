@extends('layouts.admin')

@section('title', 'Pending Fees')

@section('content')
<div class="container-fluid py-4">
    <h3>Pending Fees</h3>
    @if(count($pendingFees))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fee Type</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingFees as $fee)
                <tr>
                    <td>{{ $fee->fee_type }}</td>
                    <td>₹{{ number_format($fee->amount, 2) }}</td>
                    <td>
                        <form action="{{ route('student.fees.pay', $fee->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Pay Now</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No pending fees found.</div>
    @endif
</div>
@endsection 