@extends('layouts.admin')

@section('title', 'Events Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Events Management</h1>
                    <p class="text-muted mt-1">Manage all event/workshop events</p>
                </div>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create New Event
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.events.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by title..." 
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="free" {{ request('type') === 'free' ? 'selected' : '' }}>Free</option>
                                <option value="paid" {{ request('type') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Event Title</th>
                                <th>Type</th>
                                <th>City</th>
                                <th>Date</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($event->banner_image)
                                        <img src="{{ asset('storage/' . $event->banner_image) }}" 
                                            alt="{{ $event->title }}" class="rounded me-2" 
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                        <div class="bg-light rounded me-2" style="width: 40px; height: 40px; 
                                            display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <strong>{{ $event->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $event->venue ?? 'No venue' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $event->event_type === 'free' ? 'success' : 'warning' }}">
                                        {{ ucfirst($event->event_type) }}
                                    </span>
                                </td>
                                <td>{{ $event->city?->name ?? 'N/A' }}</td>
                                <td>
                                    {{ $event->start_datetime?->format('M d, Y') ?? 'TBD' }}
                                    @if($event->start_datetime)
                                    <br>
                                    <small class="text-muted">{{ $event->start_datetime->format('H:i') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($event->capacity)
                                    {{ $event->registrations_count ?? 0 }}/{{ $event->capacity }}
                                    @else
                                    Unlimited
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $event->status === 'published' ? 'success' : 
                                        ($event->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($event->is_featured)
                                    <span class="badge bg-info"><i class="fas fa-star me-1"></i>Featured</span>
                                    @else
                                    <span class="badge bg-light text-dark">Not Featured</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" onclick="deleteEvent({{ $event->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0">No events found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($events->hasPages())
            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-center">
                    {{ $events->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this event? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteEvent(eventId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/events/${eventId}`;
    modal.show();
}
</script>
@endsection
