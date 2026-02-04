@extends('layouts.admin')

@section('title', 'Edit Event: ' . $event->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Edit Event</h1>
            <p class="text-muted mt-1">{{ $event->title }}</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.events._form', ['event' => $event])
    </form>
</div>
@endsection
