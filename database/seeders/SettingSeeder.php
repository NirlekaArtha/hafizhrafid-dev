<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Portfolio\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed default site and profile settings matching TDD §4.3.
     */
    public function run(): void
    {
        $github = (string) config('site.social.github', 'https://github.com/NirlekaArtha');
        $linkedin = (string) config('site.social.linkedin', 'https://www.linkedin.com/in/muhammad-hafizh-rafid-hidayat-37a8551b5/');
        $rawEmail = (string) config('site.social.email', 'hafizhrhidayat@gmail.com');
        $email = str_starts_with($rawEmail, 'mailto:') ? $rawEmail : 'mailto:'.$rawEmail;

        $settings = [
            'profile.about' => [
                'id' => 'Mahasiswa Teknik Informatika yang berfokus pada rekayasa perangkat lunak, pengembangan web full-stack, dan penerapan AI praktis. Suka membangun sistem yang rapi, mudah dirawat, dan terdokumentasi dengan baik.',
                'en' => 'Informatics student focused on software engineering, full-stack web development, and practical AI integration. I enjoy building clean, maintainable, and well-documented systems.',
            ],
            'profile.hero_subtitle' => [
                'id' => 'Membangun aplikasi web yang cepat dan mudah dirawat, serta mendokumentasikan eksperimen rekayasa perangkat lunak dan AI.',
                'en' => 'Building fast, maintainable web applications and documenting software engineering and AI experiments.',
            ],
            'profile.open_to_work' => true,
            'profile.skills' => [
                [
                    'group' => ['id' => 'Backend', 'en' => 'Backend'],
                    'items' => ['Laravel', 'PHP', 'PostgreSQL', 'Redis', 'REST API'],
                ],
                [
                    'group' => ['id' => 'Frontend', 'en' => 'Frontend'],
                    'items' => ['Livewire', 'Alpine.js', 'Tailwind CSS', 'Blade', 'Vite'],
                ],
                [
                    'group' => ['id' => 'AI & Data', 'en' => 'AI & Data'],
                    'items' => ['Python', 'LLM APIs', 'Prompt Engineering', 'RAG Basics'],
                ],
                [
                    'group' => ['id' => 'DevOps & Perkakas', 'en' => 'DevOps & Tooling'],
                    'items' => ['Docker', 'Linux', 'GitHub Actions', 'Pest', 'PHPStan'],
                ],
            ],
            'profile.timeline' => [
                [
                    'period' => '2023–kini',
                    'type' => 'education',
                    'title' => [
                        'id' => 'S1 Teknik Informatika',
                        'en' => 'B.Sc. in Informatics',
                    ],
                    'organization' => 'Universitas Pamulang',
                    'description' => [
                        'id' => 'Mempelajari rekayasa perangkat lunak, basis data, jaringan komputer, dan kecerdasan buatan.',
                        'en' => 'Studying software engineering, databases, computer networks, and artificial intelligence.',
                    ],
                ],
                [
                    'period' => '2025–2026',
                    'type' => 'experience',
                    'title' => [
                        'id' => 'Pengembang Web Independen',
                        'en' => 'Independent Web Developer',
                    ],
                    'organization' => 'Proyek Mandiri & Open Source',
                    'description' => [
                        'id' => 'Merancang dan membangun aplikasi web modular dengan Laravel, PostgreSQL, dan Docker.',
                        'en' => 'Designing and building modular web applications with Laravel, PostgreSQL, and Docker.',
                    ],
                ],
                [
                    'period' => '2026',
                    'type' => 'achievement',
                    'title' => [
                        'id' => 'Rilis Awal hafizhrafid.dev',
                        'en' => 'Initial Launch of hafizhrafid.dev',
                    ],
                    'organization' => 'hafizhrafid.dev',
                    'description' => [
                        'id' => 'Membangun situs portofolio dan blog teknis dua bahasa dengan arsitektur modular monolith.',
                        'en' => 'Built a bilingual portfolio and engineering blog using a modular monolith architecture.',
                    ],
                ],
            ],
            'social.links' => [
                'github' => $github,
                'linkedin' => $linkedin,
                'email' => $email,
            ],
            'cv.paths' => [
                'id' => 'cv/hafizh-rafid-id.pdf',
                'en' => 'cv/hafizh-rafid-en.pdf',
            ],
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
    }
}
