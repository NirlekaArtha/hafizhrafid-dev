<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Blog\Enums\ArticleStatus;
use App\Domain\Blog\Models\Article;
use App\Domain\Blog\Models\Category;
use App\Domain\Identity\Enums\UserRole;
use App\Domain\Localization\Enums\Locale;
use App\Domain\Portfolio\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class SampleContentSeeder extends Seeder
{
    private const string GROUP_ARCHITECTURE_ARTICLE = '01936b00-0000-7000-8000-000000000001';

    private const string GROUP_TRANSLATION_ARTICLE = '01936b00-0000-7000-8000-000000000002';

    /**
     * Seed local sample data: admin user, categories, 2 projects, 3 articles, and pivot relations.
     */
    public function run(): void
    {
        // Guard against accidentally creating default admin credentials or sample data in production.
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $admin = $this->seedAdminUser();
        $categories = $this->seedCategories();
        $projects = $this->seedProjects($categories);
        $this->seedArticles($admin, $categories, $projects);
    }

    private function seedAdminUser(): User
    {
        $admin = User::query()->firstOrNew(['email' => 'admin@hafizhrafid.dev']);
        $admin->name = 'Hafizh Rafid';
        $admin->email_verified_at ??= Carbon::now();

        if (! $admin->exists) {
            $admin->password = Hash::make('password');
        }

        // Assign role explicitly because it is intentionally excluded from #[Fillable].
        $admin->role = UserRole::Admin;
        $admin->save();

        return $admin;
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $definitions = [
            [
                'key' => 'web-engineering',
                'name' => ['id' => 'Rekayasa Web', 'en' => 'Web Engineering'],
                'slug' => ['id' => 'rekayasa-web', 'en' => 'web-engineering'],
                'description' => [
                    'id' => 'Catatan pengembangan web full-stack, Laravel, performa, dan standar aksesibilitas.',
                    'en' => 'Full-stack web engineering notes covering Laravel, performance, and accessibility.',
                ],
                'sort_order' => 1,
            ],
            [
                'key' => 'ai-ml',
                'name' => ['id' => 'AI & Pembelajaran Mesin', 'en' => 'AI & Machine Learning'],
                'slug' => ['id' => 'ai-ml', 'en' => 'ai-ml'],
                'description' => [
                    'id' => 'Eksperimen integrasi LLM, pipeline terjemahan, dan evaluasi model praktis.',
                    'en' => 'Experiments with LLM integration, translation pipelines, and practical model evaluation.',
                ],
                'sort_order' => 2,
            ],
            [
                'key' => 'devops',
                'name' => ['id' => 'DevOps & Infrastruktur', 'en' => 'DevOps & Infrastructure'],
                'slug' => ['id' => 'devops', 'en' => 'devops'],
                'description' => [
                    'id' => 'Kontainerisasi Docker, CI/CD, pemantauan, dan keamanan deployment.',
                    'en' => 'Docker containerization, CI/CD pipelines, monitoring, and deployment security.',
                ],
                'sort_order' => 3,
            ],
            [
                'key' => 'architecture',
                'name' => ['id' => 'Arsitektur', 'en' => 'Architecture'],
                'slug' => ['id' => 'arsitektur', 'en' => 'architecture'],
                'description' => [
                    'id' => 'Keputusan desain sistem, modular monolith, skema basis data, dan trade-off teknis.',
                    'en' => 'System design decisions, modular monoliths, database schemas, and engineering trade-offs.',
                ],
                'sort_order' => 4,
            ],
        ];

        $categories = [];

        foreach ($definitions as $definition) {
            $category = Category::query()
                ->where('slug->en', $definition['slug']['en'])
                ->first() ?? new Category;

            $category->fill([
                'name' => $definition['name'],
                'slug' => $definition['slug'],
                'description' => $definition['description'],
                'sort_order' => $definition['sort_order'],
            ]);
            $category->save();

            $categories[$definition['key']] = $category;
        }

        return $categories;
    }

    /**
     * @param  array<string, Category>  $categories
     * @return array<string, Project>
     */
    private function seedProjects(array $categories): array
    {
        $definitions = [
            [
                'key' => 'hafizhrafid-dev',
                'title' => [
                    'id' => 'hafizhrafid.dev — Portofolio & Blog Teknis Dua Bahasa',
                    'en' => 'hafizhrafid.dev — Bilingual Portfolio & Engineering Blog',
                ],
                'slug' => [
                    'id' => 'hafizhrafid-dev',
                    'en' => 'hafizhrafid-dev',
                ],
                'summary' => [
                    'id' => 'Platform pribadi berbasis Laravel modular monolith dengan dukungan bahasa Indonesia dan Inggris, pipeline sanitasi konten, serta panel admin Filament.',
                    'en' => 'Personal platform built as a Laravel modular monolith featuring Indonesian/English localization, a content sanitization pipeline, and a Filament admin panel.',
                ],
                'problem' => [
                    'id' => 'Jejak karya dan tulisan teknis tersebar di berbagai platform tanpa narasi tunggal yang cepat diakses recruiter maupun sesama engineer.',
                    'en' => 'Projects and engineering notes were scattered across platforms without a single fast, searchable showcase for recruiters and peers.',
                ],
                'solution' => [
                    'id' => 'Membangun aplikasi server-rendered dengan pemisahan domain (Blog, Portfolio, Localization, Contact), routing berprefiks locale, dan praproses HTML saat simpan.',
                    'en' => 'Built a server-rendered application with clear domain boundaries (Blog, Portfolio, Localization, Contact), locale-prefixed routing, and save-time HTML preprocessing.',
                ],
                'role' => [
                    'id' => 'Perancang Arsitektur & Full-stack Developer',
                    'en' => 'System Architect & Full-stack Developer',
                ],
                'technical_decisions' => [
                    'id' => 'Memilih modular monolith Laravel + PostgreSQL agar mudah dirawat satu orang. Syntax highlighting dijalankan di sisi server dengan Phiki (PHP murni) tanpa runtime Node.js di container produksi.',
                    'en' => 'Chose a Laravel + PostgreSQL modular monolith for solo maintainability. Server-side syntax highlighting runs on Phiki (pure PHP) to avoid shipping a Node.js runtime in production containers.',
                ],
                'result' => [
                    'id' => 'Struktur kode teruji otomatis dengan Pest & Larastan, siap melangkah ke rilis MVP dengan target Lighthouse mobile >= 95.',
                    'en' => 'Automated coverage with Pest and Larastan, ready for MVP launch targeting mobile Lighthouse scores >= 95.',
                ],
                'tech_stack' => ['Laravel', 'Livewire', 'Tailwind CSS', 'Filament', 'PostgreSQL', 'Redis'],
                'demo_url' => 'https://hafizhrafid.dev',
                'repo_url' => 'https://github.com/NirlekaArtha/hafizhrafid-dev',
                'is_featured' => true,
                'sort_order' => 1,
                'published_at' => now()->subDays(10),
                'categories' => ['web-engineering', 'architecture'],
            ],
            [
                'key' => 'bilingual-tech-translator',
                'title' => [
                    'id' => 'Pipeline Terjemahan Artikel Teknis Aman Code Block',
                    'en' => 'Code-Safe Technical Article Translation Pipeline',
                ],
                'slug' => [
                    'id' => 'pipeline-terjemahan-artikel-teknis',
                    'en' => 'code-safe-technical-translation-pipeline',
                ],
                'summary' => [
                    'id' => 'Alur terjemahan artikel ID/EN berbasis antrean yang melindungi tag <pre>, <code>, dan URL menggunakan placeholder deterministik sebelum dikirim ke LLM.',
                    'en' => 'Queue-backed ID/EN article translation workflow that protects <pre>, <code>, and URLs with deterministic placeholders before sending prose to an LLM.',
                ],
                'problem' => [
                    'id' => 'Terjemahan mesin sering merusak sintaks kode, nama variabel, dan tautan dokumentasi di dalam artikel pemrograman.',
                    'en' => 'Machine translation frequently corrupts code syntax, variable identifiers, and documentation URLs inside programming articles.',
                ],
                'solution' => [
                    'id' => 'Menerapkan PlaceholderProtector untuk mengganti segmen sensitif dengan token [[X0]] sebelum pemanggilan TranslatorInterface (Gemini/DeepL), lalu memulihkannya secara ketat.',
                    'en' => 'Implemented PlaceholderProtector to swap sensitive segments with [[X0]] tokens before invoking TranslatorInterface (Gemini/DeepL), then strictly restoring them afterwards.',
                ],
                'role' => [
                    'id' => 'Backend & AI Integration Engineer',
                    'en' => 'Backend & AI Integration Engineer',
                ],
                'technical_decisions' => [
                    'id' => 'Abstraksi TranslatorInterface memungkinkan perpindahan antara Gemini API dan DeepL tanpa mengubah job antrean. Seluruh hasil terjemahan disimpan sebagai draf dan wajib ditinjau manual sebelum terbit.',
                    'en' => 'A TranslatorInterface abstraction allows switching between Gemini API and DeepL without changing queue jobs. All translations are saved as drafts requiring manual review before publication.',
                ],
                'result' => [
                    'id' => 'Blok kode dan URL tetap 100% utuh pada pengujian unit serta menekan biaya terjemahan di bawah anggaran bulanan.',
                    'en' => 'Code blocks and URLs remain 100% intact across unit tests while keeping translation costs well within the monthly budget.',
                ],
                'tech_stack' => ['PHP', 'Laravel Queue', 'Gemini API', 'DeepL API', 'Pest'],
                'demo_url' => null,
                'repo_url' => 'https://github.com/NirlekaArtha/hafizhrafid-dev',
                'is_featured' => true,
                'sort_order' => 2,
                'published_at' => now()->subDays(4),
                'categories' => ['ai-ml', 'web-engineering'],
            ],
        ];

        $projects = [];

        foreach ($definitions as $definition) {
            $project = Project::query()
                ->where('slug->en', $definition['slug']['en'])
                ->first() ?? new Project;

            $project->fill([
                'title' => $definition['title'],
                'slug' => $definition['slug'],
                'summary' => $definition['summary'],
                'problem' => $definition['problem'],
                'solution' => $definition['solution'],
                'role' => $definition['role'],
                'technical_decisions' => $definition['technical_decisions'],
                'result' => $definition['result'],
                'tech_stack' => $definition['tech_stack'],
                'demo_url' => $definition['demo_url'],
                'repo_url' => $definition['repo_url'],
                'is_featured' => $definition['is_featured'],
                'sort_order' => $definition['sort_order'],
                'published_at' => $definition['published_at'],
            ]);
            $project->save();

            $categoryIds = array_map(
                fn (string $key): int => $categories[$key]->id,
                $definition['categories'],
            );
            $project->categories()->sync($categoryIds);

            $projects[$definition['key']] = $project;
        }

        return $projects;
    }

    /**
     * @param  array<string, Category>  $categories
     * @param  array<string, Project>  $projects
     */
    private function seedArticles(User $admin, array $categories, array $projects): void
    {
        $architectureBodyId = <<<'HTML'
<p>Saat membangun situs pribadi yang juga berfungsi sebagai blog teknis, menjaga arsitektur tetap sederhana namun terstruktur sangat menentukan kecepatan iterasi.</p>
<h2 id="mengapa-modular-monolith">Mengapa Modular Monolith</h2>
<p>Pemecahan domain ke dalam <code>app/Domain/{Blog,Portfolio,Localization,Contact}</code> menjaga batas tanggung jawab tetap jelas tanpa beban operasional microservices.</p>
<h2 id="praproses-konten-saat-simpan">Praproses Konten Saat Simpan</h2>
<p>Sanitasi HTML, pembuatan daftar isi, dan syntax highlighting dilakukan sekali saat artikel disimpan sehingga setiap permintaan halaman publik tetap ringan.</p>
<pre><code class="language-php">Article::query()->published()->forLocale(Locale::Id)->latest('published_at')->paginate(10);</code></pre>
HTML;

        $architectureArticleId = Article::query()->updateOrCreate(
            [
                'locale' => Locale::Id->value,
                'slug' => 'membangun-web-personal-modular-monolith-laravel',
            ],
            [
                'translation_group_id' => self::GROUP_ARCHITECTURE_ARTICLE,
                'title' => 'Membangun Website Personal Branding dengan Modular Monolith Laravel',
                'excerpt' => 'Ringkasan keputusan arsitektur di balik hafizhrafid.dev: batas domain, isolasi locale, dan praproses konten saat simpan.',
                'body_html' => $architectureBodyId,
                'body_rendered' => $architectureBodyId,
                'toc' => [
                    ['id' => 'mengapa-modular-monolith', 'text' => 'Mengapa Modular Monolith', 'level' => 2],
                    ['id' => 'praproses-konten-saat-simpan', 'text' => 'Praproses Konten Saat Simpan', 'level' => 2],
                ],
                'status' => ArticleStatus::Published,
                'published_at' => now()->subDays(7),
                'reading_time_min' => 5,
                'meta_title' => 'Membangun Website Personal Branding dengan Modular Monolith Laravel',
                'meta_description' => 'Keputusan arsitektur modular monolith Laravel, isolasi locale ID/EN, dan praproses konten pada hafizhrafid.dev.',
                'is_machine_translated' => false,
                'reviewed_at' => null,
                'translated_from_id' => null,
                'author_id' => $admin->id,
            ],
        );

        $architectureArticleId->categories()->sync([
            $categories['web-engineering']->id,
            $categories['architecture']->id,
        ]);

        $architectureBodyEn = <<<'HTML'
<p>When building a personal site that doubles as an engineering blog, keeping the architecture simple yet structured directly impacts iteration speed.</p>
<h2 id="why-modular-monolith">Why a Modular Monolith</h2>
<p>Grouping domain logic under <code>app/Domain/{Blog,Portfolio,Localization,Contact}</code> keeps boundaries explicit without the operational overhead of microservices.</p>
<h2 id="save-time-content-preprocessing">Save-Time Content Preprocessing</h2>
<p>HTML sanitization, table of contents generation, and syntax highlighting run once on save so every public page request stays fast.</p>
<pre><code class="language-php">Article::query()->published()->forLocale(Locale::En)->latest('published_at')->paginate(10);</code></pre>
HTML;

        $architectureArticleEn = Article::query()->updateOrCreate(
            [
                'locale' => Locale::En->value,
                'slug' => 'building-personal-engineering-site-laravel-modular-monolith',
            ],
            [
                'translation_group_id' => self::GROUP_ARCHITECTURE_ARTICLE,
                'title' => 'Building a Personal Engineering Site with a Laravel Modular Monolith',
                'excerpt' => 'Architectural decisions behind hafizhrafid.dev: domain boundaries, locale isolation, and save-time content preprocessing.',
                'body_html' => $architectureBodyEn,
                'body_rendered' => $architectureBodyEn,
                'toc' => [
                    ['id' => 'why-modular-monolith', 'text' => 'Why a Modular Monolith', 'level' => 2],
                    ['id' => 'save-time-content-preprocessing', 'text' => 'Save-Time Content Preprocessing', 'level' => 2],
                ],
                'status' => ArticleStatus::Published,
                'published_at' => now()->subDays(6),
                'reading_time_min' => 5,
                'meta_title' => 'Building a Personal Engineering Site with a Laravel Modular Monolith',
                'meta_description' => 'Modular monolith architecture decisions, ID/EN locale isolation, and save-time content rendering on hafizhrafid.dev.',
                'is_machine_translated' => true,
                'reviewed_at' => now()->subDays(6),
                'translated_from_id' => $architectureArticleId->id,
                'author_id' => $admin->id,
            ],
        );

        $architectureArticleEn->categories()->sync([
            $categories['web-engineering']->id,
            $categories['architecture']->id,
        ]);

        $translationBodyId = <<<'HTML'
<p>Menerjemahkan artikel teknis secara otomatis memiliki satu tantangan klasik: blok kode, inline code, dan URL sering ikut berubah menjadi teks yang tidak valid.</p>
<h2 id="strategi-placeholder">Strategi Placeholder Deterministik</h2>
<p>Sebelum teks dikirim ke penyedia terjemahan, setiap blok <code>&lt;pre&gt;</code>, <code>&lt;code&gt;</code>, dan atribut <code>href</code> diganti dengan token sementara seperti <code>[[X0]]</code>.</p>
<h2 id="tinjauan-manual-wajib">Tinjauan Manual Sebelum Terbit</h2>
<p>Hasil terjemahan selalu disimpan sebagai draf agar penulis memeriksa istilah teknis dan mengisi <code>reviewed_at</code> sebelum publikasi.</p>
HTML;

        $translationArticleId = Article::query()->updateOrCreate(
            [
                'locale' => Locale::Id->value,
                'slug' => 'menjaga-code-block-saat-menerjemahkan-artikel-teknis',
            ],
            [
                'translation_group_id' => self::GROUP_TRANSLATION_ARTICLE,
                'title' => 'Menjaga Code Block Tetap Utuh Saat Menerjemahkan Artikel Teknis',
                'excerpt' => 'Cara melindungi blok kode dan URL dengan placeholder sebelum menerjemahkan artikel pemrograman menggunakan LLM.',
                'body_html' => $translationBodyId,
                'body_rendered' => $translationBodyId,
                'toc' => [
                    ['id' => 'strategi-placeholder', 'text' => 'Strategi Placeholder Deterministik', 'level' => 2],
                    ['id' => 'tinjauan-manual-wajib', 'text' => 'Tinjauan Manual Sebelum Terbit', 'level' => 2],
                ],
                'status' => ArticleStatus::Published,
                'published_at' => now()->subDays(3),
                'reading_time_min' => 4,
                'meta_title' => 'Menjaga Code Block Tetap Utuh Saat Menerjemahkan Artikel Teknis',
                'meta_description' => 'Strategi placeholder untuk melindungi blok kode dan URL dalam pipeline terjemahan artikel teknis.',
                'is_machine_translated' => false,
                'reviewed_at' => null,
                'translated_from_id' => null,
                'author_id' => $admin->id,
            ],
        );

        $translationArticleId->categories()->sync([
            $categories['ai-ml']->id,
            $categories['web-engineering']->id,
        ]);

        $projects['hafizhrafid-dev']->articles()->sync([
            $architectureArticleId->id,
            $architectureArticleEn->id,
        ]);

        $projects['bilingual-tech-translator']->articles()->sync([
            $translationArticleId->id,
        ]);
    }
}
