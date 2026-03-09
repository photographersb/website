<?php

namespace Database\Seeders;

use App\Models\CommunityDiscussion;
use App\Models\CommunityMentorshipProfile;
use App\Models\LearningCourse;
use App\Models\LearningCourseReview;
use App\Models\LearningInstructorProfile;
use App\Models\LearningLesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HubExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->orderBy('id')->limit(12)->get();

        if ($users->isEmpty()) {
            $this->command?->warn('HubExperienceSeeder skipped: no users found.');
            return;
        }

        $instructors = $users->take(2);
        $reviewers = $users->skip(2)->take(4);

        foreach ($instructors as $index => $instructor) {
            LearningInstructorProfile::query()->updateOrCreate(
                ['user_id' => $instructor->id],
                [
                    'bio' => $index === 0
                        ? 'Commercial and portrait photographer focused on practical lighting and client workflow.'
                        : 'Travel and documentary photographer with a focus on storytelling and visual sequencing.',
                    'expertise' => $index === 0
                        ? ['lighting', 'portrait', 'editing']
                        : ['storytelling', 'street', 'travel'],
                    'is_approved' => true,
                    'is_active' => true,
                    'student_rating' => $index === 0 ? 4.8 : 4.6,
                    'courses_created' => 2,
                    'students_count' => $index === 0 ? 120 : 95,
                ]
            );
        }

        $courseRows = [
            [
                'title' => 'Portrait Lighting Foundations',
                'category' => 'Portrait Photography',
                'difficulty_level' => 'beginner',
                'price' => 0,
                'duration_minutes' => 95,
                'description' => 'Build confident portrait lighting setups with natural light, reflectors, and one-light flash scenarios.',
                'instructor_user_id' => $instructors->first()->id,
            ],
            [
                'title' => 'Street Storytelling Workflow',
                'category' => 'Street Photography',
                'difficulty_level' => 'intermediate',
                'price' => 499,
                'duration_minutes' => 130,
                'description' => 'Learn pre-visualization, shot sequencing, and post-processing choices for stronger street photo narratives.',
                'instructor_user_id' => $instructors->last()->id,
            ],
            [
                'title' => 'Event Coverage Fast Edit System',
                'category' => 'Event Photography',
                'difficulty_level' => 'advanced',
                'price' => 799,
                'duration_minutes' => 160,
                'description' => 'Design a reliable shoot-to-delivery workflow for events, including culling, color consistency, and export presets.',
                'instructor_user_id' => $instructors->first()->id,
            ],
        ];

        foreach ($courseRows as $courseRow) {
            $slug = Str::slug($courseRow['title']);

            $course = LearningCourse::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $courseRow['title'],
                    'description' => $courseRow['description'],
                    'category' => $courseRow['category'],
                    'difficulty_level' => $courseRow['difficulty_level'],
                    'instructor_user_id' => $courseRow['instructor_user_id'],
                    'price' => $courseRow['price'],
                    'duration_minutes' => $courseRow['duration_minutes'],
                    'is_featured' => true,
                    'status' => 'published',
                    'published_at' => now()->subDays(5),
                ]
            );

            $lessons = [
                ['title' => 'Welcome and Setup', 'lesson_type' => 'text', 'duration_minutes' => 10, 'sort_order' => 1],
                ['title' => 'Core Technique Demo', 'lesson_type' => 'video', 'duration_minutes' => 35, 'sort_order' => 2],
                ['title' => 'Practice Assignment', 'lesson_type' => 'photo_tutorial', 'duration_minutes' => 25, 'sort_order' => 3],
            ];

            foreach ($lessons as $lessonRow) {
                LearningLesson::query()->updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $lessonRow['title'],
                    ],
                    [
                        'lesson_type' => $lessonRow['lesson_type'],
                        'content' => 'Lesson content and checkpoints for practical execution.',
                        'duration_minutes' => $lessonRow['duration_minutes'],
                        'sort_order' => $lessonRow['sort_order'],
                        'is_published' => true,
                    ]
                );
            }

            $course->update([
                'lessons_count' => LearningLesson::query()->where('course_id', $course->id)->count(),
            ]);

            foreach ($reviewers as $reviewer) {
                LearningCourseReview::query()->updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'user_id' => $reviewer->id,
                    ],
                    [
                        'rating' => 5,
                        'feedback' => 'Practical and easy to apply in real shoots.',
                        'status' => 'published',
                        'reviewed_at' => now()->subDays(2),
                    ]
                );
            }
        }

        $mentorUsers = $users->take(3);
        foreach ($mentorUsers as $i => $mentorUser) {
            CommunityMentorshipProfile::query()->updateOrCreate(
                ['user_id' => $mentorUser->id],
                [
                    'expertise' => $i === 0
                        ? ['portrait', 'lighting']
                        : ($i === 1 ? ['wedding', 'workflow'] : ['travel', 'storytelling']),
                    'years_experience' => 5 + $i,
                    'availability_status' => 'available',
                    'session_types' => ['portfolio_review', 'career_advice'],
                    'bio' => 'Open for mentorship sessions focused on practical growth for photographers.',
                    'is_active' => true,
                ]
            );
        }

        $discussionRows = [
            [
                'title' => 'Best low-light event workflow in Dhaka venues?',
                'content' => 'Sharing my current setup for noisy halls. Looking for practical shutter-speed and flash sync suggestions that still preserve ambient mood.',
                'category' => 'event-photography',
                'tags' => ['low-light', 'event', 'flash'],
            ],
            [
                'title' => 'Street composition critique thread (weekly)',
                'content' => 'Post one frame and one sentence about your intent. Community can suggest tighter framing, timing, and subject isolation improvements.',
                'category' => 'street-photography',
                'tags' => ['street', 'composition', 'critique'],
            ],
            [
                'title' => 'Color grading presets for wedding albums',
                'content' => 'What is your process to keep skin tone consistency across indoor tungsten, mixed light, and outdoor golden hour sessions?',
                'category' => 'editing',
                'tags' => ['editing', 'color-grading', 'wedding'],
            ],
        ];

        foreach ($discussionRows as $idx => $row) {
            $author = $users[$idx % $users->count()];

            CommunityDiscussion::query()->updateOrCreate(
                [
                    'user_id' => $author->id,
                    'title' => $row['title'],
                ],
                [
                    'content' => $row['content'],
                    'category' => $row['category'],
                    'tags' => $row['tags'],
                    'is_featured' => true,
                    'status' => 'active',
                    'likes_count' => 8 + ($idx * 3),
                    'comments_count' => 4 + ($idx * 2),
                    'shares_count' => 2 + $idx,
                    'last_activity_at' => now()->subHours($idx + 1),
                ]
            );
        }

        $this->command?->info('HubExperienceSeeder: community and learning hub data seeded.');
    }
}
