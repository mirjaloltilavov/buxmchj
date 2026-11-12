<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use Illuminate\Http\Response;

/** robots.txt va sitemap.xml — sayt ma'lumotlaridan avtomatik */
class SeoController extends Controller
{
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            'Disallow: /lead',
            'Allow: /',
            '',
            // Yandex: reklama belgilari (utm va h.k.) bilan kelgan manzillarni bitta sahifa deb hisoblaydi
            'User-agent: Yandex',
            'Disallow: /admin',
            'Disallow: /livewire',
            'Disallow: /lead',
            'Clean-param: utm_source&utm_medium&utm_campaign&utm_content&utm_term&gclid&fbclid&yclid',
            'Allow: /',
            '',
            'Sitemap: '.url('sitemap.xml'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(): Response
    {
        $urls = [];
        // [route nomi, parametrlar, oxirgi o'zgarish vaqti (bo'lsa)]
        foreach (['home', 'courses', 'services', 'blog', 'contact', 'privacy', 'offer'] as $name) {
            $urls[] = [$name, [], null];
        }
        foreach (Course::where('is_active', true)->orderBy('sort')->get(['slug', 'updated_at']) as $c) {
            $urls[] = ['course', ['course' => $c->slug], $c->updated_at];
        }
        foreach (\App\Models\Page::active()->get(['slug', 'updated_at']) as $pg) {
            $urls[] = ['page', ['page' => $pg->slug], $pg->updated_at];
        }
        foreach (Article::published()->get(['slug', 'updated_at', 'published_at']) as $a) {
            $urls[] = ['article', ['article' => $a->slug], max($a->updated_at, $a->published_at)];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";
        foreach ($urls as [$name, $params, $mod]) {
            $uz = route($name, $params);
            $ru = route('ru.'.$name, $params);
            foreach ([$uz, $ru] as $loc) {
                $xml .= '  <url><loc>'.e($loc).'</loc>'
                    .($mod ? '<lastmod>'.$mod->toAtomString().'</lastmod>' : '')
                    .'<xhtml:link rel="alternate" hreflang="uz" href="'.e($uz).'"/>'
                    .'<xhtml:link rel="alternate" hreflang="ru" href="'.e($ru).'"/>'
                    .'<xhtml:link rel="alternate" hreflang="x-default" href="'.e($uz).'"/>'
                    .'</url>'."\n";
            }
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
