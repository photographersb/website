<?php

namespace Database\Factories;

use App\Models\ShareFrame;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShareFrameFactory extends Factory
{
    protected $model = ShareFrame::class;
    
    public function definition()
    {
        $colors = ['#8B0000', '#1a1a1a', '#2d3436', '#636e72', '#ff9500'];
        $fonts = ['DM Sans', 'Poppins', 'Inter', 'Roboto', 'Playfair Display'];
        
        return [
            'competition_id' => Competition::inRandomOrder()->first()?->id ?? Competition::factory(),
            'name' => $this->faker->words(3, true) . ' Template',
            'background_color' => $this->faker->randomElement($colors),
            'text_color' => '#FFFFFF',
            'accent_color' => '#D4A574',
            'font_family' => $this->faker->randomElement($fonts),
            'cta_message' => $this->faker->sentence(),
            'show_competition_name' => true,
            'show_photographer_name' => true,
            'show_submission_title' => true,
            'show_watermark' => true,
            'watermark_position' => $this->faker->randomElement(['bottom-left', 'bottom-right', 'top-right', 'top-left']),
            'show_qr_code' => true,
            'qr_position' => $this->faker->randomElement(['bottom-left', 'bottom-right']),
            'padding_top' => 80,
            'padding_bottom' => 80,
            'padding_left' => 40,
            'padding_right' => 40,
            'image_fit_strategy' => $this->faker->randomElement(['contain', 'cover']),
            'add_text_overlay_gradient' => true,
            'is_active' => true,
        ];
    }
}
