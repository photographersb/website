<?php

namespace App\Services;

use App\Models\SiteLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SiteLinkService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    protected const CACHE_DURATION = 3600;

    /**
     * Get navbar links
     *
     * @param bool|null $isAuthenticated
     * @return Collection
     */
    public function getNavbarLinks(?bool $isAuthenticated = null): Collection
    {
        return $this->getLinksBySection('navbar', $isAuthenticated);
    }

    /**
     * Get footer company links
     *
     * @param bool|null $isAuthenticated
     * @return Collection
     */
    public function getFooterCompanyLinks(?bool $isAuthenticated = null): Collection
    {
        return $this->getLinksBySection('footer_company', $isAuthenticated);
    }

    /**
     * Get footer legal links
     *
     * @param bool|null $isAuthenticated
     * @return Collection
     */
    public function getFooterLegalLinks(?bool $isAuthenticated = null): Collection
    {
        return $this->getLinksBySection('footer_legal', $isAuthenticated);
    }

    /**
     * Get footer useful links
     *
     * @param bool|null $isAuthenticated
     * @return Collection
     */
    public function getFooterUsefulLinks(?bool $isAuthenticated = null): Collection
    {
        return $this->getLinksBySection('footer_useful', $isAuthenticated);
    }

    /**
     * Get social media links
     *
     * @param bool|null $isAuthenticated
     * @return Collection
     */
    public function getSocialLinks(?bool $isAuthenticated = null): Collection
    {
        return $this->getLinksBySection('social', $isAuthenticated);
    }

    /**
     * Get CTA links
     *
     * @param bool|null $isAuthenticated
     * @return Collection
     */
    public function getCtaLinks(?bool $isAuthenticated = null): Collection
    {
        return $this->getLinksBySection('cta', $isAuthenticated);
    }

    /**
     * Get all links grouped by section
     *
     * @param bool|null $isAuthenticated
     * @return Collection
     */
    public function getAllLinksGrouped(?bool $isAuthenticated = null): Collection
    {
        $isAuth = $isAuthenticated ?? auth()->check();
        $cacheKey = "site_links_grouped_auth_{$isAuth}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($isAuth) {
            $links = SiteLink::active()
                ->visibleTo($isAuth)
                ->ordered()
                ->get();

            return $links->groupBy('section');
        });
    }

    /**
     * Get links by section with caching
     *
     * @param string $section
     * @param bool|null $isAuthenticated
     * @return Collection
     */
    protected function getLinksBySection(string $section, ?bool $isAuthenticated = null): Collection
    {
        $isAuth = $isAuthenticated ?? auth()->check();
        $cacheKey = "site_links_{$section}_auth_{$isAuth}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($section, $isAuth) {
            return SiteLink::active()
                ->bySection($section)
                ->visibleTo($isAuth)
                ->ordered()
                ->get()
                ->map(function ($link) {
                    return [
                        'id' => $link->id,
                        'title' => $link->title,
                        'url' => $link->final_url,
                        'target' => $link->target,
                        'rel' => $link->rel,
                        'icon' => $link->icon,
                        'open_in_new_tab' => $link->open_in_new_tab,
                    ];
                });
        });
    }

    /**
     * Clear all site links cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        try {
            // Clear all possible auth state combinations
            foreach ([true, false] as $authState) {
                Cache::forget("site_links_grouped_auth_{$authState}");
                
                foreach (array_keys(SiteLink::SECTIONS) as $section) {
                    Cache::forget("site_links_{$section}_auth_{$authState}");
                }
            }

            Log::info('Site links cache cleared successfully');
        } catch (\Exception $e) {
            Log::error('Failed to clear site links cache: ' . $e->getMessage());
        }
    }

    /**
     * Refresh cache for all links
     *
     * @return void
     */
    public function refreshCache(): void
    {
        $this->clearCache();

        // Pre-warm cache for both auth states
        foreach ([true, false] as $authState) {
            $this->getAllLinksGrouped($authState);
            
            foreach (array_keys(SiteLink::SECTIONS) as $section) {
                $this->getLinksBySection($section, $authState);
            }
        }

        Log::info('Site links cache refreshed successfully');
    }

    /**
     * Get a single link by ID
     *
     * @param int $id
     * @return SiteLink|null
     */
    public function find(int $id): ?SiteLink
    {
        return SiteLink::find($id);
    }

    /**
     * Create a new link
     *
     * @param array $data
     * @return SiteLink
     */
    public function create(array $data): SiteLink
    {
        $link = SiteLink::create($data);
        $this->clearCache();
        
        return $link;
    }

    /**
     * Update a link
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $link = SiteLink::findOrFail($id);
        $updated = $link->update($data);
        
        if ($updated) {
            $this->clearCache();
        }
        
        return $updated;
    }

    /**
     * Delete a link
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $link = SiteLink::findOrFail($id);
        $deleted = $link->delete();
        
        if ($deleted) {
            $this->clearCache();
        }
        
        return $deleted;
    }

    /**
     * Bulk update sort orders
     *
     * @param array $orders Array of ['id' => sort_order]
     * @return void
     */
    public function updateSortOrders(array $orders): void
    {
        foreach ($orders as $id => $sortOrder) {
            SiteLink::where('id', $id)->update(['sort_order' => $sortOrder]);
        }

        $this->clearCache();
    }

    /**
     * Toggle link active status
     *
     * @param int $id
     * @return bool
     */
    public function toggleActive(int $id): bool
    {
        $link = SiteLink::findOrFail($id);
        $link->is_active = !$link->is_active;
        $saved = $link->save();
        
        if ($saved) {
            $this->clearCache();
        }
        
        return $saved;
    }
}
