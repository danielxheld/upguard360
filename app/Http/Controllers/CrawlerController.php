<?php

namespace App\Http\Controllers;

use App\Jobs\CrawlPageJob;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    public function crawl(Request $request)
    {
        $url = $request->input('url');

        CrawlPageJob::dispatch($url, $url);

        return view('results', [
            'message' => 'Der Crawl-Job wurde in die Warteschlange gestellt. Bitte warten Sie, bis der Crawl abgeschlossen ist.',
        ]);
    }
}
