@props(['breadcrumbs'])

<nav aria-label="Breadcrumb" class="bg-gray-50 border-b border-gray-200 py-3 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <ol class="flex items-center space-x-2 text-sm">
            @foreach($breadcrumbs as $crumb)
                @if($loop->last)
                    <li class="text-gray-900 font-medium">
                        {{ $crumb['label'] }}
                    </li>
                @else
                    <li>
                        <a href="{{ $crumb['url'] }}" class="text-blue-600 hover:text-blue-800 underline">
                            {{ $crumb['label'] }}
                        </a>
                        <span class="mx-2 text-gray-400">/</span>
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</nav>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    @foreach($breadcrumbs as $crumb)
      {
        "@type": "ListItem",
        "position": {{ $loop->index + 1 }},
        "name": "{{ $crumb['label'] }}",
        "item": "{{ $crumb['url'] ?? request()->url() }}"
      }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
