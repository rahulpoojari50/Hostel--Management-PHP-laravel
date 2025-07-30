@extends('layouts.admin')

@section('title', 'Apply for Hostel')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Apply for {{ $hostel->name }}</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('student.applications.store', $hostel->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="room_type_id">Select Room Type</label>
                            <select class="form-control" id="room_type_id" name="room_type_id" required onchange="updatePrice()" {{ $disableRoomType ? 'disabled' : '' }}>
                                <option value="">-- Select Room Type --</option>
                                @foreach($hostel->roomTypes as $type)
                                    <option value="{{ $type->id }}" data-price="{{ $type->price_per_month }}" {{ $existingApplication && $existingApplication->room_type_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->type }} (₹{{ $type->price_per_month }})
                                    </option>
                                @endforeach
                            </select>
                            @if($disableRoomType)
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Room type is locked because you're paying pending fees for your existing application.
                                </small>
                            @endif
                        </div>
                        
                        <div id="fee-checkboxes" class="mb-3" style="display:none;">
                            <h6 class="font-weight-bold text-primary mb-3">Fee Breakdown</h6>
                            <div id="fee-list"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="amount">Total Price / Rent</label>
                            <input type="text" class="form-control" id="amount" name="amount" readonly required>
                        </div>
                        
                        <input type="hidden" name="fees_to_pay" id="fees_to_pay">
                        <input type="hidden" name="fees_pay_later" id="fees_pay_later">
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-credit-card"></i> {{ $disableRoomType ? 'Pay Pending Fees' : 'Pay & Apply' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
@php
    $preselectFeeType = request('fee_type');
@endphp
function updatePrice() {
    var sel = document.getElementById('room_type_id');
    var price = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].getAttribute('data-price') : '';
    var fees = @json($hostel->fees ?? []);
    var existingFees = @json($existingFees ?? []);
    var preselectFeeType = @json($preselectFeeType);
    var feeSum = 0;
    var feeList = '';
    
    if (sel.value) {
        // Room Rent
        var roomRentStatus = existingFees['room_rent'] ? existingFees['room_rent'].status : 'pending';
        var roomRentDisabled = roomRentStatus === 'paid';
        // Pre-select if matches preselectFeeType, otherwise default logic
        var roomRentChecked = (preselectFeeType === 'room_rent') ? true : !roomRentDisabled;
        
        feeList += '<div class="form-check mb-2">';
        feeList += '<input class="form-check-input fee-check" type="checkbox" ' + (roomRentChecked ? 'checked' : '') + ' ' + (roomRentDisabled ? 'disabled' : '') + ' id="feeCheckRoomRent" data-amount="' + price + '" data-type="room_rent">';
        feeList += '<label class="form-check-label" for="feeCheckRoomRent">';
        feeList += 'Room Rent (₹' + (price || 0) + ')';
        if (roomRentStatus === 'paid') {
            feeList += ' <span class="badge badge-success">Paid</span>';
        } else {
            feeList += ' <span class="badge badge-warning">Pending</span>';
        }
        feeList += '</label></div>';
    }
    
    if (fees.length > 0) {
        fees.forEach(function(fee, idx) {
            var feeStatus = existingFees[fee.type] ? existingFees[fee.type].status : 'pending';
            var feeDisabled = feeStatus === 'paid';
            // Pre-select if matches preselectFeeType, otherwise default logic
            var feeChecked = (preselectFeeType === fee.type) ? true : !feeDisabled;
            
            feeList += '<div class="form-check mb-2">';
            feeList += '<input class="form-check-input fee-check" type="checkbox" ' + (feeChecked ? 'checked' : '') + ' ' + (feeDisabled ? 'disabled' : '') + ' id="feeCheck' + idx + '" data-amount="' + fee.amount + '" data-type="' + fee.type + '">';
            feeList += '<label class="form-check-label" for="feeCheck' + idx + '">';
            feeList += (fee.type ? fee.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Fee') + ' (₹' + fee.amount + ')';
            if (feeStatus === 'paid') {
                feeList += ' <span class="badge badge-success">Paid</span>';
            } else {
                feeList += ' <span class="badge badge-warning">Pending</span>';
            }
            feeList += '</label></div>';
        });
    }
    
    var feeCheckboxesDiv = document.getElementById('fee-checkboxes');
    var feeListDiv = document.getElementById('fee-list');
    
    if (sel.value && (price || fees.length > 0)) {
        feeCheckboxesDiv.style.display = 'block';
        feeListDiv.innerHTML = feeList;
    } else {
        feeCheckboxesDiv.style.display = 'none';
        feeListDiv.innerHTML = '';
    }
    
    updateTotal();
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.fee-check').forEach(function(cb) {
        cb.addEventListener('change', updateTotal);
    });
}

function updateTotal() {
    var total = 0;
    var feesToPay = [];
    var feesPayLater = [];
    
    document.querySelectorAll('.fee-check').forEach(function(cb) {
        var fee = { 
            type: cb.getAttribute('data-type'), 
            amount: parseFloat(cb.getAttribute('data-amount')) 
        };
        
        if (cb.checked && !cb.disabled) {
            total += fee.amount;
            feesToPay.push(fee);
        } else if (!cb.disabled) {
            feesPayLater.push(fee);
        }
    });
    
    document.getElementById('amount').value = total ? total.toFixed(2) : '';
    document.getElementById('fees_to_pay').value = JSON.stringify(feesToPay);
    document.getElementById('fees_pay_later').value = JSON.stringify(feesPayLater);
}

// Initialize on page load if a room type is preselected
if(document.getElementById('room_type_id').value) updatePrice();
</script>
@endsection 