<?php

namespace Database\Factories;

use App\Models\ErrorLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ErrorLogFactory extends Factory
{
    protected $model = ErrorLog::class;
    
    public function definition()
    {
        $severities = ['P0', 'P1', 'P2'];
        $environments = ['local', 'staging', 'production'];
        
        $exceptions = [
            'Illuminate\Database\Eloquent\ModelNotFoundException',
            'Illuminate\Validation\ValidationException',
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'Exception',
        ];
        
        $files = [
            'app/Http/Controllers/BookingController.php',
            'app/Models/User.php',
            'app/Services/PaymentService.php',
            'routes/api.php',
            'database/migrations/2024_01_01_create_competitions_table.php',
        ];
        
        $routes = [
            'api.v1.admin.bookings.index',
            'api.v1.competitions.store',
            'api.v1.users.update',
            'api.v1.payments.process',
        ];
        
        return [
            'severity' => $this->faker->randomElement($severities),
            'environment' => $this->faker->randomElement($environments),
            'url' => $this->faker->url(),
            'route_name' => $this->faker->randomElement($routes),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'status_code' => $this->faker->randomElement([404, 500, 422, 403]),
            'message' => $this->faker->sentence(),
            'exception_class' => $this->faker->randomElement($exceptions),
            'file' => $this->faker->randomElement($files),
            'line' => $this->faker->numberBetween(10, 500),
            'error_signature' => hash('sha256', $this->faker->sentence()),
            'occurrence_count' => $this->faker->numberBetween(1, 50),
            'last_occurrence_at' => Carbon::now()->subDays($this->faker->numberBetween(0, 5)),
            'is_resolved' => $this->faker->boolean(30),
        ];
    }
}
