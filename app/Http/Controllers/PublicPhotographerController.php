<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Photographer;
use App\Services\UsernameService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicPhotographerController extends Controller
{
    protected UsernameService $usernameService;
    protected SeoService $seoService;

    public function __construct(UsernameService $usernameService, SeoService $seoService)
    {
        $this->usernameService = $usernameService;
        $this->seoService = $seoService;
    }

    /**
     * Show photographer profile by username
     */
    public function showByUsername(Request $request, string $username)
    {
        // Find user by username (handles history redirects)
        $user = $this->usernameService->findByUsername($username);

        // Handle old username redirect (301)
        if (!$user) {
            return $this->handleNotFound();
        }

        // Check if this is an old username (should redirect with 301)
        $currentUser = User::where('username', $username)->first();
        if (!$currentUser && $user) {
            // This is from username history - do 301 redirect
            return redirect(
                $this->usernameService->getProfileUrl($user),
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        // Verify user is photographer
        if (!$user->isPhotographer()) {
            return $this->handleNotFound();
        }

        $photographer = $user->photographer;

        // Get or generate SEO metadata
        $seoMeta = $this->seoService->getSeoMeta($user);

        // Increment profile view count
        $this->incrementProfileViews($user);

        // Get photographer data
        $portfolios = $photographer->portfolios()->orderBy('featured', 'desc')->paginate(12);
        $packages = $photographer->packages()->active()->get();
        $reviews = $photographer->reviews()->with('reviewer')->orderBy('created_at', 'desc')->paginate(5);
        $averageRating = $photographer->averageRating;
        $ratingCount = $photographer->reviews()->count();
        $isVerified = $user->is_verified ?? false;
        $isAvailable = $photographer->is_available ?? true;

        return view('photographer.profile', [
            'user' => $user,
            'photographer' => $photographer,
            'seoMeta' => $seoMeta,
            'portfolios' => $portfolios,
            'packages' => $packages,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'ratingCount' => $ratingCount,
            'isVerified' => $isVerified,
            'isAvailable' => $isAvailable,
        ]);
    }

    /**
     * Show photographer profile by ID (legacy support)
     */
    public function showById(Request $request, $id)
    {
        if (!is_numeric($id)) {
            // Non-numeric ID - redirect to username-based URL
            return redirect(
                route('photographer.profile.public', ['username' => $id]),
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        $user = User::where('id', (int) $id)
            ->where(function ($q) {
                $q->whereHas('photographer')
                  ->where(function ($subQ) {
                      $subQ->where('role', 'photographer')
                           ->orWhere('role', 'studio_owner')
                           ->orWhere('role', 'studio_photographer');
                  });
            })
            ->first();

        if (!$user) {
            return $this->handleNotFound();
        }

        // Redirect to username-based URL if username exists
        if ($user->username) {
            return redirect(
                $this->usernameService->getProfileUrl($user),
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        return $this->showByUsername($request, (string) $user->id);
    }

    /**
     * Get photographer portfolio (AJAX/API endpoint)
     */
    public function getPortfolio(Request $request, string $username)
    {
        $user = $this->usernameService->findByUsername($username);

        if (!$user || !$user->isPhotographer()) {
            return response()->json(['error' => 'Photographer not found'], 404);
        }

        $photographer = $user->photographer;
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 12);

        $portfolios = $photographer->portfolios()
            ->orderBy('featured', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($portfolios);
    }

    /**
     * Get photographer packages (AJAX/API endpoint)
     */
    public function getPackages(Request $request, string $username)
    {
        $user = $this->usernameService->findByUsername($username);

        if (!$user || !$user->isPhotographer()) {
            return response()->json(['error' => 'Photographer not found'], 404);
        }

        $packages = $user->photographer->packages()
            ->active()
            ->get()
            ->map(fn ($pkg) => [
                'id' => $pkg->id,
                'name' => $pkg->name,
                'price' => $pkg->price,
                'description' => $pkg->description,
                'deliverables' => $pkg->deliverables,
            ]);

        return response()->json($packages);
    }

    /**
     * Get photographer reviews (AJAX/API endpoint)
     */
    public function getReviews(Request $request, string $username)
    {
        $user = $this->usernameService->findByUsername($username);

        if (!$user || !$user->isPhotographer()) {
            return response()->json(['error' => 'Photographer not found'], 404);
        }

        $reviews = $user->photographer->reviews()
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return response()->json($reviews);
    }

    /**
     * Search photographers by name or category
     */
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        $category = $request->query('category');
        $page = $request->query('page', 1);

        $photographers = User::query()
            ->whereHas('photographer')
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('username', 'like', "%{$query}%")
                      ->orWhereHas('photographer', fn ($subQ) => 
                          $subQ->where('bio', 'like', "%{$query}%")
                               ->orWhere('specializations', 'like', "%{$query}%")
                      );
                }
            })
            ->when($category, fn ($q) => 
                $q->whereHas('photographer', fn ($subQ) => 
                    $subQ->where('specializations', 'like', "%{$category}%")
                )
            )
            ->active()
            ->paginate(20, ['*'], 'page', $page);

        return response()->json($photographers);
    }

    /**
     * Handle 404 responses
     */
    protected function handleNotFound()
    {
        abort(404, 'Photographer not found');
    }

    /**
     * Increment profile view count
     */
    protected function incrementProfileViews(User $user): void
    {
        // This could be stored in a separate analytics table
        // For now, we just track it in cache for real-time stats
        $cacheKey = "profile_views_{$user->id}_" . now()->format('Y-m-d');
        cache()->increment($cacheKey, 1, 86400); // Expire after 24 hours
    }
}
