<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\UI\MetaTag;
use App\Models\UI\PageMeta;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function index(Request $request)
    {
        $meta = (new PageMeta('CatalyticCrime.com', [
            MetaTag::description("Tracking Bakersfield's catalytic converter thefts."),
        ]))->excludeSiteInTitle();

        $reports =  Report::query()
            ->where('user_id', $request->user()->id)
            ->with('vehicle')
            ->orderBy('id', 'desc')->paginate(20);

        return view('user-reports.index', [
            'reports' => $reports,
            'meta' => $meta
        ]);
    }
}
