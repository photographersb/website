<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class LogActivity
{
    /**
     * Handle an incoming request.
     * Logs significant user activities after request completion.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (!auth()->check()) {
            return $response;
        }

        // Only log successful requests (2xx status codes)
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            return $response;
        }

        // Determine action based on HTTP method and route
        $method = $request->method();
        $action = $this->determineAction($method, $request);

        // Only log write operations (POST, PUT, PATCH, DELETE)
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logActivity($request, $action);
        }

        return $response;
    }

    /**
     * Determine the action based on HTTP method
     */
    private function determineAction(string $method, Request $request): string
    {
        return match($method) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'accessed',
        };
    }

    /**
     * Log the activity
     */
    private function logActivity(Request $request, string $action): void
    {
        try {
            $routeName = $request->route()?->getName();
            $uri = $request->path();
            
            // Extract model info from route if available
            $modelType = $this->extractModelType($uri);
            $modelId = $this->extractModelId($request);

            // Create description
            $description = $this->createDescription($action, $modelType, $uri);

            // Get relevant request data (exclude sensitive fields)
            $properties = $this->getRelevantData($request);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'description' => $description,
                'properties' => $properties,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't disrupt user experience
            \Log::error('Activity logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Extract model type from URI
     */
    private function extractModelType(string $uri): ?string
    {
        $patterns = [
            '/photographers/' => 'App\\Models\\Photographer',
            '/bookings/' => 'App\\Models\\Booking',
            '/reviews/' => 'App\\Models\\Review',
            '/events/' => 'App\\Models\\Event',
            '/competitions/' => 'App\\Models\\Competition',
            '/inquiries/' => 'App\\Models\\Inquiry',
        ];

        foreach ($patterns as $pattern => $model) {
            if (str_contains($uri, $pattern)) {
                return $model;
            }
        }

        return null;
    }

    /**
     * Extract model ID from request
     */
    private function extractModelId(Request $request): ?int
    {
        // Try to get ID from route parameters
        $params = $request->route()?->parameters() ?? [];
        
        foreach (['id', 'photographer', 'booking', 'review', 'event', 'competition'] as $param) {
            if (isset($params[$param])) {
                $value = $params[$param];
                return is_numeric($value) ? (int)$value : null;
            }
        }

        return null;
    }

    /**
     * Create human-readable description
     */
    private function createDescription(string $action, ?string $modelType, string $uri): string
    {
        $modelName = $modelType ? class_basename($modelType) : 'resource';
        $user = auth()->user()->name;

        return match($action) {
            'created' => "{$user} created a new {$modelName}",
            'updated' => "{$user} updated {$modelName}",
            'deleted' => "{$user} deleted {$modelName}",
            default => "{$user} {$action} {$uri}",
        };
    }

    /**
     * Get relevant request data (exclude sensitive fields)
     */
    private function getRelevantData(Request $request): array
    {
        $data = $request->except(['password', 'password_confirmation', 'token', 'api_key']);
        
        // Limit data size to prevent huge logs
        $json = json_encode($data);
        if (strlen($json) > 5000) {
            return ['_note' => 'Data too large to log'];
        }

        return $data;
    }
}
