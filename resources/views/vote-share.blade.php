@section('meta')
    <x-seo
        :title="'Vote for ' . ($submission->title ?? 'this entry') . ' | ' . ($competition->title ?? 'Competition')"
        :description="'Support this entry in ' . ($competition->title ?? 'our competition') . ' on Photographer SB.'"
        :canonical="url()->current()"
        :image="$imageUrl"
        type="article"
    />
@endsection

@extends('app')

@section('content')
    <main class="min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto px-6 py-16">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex flex-col gap-6">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-500">Photographer SB</p>
                        <h1 class="text-2xl font-semibold text-gray-900 mt-2">
                            Vote for {{ $submission->title ?? 'this entry' }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-2">
                            {{ $competition->title ?? 'Photography Competition' }}
                        </p>
                    </div>

                    <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-100">
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $submission->title ?? 'Submission image' }}"
                            class="w-full h-auto object-cover"
                        />
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <p class="text-sm text-gray-600">
                            Redirecting you to the voting page...
                        </p>
                        <a
                            href="{{ $voteUrl }}"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-burgundy text-white font-semibold hover:bg-burgundy-dark"
                        >
                            Go to voting page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        setTimeout(function () {
            window.location.href = @json($voteUrl);
        }, 1200);
    </script>
@endsection
