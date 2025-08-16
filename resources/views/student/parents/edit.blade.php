@extends('layouts.admin')

@section('title', 'Add Parent Details')

@section('content')

   
   
 
<div class="row justify-content-center">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add / Edit Parent Details</h6>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success" id="success-alert">{{ session('success') }}</div>
                    @php session()->forget('success'); @endphp
                @endif
                <form method="POST" action="{{ route('student.parents.update') }}">
                    @csrf
                    <div class="form-group">
                        <label for="parent_mobile">Parent Mobile Number</label>
                        <input type="text" class="form-control" id="parent_mobile" name="parent_mobile" value="{{ old('parent_mobile', $student->parent_mobile ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="parent_email">Parent Email ID</label>
                        <input type="email" class="form-control" id="parent_email" name="parent_email" value="{{ old('parent_email', $student->parent_email ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="alternate_mobile">Alternate Mobile Number</label>
                        <input type="text" class="form-control" id="alternate_mobile" name="alternate_mobile" value="{{ old('alternate_mobile', $student->alternate_mobile ?? '') }}">
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Current Parent Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th>Parent Mobile Number</th>
                        <td>{{ $student->parent_mobile ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Parent Email ID</th>
                        <td>{{ $student->parent_email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alternate Mobile Number</th>
                        <td>{{ $student->alternate_mobile ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var alert = document.getElementById('success-alert');
        if(alert) {
            setTimeout(function() {
                alert.style.display = 'none';
            }, 2000);
        }
    });
</script>
@endpush 