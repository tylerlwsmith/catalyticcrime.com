<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportApprovalController extends Controller
{
    public function store(Request $request, Report $report)
    {
        $data = $request->validate(['admin_approved' => 'required|boolean']);
        $report->admin_approved = $data['admin_approved'];
        $report->save();

        return redirect('/');
    }
}
