@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>
    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf
        @method('PATCH')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', auth()->user()->name) }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ old('email', auth()->user()->email) }}" disabled>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Phone</label>
            <input type="text" name="phone" class="w-full border rounded px-3 py-2" value="{{ old('phone', auth()->user()->phone) }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Address</label>
            <input type="text" name="address" class="w-full border rounded px-3 py-2" value="{{ old('address', auth()->user()->address) }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Upload Document (optional)</label>
            <input type="file" name="document" class="w-full border rounded px-3 py-2">
            @if(auth()->user()->document_path)
                <div class="mt-2">
                    <a href="{{ asset('storage/' . auth()->user()->document_path) }}" target="_blank" class="text-blue-600 underline">Download Current Document</a>
                </div>
            @endif
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Profile</button>
    </form>
</div>
@endsection
