@extends('layouts.admin')

@section('title', $event->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="h3 mb-0">{{ $event->title }}</h1>
                    <p class="text-muted mt-1">{{ $event->venue_name ?? 'TBD' }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <button class="btn btn-outline-secondary" onclick="document.getElementById('copyUrlForm').submit()">
                        <i class="fas fa-link me-2"></i>View Public
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Event Banner -->
            @if($event->banner_image)
            <div class="card border-0 shadow-sm mb-4">
                <img src="{{ asset('storage/' . $event->banner_image) }}" class="card-img-top" alt="{{ $event->title }}"
                    <picture>
                        <source srcset="{{ preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', asset('storage/' . $event->banner_image)) }}" type="image/webp">
                        <img src="{{ asset('storage/' . $event->banner_image) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 300px; object-fit: cover;" loading="lazy">
                    </picture>
            </div>
            @endif

            <!-- Event Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Event Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">Status</h6>
                            <span class="badge bg-{{ $event->status === 'published' ? 'success' : 
                                ($event->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">Type</h6>
                            <span class="badge bg-{{ $event->event_type === 'free' ? 'success' : 'warning' }}">
                                {{ ucfirst($event->event_type) }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">Start Date & Time</h6>
                            <p class="mb-3">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $event->start_datetime?->format('M d, Y H:i') ?? 'TBD' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">End Date & Time</h6>
                            <p class="mb-3">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $event->end_datetime?->format('M d, Y H:i') ?? 'TBD' }}
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">Registration Deadline</h6>
                            <p>{{ $event->registration_deadline?->format('M d, Y H:i') ?? 'No deadline' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">Booking Close</h6>
                            <p>{{ $event->booking_close_datetime?->format('M d, Y H:i') ?? 'No close date' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>{{ $event->venue_name }}</strong><br>
                        {{ $event->venue_address }}
                    </p>
                    @if($event->city)
                    <p class="text-muted mb-2">
                        <i class="fas fa-city me-2"></i>{{ $event->city->name }}
                    </p>
                    @endif
                    @if($event->latitude && $event->longitude)
                    <small class="text-muted">📍 {{ $event->latitude }}, {{ $event->longitude }}</small>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Description</h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>

            @if($event->requirements)
            <!-- Requirements -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Requirements</h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($event->requirements)) !!}
                </div>
            </div>
            @endif

            @if($event->refund_policy)
            <!-- Refund Policy -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Refund Policy</h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($event->refund_policy)) !!}
                </div>
            </div>
            @endif

            <!-- Mentors -->
            @if($event->mentors->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Mentors ({{ $event->mentors->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($event->mentors as $mentor)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <strong>{{ $mentor->name }}</strong><br>
                                    <small class="text-muted">{{ $mentor->email ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Pricing -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Pricing & Capacity</h5>
                </div>
                <div class="card-body">
                    @if($event->event_type === 'paid')
                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase small mb-2">Price</h6>
                        <p class="h5 mb-0">৳ {{ number_format($event->price ?? 0, 2) }}</p>
                    </div>
                    @else
                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase small mb-2">Price</h6>
                        <p class="h5 mb-0">FREE</p>
                    </div>
                    @endif

                    <hr>

                    <div>
                        <h6 class="text-muted text-uppercase small mb-2">Capacity</h6>
                        <p class="mb-0">
                            <strong>{{ $event->registrations_count ?? 0 }}/{{ $event->capacity ?? '∞' }}</strong>
                            registered
                        </p>
                    </div>
                </div>
            </div>

            <!-- Certificates -->
            @if($event->certificates_enabled)
            <div class="card border-0 shadow-sm mb-4 border-success">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-certificate me-2 text-success"></i>Certificates</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <small class="text-muted">Auto-issue certificates to attendees</small><br>
                        Template: <strong>{{ $event->certificateTemplate->name ?? 'Default' }}</strong>
                    </p>
                </div>
            </div>
            @endif

            <!-- Organizer -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Organizer</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <strong>{{ $event->organizer->name ?? 'N/A' }}</strong><br>
                        <small class="text-muted">{{ $event->organizer->email ?? '' }}</small>
                    </p>
                </div>
            </div>

            <!-- Category -->
            @if($event->category)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Category</h5>
                </div>
                <div class="card-body">
                    <span class="badge bg-primary">{{ $event->category->name }}</span>
                </div>
            </div>
            @endif

            <!-- Featured -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Featured</h5>
                </div>
                <div class="card-body">
                    @if($event->is_featured)
                    <span class="badge bg-info"><i class="fas fa-star me-1"></i>Featured</span>
                    @if($event->featured_until)
                    <p class="small text-muted mt-2 mb-0">Until: {{ $event->featured_until->format('M d, Y') }}</p>
                    @endif
                    @else
                    <span class="badge bg-light text-dark">Not Featured</span>
                    @endif
                </div>
            </div>

            <!-- Meta Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Meta Information</h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Created:</strong><br>
                        {{ $event->created_at->format('M d, Y H:i') }}
                    </p>
                    <p class="mb-2">
                        <strong>Updated:</strong><br>
                        {{ $event->updated_at->format('M d, Y H:i') }}
                    </p>
                    <p class="mb-0">
                        <strong>UUID:</strong><br>
                        <code class="small">{{ $event->uuid }}</code>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for public link -->
<form id="copyUrlForm" action="{{ route('events.show', $event->slug) }}" style="display:none;"></form>
@endsection
