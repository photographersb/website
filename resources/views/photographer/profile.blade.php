@extends('layouts.public')

@section('meta')
    @if($seoMeta)
        <!-- Title -->
        <title>{{ $seoMeta->meta_title }}</title>
        
        <!-- Meta Description -->
        <meta name="description" content="{{ $seoMeta->meta_description }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ $seoMeta->canonical_url }}">
        
        <!-- OpenGraph Tags -->
        <meta property="og:type" content="profile">
        <meta property="og:title" content="{{ $seoMeta->meta_title }}">
        <meta property="og:description" content="{{ $seoMeta->meta_description }}">
        <meta property="og:url" content="{{ $seoMeta->canonical_url }}">
        @if($seoMeta->og_image)
            <meta property="og:image" content="{{ $seoMeta->og_image }}">
            <meta property="og:image:alt" content="{{ $seoMeta->meta_title }}">
        @endif
        <meta property="og:site_name" content="Photographer SB">
        
        <!-- Twitter Card Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoMeta->meta_title }}">
        <meta name="twitter:description" content="{{ $seoMeta->meta_description }}">
        @if($seoMeta->og_image)
            <meta name="twitter:image" content="{{ $seoMeta->og_image }}">
        @endif
        
        <!-- Robots Meta -->
        <meta name="robots" content="{{ $seoMeta->getRobotsMetaTag() }}">
        
        <!-- Schema.org JSON-LD -->
        @if($seoMeta->schema_json)
            <script type="application/ld+json">
                {!! json_encode($seoMeta->schema_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
            </script>
        @endif
    @endif
@endsection

@section('content')
@php
    $profilePhoto = $photographer?->profile_picture ?: $user->profile_photo_url ?: '/placeholder-photographer.jpg';
    $portfolioItems = collect($portfolios->items());
    $featuredPortfolio = $portfolioItems->take(5);
    $heroPortfolio = $featuredPortfolio->first();
    $galleryPreview = $featuredPortfolio->slice(1, 4);

    $specializationsRaw = $photographer?->specializations;
    if (is_string($specializationsRaw)) {
        $decoded = json_decode($specializationsRaw, true);
        $specializations = is_array($decoded)
            ? $decoded
            : collect(explode(',', $specializationsRaw))->map(fn($item) => trim($item))->filter()->values()->all();
    } elseif (is_array($specializationsRaw)) {
        $specializations = collect($specializationsRaw)->filter()->values()->all();
    } else {
        $specializations = [];
    }

    $awardsRaw = data_get($photographer, 'awards', []);
    if (is_string($awardsRaw)) {
        $decodedAwards = json_decode($awardsRaw, true);
        $awards = is_array($decodedAwards) ? $decodedAwards : [];
    } elseif ($awardsRaw instanceof \Illuminate\Support\Collection) {
        $awards = $awardsRaw->toArray();
    } elseif (is_array($awardsRaw)) {
        $awards = $awardsRaw;
    } else {
        $awards = [];
    }

    $socialLinks = [
        ['label' => 'Instagram', 'key' => 'instagram_url', 'icon' => 'IG'],
        ['label' => 'Facebook', 'key' => 'facebook_url', 'icon' => 'FB'],
        ['label' => 'Twitter/X', 'key' => 'twitter_url', 'icon' => 'X'],
        ['label' => 'YouTube', 'key' => 'youtube_url', 'icon' => 'YT'],
        ['label' => 'Website', 'key' => 'website', 'icon' => 'WEB'],
        ['label' => 'Pexels', 'key' => 'pexels_url', 'icon' => 'PX'],
    ];

    $resolvedSocialLinks = collect($socialLinks)->map(function ($item) use ($photographer, $user) {
        $value = data_get($photographer, $item['key']) ?: data_get($user, $item['key']);
        if (blank($value)) {
            return null;
        }
        $url = str_starts_with($value, 'http') ? $value : 'https://' . ltrim($value, '/');
        return array_merge($item, ['url' => $url]);
    })->filter()->values();

    $profileUrl = $seoMeta->canonical_url ?? route('photographer.profile.public', ['username' => $user->username]);

    $experienceYears = data_get($photographer, 'experience_years')
        ?? data_get($photographer, 'years_of_experience')
        ?? null;

    $photographyStyle = data_get($photographer, 'photography_style')
        ?? data_get($photographer, 'style')
        ?? null;

    $responseTime = data_get($photographer, 'response_time')
        ?? data_get($photographer, 'average_response_time')
        ?? 'Not specified';

    $completedBookings = data_get($photographer, 'completed_bookings_count')
        ?? data_get($photographer, 'completed_bookings')
        ?? 0;
@endphp

<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="grid grid-cols-1 lg:grid-cols-[auto,1fr,auto] gap-6 items-start lg:items-center">
                <div class="mx-auto lg:mx-0">
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden border-4 border-burgundy shadow-lg">
                        <picture>
                            <source srcset="{{ preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $profilePhoto) }}" type="image/webp">
                            <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                        </picture>
                    </div>
                </div>

                <div class="space-y-3 text-center lg:text-left">
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">{{ $user->name }}</h1>
                        @if($isVerified)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Verified</span>
                        @endif
                        @if($isAvailable)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Available</span>
                        @endif
                    </div>

                    <p class="text-gray-600 font-medium">{{ '@' . $user->username }}</p>

                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 text-sm text-gray-600">
                        @if($photographer?->district)
                            <span>📍 {{ $photographer->district }}</span>
                        @endif
                        @if($experienceYears)
                            <span>🧭 {{ $experienceYears }}+ years</span>
                        @endif
                        <span>⭐ {{ number_format((float)$averageRating, 1) }} / 5</span>
                        <span>📝 {{ $ratingCount }} {{ Str::plural('review', $ratingCount) }}</span>
                    </div>

                    @if(!empty($specializations))
                        <div class="flex flex-wrap gap-2 justify-center lg:justify-start">
                            @foreach($specializations as $category)
                                <a
                                    href="{{ url('/photographers') . '?category=' . urlencode($category) }}"
                                    class="inline-flex items-center px-3 py-1 rounded-full bg-burgundy-50 text-burgundy-700 text-xs font-semibold hover:bg-burgundy-100 transition"
                                >
                                    {{ $category }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="w-full lg:w-auto">
                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                        <a href="{{ url('/bookings/create') . '?photographer_id=' . $photographer->id }}" class="sb-ui-btn sb-ui-btn--primary w-full">Book Photographer</a>
                        <a href="mailto:{{ $user->email }}" class="sb-ui-btn sb-ui-btn--secondary w-full">Send Inquiry</a>
                        <button type="button" id="savePhotographerBtn" class="sb-ui-btn sb-ui-btn--secondary w-full">Save Photographer</button>
                        <a href="#share-actions" class="sb-ui-btn sb-ui-btn--outline w-full">Share Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Featured Portfolio</h2>
                <span class="text-sm text-gray-500">Highlighted work</span>
            </div>

            @if($heroPortfolio)
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <button
                        type="button"
                        class="lg:col-span-8 relative group overflow-hidden rounded-xl"
                        data-gallery-index="0"
                        data-gallery-title="{{ $heroPortfolio->title ?? $user->name }}"
                        data-gallery-url="{{ $heroPortfolio->image_url ?? '/images/placeholder.svg' }}"
                    >
                        <img
                            src="{{ $heroPortfolio->image_url ?? '/images/placeholder.svg' }}"
                            alt="{{ $heroPortfolio->title ?? 'Featured photo' }}"
                            class="w-full aspect-[4/3] object-cover group-hover:scale-[1.02] transition duration-300"
                            loading="lazy"
                        >
                        <span class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition"></span>
                    </button>

                    <div class="lg:col-span-4 grid grid-cols-2 gap-3">
                        @foreach($galleryPreview as $index => $image)
                            <button
                                type="button"
                                class="relative group overflow-hidden rounded-xl"
                                data-gallery-index="{{ $index + 1 }}"
                                data-gallery-title="{{ $image->title ?? $user->name }}"
                                data-gallery-url="{{ $image->image_url ?? '/images/placeholder.svg' }}"
                            >
                                <img
                                    src="{{ $image->image_url ?? '/images/placeholder.svg' }}"
                                    alt="{{ $image->title ?? 'Portfolio image' }}"
                                    class="w-full aspect-square object-cover group-hover:scale-105 transition duration-300"
                                    loading="lazy"
                                >
                                <span class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition"></span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-center py-10">No featured portfolio yet.</p>
            @endif
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <div class="xl:col-span-8 space-y-8">
                <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">About</h2>
                    <div class="space-y-4">
                        <p class="text-gray-700 leading-relaxed">
                            {{ Str::limit($user->bio ?? 'No bio provided yet.', 180) }}
                        </p>
                        @if($user->bio)
                            <p class="text-gray-600 leading-relaxed">{{ $user->bio }}</p>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <p class="text-gray-500">Experience</p>
                                <p class="font-semibold text-gray-900">{{ $experienceYears ? $experienceYears . '+ years' : 'Not specified' }}</p>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <p class="text-gray-500">Photography Style</p>
                                <p class="font-semibold text-gray-900">{{ $photographyStyle ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">Portfolio Gallery</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                        @forelse($portfolios as $idx => $portfolio)
                            <button
                                type="button"
                                class="relative group overflow-hidden rounded-lg"
                                data-gallery-index="{{ $idx }}"
                                data-gallery-title="{{ $portfolio->title ?? $user->name }}"
                                data-gallery-url="{{ $portfolio->image_url ?? '/images/placeholder.svg' }}"
                            >
                                <img
                                    src="{{ $portfolio->image_url ?? '/images/placeholder.svg' }}"
                                    alt="{{ $portfolio->title ?? 'Portfolio image' }}"
                                    class="w-full aspect-[4/3] object-cover group-hover:scale-105 transition duration-300"
                                    loading="lazy"
                                >
                                <span class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition"></span>
                            </button>
                        @empty
                            <p class="col-span-full text-center text-gray-500 py-8">No portfolio items yet.</p>
                        @endforelse
                    </div>

                    @if($portfolios->hasPages())
                        <div class="mt-6">{{ $portfolios->links() }}</div>
                    @endif
                </section>

                <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Reviews & Ratings</h2>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format((float)$averageRating, 1) }}</p>
                            <p class="text-sm text-gray-500">{{ $ratingCount }} {{ Str::plural('review', $ratingCount) }}</p>
                        </div>
                    </div>

                    @if($reviews->count() > 0)
                        <div class="space-y-5">
                            @foreach($reviews as $review)
                                <article class="border border-gray-100 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <img
                                            src="{{ $review->reviewer->profile_photo_url ?? '/placeholder.jpg' }}"
                                            alt="{{ $review->reviewer->name ?? 'Reviewer' }}"
                                            class="w-11 h-11 rounded-full object-cover"
                                            loading="lazy"
                                        >
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 justify-between">
                                                <h4 class="font-semibold text-gray-900">{{ $review->reviewer->name }}</h4>
                                                <span class="text-xs text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <p class="text-gray-700 mt-2 leading-relaxed">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @if($reviews->hasPages())
                            <div class="mt-6">{{ $reviews->links() }}</div>
                        @endif
                    @else
                        <p class="text-gray-500 text-center py-8">No reviews yet. Be the first to review.</p>
                    @endif
                </section>

                @if(!empty($awards))
                    <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">Awards</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($awards as $award)
                                <div class="p-4 rounded-lg border border-gray-100 bg-gray-50">
                                    <p class="font-semibold text-gray-900">
                                        {{ data_get($award, 'placement', data_get($award, 'position', 'Award')) }} — {{ data_get($award, 'title', data_get($award, 'award_title', 'Untitled')) }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ data_get($award, 'organization', 'Organization N/A') }} — {{ data_get($award, 'year', 'Year N/A') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="xl:col-span-4 space-y-6" id="share-actions">
                <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Trust & Performance</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500">Verified</p>
                            <p class="font-semibold text-gray-900">{{ $isVerified ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500">Completed Bookings</p>
                            <p class="font-semibold text-gray-900">{{ $completedBookings }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500">Response Time</p>
                            <p class="font-semibold text-gray-900">{{ $responseTime }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                            <p class="text-xs text-gray-500">Member Since</p>
                            <p class="font-semibold text-gray-900">{{ $user->created_at?->format('M Y') }}</p>
                        </div>
                    </div>
                </section>

                @if($resolvedSocialLinks->isNotEmpty())
                    <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Social & Web</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($resolvedSocialLinks as $social)
                                <a
                                    href="{{ $social['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    <span class="inline-flex w-7 h-7 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-700">{{ $social['icon'] }}</span>
                                    <span>{{ $social['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Share Profile</h3>
                    @include('photographer.partials.share-buttons', [
                        'user' => $user,
                        'seoMeta' => $seoMeta,
                    ])
                </section>

                <buy-me-coffee-button :photographer-id="{{ $photographer->id }}"></buy-me-coffee-button>

                @if($packages->count() > 0)
                    <section class="bg-white rounded-xl border border-gray-200 p-4 sm:p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Packages</h3>
                        <div class="space-y-3">
                            @foreach($packages as $package)
                                <article class="rounded-lg border border-gray-200 p-4 hover:border-burgundy/40 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="font-semibold text-gray-900">{{ $package->name }}</h4>
                                        <span class="text-burgundy-700 font-bold">৳{{ number_format((float)$package->price) }}</span>
                                    </div>

                                    @if($package->description)
                                        <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $package->description }}</p>
                                    @endif

                                    <div class="grid grid-cols-2 gap-2 mt-3 text-xs text-gray-600">
                                        <span>Duration: {{ data_get($package, 'duration_hours', 'N/A') }}h</span>
                                        <span>Delivery: {{ data_get($package, 'delivery_days', 'N/A') }}d</span>
                                    </div>

                                    <a href="{{ url('/bookings/create') . '?photographer_id=' . $photographer->id . '&package_id=' . $package->id }}" class="sb-ui-btn sb-ui-btn--primary sb-ui-btn--sm w-full mt-3">Book Package</a>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>
        </section>
    </div>

    <div id="portfolioLightbox" class="fixed inset-0 z-[100] hidden bg-black/80 backdrop-blur-sm p-4 sm:p-8">
        <div class="max-w-5xl mx-auto h-full flex flex-col">
            <div class="flex items-center justify-between text-white mb-3">
                <h3 id="lightboxTitle" class="font-semibold truncate pr-4">Portfolio Preview</h3>
                <button type="button" id="lightboxClose" class="sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm">Close</button>
            </div>
            <div class="relative flex-1 flex items-center justify-center">
                <button type="button" id="lightboxPrev" class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm">‹</button>
                <img id="lightboxImage" src="" alt="Portfolio image" class="max-h-[78vh] w-auto max-w-full object-contain rounded-lg">
                <button type="button" id="lightboxNext" class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm">›</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const saveButton = document.getElementById('savePhotographerBtn');
    const saveKey = 'saved_photographers';
    const photographerId = '{{ $photographer->id }}';

    if (saveButton) {
        const saved = JSON.parse(localStorage.getItem(saveKey) || '[]');
        const isSaved = saved.includes(String(photographerId));
        if (isSaved) saveButton.textContent = 'Saved ✓';

        saveButton.addEventListener('click', function () {
            const savedList = JSON.parse(localStorage.getItem(saveKey) || '[]');
            const id = String(photographerId);
            if (savedList.includes(id)) {
                localStorage.setItem(saveKey, JSON.stringify(savedList.filter(item => item !== id)));
                saveButton.textContent = 'Save Photographer';
                return;
            }
            savedList.push(id);
            localStorage.setItem(saveKey, JSON.stringify(savedList));
            saveButton.textContent = 'Saved ✓';
        });
    }

    const triggers = Array.from(document.querySelectorAll('[data-gallery-url]'));
    const lightbox = document.getElementById('portfolioLightbox');
    const image = document.getElementById('lightboxImage');
    const title = document.getElementById('lightboxTitle');
    const btnClose = document.getElementById('lightboxClose');
    const btnPrev = document.getElementById('lightboxPrev');
    const btnNext = document.getElementById('lightboxNext');

    const items = triggers.map((node) => ({
        url: node.getAttribute('data-gallery-url'),
        title: node.getAttribute('data-gallery-title') || 'Portfolio Image',
    }));
    let current = 0;

    const render = () => {
        if (!items.length) return;
        image.src = items[current].url;
        title.textContent = items[current].title;
    };

    const open = (index) => {
        current = index;
        render();
        lightbox.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        lightbox.classList.add('hidden');
        document.body.style.overflow = '';
    };

    triggers.forEach((node, index) => {
        node.addEventListener('click', () => open(index));
    });

    btnClose?.addEventListener('click', close);
    lightbox?.addEventListener('click', (event) => {
        if (event.target === lightbox) close();
    });
    btnPrev?.addEventListener('click', () => {
        current = (current - 1 + items.length) % items.length;
        render();
    });
    btnNext?.addEventListener('click', () => {
        current = (current + 1) % items.length;
        render();
    });

    window.addEventListener('keydown', (event) => {
        if (lightbox?.classList.contains('hidden')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft') btnPrev?.click();
        if (event.key === 'ArrowRight') btnNext?.click();
    });

});
</script>
@endsection
