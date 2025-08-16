@extends('layouts.admin')

@section('title', 'Add Fees')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])
    </div>
    <div>
        {{-- <h1 class="h3 mb-0 text-gray-800">Add Fees</h1> --}}
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Add Fees</h5>
</div>

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
                <div class="form-group">
                    <label for="hostel_id">Select Hostel</label>
                    <select class="form-control" id="hostel_id" name="hostel_id" onchange="updateHostelSelection(this.value)" required>
                        <option value="">-- Select Hostel --</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ $selectedHostelId == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <form method="POST" id="add-fees-form" style="display: none;">
                    @csrf
                    <!-- Custom Fees Section -->
                    <div id="custom-fees-section">
                        <div class="text-center text-muted">
                            <p>Please select a hostel to add fees.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link p-0 mb-3" id="add-fee-btn" style="display: none;">
                        <i class="fas fa-plus"></i> Add Another Fee
                    </button>
                    <button type="submit" class="btn btn-success">Add Fees</button>
                </form>
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
                <!-- Hostel Selector -->
                <div class="form-group mb-3">
                    <label for="fees_hostel_id">Select Hostel to View/Manage Fees</label>
                    <select class="form-control" id="fees_hostel_id" name="fees_hostel_id" onchange="updateFeesDisplay(this.value)" required>
                        <option value="">-- Select Hostel --</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ request('fees_hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div id="fees-display-container">
                    <div class="text-center text-muted">
                        <p>Please select a hostel above to view and manage fees.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Rent Section -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Rent</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="mb-3">
                    <div class="form-group">
                        <label for="rent_hostel_id">Select Hostel for Rent Update</label>
                        <select class="form-control" id="rent_hostel_id" name="rent_hostel_id" onchange="this.form.submit()" required>
                            <option value="">-- Select Hostel --</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}" {{ request('rent_hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                
                @php
                    $rentHostelId = request('rent_hostel_id');
                    $rentHostel = $hostels->where('id', $rentHostelId)->first();
                @endphp
                
                @if($rentHostel)
                <form action="{{ route('warden.manage-hostel.rent.update', $rentHostel) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="rent_room_type_id">Room Type</label>
                        <select class="form-control" id="rent_room_type_id" name="room_type_id" required>
                            <option value="">Select Room Type</option>
                            @foreach($rentHostel->roomTypes as $roomType)
                                <option value="{{ $roomType->id }}">
                                    {{ $roomType->type }} - Current: ₹{{ $roomType->price_per_month }}/month
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new_price_per_month">New Rent</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₹</span>
                            </div>
                            <input type="number" class="form-control" id="new_price_per_month" name="price_per_month" 
                                   min="0" step="0.01" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-dollar-sign fa-sm"></i> Update Rent
                    </button>
                </form>
                @elseif($rentHostelId)
                <div class="text-center text-muted">
                    <p>Selected hostel has no room types. Please add room types first.</p>
                </div>
                @else
                <div class="text-center text-muted">
                    <p>Please select a hostel above to update rent.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Fee Modal -->
<div class="modal fade" id="editFeeModal" tabindex="-1" role="dialog" aria-labelledby="editFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFeeModalLabel">Edit Fee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editFeeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_fee_type">Fee Name</label>
                        <input type="text" class="form-control" id="edit_fee_type" name="fee_type" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_fee_amount">Amount</label>
                        <input type="number" class="form-control" id="edit_fee_amount" name="fee_amount" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Fee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Fee Modal -->
<div class="modal fade" id="deleteFeeModal" tabindex="-1" role="dialog" aria-labelledby="deleteFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteFeeModalLabel">Delete Fee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the fee <strong id="feeNameToDelete"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone. All associated student fee records will also be deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteFee()">Delete Fee</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Fee Form (Hidden) -->
<form id="deleteFeeForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    // Global functions for fee management
    function editFee(hostelId, feeIndex, feeType, feeAmount) {
        document.getElementById('edit_fee_type').value = feeType;
        document.getElementById('edit_fee_amount').value = feeAmount;
        
        // Set the form action to update the specific fee
        const form = document.getElementById('editFeeForm');
        form.action = '{{ route("warden.manage-hostel.fees.individual.update", ":hostelId") }}'.replace(':hostelId', hostelId);
        
        // Add hidden fields for fee index and hostel ID
        let hiddenIndex = form.querySelector('input[name="fee_index"]');
        if (!hiddenIndex) {
            hiddenIndex = document.createElement('input');
            hiddenIndex.type = 'hidden';
            hiddenIndex.name = 'fee_index';
            form.appendChild(hiddenIndex);
        }
        hiddenIndex.value = feeIndex;
        
        let hiddenHostelId = form.querySelector('input[name="hostel_id"]');
        if (!hiddenHostelId) {
            hiddenHostelId = document.createElement('input');
            hiddenHostelId.type = 'hidden';
            hiddenHostelId.name = 'hostel_id';
            form.appendChild(hiddenHostelId);
        }
        hiddenHostelId.value = hostelId;
        
        $('#editFeeModal').modal('show');
    }
    
    function deleteFee(hostelId, feeIndex, feeType) {
        document.getElementById('feeNameToDelete').textContent = feeType;
        
        // Set the form action to delete the specific fee
        const form = document.getElementById('deleteFeeForm');
        form.action = '{{ route("warden.manage-hostel.fees.individual.delete", ":hostelId") }}'.replace(':hostelId', hostelId);
        
        // Add hidden fields for fee index and hostel ID
        let hiddenIndex = form.querySelector('input[name="fee_index"]');
        if (!hiddenIndex) {
            hiddenIndex = document.createElement('input');
            hiddenIndex.type = 'hidden';
            hiddenIndex.name = 'fee_index';
            form.appendChild(hiddenIndex);
        }
        hiddenIndex.value = feeIndex;
        
        let hiddenHostelId = form.querySelector('input[name="hostel_id"]');
        if (!hiddenHostelId) {
            hiddenHostelId = document.createElement('input');
            hiddenHostelId.type = 'hidden';
            hiddenHostelId.name = 'hostel_id';
            form.appendChild(hiddenHostelId);
        }
        hiddenHostelId.value = hostelId;
        
        $('#deleteFeeModal').modal('show');
    }
    
            function confirmDeleteFee() {
            document.getElementById('deleteFeeForm').submit();
        }
        
        // Dynamic hostel selection functions
        function updateHostelSelection(hostelId) {
            const form = document.getElementById('add-fees-form');
            const customFeesSection = document.getElementById('custom-fees-section');
            const addFeeBtn = document.getElementById('add-fee-btn');
            
            if (!hostelId) {
                // Hide the form if no hostel selected
                form.style.display = 'none';
                return;
            }
            
            // Update the form action
            form.action = `/warden/manage-hostel/${hostelId}/fees`;
            
            // Show the fee input form
            customFeesSection.innerHTML = `
                <div class="form-group form-row align-items-end mb-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="fees[0][type]" placeholder="Fee Name (e.g., Admission Fee)" required>
                    </div>
                    <div class="col-md-4">
                        <input type="number" class="form-control" name="fees[0][amount]" min="0" step="0.01" placeholder="Amount" required>
                    </div>
                </div>
            `;
            
            // Show the form and buttons
            form.style.display = 'block';
            addFeeBtn.style.display = 'block';
            
            // Also update the fees display if the same hostel is selected there
            const feesHostelSelect = document.getElementById('fees_hostel_id');
            if (feesHostelSelect && feesHostelSelect.value === hostelId) {
                updateFeesDisplay(hostelId);
            }
        }
        
        function updateFeesDisplay(hostelId) {
            if (!hostelId) {
                document.getElementById('fees-display-container').innerHTML = `
                    <div class="text-center text-muted">
                        <p>Please select a hostel above to view and manage fees.</p>
                    </div>
                `;
                return;
            }
            
            // Show loading
            document.getElementById('fees-display-container').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading fees...</p>
                </div>
            `;
            
            // Fetch fees data via AJAX
            fetch(`/warden/fees/get-hostel-fees/${hostelId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayFees(data.hostel, data.fees);
                    } else {
                        document.getElementById('fees-display-container').innerHTML = `
                            <div class="text-center text-muted">
                                <p>Error loading fees: ${data.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('fees-display-container').innerHTML = `
                        <div class="text-center text-muted">
                            <p>Error loading fees. Please try again.</p>
                        </div>
                    `;
                });
        }
        
        function displayFees(hostel, fees) {
            const container = document.getElementById('fees-display-container');
            
            if (!fees || fees.length === 0) {
                container.innerHTML = `
                    <div class="mb-3">
                        <h6 class="font-weight-bold">${hostel.name} - Fees</h6>
                        <span class="text-muted">No fees set for this hostel.</span>
                    </div>
                `;
                return;
            }
            
            let feesHtml = `
                <div class="mb-3">
                    <h6 class="font-weight-bold">${hostel.name} - Fees</h6>
                    <ul class="list-group mb-2">
            `;
            
            fees.forEach((fee, index) => {
                feesHtml += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span>${fee.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                            <span class="badge badge-primary badge-pill ml-2">₹${parseFloat(fee.amount).toFixed(2)}</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-warning mr-1" 
                                    onclick="editFee(${hostel.id}, ${index}, '${fee.type}', ${fee.amount})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="deleteFee(${hostel.id}, ${index}, '${fee.type}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </li>
                `;
            });
            
            feesHtml += `
                    </ul>
                </div>
            `;
            
            container.innerHTML = feesHtml;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            let feeIndex = 0;
            
            // Initialize form based on current hostel selection
            const currentHostelId = document.getElementById('hostel_id').value;
            if (currentHostelId) {
                updateHostelSelection(currentHostelId);
            }
            
            // Remove fee row functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-fee-btn')) {
                    e.target.closest('.form-row').remove();
                }
            });
            
            document.getElementById('add-fee-btn').addEventListener('click', function(e) {
                e.preventDefault();
                addFeeRow();
            });
        
        function addFeeRow() {
            feeIndex++;
            const section = document.getElementById('custom-fees-section');
            const row = document.createElement('div');
            row.className = 'form-group form-row align-items-end mb-2';
            row.innerHTML = `
                <div class="col-md-6">
                    <input type="text" class="form-control" name="fees[${feeIndex}][type]" placeholder="Fee Name" required>
                </div>
                <div class="col-md-4">
                    <input type="number" class="form-control" name="fees[${feeIndex}][amount]" min="0" step="0.01" placeholder="Amount" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-fee-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            section.appendChild(row);
        }
        
        // Edit fee functionality
        function editFee(hostelId, feeIndex, feeType, feeAmount) {
            document.getElementById('edit_fee_type').value = feeType;
            document.getElementById('edit_fee_amount').value = feeAmount;
            
            // Set the form action to update the specific fee
            const form = document.getElementById('editFeeForm');
            form.action = '{{ route("warden.manage-hostel.fees.individual.update", ":hostelId") }}'.replace(':hostelId', hostelId);
            
            // Add hidden fields for fee index and hostel ID
            let hiddenIndex = form.querySelector('input[name="fee_index"]');
            if (!hiddenIndex) {
                hiddenIndex = document.createElement('input');
                hiddenIndex.type = 'hidden';
                hiddenIndex.name = 'fee_index';
                form.appendChild(hiddenIndex);
            }
            hiddenIndex.value = feeIndex;
            
            let hiddenHostelId = form.querySelector('input[name="hostel_id"]');
            if (!hiddenHostelId) {
                hiddenHostelId = document.createElement('input');
                hiddenHostelId.type = 'hidden';
                hiddenHostelId.name = 'hostel_id';
                form.appendChild(hiddenHostelId);
            }
            hiddenHostelId.value = hostelId;
            
            $('#editFeeModal').modal('show');
        }
        
        // Delete fee functionality
        function deleteFee(hostelId, feeIndex, feeType) {
            document.getElementById('feeNameToDelete').textContent = feeType;
            
            // Set the form action to delete the specific fee
            const form = document.getElementById('deleteFeeForm');
            form.action = '{{ route("warden.manage-hostel.fees.individual.delete", ":hostelId") }}'.replace(':hostelId', hostelId);
            
            // Add hidden fields for fee index and hostel ID
            let hiddenIndex = form.querySelector('input[name="fee_index"]');
            if (!hiddenIndex) {
                hiddenIndex = document.createElement('input');
                hiddenIndex.type = 'hidden';
                hiddenIndex.name = 'fee_index';
                form.appendChild(hiddenIndex);
            }
            hiddenIndex.value = feeIndex;
            
            let hiddenHostelId = form.querySelector('input[name="hostel_id"]');
            if (!hiddenHostelId) {
                hiddenHostelId = document.createElement('input');
                hiddenHostelId.type = 'hidden';
                hiddenHostelId.name = 'hostel_id';
                form.appendChild(hiddenHostelId);
            }
            hiddenHostelId.value = hostelId;
            
            $('#deleteFeeModal').modal('show');
        }
        
        function confirmDeleteFee() {
            document.getElementById('deleteFeeForm').submit();
        }
    });
</script>
@endpush
@endsection 