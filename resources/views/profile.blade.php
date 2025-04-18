@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center mb-4">Profile</h2>

    @if (session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if (!$editMode)
        <div class="card mx-auto p-4" style="max-width: 500px;">
            <div class="text-center mb-3">
                <img src="{{ $user->avatar && Storage::exists($user->avatar)
                    ? Storage::url($user->avatar)
                    : asset('images/default-avatar.png') }}"
                    alt="Avatar"
                    class="rounded-circle"
                    width="120"
                    height="120">
            </div>

            <p><strong>Nickname:</strong> {{ $user->nickname ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone Number:</strong> {{ $user->phone ?? '-' }}</p>
            <p><strong>City:</strong> {{ $user->city ?? '-' }}</p>

            <div class="text-center mt-4">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    @else
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
            @csrf
            @method('PUT')

            <div class="form-group text-center">
                <label class="d-block mb-2">Current Avatar</label>
                <img src="{{ $user->avatar && Storage::exists($user->avatar)
                    ? Storage::url($user->avatar)
                    : asset('images/default-avatar.png') }}"
                    alt="Avatar"
                    class="rounded-circle mb-3"
                    width="120"
                    height="120">
                <input type="file" name="avatar" class="form-control-file">
            </div>

            <div class="form-group">
                <label for="nickname">Nickname</label>
                <input type="text" name="nickname" id="nickname"
                    value="{{ old('nickname', $user->nickname) }}"
                    class="form-control" />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email"
                    value="{{ old('email', $user->email) }}"
                    class="form-control" />
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone"
                    value="{{ old('phone', $user->phone) }}"
                    class="form-control" />
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input type="text" name="city" id="city"
                    value="{{ old('city', $user->city) }}"
                    class="form-control" />
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" class="form-control" />
                <small class="form-text text-muted">Leave blank to keep current password.</small>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" />
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success">Update Profile</button>

                <form action="{{ route('profile.destroy') }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete your account?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </form>
            </div>
        </form>
    @endif
</div>
@endsection
