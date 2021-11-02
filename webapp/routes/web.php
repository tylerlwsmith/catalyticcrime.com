<?php

use App\Models\MarkdownFile;
use App\Models\UI\MetaTag;
use App\Models\UI\PageMeta;
use Illuminate\Support\Facades\Route;

/**
 * Reports
 */
Route::get('/', 'ReportController@index')->name('reports.index');

Route::get('/reports/create', 'ReportController@create')
    ->middleware('auth')
    ->name('reports.create');

Route::get('/reports/{report}', 'ReportController@show')
    ->where(['report' => '\d+'])
    ->middleware('can:show,report')
    ->name('reports.show');

Route::get('/reports/{report}/edit', 'ReportController@edit')
    ->middleware(['auth', 'can:edit,report'])
    ->where(['report' => '\d+'])->name('reports.edit');

Route::post('/report-approval/{report}', 'ReportApprovalController@store')
    ->middleware(['auth', 'admin'])
    ->name('report-approvals.store');

/**
 * User reports
 */
Route::get('/user-reports', 'UserReportController@index')
    ->middleware('auth')
    ->name('user-reports.index');

/**
 * Static pages
 */
Route::get('/about', function () {
    $file = new MarkdownFile('about');

    $meta = new PageMeta('About', [
        MetaTag::description('About CatalyticCrime.com.'),
    ]);

    return view('layouts.markdown', ['meta' => $meta, 'page' => $file]);
})->name('about');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
