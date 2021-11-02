<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\UI\MetaTag;
use App\Models\UI\PageMeta;

class ReportController extends Controller
{
    public function index()
    {
        $meta = (new PageMeta('CatalyticCrime.com', [
            MetaTag::description("Tracking Bakersfield's catalytic converter thefts."),
        ]))->excludeSiteInTitle();

        $reports =  Report::query()
            ->where('admin_approved', true)
            ->with('vehicle')
            ->orderBy('id', 'desc')->paginate(20);

        return view('reports.index', [
            'reports' => $reports,
            'meta' => $meta
        ]);
    }

    public function create()
    {
        $meta = (new PageMeta('Report a theft', [
            MetaTag::description("Report a catalytic converter as stolen."),
        ]));

        return view('reports.create', ['meta' => $meta]);
    }

    public function show(Report $report)
    {
        $meta = (new PageMeta("Theft of $report->vehicle: Report $report->id", [
            MetaTag::description("Tracking Bakersfield's catalytic converter thefts."),
        ]));

        return view('reports.show', ['report' => $report, 'meta' => $meta]);
    }

    public function edit(Report $report)
    {
        $meta = new PageMeta('Edit Report');
        return view('reports.edit', ['report' => $report, 'meta' => $meta]);
    }

    public function destroy(Report $report)
    {
        // todo
    }
}
