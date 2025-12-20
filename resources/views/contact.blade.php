@extends('layouts.master')

@section('title', 'Contact Us • MyStore')

@section('content')
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-6">
            <h1 class="fw-bold mb-4">Get in Touch</h1>
            <p class="text-muted mb-5">Have questions about our products or your order? We're here to help! Fill out the form or reach us via the details below.</p>

            <div class="d-flex mb-4">
                <div class="flex-shrink-0">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="bi bi-geo-alt fs-4"></i>
                    </div>
                </div>
                <div class="ms-3">
                    <h5 class="fw-bold mb-1">Our Location</h5>
                    <p class="text-muted mb-0">123 Commerce St, Tech City, TC 90210</p>
                </div>
            </div>

            <div class="d-flex mb-4">
                <div class="flex-shrink-0">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="bi bi-envelope fs-4"></i>
                    </div>
                </div>
                <div class="ms-3">
                    <h5 class="fw-bold mb-1">Email Us</h5>
                    <p class="text-muted mb-0">support@mystore.com</p>
                </div>
            </div>

            <div class="d-flex justify-content-start gap-3 mt-5">
                <a href="#" class="btn btn-outline-dark rounded-circle"><i class="bi bi-facebook"></i></a>
                <a href="#" class="btn btn-outline-dark rounded-circle"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="btn btn-outline-dark rounded-circle"><i class="bi bi-instagram"></i></a>
                <a href="#" class="btn btn-outline-dark rounded-circle"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h4 class="fw-bold text-primary">Send us a Message</h4>
                    <p class="text-muted small">We typically reply within 24 hours.</p>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">First Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                    <input type="text" name="first_name" class="form-control bg-light border-0 py-2" required placeholder="John" value="{{ old('first_name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Last Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                    <input type="text" name="last_name" class="form-control bg-light border-0 py-2" required placeholder="Doe" value="{{ old('last_name') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control bg-light border-0 py-2" required placeholder="john@example.com" value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Subject</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-chat-dots"></i></span>
                                    <input type="text" name="subject" class="form-control bg-light border-0 py-2" required placeholder="Order #12345" value="{{ old('subject') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Message</label>
                                <textarea name="message" class="form-control bg-light border-0 p-3" rows="5" required placeholder="How can we help you?">{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm hover-scale text-uppercase" style="letter-spacing: 0.5px;">
                                    Send Message <i class="bi bi-send-fill ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
