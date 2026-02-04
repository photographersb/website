@extends('layouts.admin')

@section('title', 'Error Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        {{-- Back Link --}}
        <div class="mb-6">
            <a href="{{ route('admin.error-center.index') }}" class="text-blue-600 hover:text-blue-900 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Error Center
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content (2 columns) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Error Header Card --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                {{-- Severity Badge --}}
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold
                                    @if($error->severity === 'P0') bg-red-100 text-red-800
                                    @elseif($error->severity === 'P1') bg-orange-100 text-orange-800
                                    @elseif($error->severity === 'P2') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ $error->severity }}
                                </span>

                                {{-- Status Badge --}}
                                @if($error->is_muted)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gray-100 text-gray-700">
                                        🔇 Muted
                                    </span>
                                @elseif($error->is_resolved)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-700">
                                        ✅ Resolved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-700">
                                        ⚠️ Open
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $error->message }}</h1>
                        </div>

                        {{-- Quick Actions --}}
                        <div class="flex gap-2 ml-4">
                            @if(!$error->is_resolved && !$error->is_muted)
                                <button 
                                    onclick="resolveError()" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium"
                                    title="Mark as Resolved"
                                >
                                    Resolve
                                </button>
                                
                                <button 
                                    onclick="muteError()" 
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium"
                                    title="Mute Similar Errors"
                                >
                                    Mute
                                </button>
                            @elseif($error->is_resolved)
                                <button 
                                    onclick="reopenError()" 
                                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-medium"
                                    title="Reopen"
                                >
                                    Reopen
                                </button>
                            @elseif($error->is_muted)
                                <button 
                                    onclick="unmuteError()" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium"
                                    title="Unmute"
                                >
                                    Unmute
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Error Details --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">Error Details</h2>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Exception Class</label>
                                <code class="block text-sm bg-gray-100 p-2 rounded font-mono text-gray-900">
                                    {{ $error->exception_class }}
                                </code>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Status Code</label>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $error->status_code ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">File</label>
                                <code class="block text-sm bg-gray-100 p-2 rounded font-mono text-gray-900 break-all">
                                    {{ $error->file }}:{{ $error->line }}
                                </code>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Environment</label>
                                <p class="text-sm text-gray-900">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($error->environment === 'production') bg-red-100 text-red-700
                                        @elseif($error->environment === 'staging') bg-yellow-100 text-yellow-700
                                        @else bg-blue-100 text-blue-700
                                        @endif
                                    ">
                                        {{ ucfirst($error->environment) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Route</label>
                            <p class="text-sm text-gray-900">{{ $error->route_name ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">URL</label>
                            <a 
                                href="{{ $error->url }}" 
                                target="_blank" 
                                class="text-blue-600 hover:text-blue-900 text-sm break-all"
                            >
                                {{ $error->url }}
                                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Stack Trace (Super Admin Only) --}}
                @if(Auth::user()->hasRole('super_admin') && $error->trace)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-lg font-semibold text-gray-900">Stack Trace</h2>
                        </div>
                        <div class="px-6 py-4">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded text-xs overflow-x-auto"><code>{{ json_decode($error->trace, true) ? json_encode(json_decode($error->trace), JSON_PRETTY_PRINT) : $error->trace }}</code></pre>
                        </div>
                    </div>
                @endif

                {{-- Request Information --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">Request Information</h2>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Method</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                                    @if($error->method === 'GET') bg-blue-100 text-blue-700
                                    @elseif($error->method === 'POST') bg-green-100 text-green-700
                                    @elseif($error->method === 'PUT' || $error->method === 'PATCH') bg-yellow-100 text-yellow-700
                                    @elseif($error->method === 'DELETE') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ $error->method }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">IP Address</label>
                                <p class="text-sm text-gray-900 font-mono">{{ $error->ip }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">User Agent</label>
                            <p class="text-sm text-gray-900 break-all">{{ $error->user_agent }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">First Seen</label>
                                <p class="text-sm text-gray-900">{{ $error->first_seen_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Last Seen</label>
                                <p class="text-sm text-gray-900">{{ $error->last_seen_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar (1 column) --}}
            <div class="space-y-6">
                {{-- Statistics Card --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Occurrences</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $error->occurrences }}</p>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 mb-2">Signature Hash</p>
                            <code class="text-xs bg-gray-100 p-2 rounded block break-all font-mono">
                                {{ $error->signature_hash }}
                            </code>
                        </div>

                        @if($error->user)
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600 mb-2">User</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $error->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $error->user->email }}</p>
                            </div>
                        @endif

                        @if($error->is_resolved && $error->resolvedByUser)
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600 mb-2">Resolved By</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $error->resolvedByUser->name }}</p>
                                <p class="text-xs text-gray-500">{{ $error->resolved_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Related Errors --}}
                @if($relatedErrors->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Related Errors ({{ $relatedErrors->count() }})
                        </h3>
                        <div class="space-y-2">
                            @foreach($relatedErrors as $related)
                                <a 
                                    href="{{ route('admin.error-center.show', $related) }}"
                                    class="block p-2 bg-gray-50 hover:bg-gray-100 rounded text-sm text-blue-600 hover:text-blue-900 transition"
                                >
                                    <p class="font-medium truncate">{{ Str::limit($related->message, 40) }}</p>
                                    <p class="text-xs text-gray-500">{{ $related->last_seen_at->diffForHumans() }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Similar Errors --}}
                @if($similarErrors->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Similar Errors ({{ $similarErrors->count() }})
                        </h3>
                        <div class="space-y-2">
                            @foreach($similarErrors as $similar)
                                <a 
                                    href="{{ route('admin.error-center.show', $similar) }}"
                                    class="block p-2 bg-gray-50 hover:bg-gray-100 rounded text-sm text-blue-600 hover:text-blue-900 transition"
                                >
                                    <p class="font-medium truncate">{{ Str::limit($similar->message, 40) }}</p>
                                    <p class="text-xs text-gray-500">{{ $similar->last_seen_at->diffForHumans() }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notes Section --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>

                    {{-- Add Note Form --}}
                    <form onsubmit="addNote(event)" class="mb-4">
                        <textarea 
                            id="noteText"
                            placeholder="Add a note..." 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows="3"
                        ></textarea>
                        <button 
                            type="submit" 
                            class="mt-2 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 w-full"
                        >
                            Add Note
                        </button>
                    </form>

                    {{-- Notes List --}}
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($notes as $note)
                            <div class="p-3 bg-gray-50 rounded border border-gray-200 text-sm">
                                <div class="flex items-start justify-between mb-1">
                                    <p class="font-medium text-gray-900">{{ $note->addedBy->name }}</p>
                                    <span class="text-xs text-gray-500">{{ $note->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700">{{ $note->note }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No notes yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const errorId = {{ $error->id }};

    function resolveError() {
        if (confirm('Mark this error as resolved?')) {
            fetch(`/api/v1/admin/system-health/errors/${errorId}/resolve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to resolve error');
                }
            });
        }
    }

    function reopenError() {
        if (confirm('Reopen this error?')) {
            fetch(`/api/v1/admin/system-health/errors/${errorId}/reopen`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to reopen error');
                }
            });
        }
    }

    function muteError() {
        if (confirm('Mute all errors with this signature?')) {
            fetch(`/api/v1/admin/system-health/errors/${errorId}/mute`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to mute error');
                }
            });
        }
    }

    function unmuteError() {
        if (confirm('Unmute all errors with this signature?')) {
            fetch(`/api/v1/admin/system-health/errors/${errorId}/unmute`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to unmute error');
                }
            });
        }
    }

    function addNote(event) {
        event.preventDefault();
        const noteText = document.getElementById('noteText').value.trim();
        
        if (!noteText) {
            alert('Please enter a note');
            return;
        }

        fetch(`/api/v1/admin/system-health/errors/${errorId}/notes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ note: noteText })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('noteText').value = '';
                location.reload();
            } else {
                alert('Failed to add note');
            }
        });
    }
</script>
@endsection
