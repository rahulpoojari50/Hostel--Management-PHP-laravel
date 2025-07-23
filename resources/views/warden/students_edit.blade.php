@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Edit Student</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('warden.students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $student->address) }}">
                </div>
                @if(isset($assignment) && $assignment)
                <div class="form-group">
                    <label>Current Room</label>
                    <input type="text" class="form-control" value="Room {{ $assignment->room->room_number ?? '-' }} (Floor {{ $assignment->room->floor ?? '-' }})" disabled>
                </div>
                <div class="form-group">
                    <label>Change Room</label>
                    <select name="room_id" class="form-control">
                        <option value="">-- Keep Current Room --</option>
                        @foreach($availableRooms as $room)
                            <option value="{{ $room->id }}" @if($assignment->room_id == $room->id) selected @endif>
                                Room {{ $room->room_number }} (Floor {{ $room->floor }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <button type="submit" class="btn btn-primary">Update Student</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection 