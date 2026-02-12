<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Photographer;
use App\Services\UsernameService;
use App\Services\SeoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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

        // If not found, return 404
        if (!$user) {
            return $this->handleNotFound();
        }

        // Verify user is photographer
        if (!$user->isPhotographer()) {
            return $this->handleNotFound();
        }

        // Redirect old usernames to the latest username
        if (!empty($user->username) && $user->username !== $username) {
            return redirect(
                $this->usernameService->getProfileUrl($user),
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        $photographer = $user->photographer;

        // Get or generate SEO metadata
        $seoMeta = $this->seoService->getSeoMeta($user);

        // Increment profile view count
        $this->incrementProfileViews($user);

        // Get photographer data
        $portfolios = $photographer->albums()->orderBy('display_order', 'desc')->paginate(12);
        $packages = $photographer->packages()->where('is_active', true)->get();
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

        $portfolios = $photographer->albums()
            ->orderBy('display_order', 'desc')
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
            ->where('is_active', true)
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
     * Generate OpenGraph image for photographer profile
     */
    public function ogImageByUsername(string $username)
    {
        $user = $this->usernameService->findByUsername($username);

        if ($user && $user->isPhotographer() && !empty($user->username) && $user->username !== $username) {
            return redirect(
                route('og.photographer', ['username' => $user->username]),
                Response::HTTP_MOVED_PERMANENTLY
            );
        }

        if (!$user || !$user->isPhotographer()) {
            return $this->buildOgImageResponse(null);
        }

        return $this->buildOgImageResponse($user);
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
     * Build OG image response with caching
     */
    protected function buildOgImageResponse(?User $user)
    {
        $cacheKey = $user ? "og_image_photographer_{$user->id}" : 'og_image_photographer_default';

        $imageData = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($user) {
            return $this->renderOgImage($user);
        });

        return response($imageData, 200)
            ->header('Content-Type', 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Render a simple OG image using GD (with SVG->PNG fallback)
     */
    protected function renderOgImage(?User $user): string
    {
        $width = 1200;
        $height = 630;
        $canvas = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate($canvas, 17, 24, 39);
        $accentColor = imagecolorallocate($canvas, 142, 14, 63);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $gray = imagecolorallocate($canvas, 156, 163, 175);

        imagefill($canvas, 0, 0, $bgColor);
        imagefilledrectangle($canvas, 0, 0, $width, 14, $accentColor);

        $logo = $this->loadOgLogoImage();
        if ($logo) {
            $logoWidth = imagesx($logo);
            $logoHeight = imagesy($logo);
            $maxLogo = 220;
            $scale = min($maxLogo / $logoWidth, $maxLogo / $logoHeight, 1);
            $destW = (int)($logoWidth * $scale);
            $destH = (int)($logoHeight * $scale);

            $resized = imagecreatetruecolor($destW, $destH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $destW, $destH, $transparent);
            imagecopyresampled($resized, $logo, 0, 0, 0, 0, $destW, $destH, $logoWidth, $logoHeight);
            imagecopy($canvas, $resized, 60, 60, 0, 0, $destW, $destH);
            imagedestroy($resized);
            imagedestroy($logo);
        }

        $displayName = $user ? Str::ascii($user->name) : 'PhotographersB';
        if (strlen($displayName) > 48) {
            $displayName = substr($displayName, 0, 45) . '...';
        }
        $handle = $user && $user->username ? '@' . $user->username : 'photographersb.com';
        $city = $user?->photographer?->city?->name;
        $tagline = $city
            ? "Photographer in {$city}"
            : 'Professional Photographers & Booking Platform in Bangladesh';

        imagestring($canvas, 5, 60, 320, $displayName, $white);
        imagestring($canvas, 4, 60, 350, $handle, $gray);
        imagestring($canvas, 3, 60, 380, $tagline, $gray);

        ob_start();
        imagejpeg($canvas, null, 90);
        $output = ob_get_clean();
        imagedestroy($canvas);

        return $output;
    }

    /**
     * Load logo image for OG generation
     */
    protected function loadOgLogoImage()
    {
        $svgPath = public_path('images/logo.svg');
        $pngFallback = public_path('images/Fev.png');

        if (extension_loaded('imagick') && file_exists($svgPath)) {
            try {
                $imagick = new \Imagick();
                $imagick->readImage($svgPath);
                $imagick->setImageFormat('png');
                $blob = $imagick->getImageBlob();
                $imagick->clear();
                $imagick->destroy();

                return imagecreatefromstring($blob);
            } catch (\Throwable $e) {
                // Fall back to PNG if SVG loading fails
            }
        }

        if (file_exists($pngFallback)) {
            return imagecreatefrompng($pngFallback);
        }

        return null;
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
