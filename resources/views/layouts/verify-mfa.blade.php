@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3>Verify Your Email Code</h3>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first('code') }}
        </div>
    @endif

    <form action="{{ url('/verify-mfa') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="code">Enter the 6-digit code sent to your email:</label>
            <input type="text" name="code" id="code" class="form-control" required>
        </div>
        <button class="btn btn-primary mt-3" type="submit">Verify Code</button>
    </form>

    <form action="{{ url('/resend-mfa') }}" method="POST" class="mt-3">
        @csrf
        <button class="btn btn-secondary">Resend Code</button>
    </form>
</div>
@endsection
