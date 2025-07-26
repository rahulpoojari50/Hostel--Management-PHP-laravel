@extends('layouts.admin')

@section('title', 'Add Fees')

@section('content')
@include('components.breadcrumb', [
    'pageTitle' => 'Add Fees',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Fees', 'url' => route('warden.fees.index')],
        ['name' => 'Add Fees', 'url' => '']
    ]
])
<!-- Page Heading -->
{{-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Fees</h1>
</div> --}}
@php
    $hostels = Auth::user()->managedHostels;
    $selectedHostelId = request('hostel_id') ?? ($hostels->count() === 1 ? $hostels->first()->id : null);
    $selectedHostel = $hostels->where('id', $selectedHostelId)->first();
@endphp
<div class="row">
    <!-- Add Fees Box -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add Fees</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="hostel_id">Select Hostel</label>
                        <select class="form-control" id="hostel_id" name="hostel_id" onchange="this.form.submit()" required>
                            <option value="">-- Select Hostel --</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}" {{ $selectedHostelId == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                @if($selectedHostel)
                <form method="POST" action="{{ route('warden.manage-hostel.fees.update', $selectedHostel) }}">
                    @csrf
                    <!-- Default Fees (now removable) -->
                    <div id="default-fees-section">
                        <div class="form-group form-row align-items-end mb-2" id="fee-row-admission_fee">
                            <div class="col-md-6">
                                <label for="admission_fee">Admission Fee</label>
                                <input type="number" class="form-control" id="admission_fee" name="fees[admission_fee]" value="1" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-default-fee-btn" data-fee="admission_fee"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="form-group form-row align-items-end mb-2" id="fee-row-seat_rent">
                            <div class="col-md-6">
                                <label for="seat_rent">Security Fees</label>
                                <input type="number" class="form-control" id="seat_rent" name="fees[seat_rent]" value="1" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-default-fee-btn" data-fee="seat_rent"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="form-group form-row align-items-end mb-2" id="fee-row-medical_aid_fee">
                            <div class="col-md-6">
                                <label for="medical_aid_fee">Medical Aid Fee</label>
                                <input type="number" class="form-control" id="medical_aid_fee" name="fees[medical_aid_fee]" value="1" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-default-fee-btn" data-fee="medical_aid_fee"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="form-group form-row align-items-end mb-2" id="fee-row-mess_fee">
                            <div class="col-md-6">
                                <label for="mess_fee">Mess Fee</label>
                                <input type="number" class="form-control" id="mess_fee" name="fees[mess_fee]" value="1" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-default-fee-btn" data-fee="mess_fee"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- Dynamic Optional Fees -->
                    <div id="optional-fees-section"></div>
                    <button type="button" class="btn btn-link p-0 mb-3" id="add-fee-btn">
                        <i class="fas fa-plus"></i> Add Another Fee
                    </button>
                    <button type="submit" class="btn btn-success">Add/Update Fees</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    <!-- Updated Fees Box -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Updated Fees</h6>
            </div>
            <div class="card-body">
                @foreach($hostels as $hostel)
                    <div class="mb-3">
                        <h6 class="font-weight-bold">{{ $hostel->name }}</h6>
                        @if(is_array($hostel->fees) && count($hostel->fees))
                            <ul class="list-group mb-2">
                                @foreach($hostel->fees as $fee)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ ucwords(str_replace('_', ' ', $fee['type'])) }}</span>
                                        <span class="badge badge-primary badge-pill">₹{{ number_format($fee['amount'], 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">No fees set.</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let feeIndex = 0;
        // Remove default fee row
        document.querySelectorAll('.remove-default-fee-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const fee = btn.getAttribute('data-fee');
                const row = document.getElementById('fee-row-' + fee.replace('_', '-')) || document.getElementById('fee-row-' + fee);
                if (row) row.remove();
            });
        });
        document.getElementById('add-fee-btn').addEventListener('click', function(e) {
            e.preventDefault();
            addOptionalFeeRow();
        });
        function addOptionalFeeRow() {
            feeIndex++;
            const section = document.getElementById('optional-fees-section');
            const row = document.createElement('div');
            row.className = 'form-row align-items-end mb-2';
            row.innerHTML = `
                <div class="col-md-6">
                    <input type="text" class="form-control" name="optional_fees[${feeIndex}][type]" placeholder="Fee Name" required>
                </div>
                <div class="col-md-4">
                    <input type="number" class="form-control" name="optional_fees[${feeIndex}][amount]" min="0" step="0.01" placeholder="Amount">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-fee-btn"><i class="fas fa-trash"></i></button>
                </div>
            `;
            section.appendChild(row);
            row.querySelector('.remove-fee-btn').addEventListener('click', function() {
                row.remove();
            });
        }
    });
</script>
@endpush
@endsection 