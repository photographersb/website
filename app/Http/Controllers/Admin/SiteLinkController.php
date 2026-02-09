<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteLink;
use App\Services\SiteLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SiteLinkController extends Controller
{
    protected SiteLinkService $siteLinkService;

    public function __construct(SiteLinkService $siteLinkService)
    {
        $this->siteLinkService = $siteLinkService;
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin'); // Ensure only admins can access
    }

    /**
     * Display a listing of site links
     */
    public function index(Request $request)
    {
        $section = $request->get('section');

        $query = SiteLink::with('creator')->orderBy('sort_order')->orderBy('id');

        if ($section && array_key_exists($section, SiteLink::SECTIONS)) {
            $query->where('section', $section);
        }

        $links = $query->paginate(50);

        return inertia('Admin/SiteLinks/Index', [
            'links' => $links,
            'sections' => SiteLink::SECTIONS,
            'visibilityOptions' => SiteLink::VISIBILITY_OPTIONS,
            'currentSection' => $section,
        ]);
    }

    /**
     * Show the form for creating a new link
     */
    public function create()
    {
        return inertia('Admin/SiteLinks/Create', [
            'sections' => SiteLink::SECTIONS,
            'visibilityOptions' => SiteLink::VISIBILITY_OPTIONS,
        ]);
    }

    /**
     * Store a newly created link
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section' => ['required', Rule::in(array_keys(SiteLink::SECTIONS))],
            'title' => 'required|string|max:255',
            'url' => 'required_without:route_name|nullable|string|max:2000',
            'route_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'visibility' => ['required', Rule::in(array_keys(SiteLink::VISIBILITY_OPTIONS))],
        ]);

        // Security validation
        if (!empty($validated['url'])) {
            if (preg_match('/^(javascript|data):/i', $validated['url'])) {
                return back()->withErrors(['url' => 'JavaScript and data URLs are not allowed for security reasons.']);
            }
        }

        $validated['created_by_user_id'] = auth()->id();

        $link = $this->siteLinkService->create($validated);

        // Clear public site links cache
        Cache::forget('site_links_public');

        return redirect()
            ->route('admin.site-links.index')
            ->with('success', 'Site link created successfully!');
    }

    /**
     * Show the form for editing a link
     */
    public function edit(SiteLink $siteLink)
    {
        return inertia('Admin/SiteLinks/Edit', [
            'link' => $siteLink->load('creator'),
            'sections' => SiteLink::SECTIONS,
            'visibilityOptions' => SiteLink::VISIBILITY_OPTIONS,
        ]);
    }

    /**
     * Update the specified link
     */
    public function update(Request $request, SiteLink $siteLink)
    {
        $validated = $request->validate([
            'section' => ['required', Rule::in(array_keys(SiteLink::SECTIONS))],
            'title' => 'required|string|max:255',
            'url' => 'required_without:route_name|nullable|string|max:2000',
            'route_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'visibility' => ['required', Rule::in(array_keys(SiteLink::VISIBILITY_OPTIONS))],
        ]);

        // Security validation
        if (!empty($validated['url'])) {
            if (preg_match('/^(javascript|data):/i', $validated['url'])) {
                return back()->withErrors(['url' => 'JavaScript and data URLs are not allowed for security reasons.']);
            }
        }

        $this->siteLinkService->update($siteLink->id, $validated);

        // Clear public site links cache
        Cache::forget('site_links_public');

        return redirect()
            ->route('admin.site-links.index')
            ->with('success', 'Site link updated successfully!');
    }

    /**
     * Remove the specified link
     */
    public function destroy(SiteLink $siteLink)
    {
        $this->siteLinkService->delete($siteLink->id);

        // Clear public site links cache
        Cache::forget('site_links_public');

        return redirect()
            ->route('admin.site-links.index')
            ->with('success', 'Site link deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(SiteLink $siteLink)
    {
        $this->siteLinkService->toggleActive($siteLink->id);

        // Clear public site links cache
        Cache::forget('site_links_public');

        return response()->json([
            'success' => true,
            'message' => 'Link status updated successfully',
            'is_active' => $siteLink->fresh()->is_active,
        ]);
    }

    /**
     * Update sort orders (for drag & drop)
     */
    public function updateSortOrders(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|min:0',
        ]);

        $this->siteLinkService->updateSortOrders($validated['orders']);

        return response()->json([
            'success' => true,
            'message' => 'Sort orders updated successfully',
        ]);
    }

    /**
     * Clear cache manually
     */
    public function clearCache()
    {
        $this->siteLinkService->clearCache();

        return redirect()
            ->route('admin.site-links.index')
            ->with('success', 'Site links cache cleared successfully!');
    }

    /**
     * Preview links in different sections
     */
    public function preview()
    {
        return inertia('Admin/SiteLinks/Preview', [
            'navbarLinks' => $this->siteLinkService->getNavbarLinks(false),
            'footerCompanyLinks' => $this->siteLinkService->getFooterCompanyLinks(false),
            'footerLegalLinks' => $this->siteLinkService->getFooterLegalLinks(false),
            'footerUsefulLinks' => $this->siteLinkService->getFooterUsefulLinks(false),
            'socialLinks' => $this->siteLinkService->getSocialLinks(false),
            'ctaLinks' => $this->siteLinkService->getCtaLinks(false),
        ]);
    }

    /**
     * Public API endpoint - Get all active site links grouped by section
     * Cached for 1 hour to reduce database queries on public pages
     */
    public function publicIndex()
    {
        return Cache::remember('site_links_public', 3600, function() {
            $links = SiteLink::where('is_active', true)
                ->where('visibility', 'public')
                ->orderBy('section')
                ->orderBy('sort_order')
                ->select(['id', 'section', 'title', 'url', 'icon', 'route_name', 'open_in_new_tab'])
                ->get()
                ->groupBy('section');

            return response()->json([
                'success' => true,
                'data' => [
                    'navbar' => $links->get('navbar', []),
                    'footer_company' => $links->get('footer_company', []),
                    'footer_legal' => $links->get('footer_legal', []),
                    'footer_useful' => $links->get('footer_useful', []),
                    'social' => $links->get('social', []),
                    'cta' => $links->get('cta', []),
                ]
            ]);
        });
    }
}
