<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Menu;
use App\Models\Page;
use App\Models\PageView;
use App\Models\Post;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@nusatim.com'],
            ['name' => 'Nusatim Admin', 'password' => bcrypt('password')]
        );

        $this->seedSiteSettings();
        $services = $this->seedServices();
        $this->seedMenu($services);
        $this->seedFooterMenu($services);
        $this->seedPages();
        $this->seedProjects();
        $this->seedTeamMembers();
        $this->seedPricingPlans();
        $this->seedFaqs();
        $this->seedTestimonials();
        $this->seedClients();
        $this->seedPosts();
        $this->seedPromotion();
        $this->seedPageViews();
    }

    private function seedSiteSettings(): void
    {
        // firstOrCreate, not updateOrCreate: site settings (especially the
        // logo/favicon uploads) are routinely customized via /admin, and a
        // re-seed must never clobber them back to these placeholder defaults.
        SiteSetting::firstOrCreate(['id' => 1], [
            'company_name' => 'Nusatim',
            'legal_name' => 'PT. Nusantara Teknologi Inovasi Mandiri',
            'tagline' => 'Technology consulting, software development, and digital marketing solutions to help your business grow.',
            'email' => 'info@nusatim.com',
            'phone' => '+62 21 0000 0000',
            'address' => 'Jakarta, Indonesia',
            'logo_light' => 'media/logo-light.png',
            'logo_dark' => 'media/logo-dark.png',
            'logo_mobile' => 'media/logo-mobile.png',
            'favicon' => 'media/favicon.png',
            'default_meta_title' => 'Nusatim | Technology & Digital Solutions Company',
            'default_meta_description' => 'Nusatim (PT. Nusantara Teknologi Inovasi Mandiri) delivers technology consulting, software development, and digital marketing solutions to help businesses grow.',
            'default_meta_keywords' => 'nusatim, teknologi, software development, digital marketing, IT consulting Indonesia',
            'default_og_image' => 'media/banner/banner1.jpg',
        ]);
    }

    private function seedServices()
    {
        $items = [
            ['title' => 'Web Marketing', 'icon' => 'flaticon-shout', 'image' => 'media/menu/home1.jpg'],
            ['title' => 'Development', 'icon' => 'flaticon-growth-1', 'image' => 'media/menu/home2.jpg'],
            ['title' => 'Creative Layout', 'icon' => 'flaticon-marketing', 'image' => 'media/menu/home3.jpg'],
            ['title' => 'Interface Design', 'icon' => 'flaticon-internet', 'image' => 'media/menu/home4.jpg'],
            ['title' => 'SEO Optimized', 'icon' => 'flaticon-click', 'image' => 'media/menu/home5.jpg'],
            ['title' => 'Awesome Support', 'icon' => 'flaticon-support', 'image' => 'media/menu/home6.jpg'],
        ];

        $services = collect();
        foreach ($items as $order => $item) {
            $services->push(Service::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($item['title'])],
                [
                    'title' => $item['title'],
                    'icon' => $item['icon'],
                    'image' => $item['image'],
                    'short_description' => "Layanan {$item['title']} untuk membantu bisnis Anda berkembang dengan solusi teknologi yang tepat.",
                    'content' => "<p>Detail lengkap layanan {$item['title']} akan diisi oleh admin melalui control panel.</p>",
                    'order' => $order,
                    'is_active' => true,
                ]
            ));
        }

        return $services;
    }

    private function seedMenu($services): void
    {
        $menu = Menu::updateOrCreate(['slug' => 'header'], ['name' => 'Header Menu']);
        $menu->allItems()->delete();

        $home = $menu->allItems()->create(['label' => 'Home', 'url' => route('home'), 'type' => 'link', 'order' => 1]);
        $about = $menu->allItems()->create(['label' => 'About', 'url' => route('about'), 'type' => 'link', 'order' => 2]);

        $servicesItem = $menu->allItems()->create(['label' => 'Services', 'url' => route('services.index'), 'type' => 'mega_menu', 'order' => 3]);
        foreach ($services as $i => $service) {
            $menu->allItems()->create([
                'parent_id' => $servicesItem->id,
                'label' => $service->title,
                'url' => route('services.show', $service->slug),
                'image' => $service->image,
                'type' => 'link',
                'order' => $i,
            ]);
        }

        $portfolio = $menu->allItems()->create(['label' => 'Portfolio', 'url' => route('portfolio'), 'type' => 'link', 'order' => 4]);

        $pages = $menu->allItems()->create(['label' => 'Pages', 'type' => 'dropdown', 'order' => 5]);
        $menu->allItems()->create(['parent_id' => $pages->id, 'label' => 'Pricing', 'url' => route('pricing'), 'type' => 'link', 'order' => 1]);
        $menu->allItems()->create(['parent_id' => $pages->id, 'label' => 'Team', 'url' => route('team'), 'type' => 'link', 'order' => 2]);
        $menu->allItems()->create(['parent_id' => $pages->id, 'label' => 'FAQ', 'url' => route('faq'), 'type' => 'link', 'order' => 3]);

        $blog = $menu->allItems()->create(['label' => 'Blog', 'url' => route('blog.index'), 'type' => 'link', 'order' => 6]);
        $contact = $menu->allItems()->create(['label' => 'Contact', 'url' => route('contact'), 'type' => 'link', 'order' => 7]);
    }

    private function seedFooterMenu($services): void
    {
        $menu = Menu::updateOrCreate(['slug' => 'footer'], ['name' => 'Footer Menu']);
        $menu->allItems()->delete();

        $links = $menu->allItems()->create(['label' => 'Important Link', 'type' => 'dropdown', 'order' => 1]);
        $menu->allItems()->create(['parent_id' => $links->id, 'label' => 'About Us', 'url' => route('about'), 'type' => 'link', 'order' => 1]);
        $menu->allItems()->create(['parent_id' => $links->id, 'label' => 'Portfolio', 'url' => route('portfolio'), 'type' => 'link', 'order' => 2]);
        $menu->allItems()->create(['parent_id' => $links->id, 'label' => 'Contact Us', 'url' => route('contact'), 'type' => 'link', 'order' => 3]);
        $menu->allItems()->create(['parent_id' => $links->id, 'label' => 'FAQ', 'url' => route('faq'), 'type' => 'link', 'order' => 4]);

        $servicesColumn = $menu->allItems()->create(['label' => 'Our Services', 'type' => 'dropdown', 'order' => 2]);
        foreach ($services->take(5) as $i => $service) {
            $menu->allItems()->create([
                'parent_id' => $servicesColumn->id,
                'label' => $service->title,
                'url' => route('services.show', $service->slug),
                'type' => 'link',
                'order' => $i,
            ]);
        }
    }

    private function seedPages(): void
    {
        Page::updateOrCreate(['slug' => 'home'], [
            'name' => 'Home',
            'meta_title' => 'Nusatim | Technology & Digital Solutions Company',
            'meta_description' => 'Nusatim (PT. Nusantara Teknologi Inovasi Mandiri) delivers technology consulting, software development, and digital marketing solutions to help businesses grow.',
            'meta_keywords' => 'nusatim, teknologi, software development, digital marketing',
            'og_image' => 'media/banner/banner1.jpg',
            'content' => [
                'hero_eyebrow' => 'Technology & Digital Solutions',
                'hero_title_line1' => 'Kami bantu bisnis Anda',
                'hero_title_accent' => 'tumbuh lewat teknologi',
                'hero_text' => 'Nusatim (PT. Nusantara Teknologi Inovasi Mandiri) membantu bisnis Anda berkembang lewat konsultasi teknologi, pengembangan software, dan digital marketing yang tepat sasaran.',
                'hero_cta_text' => 'Mulai Konsultasi',
                'hero_cta_url' => null,
                'hero_secondary_cta_text' => 'Lihat Layanan',
                'hero_risk_reversal' => 'Konsultasi awal gratis, tanpa komitmen',
                'quick_pick_1_title' => 'Butuh Konsultasi?',
                'quick_pick_1_text' => 'Diskusikan kebutuhan teknologi bisnis Anda, gratis',
                'quick_pick_2_title' => 'Sudah Punya Proyek?',
                'quick_pick_2_text' => 'Lihat portofolio dan hasil kerja tim kami',
                'why_us_heading' => 'Keunggulan yang Membedakan Kami',
                'why_us_text' => 'Bukan sekadar vendor - kami mitra teknologi jangka panjang untuk pertumbuhan bisnis Anda.',
                'why_us_1_icon' => 'flaticon-growth-1',
                'why_us_1_title' => 'Tim Ahli & Berpengalaman',
                'why_us_1_text' => 'Dikerjakan langsung oleh konsultan dan developer yang sudah menangani berbagai skala proyek.',
                'why_us_2_icon' => 'flaticon-marketing',
                'why_us_2_title' => 'Solusi Sesuai Kebutuhan',
                'why_us_2_text' => 'Tidak ada solusi generik - setiap proyek dirancang sesuai tujuan bisnis spesifik Anda.',
                'why_us_3_icon' => 'flaticon-support',
                'why_us_3_title' => 'Dukungan Berkelanjutan',
                'why_us_3_text' => 'Hubungan tidak berhenti saat proyek selesai - kami tetap mendampingi setelah go-live.',
                'hero_card_title' => 'Project Growth',
                'hero_card_subtitle' => 'Rata-rata peningkatan 40% dalam 6 bulan',
                'hero_badge_1_number' => '98%',
                'hero_badge_1_label' => 'Kepuasan Klien',
                'hero_badge_2_number' => '24/7',
                'hero_badge_2_label' => 'Dukungan Teknis',
                'trust_highlight' => '50+',
                'trust_text' => 'bisnis sudah mempercayai kami',
                'logos_label' => 'Dipercaya oleh perusahaan-perusahaan berikut',
                'services_eyebrow' => 'Layanan Kami',
                'services_heading' => 'Solusi Teknologi yang Kami Tawarkan',
                'services_text' => 'Dari konsultasi hingga eksekusi, kami mendampingi bisnis Anda di setiap tahap.',
                'services_link_text' => 'Pelajari lebih lanjut',
                'why_us_eyebrow' => 'Kenapa Nusatim',
                'stat_1_number' => '16',
                'stat_1_label' => 'Global Customer',
                'stat_2_number' => '481',
                'stat_2_label' => 'Completed Projects',
                'stat_3_number' => '165',
                'stat_3_label' => 'Experts Worker',
                'stat_4_number' => '98%',
                'stat_4_label' => 'Client Satisfaction',
                'about_eyebrow' => 'Tentang Kami',
                'about_heading' => 'Mitra Teknologi yang Bisa Anda Percaya',
                'about_text' => 'Nusatim adalah perusahaan teknologi yang membantu bisnis berkembang lewat solusi digital modern - dari pengembangan software hingga digital marketing, dikerjakan oleh tim yang berpengalaman.',
                'about_checklist' => 'Tim Berpengalaman|Harga Transparan|Dukungan Responsif|Solusi Sesuai Kebutuhan',
                'about_image' => 'media/team/team1.jpg',
                'about_button_text' => 'Selengkapnya Tentang Kami',
                'testimonials_eyebrow' => 'Testimoni',
                'testimonials_heading' => 'Apa Kata Klien Kami',
                'portfolio_eyebrow' => 'Portfolio',
                'portfolio_heading' => 'Proyek yang Telah Kami Kerjakan',
                'portfolio_text' => 'Sebagian kecil hasil kerja tim kami untuk klien.',
                'portfolio_button_text' => 'Lihat Semua Portfolio',
                'blog_eyebrow' => 'Blog',
                'latest_news_section_heading' => 'Wawasan Terbaru',
                'latest_news_section_text' => 'Tips dan update seputar teknologi serta digital marketing.',
                'cta_heading' => 'Siap Mengembangkan Bisnis Anda?',
                'cta_text' => 'Konsultasikan kebutuhan teknologi dan digital marketing Anda bersama tim kami, gratis.',
                'cta_button_text' => 'Hubungi Kami Sekarang',
                'cta_button_url' => null,
            ],
        ]);

        Page::updateOrCreate(['slug' => 'about'], [
            'name' => 'About',
            'meta_title' => 'Tentang Kami | Nusatim',
            'meta_description' => 'Kenali Nusatim (PT. Nusantara Teknologi Inovasi Mandiri) - visi, tim, dan cara kami membantu bisnis Anda berkembang lewat teknologi.',
            'og_image' => 'media/about/about1.jpg',
            'content' => [
                'banner_title' => 'Tentang Kami',
                'title' => 'Mitra Teknologi Tepercaya untuk Pertumbuhan Bisnis Anda',
                'text' => 'Nusatim (PT. Nusantara Teknologi Inovasi Mandiri) adalah perusahaan konsultan teknologi dan digital marketing yang membantu bisnis di Indonesia berkembang lewat solusi digital yang tepat sasaran. Kami memadukan keahlian teknis dan pemahaman bisnis untuk menghadirkan hasil yang benar-benar terukur.',
                'read_more_text' => 'Hubungi Kami',
                'about_image' => 'media/about/about5.png',
                'feature_subtitle' => 'Tentang Perusahaan Kami',
                'feature_title' => 'Menyelesaikan Masalah Bisnis Anda Lebih Cepat',
                'feature_image' => 'media/feature/feature10.png',
                'feature_heading' => 'Kami Menjalankan Berbagai Layanan IT yang Mendukung Kesuksesan Anda',
                'feature_text' => 'Dari konsultasi awal hingga dukungan berkelanjutan, kami pastikan teknologi tidak pernah menghambat langkah bisnis Anda.',
                'feature_item_1_icon' => 'flaticon-innovation',
                'feature_item_1_title' => 'Konsultasi Gratis',
                'feature_item_1_text' => 'Sesi konsultasi awal untuk memahami kebutuhan bisnis Anda, tanpa biaya.',
                'feature_item_2_icon' => 'flaticon-shield',
                'feature_item_2_title' => 'Software Kelas Enterprise',
                'feature_item_2_text' => 'Solusi perangkat lunak andal yang dirancang sesuai alur kerja bisnis Anda.',
                'feature_item_3_icon' => 'flaticon-support',
                'feature_item_3_title' => 'Dukungan Tim 24/7',
                'feature_item_3_text' => 'Tim kami selalu siap membantu kapan pun Anda membutuhkan.',
                'history_subtitle' => 'Perjalanan Kami',
                'history_title' => 'Kisah Perjalanan Nusatim',
                'history_1_year' => '2013',
                'history_1_title' => 'Awal Didirikan',
                'history_1_text' => 'Nusatim didirikan dengan misi membantu bisnis lokal mengadopsi teknologi digital.',
                'history_2_year' => '2016',
                'history_2_title' => 'Kantor Pertama Dibuka',
                'history_2_text' => 'Membuka kantor pertama untuk melayani permintaan klien yang terus bertambah.',
                'history_3_year' => '2019',
                'history_3_title' => 'Restrukturisasi Layanan',
                'history_3_text' => 'Menata ulang tim untuk fokus pada pengembangan software dan digital marketing.',
                'history_4_year' => '2022',
                'history_4_title' => 'Ekspansi Layanan',
                'history_4_text' => 'Memperluas cakupan layanan ke konsultasi teknologi end-to-end bagi klien korporat.',
                'video_image' => 'media/about/about-back.jpg',
                'video_url' => null,
                'experience_title' => 'Berpengalaman Bertahun-tahun di Bidang Teknologi',
                'experience_text' => 'Nusatim menghadirkan konsultan, developer, dan tim marketing berpengalaman untuk memberikan solusi teknologi yang benar-benar mendorong kemajuan bisnis Anda.',
                'experience_items' => 'Desain & Pengembangan Web|Dukungan Online|Ide Kepemimpinan Terbaik|Tim Ahli|Harga Terjangkau|Akses Cepat',
                'team_eyebrow' => 'Tim Kami',
                'team_heading' => 'Kami Selalu Mencari Talenta Software Terbaik',
            ],
        ]);

        Page::updateOrCreate(['slug' => 'services'], [
            'name' => 'Services',
            'meta_title' => 'Our Services | Nusatim',
            'meta_description' => 'Explore the technology and digital solutions offered by Nusatim.',
            'content' => [
                'heading' => 'Our Services',
                'heading_text' => 'Technology and digital marketing solutions tailored to what your business actually needs.',
                'stat_1_number' => '845',
                'stat_1_label' => 'Happy Clients',
                'stat_2_number' => '1240',
                'stat_2_label' => 'Projects Done',
                'stat_3_number' => '15420',
                'stat_3_label' => 'Days Of Work',
                'stat_4_number' => '67',
                'stat_4_label' => 'Award Winner',
                'about_1_title' => 'The Next Generation of Our Marketing',
                'about_1_text' => 'We combine proven marketing fundamentals with modern technology to help your brand reach the right audience.',
                'about_2_title' => 'Getting the Maximum Out of Any Business',
                'about_2_text' => 'From strategy to execution, our team works closely with you to make sure every solution fits your business goals.',
            ],
        ]);

        $simplePages = [
            'portfolio' => ['name' => 'Portfolio', 'title' => 'Portfolio | Nusatim', 'content' => []],
            'pricing' => ['name' => 'Pricing', 'title' => 'Pricing Plans | Nusatim', 'content' => [
                'heading' => 'Affordable Pricing',
                'heading_text' => 'Choose the package that fits your business needs.',
            ]],
            'team' => ['name' => 'Team', 'title' => 'Our Team | Nusatim', 'content' => [
                'heading' => 'Dedicated Team',
                'heading_text' => 'Meet the people behind Nusatim - experienced professionals driving technology and digital innovation.',
            ]],
            'blog' => ['name' => 'Blog', 'title' => 'Blog | Nusatim', 'content' => [
                'heading' => 'Wawasan & Update Terbaru',
                'heading_text' => 'Tips, insight, dan update seputar teknologi, pengembangan software, dan digital marketing.',
            ]],
            'faq' => ['name' => 'FAQ', 'title' => 'FAQ | Nusatim', 'content' => [
                'heading' => 'Frequently Asked Questions',
                'heading_text' => 'Answers to common questions about our services and process.',
            ]],
            'contact' => ['name' => 'Contact', 'title' => 'Contact Us | Nusatim', 'content' => [
                'heading' => 'Get In Touch',
                'heading_text' => 'Reach our team for technology consulting, project inquiries, and support.',
            ]],
            'coming-soon' => ['name' => 'Coming Soon', 'title' => 'Coming Soon | Nusatim', 'content' => [
                'sub_title' => "We're Coming Soon..",
                'main_title' => "We're working on our new website, stay tuned!",
                'button_text' => 'Notify Us',
                'countdown_target' => now()->addDays(30)->format('Y-m-d H:i:s'),
            ]],
        ];

        foreach ($simplePages as $slug => $info) {
            Page::updateOrCreate(['slug' => $slug], [
                'name' => $info['name'],
                'meta_title' => $info['title'],
                'meta_description' => "{$info['name']} - Nusatim (PT. Nusantara Teknologi Inovasi Mandiri).",
                'content' => $info['content'],
            ]);
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            ['title' => 'Company Profile Website', 'category' => 'Web Design', 'image' => 'media/gallery/gallery1.jpg'],
            ['title' => 'E-Commerce Platform', 'category' => 'Web Development', 'image' => 'media/gallery/gallery2.jpg'],
            ['title' => 'Mobile Banking App', 'category' => 'App Development', 'image' => 'media/gallery/gallery3.jpg'],
        ];

        foreach ($projects as $order => $project) {
            Project::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($project['title'])],
                [
                    'title' => $project['title'],
                    'category' => $project['category'],
                    'image' => $project['image'],
                    'description' => "Studi kasus proyek {$project['title']} yang dikerjakan oleh tim Nusatim.",
                    'order' => $order,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedTeamMembers(): void
    {
        $members = [
            ['name' => 'Kate Kingston', 'position' => 'Web Designer', 'photo' => 'media/team/team1.jpg'],
            ['name' => 'John Doe', 'position' => 'Backend Developer', 'photo' => 'media/team/team2.jpg'],
            ['name' => 'Jane Smith', 'position' => 'Project Manager', 'photo' => 'media/team/team3.jpg'],
        ];

        foreach ($members as $order => $member) {
            TeamMember::updateOrCreate(
                ['name' => $member['name']],
                [
                    'position' => $member['position'],
                    'photo' => $member['photo'],
                    'order' => $order,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedPricingPlans(): void
    {
        $plans = [
            ['name' => 'Basic', 'price' => 2500000, 'is_highlighted' => false, 'features' => ['1 Landing Page', 'Basic SEO Setup', '1 Month Support']],
            ['name' => 'Professional', 'price' => 7500000, 'is_highlighted' => true, 'features' => ['Up to 10 Pages', 'Full SEO Setup', '3 Months Support', 'Control Panel Access']],
            ['name' => 'Enterprise', 'price' => 15000000, 'is_highlighted' => false, 'features' => ['Unlimited Pages', 'Advanced SEO', '12 Months Support', 'Priority Support']],
        ];

        foreach ($plans as $order => $plan) {
            PricingPlan::updateOrCreate(
                ['name' => $plan['name']],
                [
                    'price' => $plan['price'],
                    'currency' => 'Rp',
                    'period' => 'project',
                    'features' => $plan['features'],
                    'cta_text' => 'Choose Plan',
                    'is_highlighted' => $plan['is_highlighted'],
                    'order' => $order,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            ['q' => 'Berapa lama proses pengerjaan website?', 'a' => 'Rata-rata 2-4 minggu tergantung kompleksitas proyek.'],
            ['q' => 'Apakah ada garansi setelah website selesai?', 'a' => 'Ya, kami memberikan masa dukungan teknis sesuai paket yang dipilih.'],
            ['q' => 'Apakah bisa request fitur khusus?', 'a' => 'Bisa, silakan hubungi tim kami untuk konsultasi kebutuhan spesifik Anda.'],
        ];

        foreach ($faqs as $order => $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['q']],
                ['answer' => $faq['a'], 'order' => $order, 'is_active' => true]
            );
        }
    }

    private function seedTestimonials(): void
    {
        Testimonial::updateOrCreate(
            ['name' => 'Kate Kingston'],
            [
                'position' => 'Web Designer',
                'photo' => 'media/testimonial/testimonial1.jpg',
                'quote' => 'Nusatim membantu bisnis kami tumbuh lewat solusi digital yang tepat sasaran.',
                'rating' => 5,
                'order' => 0,
                'is_active' => true,
            ]
        );
    }

    private function seedClients(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Client::updateOrCreate(
                ['name' => "Client Brand {$i}"],
                ['logo' => "media/brand/brand{$i}.png", 'order' => $i, 'is_active' => true]
            );
        }
    }

    private function seedPosts(): void
    {
        $posts = [
            ['title' => '5 Ways Technology Can Improve Your Business', 'image' => 'media/blog/blog4.jpg', 'category' => 'Teknologi', 'tags' => ['teknologi', 'bisnis'], 'is_featured' => true],
            ['title' => 'How Wireless Technology is Changing Business', 'image' => 'media/blog/blog5.jpg', 'category' => 'Digital Marketing', 'tags' => ['marketing', 'digital']],
            ['title' => 'Thirty-Two Synonyms for the Word Charged', 'image' => 'media/blog/blog6.jpg', 'category' => 'Teknologi', 'tags' => ['teknologi', 'wireless']],
            ['title' => '5 Tren Teknologi yang Akan Mengubah Bisnis di 2026', 'image' => 'media/blog/blog10.jpg', 'category' => 'Teknologi', 'tags' => ['teknologi', 'tren', 'bisnis'], 'days_ago' => 3],
            ['title' => 'Cara Memilih Vendor Software Development yang Tepat', 'image' => 'media/blog/blog11.jpg', 'category' => 'Software Development', 'tags' => ['software', 'vendor', 'tips'], 'days_ago' => 6],
            ['title' => 'Strategi SEO untuk UMKM: Panduan Lengkap', 'image' => 'media/blog/blog12.jpg', 'category' => 'Digital Marketing', 'tags' => ['seo', 'umkm', 'digital marketing'], 'days_ago' => 9],
            ['title' => 'Apa itu Cloud Computing dan Manfaatnya untuk Bisnis', 'image' => 'media/blog/blog13.jpg', 'category' => 'Teknologi', 'tags' => ['cloud', 'teknologi'], 'days_ago' => 13],
            ['title' => '5 Kesalahan Umum saat Membangun Aplikasi Mobile', 'image' => 'media/blog/blog14.jpg', 'category' => 'Software Development', 'tags' => ['mobile', 'software', 'aplikasi'], 'days_ago' => 17],
            ['title' => 'Cara Meningkatkan Konversi Website dengan UX yang Baik', 'image' => 'media/blog/blog15.jpg', 'category' => 'Digital Marketing', 'tags' => ['ux', 'konversi', 'website'], 'days_ago' => 21],
            ['title' => 'Keamanan Siber: Apa yang Perlu Diketahui Setiap Bisnis', 'image' => 'media/blog/blog16.jpg', 'category' => 'Teknologi', 'tags' => ['keamanan', 'siber', 'teknologi'], 'days_ago' => 25],
            ['title' => 'Panduan Memilih CMS yang Tepat untuk Website Perusahaan', 'image' => 'media/blog/blog17.jpg', 'category' => 'Software Development', 'tags' => ['cms', 'website', 'software'], 'days_ago' => 29],
            ['title' => 'Social Media Marketing: Strategi Efektif di 2026', 'image' => 'media/blog/blog18.jpg', 'category' => 'Digital Marketing', 'tags' => ['social media', 'marketing'], 'days_ago' => 33],
            ['title' => 'Otomatisasi Bisnis dengan Teknologi AI', 'image' => 'media/blog/blog19.jpg', 'category' => 'Teknologi', 'tags' => ['ai', 'otomatisasi', 'teknologi'], 'days_ago' => 38],
            ['title' => 'Tips Mengelola Tim Remote untuk Startup Teknologi', 'image' => 'media/blog/blog20.jpg', 'category' => 'Bisnis', 'tags' => ['remote', 'tim', 'startup'], 'days_ago' => 42],
            ['title' => 'Perbedaan Website Statis dan Dinamis, Mana yang Cocok untuk Anda?', 'image' => 'media/blog/blog21.jpg', 'category' => 'Software Development', 'tags' => ['website', 'software'], 'days_ago' => 47],
            ['title' => 'Cara Membangun Brand Awareness Melalui Content Marketing', 'image' => 'media/blog/blog22.jpg', 'category' => 'Digital Marketing', 'tags' => ['brand', 'content marketing'], 'days_ago' => 52],
            ['title' => '5 Alasan Bisnis Anda Butuh Custom Software', 'image' => 'media/blog/blog23.jpg', 'category' => 'Bisnis', 'tags' => ['custom software', 'bisnis'], 'days_ago' => 58],
            ['title' => 'Panduan Dasar API untuk Pemilik Bisnis Non-Teknis', 'image' => 'media/blog/blog24.jpg', 'category' => 'Tips & Trik', 'tags' => ['api', 'panduan', 'bisnis'], 'days_ago' => 64],
            ['title' => 'Cara Menghitung ROI dari Investasi Teknologi', 'image' => 'media/blog/blog25.jpg', 'category' => 'Bisnis', 'tags' => ['roi', 'investasi', 'teknologi'], 'days_ago' => 70],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($post['title'])],
                [
                    'title' => $post['title'],
                    'excerpt' => "Ringkasan singkat artikel {$post['title']}.",
                    'content' => "<p>Isi lengkap artikel {$post['title']} akan diisi oleh admin melalui control panel.</p>",
                    'featured_image' => $post['image'],
                    'category' => $post['category'] ?? null,
                    'tags' => $post['tags'] ?? null,
                    'author_name' => 'Nusatim Team',
                    'published_at' => isset($post['days_ago']) ? now()->subDays($post['days_ago']) : now(),
                    'is_published' => true,
                    'is_featured' => $post['is_featured'] ?? false,
                ]
            );
        }
    }

    /**
     * Backfills ~13 months of realistic page-view history so the dashboard's
     * traffic chart and country map aren't empty on first impression. Only
     * runs once (guarded by count) since these are event rows, not
     * upsertable records like the rest of this seeder.
     */
    private function seedPageViews(): void
    {
        if (PageView::count() > 200) {
            return;
        }

        // Weighted so Indonesia dominates but the map still shows a
        // believable international spread for a tech consulting company.
        $countries = [
            ['code' => 'ID', 'name' => 'Indonesia', 'weight' => 50],
            ['code' => 'SG', 'name' => 'Singapore', 'weight' => 10],
            ['code' => 'MY', 'name' => 'Malaysia', 'weight' => 8],
            ['code' => 'US', 'name' => 'United States', 'weight' => 8],
            ['code' => 'AU', 'name' => 'Australia', 'weight' => 6],
            ['code' => 'IN', 'name' => 'India', 'weight' => 5],
            ['code' => 'JP', 'name' => 'Japan', 'weight' => 4],
            ['code' => 'GB', 'name' => 'United Kingdom', 'weight' => 4],
            ['code' => 'DE', 'name' => 'Germany', 'weight' => 3],
            ['code' => 'NL', 'name' => 'Netherlands', 'weight' => 2],
        ];
        $countryPool = [];
        foreach ($countries as $country) {
            for ($i = 0; $i < $country['weight']; $i++) {
                $countryPool[] = $country;
            }
        }

        $referrers = [
            null, null, null,
            'https://www.google.com/',
            'https://www.google.com/',
            'https://www.facebook.com/',
            'https://www.linkedin.com/',
            'https://www.instagram.com/',
        ];

        $staticPaths = ['/', '/about', '/services', '/portfolio', '/blog', '/contact', '/pricing', '/faq'];

        $posts = Post::query()->where('is_published', true)->get(['id', 'slug', 'published_at']);

        $start = now()->subMonths(13)->startOfMonth();
        $end = now();

        $rows = [];
        $postViewTotals = [];

        // Static pages: steady traffic with a gentle upward trend as the
        // site "matures", plus weekday-heavier patterns.
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $monthsFromStart = $start->diffInMonths($date);
            $trendFactor = 1 + ($monthsFromStart * 0.12);
            $weekdayFactor = $date->isWeekend() ? 0.6 : 1;
            $dailyVisits = (int) round(random_int(6, 14) * $trendFactor * $weekdayFactor);

            for ($i = 0; $i < $dailyVisits; $i++) {
                $path = $staticPaths[array_rand($staticPaths)];
                $country = $countryPool[array_rand($countryPool)];
                $viewedAt = $date->copy()->addSeconds(random_int(0, 86399));

                if ($viewedAt->gt($end)) {
                    continue;
                }

                $rows[] = [
                    'path' => $path,
                    'url' => url($path),
                    'ip_address' => random_int(1, 223).'.'.random_int(0, 255).'.'.random_int(0, 255).'.'.random_int(1, 254),
                    'country_code' => $country['code'],
                    'country_name' => $country['name'],
                    'referrer' => $referrers[array_rand($referrers)],
                    'user_agent' => 'Mozilla/5.0 (demo-seed)',
                    'post_id' => null,
                    'viewed_at' => $viewedAt->format('Y-m-d H:i:s'),
                ];
            }
        }

        // Blog posts: only after each post was actually published, weighted
        // so newer/featured articles read more like real traffic.
        foreach ($posts as $post) {
            $publishedAt = $post->published_at ?: $start;
            $rangeStart = $publishedAt->greaterThan($start) ? $publishedAt : $start;
            $daysLive = max(1, (int) $rangeStart->diffInDays($end));
            $views = random_int(15, 60) + (int) round($daysLive * random_int(1, 3) / 7);

            for ($i = 0; $i < $views; $i++) {
                $viewedAt = $rangeStart->copy()->addSeconds(random_int(0, max(1, $rangeStart->diffInSeconds($end))));
                $country = $countryPool[array_rand($countryPool)];
                $path = '/blog/'.$post->slug;

                $rows[] = [
                    'path' => $path,
                    'url' => url($path),
                    'ip_address' => random_int(1, 223).'.'.random_int(0, 255).'.'.random_int(0, 255).'.'.random_int(1, 254),
                    'country_code' => $country['code'],
                    'country_name' => $country['name'],
                    'referrer' => $referrers[array_rand($referrers)],
                    'user_agent' => 'Mozilla/5.0 (demo-seed)',
                    'post_id' => $post->id,
                    'viewed_at' => $viewedAt->format('Y-m-d H:i:s'),
                ];

                $postViewTotals[$post->id] = ($postViewTotals[$post->id] ?? 0) + 1;
            }
        }

        collect($rows)->chunk(500)->each(function ($chunk) {
            \Illuminate\Support\Facades\DB::table('page_views')->insert($chunk->all());
        });

        foreach ($postViewTotals as $postId => $total) {
            Post::whereKey($postId)->increment('views_count', $total);
        }
    }

    private function seedPromotion(): void
    {
        Promotion::updateOrCreate(
            ['title' => 'Promo Peluncuran Nusatim'],
            [
                'image' => 'media/banner/banner1.jpg',
                'link_url' => route('contact'),
                'is_active' => true,
            ]
        );
    }
}
