<?php

namespace Database\Seeders;

use App\Models\HeroSection;
use Illuminate\Database\Seeder;

class HeroSectionSeeder extends Seeder
{
    public function run(): void
    {
        $hero = HeroSection::create([
            'logo_path'           => 'theme/images/am35.png',
            'heading_prefix'      => 'Learn the',
            'heading_highlight'   => 'AI-era skills',
            'heading_bold'        => 'employers',
            'heading_suffix'      => 'actually hire for.',
            'lede'                => 'We re excited to have you here! Explore your live demo session, interact with expert mentors, discover career-focused courses, and experience how practical learning can transform your future.',
            'cta_primary_label'   => 'Book Demo class',
            'cta_primary_url'     => 'https://lms.academicmantraservices.com',
            'cta_secondary_label' => 'Explore courses',
            'cta_secondary_url'   => '#courses',
            'mascot_image'        => 'theme/images/hii-bitmoji.png',
            'guide_tag'           => 'Your Personal Guide',
            'guide_name'          => "Hi, I'm Academic Mantra",
            'guide_text'          => "I'll walk you through live demos, courses, and help you pick your next skill.",
            'hand_images'         => [
                'theme/images/am21.png',
                'theme/images/am21.png',
                'theme/images/am21.png',
            ],
            'is_active'           => true,
        ]);

        $hero->stats()->createMany([
            ['number' => '50+',  'label' => 'Working mentors',           'sort_order' => 1],
            ['number' => '100+', 'label' => 'Live client projects',      'sort_order' => 2],
            ['number' => '8',    'label' => 'Industry tracks',           'sort_order' => 3],
            ['number' => '1M+',  'label' => 'Traffic on in-house projects', 'sort_order' => 4],
        ]);

        $hero->ratings()->createMany([
            ['score' => '4.7', 'label' => 'Student reviews', 'sort_order' => 1],
            ['score' => '4.6', 'label' => 'Trustpilot',       'sort_order' => 2],
            ['score' => '5.0', 'label' => 'Google',           'sort_order' => 3],
        ]);
    }
}