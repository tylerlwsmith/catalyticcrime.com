<x-layout :meta="$meta">
    <x-container>
        <x-page-wrapper>
            <div class="max-w-2xl mx-auto">
                <x-page-title>CatalyticCrime.com</x-page-title>
                <p class="text-xl font-light">Tracking Bakersfield's Catalytic Converter theft</p>
                <div class="mt-5 mb-10">
                    <a href="/reports/create"
                        class="bg-blue-800 text-white inline-block px-5 py-2 rounded-md hover:bg-blue-400">
                        Report a theft
                    </a>
                </div>
                @if ($reports->count() > 0)
                    @foreach ($reports as $report)
                        <article class="border-b border-gray-200 pt-2 last-of-type:border-b-0">
                            <h2 class="text-xl font-light">
                                <a href="{{ route('reports.show', ['report' => $report->id]) }}">
                                    {{ $report->vehicle }}
                                </a>
                            </h2>
                            <div class="flex flex-wrap justify-between text-gray-600">
                                <div class="text-sm pr-4 pb-2">
                                    Converter stolen {{ $report->date->format('F d, Y') }}
                                    {{ $report->time ? 'at ' . $report->time->format('g:i a') : '' }}
                                </div>
                                <div class="text-sm pb-2">{{ $report->zip }}</div>
                            </div>
                        </article>
                    @endforeach

                    {{ $reports->links('components.pagination') }}
                @else
                    <p>No reports to show at this time.</p>
                @endif
            </div>
        </x-page-wrapper>
    </x-container>
</x-layout>
