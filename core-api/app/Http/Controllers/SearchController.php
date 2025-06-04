<?php

namespace App\Http\Controllers;

use App\Services\Web\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private SearchService $svc) {}

    public function index(Request $r)
    {
        $q = $r->query('q');
        abort_if(!$q, 400, 'Query required');

        return response()->json(
            $this->svc->run($q, auth()->id())
        );
    }
}
