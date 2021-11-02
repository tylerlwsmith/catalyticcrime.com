<?php
/** @var \App\Models\Report $report */
?>
<x-layout :meta="$meta">
    <x-container>
        <x-page-wrapper>
            <div class="max-w-2xl mx-auto">
                <x-page-title>{{ $report->vehicle }}</x-page-title>

                @can('edit', $report)
                    <a href="{{ route('reports.edit', ['report' => $report]) }}">Edit</a>
                @endcan

                @can('admin')
                    @if(!$report->admin_approved)
                        <form method="post" action="{{ route('report-approvals.store', ['report' => $report]) }}">
                            @csrf
                            <input type="hidden" name="admin_approved" value="1">
                            <input type="submit" value="Approve">
                        </form>

                        <form method="post" action="{{ route('report-approvals.store', ['report' => $report]) }}">
                            @csrf
                            <input type="hidden" name="admin_approved" value="0">
                            <input
                                type="submit"
                                value="Reject"
                                @submit="function (event) {
                                    if(!confirm('Are you sure you want to reject the approval of this report?')) {
                                        event.preventDefault();
                                    }
                                }"
                            >
                        </form>
                    @else
                        <form
                            method="post"
                            action="{{ route('report-approvals.store', ['report' => $report]) }}"
                            @submit="function (event) {
                                if(!confirm('Are you sure you want to revoke the approval of this report?')) {
                                    event.preventDefault();
                                }
                            }"
                        >
                            @csrf
                            <input type="hidden" name="admin_approved" value="0">
                            <input type="submit" value="Revoke approval">
                        </form>
                    @endif
                @endcan
                <article class="border-b border-gray-200 py-2 last-of-type:border-b-0">
                    <div class="flex justify-between">
                        <dl class="grid grid-cols-2">
                            <dt class="border-b font-bold">Date stolen</dt>
                            <dd class="border-b">{{ $report->date->format('F d, Y') }}</dd>

                            <dt class="border-b font-bold">Time stolen</dt>
                            <dd class="border-b">{{ $report->time ? $report->time->format('g:i a') : 'unknown' }}
                            </dd>

                            <dt class="border-b font-bold">Address where stolen</dt>
                            <dd class="border-b">{{ $report->address }}</dd>

                            @if ($report->police_report_number)
                                <dt class="border-b font-bold">Police Report Number</dt>
                                <dd class="border-b">{{ $report->police_report_number }}</dd>
                            @endif
                        </dl>
                    </div>

                    <div class="pt-6 pb-10">
                        {{ $report->description }}
                    </div>

                    <div>
                        @foreach($report->uploads as $upload)
                            <div class="pb-4">
                                <x-upload :upload="$upload" />
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </x-page-wrapper>
    </x-container>
</x-layout>
