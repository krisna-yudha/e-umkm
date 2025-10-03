<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = $this->scrapeArticles();
        
        return Inertia::render('Articles/Index', [
            'articles' => $articles
        ]);
    }

    private function scrapeArticles()
    {
        // Cache artikel selama 1 jam untuk mengurangi load
        return Cache::remember('semarang_articles', 3600, function () {
            try {
                // Gunakan User-Agent untuk menghindari blocking
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ])->timeout(15)->get('https://www.diskopumkm.semarangkota.go.id/site/index');
                
                if ($response->successful()) {
                    $html = $response->body();
                    
                    // Parse HTML untuk mengambil artikel
                    $articles = $this->parseArticlesSimple($html);
                    
                    if (count($articles) > 0) {
                        return $articles;
                    }
                }
                
                return $this->getFallbackArticles();
            } catch (\Exception $e) {
                // Log error untuk debugging
                Log::error('Scraping failed: ' . $e->getMessage());
                
                // Jika scraping gagal, return artikel fallback
                return $this->getFallbackArticles();
            }
        });
    }

    private function parseArticlesSimple($html)
    {
        $articles = [];
        
        try {
            // Gunakan regex sederhana untuk mencari link dan judul
            // Pattern untuk mencari link artikel
            preg_match_all('/<a[^>]+href=["\']([^"\']*(?:detail|artikel|berita|news)[^"\']*)["\'][^>]*>([^<]+)<\/a>/i', $html, $linkMatches);
            
            // Pattern untuk mencari judul dalam h1-h6
            preg_match_all('/<h[1-6][^>]*>([^<]+)<\/h[1-6]>/i', $html, $titleMatches);
            
            // Gabungkan hasil
            for ($i = 0; $i < min(count($linkMatches[1]), count($linkMatches[2]), 8); $i++) {
                $link = $linkMatches[1][$i];
                $title = trim(strip_tags($linkMatches[2][$i]));
                
                // Skip jika title terlalu pendek atau link tidak valid
                if (strlen($title) < 10 || strlen($title) > 150) {
                    continue;
                }
                
                // Convert relative URL ke absolute
                if (!preg_match('/^https?:\/\//', $link)) {
                    $link = 'https://www.diskopumkm.semarangkota.go.id' . ltrim($link, '/');
                }
                
                $articles[] = [
                    'id' => $i + 1,
                    'title' => $title,
                    'excerpt' => 'Klik untuk membaca artikel lengkap dari Dinas Koperasi dan UMKM Kota Semarang.',
                    'link' => $link,
                    'date' => date('Y-m-d'),
                    'image' => '/kotasmg.png',
                    'category' => 'Informasi UMKM'
                ];
            }
            
            return array_slice($articles, 0, 6);
            
        } catch (\Exception $e) {
            Log::error('HTML parsing failed: ' . $e->getMessage());
            return [];
        }
    }

    private function getFallbackArticles()
    {
        return [
            [
                'id' => 1,
                'title' => 'Tips Meningkatkan Penjualan UMKM di Era Digital',
                'excerpt' => 'Pelajari strategi-strategi efektif untuk meningkatkan visibility dan penjualan UMKM Anda melalui platform digital.',
                'link' => 'https://www.diskopumkm.semarangkota.go.id',
                'date' => '2025-09-25',
                'image' => '/kotasmg.png',
                'category' => 'Bisnis'
            ],
            [
                'id' => 2,
                'title' => 'Program Bantuan Modal UMKM Kota Semarang 2025',
                'excerpt' => 'Informasi lengkap mengenai program bantuan modal untuk UMKM di Kota Semarang tahun 2025.',
                'link' => 'https://www.diskopumkm.semarangkota.go.id',
                'date' => '2025-09-20',
                'image' => '/kotasmg.png',
                'category' => 'Kebijakan'
            ],
            [
                'id' => 3,
                'title' => 'Pelatihan Digital Marketing untuk UMKM',
                'excerpt' => 'Workshop gratis digital marketing untuk meningkatkan kemampuan pemasaran online UMKM.',
                'link' => 'https://www.diskopumkm.semarangkota.go.id',
                'date' => '2025-09-15',
                'image' => '/kotasmg.png',
                'category' => 'Pelatihan'
            ],
            [
                'id' => 4,
                'title' => 'Cara Mendaftar UMKM Online di Kota Semarang',
                'excerpt' => 'Panduan lengkap untuk mendaftarkan UMKM secara online melalui sistem digital Kota Semarang.',
                'link' => 'https://www.diskopumkm.semarangkota.go.id',
                'date' => '2025-09-10',
                'image' => '/kotasmg.png',
                'category' => 'Tutorial'
            ],
            [
                'id' => 5,
                'title' => 'Pameran UMKM Kota Semarang 2025',
                'excerpt' => 'Event pameran produk UMKM terbesar di Kota Semarang untuk memperkenalkan produk lokal.',
                'link' => 'https://www.diskopumkm.semarangkota.go.id',
                'date' => '2025-09-05',
                'image' => '/kotasmg.png',
                'category' => 'Event'
            ],
            [
                'id' => 6,
                'title' => 'Inovasi Produk UMKM dalam Menghadapi Persaingan Global',
                'excerpt' => 'Strategi inovasi yang dapat diterapkan UMKM untuk tetap kompetitif di pasar global.',
                'link' => 'https://www.diskopumkm.semarangkota.go.id',
                'date' => '2025-08-30',
                'image' => '/kotasmg.png',
                'category' => 'Inovasi'
            ]
        ];
    }

    public function api()
    {
        $articles = $this->scrapeArticles();
        
        return response()->json($articles);
    }
}