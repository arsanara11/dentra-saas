{{--
    Shared by create and edit.

    Expects: $service, $categories, $tenant, $action, $submitLabel
    Optional: $method ('PUT' for edit)
--}}
@php
    $currency = $tenant->currency ?: 'IDR';

    $inputClass = 'w-full rounded-[16px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm text-[#263550] outline-none transition placeholder:text-[#A1AFBF] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]';
    $labelClass = 'mb-2 block text-sm font-semibold text-[#526176]';
    $errorClass = 'mt-2 text-xs font-medium text-[#D97791]';
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="grid grid-cols-1 gap-5 px-6 py-7 sm:px-8 sm:py-8 lg:grid-cols-2">

        {{-- CODE --}}
        <div>
            <label for="code" class="{{ $labelClass }}">
                Service Code <span class="text-[#D97791]">*</span>
            </label>
            <input id="code" type="text" name="code" required maxlength="30"
                   value="{{ old('code', $service->code) }}"
                   placeholder="e.g. SCALING"
                   style="text-transform: uppercase"
                   class="{{ $inputClass }} @error('code') border-[#E7A3B2] @enderror">
            @error('code')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-[#9BA8B7]">Short unique code. Letters, numbers, dashes and underscores.</p>
        </div>

        {{-- NAME --}}
        <div>
            <label for="name" class="{{ $labelClass }}">
                Service Name <span class="text-[#D97791]">*</span>
            </label>
            <input id="name" type="text" name="name" required maxlength="150"
                   value="{{ old('name', $service->name) }}"
                   placeholder="e.g. Teeth Scaling & Polishing"
                   class="{{ $inputClass }} @error('name') border-[#E7A3B2] @enderror">
            @error('name')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- CATEGORY --}}
        <div>
            <label for="category" class="{{ $labelClass }}">Category</label>
            <input id="category" type="text" name="category" maxlength="50" list="service-categories"
                   value="{{ old('category', $service->category) }}"
                   placeholder="e.g. Preventive, Restorative, Surgery"
                   class="{{ $inputClass }} @error('category') border-[#E7A3B2] @enderror">
            <datalist id="service-categories">
                @foreach ($categories as $category)
                    <option value="{{ $category }}"></option>
                @endforeach
            </datalist>
            @error('category')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- STATUS --}}
        <div>
            <label for="status" class="{{ $labelClass }}">
                Status <span class="text-[#D97791]">*</span>
            </label>
            <select id="status" name="status" required
                    class="{{ $inputClass }} @error('status') border-[#E7A3B2] @enderror">
                <option value="active" @selected(old('status', $service->status) === 'active')>Active — available for booking</option>
                <option value="inactive" @selected(old('status', $service->status) === 'inactive')>Inactive — hidden from booking</option>
            </select>
            @error('status')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- DURATION --}}
        <div>
            <label for="duration_minutes" class="{{ $labelClass }}">
                Default Duration (minutes) <span class="text-[#D97791]">*</span>
            </label>
            <input id="duration_minutes" type="number" name="duration_minutes" required min="5" max="480" step="5"
                   value="{{ old('duration_minutes', $service->duration_minutes) }}"
                   class="{{ $inputClass }} @error('duration_minutes') border-[#E7A3B2] @enderror">
            @error('duration_minutes')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-[#9BA8B7]">Fills the end time when an appointment is booked.</p>
        </div>

        {{-- PRICE --}}
        <div>
            <label for="price" class="{{ $labelClass }}">
                Price ({{ $currency }}) <span class="text-[#D97791]">*</span>
            </label>
            <input id="price" type="number" name="price" required min="0" step="0.01"
                   value="{{ old('price', $service->price) }}"
                   class="{{ $inputClass }} @error('price') border-[#E7A3B2] @enderror">
            @error('price')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- DESCRIPTION --}}
        <div class="lg:col-span-2">
            <label for="description" class="{{ $labelClass }}">Description</label>
            <textarea id="description" name="description" rows="4" maxlength="5000"
                      placeholder="What the treatment includes, preparation, notes for staff..."
                      class="{{ $inputClass }} resize-y leading-6 @error('description') border-[#E7A3B2] @enderror">{{ old('description', $service->description) }}</textarea>
            @error('description')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-[#E9EFF4] bg-[#FCFDFE] px-6 py-5 sm:flex-row sm:items-center sm:justify-end sm:px-8">
        <a href="{{ route('clinic.services.index') }}"
           class="inline-flex items-center justify-center rounded-[14px] border border-[#DCE6EE] bg-white px-5 py-3 text-sm font-semibold text-[#5F6C7F] transition hover:bg-[#F8FAFC]">
            Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center rounded-[14px] bg-[#5B9DF9] px-6 py-3 text-sm font-semibold text-white shadow-[0_6px_16px_rgba(91,157,249,0.20)] transition hover:bg-[#4E91F0]">
            {{ $submitLabel }}
        </button>
    </div>
</form>