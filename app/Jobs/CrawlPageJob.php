<?php

namespace App\Jobs;

use App\Models\VisitedUrl;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CrawlPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $baseUrl;

    public function __construct($url, $baseUrl)
    {
        $this->url = $url;
        $this->baseUrl = $baseUrl;
    }

    public function handle()
    {
        $httpClient = HttpClient::create();
        $client = new HttpBrowser($httpClient);

        $this->crawlPage($client, $this->url, $this->baseUrl);
    }

    private function crawlPage(HttpBrowser $client, $url, $baseUrl)
    {
        if (VisitedUrl::where('url', $url)->exists()) {
            return;
        }

        try {
            $crawler = $client->request('GET', $url);
        } catch (\Exception $e) {
            // Hier können Sie den fehlerhaften Link in die Datenbank oder einen anderen Speicher schreiben.
            return;
        }

        VisitedUrl::create(['url' => $url]);

        $crawler->filter('a')->each(function ($node) use ($client, $baseUrl) {
            $nextUrl = $node->attr('href');
            $nextUrl = $this->normalizeUrl($nextUrl, $baseUrl);

            if ($nextUrl && str_starts_with($nextUrl, $baseUrl)) {
                CrawlPageJob::dispatch($nextUrl, $baseUrl)->delay(now()->addSeconds(5));
            }
        });
    }

    private function normalizeUrl($url, $baseUrl)
    {
        if (!$url || str_starts_with($url, '#')) {
            return null;
        }

        if (!str_starts_with($url, 'http')) {
            if ($url[0] === '/') {
                $url = $baseUrl . $url;
            } else {
                $url = $baseUrl . '/' . $url;
            }
        }

        return $url;
    }

}
