<?php
/** @var \App\Models\Report $report */
?>

<x-layout :meta="$meta">
    <x-container>
        <x-page-wrapper>
            <div class="max-w-2xl mx-auto">
                <x-page-title>{{ $page->title }}</x-page-title>
                    <div class="prose mt-8">
                    {!! $page->body !!}
                </div>
            </div>
        </x-page-wrapper>
    </x-container>
</x-layout>
