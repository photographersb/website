<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '01' . $this->faker->numerify('#########'),
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'profile_photo_url' => null,
            'bio' => $this->faker->sentence(),
            'is_suspended' => false,
            'suspension_reason' => null,
            'suspended_at' => null,
            'last_login_at' => now(),
            'last_login_ip' => $this->faker->ipv4(),
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'approval_status' => 'approved',
            'rejection_reason' => null,
            'approved_at' => now(),
            'approved_by_admin_id' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function judge(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'judge',
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function photographer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'photographer',
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => true,
            'suspension_reason' => 'Policy violation',
            'suspended_at' => now(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'phone_verified_at' => null,
        ]);
    }
}
