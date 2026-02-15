<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificateTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $templates = [
            [
                'title' => 'Classic Participation',
                'description' => 'Clean serif layout for participation certificates.',
                'type' => 'participation',
                'width' => 297,
                'height' => 210,
                'background_color' => '#ffffff',
                'accent_color' => '#8B0000',
                'text_color' => '#1f2937',
                'title_font' => 'serif',
                'is_default' => true,
                'template_content' => null,
            ],
            [
                'title' => 'Finalist Slate',
                'description' => 'Modern slate look for finalists.',
                'type' => 'finalist',
                'width' => 297,
                'height' => 210,
                'background_color' => '#f8fafc',
                'accent_color' => '#1d4ed8',
                'text_color' => '#111827',
                'title_font' => 'sans-serif',
                'is_default' => true,
                'template_content' => null,
            ],
            [
                'title' => 'Winner Gold',
                'description' => 'Gold accented template for winners.',
                'type' => 'winner',
                'width' => 297,
                'height' => 210,
                'background_color' => '#fffaf0',
                'accent_color' => '#d97706',
                'text_color' => '#111827',
                'title_font' => 'serif',
                'is_default' => true,
                'template_content' => null,
            ],
            [
                'title' => 'Merit Green',
                'description' => 'Fresh green tone for merit awards.',
                'type' => 'merit',
                'width' => 297,
                'height' => 210,
                'background_color' => '#f0fdf4',
                'accent_color' => '#15803d',
                'text_color' => '#064e3b',
                'title_font' => 'sans-serif',
                'is_default' => true,
                'template_content' => null,
            ],
        ];

        foreach ($templates as $template) {
            $exists = DB::table('certificate_templates')
                ->where('type', $template['type'])
                ->where('title', $template['title'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('certificate_templates')->insert([
                ...$template,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
