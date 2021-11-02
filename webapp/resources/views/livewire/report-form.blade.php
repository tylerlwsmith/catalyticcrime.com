<form wire:submit.prevent="submit" class="mt-8">
    <div class="grid grid-cols-2 gap-3">
        <div class="mb-3">
            <label for="date" class="inline-block text-sm mb-1 text-gray-600">Approximate date of theft</label>

            <input id="date" type="date" class="min-w-full border-gray-300 text-gray-600 text-sm" wire:model="date">

            @error('date')
                <div class="text-red-700 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="time" class="inline-block text-sm mb-1 text-gray-600">Approximate time of theft</label>

            <input id="time" type="time" class="min-w-full border-gray-300 text-gray-600 text-sm" wire:model="time">

            @error('time')
                <div class="text-red-700 text-sm">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="street_address_1" class="inline-block text-sm mb-1 text-gray-600">Street Address</label>

        <input id="street_address_1" type="text" class="min-w-full border-gray-300 text-gray-600 text-sm"
            wire:model="street_address_1">

        @error('street_address_1')
            <div class="text-red-700 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="street_address_2" class="inline-block text-sm mb-1 text-gray-600">Street Address 2</label>

        <input id="street_address_2" type="text" class="min-w-full border-gray-300 text-gray-600 text-sm"
            wire:model="street_address_2">

        @error('street_address_2')
            <div class="text-red-700 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="city" class="inline-block text-sm mb-1 text-gray-600">City</label>
        <input id="city" type="text" class="w-full border-gray-300 text-gray-600 text-sm" value="Bakersfield" readonly>
    </div>

    <div class="mb-3">
        <label for="zip" class="inline-block text-sm mb-1 text-gray-600">ZIP</label>
        <input id="zip" type="text" wire:model="zip" class="min-w-full border-gray-300 text-gray-600 text-sm" @keypress="
            if ($el.value.length >= 5 || !/\d/.test($event.key)) {
                $event.preventDefault();
                return false;
            }
        ">

        @error('zip')
            <div class="text-red-700 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <span class="cursor-default inline-block text-sm mb-1 text-gray-600"
        @click="document.getElementById('vehicle_make').focus()">
        Vehicle Info
    </span>

    <div class="grid grid-cols-3 gap-3" x-data="{keydown: true}">
        <div class="mb-3">
            <label class="sr-only" for="vehicle_make">Vehicle Make</label>

            <select id="vehicle_make" class="w-full border-gray-300 text-gray-600 text-sm" wire:model="vehicle_make"
                @keydown="keydown=true" @click="keydown=false">
                @foreach ($vehicle_make_list as $index => $make)
                    <option value="{{ $make }}">
                        {{ $make ?: 'Select Make' }}
                    </option>
                @endforeach
            </select>

            @error('vehicle_make')
                <div class="text-red-700 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="sr-only" for="vehicle_model">Vehicle Model</label>

            <select id="vehicle_model" class="w-full border-gray-300 text-gray-600 text-sm" wire:model="vehicle_model"
                @keydown="keydown=true" @click="keydown=false" @vehicle-make-updated.window="
                if(keydown) {
                    return keydown = false;
                }
                $el.focus()
                " {{ !$vehicle_make ? 'disabled' : '' }}>
                @foreach ($vehicle_model_list as $index => $model)
                    <option value="{{ $model }}">
                        {{ $vehicle_make && $model ? $model : 'Select Model' }}
                    </option>
                @endforeach
            </select>

            @error('vehicle_model')
                <div class="text-red-700 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="sr-only" for="vehicle_year">Vehicle Year</label>

            <select id="vehicle_year" class="w-full border-gray-300 text-gray-600 text-sm" wire:model="vehicle_year"
                @keydown="keydown=true" @click="keydown=false" @vehicle-model-updated.window="
                if(keydown) {
                    return keydown = false;
                }
                $el.focus()
                " {{ !$vehicle_model ? 'disabled' : '' }}>
                @foreach ($vehicle_year_list as $index => $year)
                    <option value="{{ $year }}">
                        {{ $vehicle_model && $year ? $year : 'Select Year' }}
                    </option>
                @endforeach
            </select>

            @error('vehicle_year')
                <div class="text-red-700 text-sm">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="police_report_number" class="inline-block text-sm mb-1 text-gray-600">Police Report Number
            (optional)</label>

        <input id="police_report_number" type="text" class="min-w-full border-gray-300 text-gray-600 text-sm"
            wire:model="police_report_number">
    </div>

    <div class="mb-3">
        <div class="cursor-default inline-block text-sm mb-1 text-gray-600" @click="
            document.getElementById('uploader').focus();
        ">
            Photos/Videos of thieves
        </div>

        @foreach ($saved_uploads as $upload)
            <div class="flex justify-between flex-wrap pb-4 items-end">
                <div class="max-w-[100px]">
                    <x-upload :upload="$upload" />
                </div>

                <div x-wire>
                    <input
                        type="button"
                        value="Delete"
                        @click="
                        if(confirm('Are you sure you want to delete this upload?')) {
                            $wire.deleteSavedUpload({{ $upload->id }})
                        }"
                    >
                </div>
            </div>
        @endforeach

        @for ($i = 0; $i < count($unsubmitted_uploads); $i++)
            <div class="flex justify-between flex-wrap mb-4">
                <p>{{ $unsubmitted_uploads[$i]->getClientOriginalName() }}</p>

                <input
                    type="button"
                    value="Delete"
                    wire:click="deleteUnsubmittedUpload({{ $i }})"
                    wire:key="delete-upload:{{ $i }}"
                >

                @error("unsubmitted_uploads.{$i}")
                    <div class="text-red-700 text-sm w-full">{{ $message }}</div>
                @enderror
            </div>
        @endfor

        <div>
            <input id="uploader" type="file" wire:model="unsubmitted_uploads.{{ count($unsubmitted_uploads) }}"
                wire:key="unsubmitted_upload:{{ count($unsubmitted_uploads) }}">
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="inline-block text-sm mb-1 text-gray-600">Description (optional)</label>

        <textarea id="description" rows="5" class="min-w-full border-gray-300 text-gray-600 text-sm"
            wire:model="description"></textarea>

        @error('description')
            <div class="text-red-700 text-sm">{{ $message }}</div>
        @enderror
    </div>

    @if ($errors->count())
        <div class="text-red-700 text-sm mb-3">
            Please correct the errors above then resubmit.
        </div>
    @endif

    <input type="submit" value="Submit" class="px-10 py-4 text-sm">
</form>
