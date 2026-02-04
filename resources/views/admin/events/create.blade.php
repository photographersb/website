@extends('layouts.admin')

@section('title', 'Create New Event')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Create New Event</h1>
            <p class="text-muted mt-1">Add a new event/workshop to the platform</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.events._form')
    </form>
</div>
@endsection
