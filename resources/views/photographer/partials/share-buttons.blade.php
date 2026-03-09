@php
    $profileUrl = $seoMeta->canonical_url ?? route('photographer.profile.public', ['username' => $user->username]);
    $profileTitle = $seoMeta->meta_title ?? $user->name;
@endphp

<div class="flex flex-wrap gap-2">
    <button
        type="button"
        class="js-copy-profile-link inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold transition"
        data-profile-url="{{ $profileUrl }}"
        title="Copy profile link"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
        </svg>
        <span class="js-copy-label">Copy Link</span>
    </button>

    <a
        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($profileUrl) }}&quote={{ urlencode($profileTitle) }}"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition"
        title="Share on Facebook"
    >
        <span>Facebook</span>
    </a>

    <a
        href="https://wa.me/?text={{ urlencode($profileTitle) }} - {{ urlencode($profileUrl) }}"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition"
        title="Share on WhatsApp"
    >
        <span>WhatsApp</span>
    </a>

    <a
        href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($profileUrl) }}"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold transition"
        title="Share on LinkedIn"
    >
        <span>LinkedIn</span>
    </a>

    <a
        href="https://twitter.com/intent/tweet?text={{ urlencode($profileTitle) }}&url={{ urlencode($profileUrl) }}"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-black hover:bg-gray-900 text-white text-sm font-semibold transition"
        title="Share on X"
    >
        <span>X</span>
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-copy-profile-link').forEach(function (button) {
        button.addEventListener('click', function () {
            const profileUrl = button.getAttribute('data-profile-url');
            const label = button.querySelector('.js-copy-label');
            const originalText = label ? label.textContent : 'Copy Link';

            navigator.clipboard.writeText(profileUrl).then(function () {
                if (label) label.textContent = 'Copied!';
                setTimeout(function () {
                    if (label) label.textContent = originalText;
                }, 1600);
            });
        });
    });
});
</script>
