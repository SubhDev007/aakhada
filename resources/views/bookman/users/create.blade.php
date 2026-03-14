@extends('layouts.bookman')

@section('title', 'Add New User')

@section('content')
<div class="row justify-content-center m-0">
    <div class="col-md-8 p-0 mt-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>Create User Account
                </h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('bookman.users.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold text-dark">Full Name</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0 shadow-sm @error('name') is-invalid @enderror" 
                            id="name" name="name" value="{{ old('name') }}" required placeholder="Enter user's full name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold text-dark">Email Address</label>
                        <input type="email" class="form-control form-control-lg bg-light border-0 shadow-sm @error('email') is-invalid @enderror" 
                            id="email" name="email" value="{{ old('email') }}" required placeholder="Enter user's email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label fw-bold text-dark">Password</label>
                            <input type="password" class="form-control form-control-lg bg-light border-0 shadow-sm @error('password') is-invalid @enderror" 
                                id="password" name="password" required placeholder="Minimum 8 characters">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="password_confirmation" class="form-label fw-bold text-dark">Confirm Password</label>
                            <input type="password" class="form-control form-control-lg bg-light border-0 shadow-sm" 
                                id="password_confirmation" name="password_confirmation" required placeholder="Retype password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                        <a href="{{ route('bookman.users.index') }}" class="btn btn-light btn-lg rounded-pill px-4 text-dark fw-medium">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
