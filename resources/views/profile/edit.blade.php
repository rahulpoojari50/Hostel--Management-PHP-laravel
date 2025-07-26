@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                    <button id="editBtn" class="btn btn-sm btn-primary" onclick="enableEdit()">Edit</button>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{-- DEBUG: Show profile data --}}
                    {{-- @if(config('app.debug'))
                        <pre>{{ var_export($profile, true) }}</pre>
                    @endif --}}
                    <!-- Profile Data Table (view mode only) -->
                    <div id="profileTableWrapper">
                        <table class="table table-bordered mb-4">
                            <tbody>
                                <tr><th>First Name</th><td>{{ $profile->first_name ?? '-' }}</td></tr>
                                <tr><th>Last Name</th><td>{{ $profile->last_name ?? '-' }}</td></tr>
                                <tr><th>Father Name</th><td>{{ $profile->father_name ?? '-' }}</td></tr>
                                <tr><th>Father Occupation</th><td>{{ $profile->father_occupation ?? '-' }}</td></tr>
                                <tr><th>Father Email</th><td>{{ $profile->father_email ?? '-' }}</td></tr>
                                <tr><th>Father Mobile</th><td>{{ $profile->father_mobile ?? '-' }}</td></tr>
                                <tr><th>Mother Name</th><td>{{ $profile->mother_name ?? '-' }}</td></tr>
                                <tr><th>Mother Occupation</th><td>{{ $profile->mother_occupation ?? '-' }}</td></tr>
                                <tr><th>Mother Email</th><td>{{ $profile->mother_email ?? '-' }}</td></tr>
                                <tr><th>Mother Mobile</th><td>{{ $profile->mother_mobile ?? '-' }}</td></tr>
                                <tr><th>Phone</th><td>{{ $profile->phone ?? '-' }}</td></tr>
                                <tr><th>Email</th><td>{{ $profile->email ?? '-' }}</td></tr>
                                <tr><th>Gender</th><td>{{ $profile->gender ?? '-' }}</td></tr>
                                <tr><th>Date of Birth</th><td>{{ $profile->dob ?? '-' }}</td></tr>
                                <tr><th>Emergency Phone</th><td>{{ $profile->emergency_phone ?? '-' }}</td></tr>
                                <tr><th>Religion</th><td>{{ $profile->religion ?? '-' }}</td></tr>
                                <tr><th>Caste Category</th><td>{{ $profile->caste_category ?? '-' }}</td></tr>
                                <tr><th>Caste</th><td>{{ $profile->caste ?? '-' }}</td></tr>
                                <tr><th>Admission Quota</th><td>{{ $profile->admission_quota ?? '-' }}</td></tr>
                                <tr><th>Mother Tongue</th><td>{{ $profile->mother_tongue ?? '-' }}</td></tr>
                                <tr><th>Nationality</th><td>{{ $profile->nationality ?? '-' }}</td></tr>
                                <tr><th>Marital Status</th><td>{{ $profile->marital_status ?? '-' }}</td></tr>
                                <tr><th>Blood Group</th><td>{{ $profile->blood_group ?? '-' }}</td></tr>
                                <tr><th>Aadhaar ID</th><td>{{ $profile->aadhaar_id ?? '-' }}</td></tr>
                                <tr><th>Passport No</th><td>{{ $profile->passport_no ?? '-' }}</td></tr>
                                <tr><th>Admission Date</th><td>{{ $profile->admission_date ?? '-' }}</td></tr>
                                <tr><th>Present State</th><td>{{ $profile->present_state ?? '-' }}</td></tr>
                                <tr><th>Present City</th><td>{{ $profile->present_city ?? '-' }}</td></tr>
                                <tr><th>Present Address</th><td>{{ $profile->present_address ?? '-' }}</td></tr>
                                <tr><th>Permanent State</th><td>{{ $profile->permanent_state ?? '-' }}</td></tr>
                                <tr><th>Permanent City</th><td>{{ $profile->permanent_city ?? '-' }}</td></tr>
                                <tr><th>Permanent Address</th><td>{{ $profile->permanent_address ?? '-' }}</td></tr>
                                <tr><th>Document</th><td>@if($profile && $profile->document_path)<a href="{{ asset('storage/' . $profile->document_path) }}" target="_blank">View Document</a>@else - @endif</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Profile Edit Form -->
                    <form id="profileForm" method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" style="display:none;">
        @csrf
        @method('PATCH')
                        <!-- 1. Basic Info -->
                        <h4 class="mb-3">🔹 1. Basic Info</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>First Name *</label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $profile->first_name ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Last Name *</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $profile->last_name ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Father Name *</label>
                                <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $profile->father_name ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Father Occupation</label>
                                <input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation', $profile->father_occupation ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Father Email</label>
                                <input type="email" name="father_email" class="form-control" value="{{ old('father_email', $profile->father_email ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Father Mobile</label>
                                <input type="text" name="father_mobile" class="form-control" value="{{ old('father_mobile', $profile->father_mobile ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother Name *</label>
                                <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $profile->mother_name ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother Occupation</label>
                                <input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation', $profile->mother_occupation ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother Email</label>
                                <input type="email" name="mother_email" class="form-control" value="{{ old('mother_email', $profile->mother_email ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother Mobile</label>
                                <input type="text" name="mother_mobile" class="form-control" value="{{ old('mother_mobile', $profile->mother_mobile ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone *</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile->phone ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $profile->email ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Gender *</label>
                                <select name="gender" class="form-control" required disabled>
                                    <option value="">-- Select --</option>
                                    <option value="Male" {{ old('gender', $profile->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $profile->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $profile->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Date of Birth *</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $profile->dob ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Emergency Phone *</label>
                                <input type="text" name="emergency_phone" class="form-control" value="{{ old('emergency_phone', $profile->emergency_phone ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Religion</label>
                                <input type="text" name="religion" class="form-control" value="{{ old('religion', $profile->religion ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Caste Category *</label>
                                <select name="caste_category" class="form-control" required disabled>
                                    <option value="">-- Select --</option>
                                    <option value="General" {{ old('caste_category', $profile->caste_category ?? '') == 'General' ? 'selected' : '' }}>General</option>
                                    <option value="OBC" {{ old('caste_category', $profile->caste_category ?? '') == 'OBC' ? 'selected' : '' }}>OBC</option>
                                    <option value="SC" {{ old('caste_category', $profile->caste_category ?? '') == 'SC' ? 'selected' : '' }}>SC</option>
                                    <option value="ST" {{ old('caste_category', $profile->caste_category ?? '') == 'ST' ? 'selected' : '' }}>ST</option>
                                    <option value="Other" {{ old('caste_category', $profile->caste_category ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Caste</label>
                                <input type="text" name="caste" class="form-control" value="{{ old('caste', $profile->caste ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Admission Quota *</label>
                                <select name="admission_quota" class="form-control" required disabled>
                                    <option value="">-- Select --</option>
                                    <option value="General" {{ old('admission_quota', $profile->admission_quota ?? '') == 'General' ? 'selected' : '' }}>General</option>
                                    <option value="Management" {{ old('admission_quota', $profile->admission_quota ?? '') == 'Management' ? 'selected' : '' }}>Management</option>
                                    <option value="NRI" {{ old('admission_quota', $profile->admission_quota ?? '') == 'NRI' ? 'selected' : '' }}>NRI</option>
                                    <option value="Other" {{ old('admission_quota', $profile->admission_quota ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother Tongue</label>
                                <input type="text" name="mother_tongue" class="form-control" value="{{ old('mother_tongue', $profile->mother_tongue ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nationality</label>
                                <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $profile->nationality ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Marital Status</label>
                                <select name="marital_status" class="form-control" disabled>
                                    <option value="">-- Select --</option>
                                    <option value="Single" {{ old('marital_status', $profile->marital_status ?? '') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('marital_status', $profile->marital_status ?? '') == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Other" {{ old('marital_status', $profile->marital_status ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Blood Group</label>
                                <select name="blood_group" class="form-control" disabled>
                                    <option value="">-- Select --</option>
                                    <option value="A+" {{ old('blood_group', $profile->blood_group ?? '') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_group', $profile->blood_group ?? '') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_group', $profile->blood_group ?? '') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_group', $profile->blood_group ?? '') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="O+" {{ old('blood_group', $profile->blood_group ?? '') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_group', $profile->blood_group ?? '') == 'O-' ? 'selected' : '' }}>O-</option>
                                    <option value="AB+" {{ old('blood_group', $profile->blood_group ?? '') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_group', $profile->blood_group ?? '') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Aadhaar ID *</label>
                                <input type="text" name="aadhaar_id" class="form-control" value="{{ old('aadhaar_id', $profile->aadhaar_id ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Passport No</label>
                                <input type="text" name="passport_no" class="form-control" value="{{ old('passport_no', $profile->passport_no ?? '') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Admission Date *</label>
                                <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $profile->admission_date ?? '') }}" required readonly>
                            </div>
                        </div>
                        <!-- 2. Present Address -->
                        <h4 class="mb-3 mt-4">🔹 2. Present Address</h4>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>State *</label>
                                <input type="text" name="present_state" class="form-control" value="{{ old('present_state', $profile->present_state ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>District/City *</label>
                                <input type="text" name="present_city" class="form-control" value="{{ old('present_city', $profile->present_city ?? '') }}" required readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Address *</label>
                                <input type="text" name="present_address" class="form-control" value="{{ old('present_address', $profile->present_address ?? '') }}" required readonly>
                            </div>
                        </div>
                        <!-- 3. Permanent Address -->
                        <h4 class="mb-3 mt-4">🔹 3. Permanent Address</h4>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>State *</label>
                                <input type="text" name="permanent_state" class="form-control" value="{{ old('permanent_state', $profile->permanent_state ?? '') }}" required readonly>
        </div>
                            <div class="col-md-4 mb-3">
                                <label>District/City *</label>
                                <input type="text" name="permanent_city" class="form-control" value="{{ old('permanent_city', $profile->permanent_city ?? '') }}" required readonly>
        </div>
                            <div class="col-md-4 mb-3">
                                <label>Address *</label>
                                <input type="text" name="permanent_address" class="form-control" value="{{ old('permanent_address', $profile->permanent_address ?? '') }}" required readonly>
        </div>
        </div>
                        <!-- Document Upload -->
                        <div class="mb-4 mt-4">
                            <label>Upload Document (optional)</label>
                            <input type="file" name="document" class="form-control" disabled>
                            @if($profile && $profile->document_path)
                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $profile->document_path) }}" target="_blank" class="text-blue-600 underline">Download Current Document</a>
                                </div>
                            @endif
                        </div>
                        <button type="submit" id="saveBtn" class="btn btn-success">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function enableEdit() {
    document.getElementById('profileTableWrapper').style.display = 'none';
    document.getElementById('profileForm').style.display = 'block';
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.type !== 'file') input.removeAttribute('readonly');
        input.removeAttribute('disabled');
    });
    document.getElementById('saveBtn').classList.remove('d-none');
    document.getElementById('editBtn').classList.add('d-none');
}

// Ensure all fields are enabled before submitting
const profileForm = document.getElementById('profileForm');
if (profileForm) {
    profileForm.addEventListener('submit', function() {
        const inputs = profileForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.removeAttribute('disabled');
        });
    });
}

// On page load, if just saved, show table and hide form
@if(session('status') === 'profile-updated')
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('profileTableWrapper').style.display = 'block';
        document.getElementById('profileForm').style.display = 'none';
        document.getElementById('editBtn').classList.remove('d-none');
    });
@endif
</script>
@endsection
