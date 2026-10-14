<?php

namespace App\Http\Controllers;

use App\Services\SeoService;

class SitemapController extends Controller
{
    public function xml()
    {
        $urls = SeoService::sitemapRows();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) {
            $xml .= '<url><loc>'.e($u['loc']).'</loc>'
                .'<lastmod>'.$u['lastmod'].'</lastmod>'
                .'<changefreq>'.$u['changefreq'].'</changefreq>'
                .'<priority>'.$u['priority'].'</priority></url>';
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        return response(SeoService::robots(), 200, ['Content-Type' => 'text/plain']);
    }
}
