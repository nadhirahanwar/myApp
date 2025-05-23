@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">

                        </div>
                    @endif

                     <form method="POST" action="{{ route('mfa.verify') }}">
                        @csrf
                        <div class="form-group">
                            <label for="code">{{ __('Enter the verification code sent to your email') }}</label>
                            <input type="text" name="code" id="code" class="form-control" required autofocus>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Verify Code') }}
                        </button>
                    </form>
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
