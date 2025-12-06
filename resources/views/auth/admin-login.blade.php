@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 text-center">
                    <h4 class="mb-0 fw-bold">Admin Login</h4>
                </div>
                <div class="card-body p-5">
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.login.submit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="admin@store.com" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg bg-light border-0" placeholder="••••••••" required>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm fw-bold">
                                Authenticate
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3 border-0">
                    <small class="text-muted">Secure Access Area</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
