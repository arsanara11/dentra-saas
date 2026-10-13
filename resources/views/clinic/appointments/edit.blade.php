<x-app-layout>
    @php
        $statusStyles = [
            'scheduled' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            'confirmed' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
            'no_show' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        ];

        $statusLabels = [
            'scheduled' => 'Scheduled',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
        ];
    @endphp

    <div class="min-h-screen bg-[#F3F7FC] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl space-y-6">

            {{-- Header --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#5B9DF9]">
                        DENTRA / Appointments
                    </p>

                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#263550]">
                        Edit Appointment
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Update appointment details and manage its current status.
                    </p>
                </div>

                <a
                    href="{{ route('clinic.appointments.show', $appointment) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-[#DCE7F0] bg-white px-5 py-3 text-sm font-semibold text-[#263550] shadow-sm transition hover:bg-slate-50"
                >
                    ← Back to Details
                </a>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                    <p class="font-semibold text-red-700">
                        Please check the following errors:
                    </p>

                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('clinic.appointments.update', $appointment) }}"
                method="POST"
                class="space-y-6"
            >
                @csrf
                @method('PUT')

                {{-- Appointment Information --}}
                <section class="overflow-hidden rounded-3xl border border-[#DCE7F0] bg-white shadow-sm">
                    <div class="border-b border-[#E8EFF6] px-6 py-5 sm:px-8">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-[#263550]">
                                    Appointment Information
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Appointment number:
                                    <span class="font-semibold text-[#263550]">
                                        {{ $appointment->appointment_number }}
                                    </span>
                                </p>
                            </div>

                            <span class="inline-flex w-fit items-center rounded-full px-3 py-1.5 text-xs font-bold ring-1 ring-inset {{ $statusStyles[$appointment->status] ?? 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                {{ $statusLabels[$appointment->status] ?? ucfirst(str_replace('_', ' ', $appointment->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2 sm:p-8">

                        {{-- Patient --}}
                        <div class="sm:col-span-2">
                            <label for="patient_id" class="mb-2 block text-sm font-semibold text-[#263550]">
                                Patient <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="patient_id"
                                name="patient_id"
                                required
                                class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="">Select a patient</option>

                                @foreach ($patients as $patient)
                                    <option
                                        value="{{ $patient->id }}"
                                        @selected((string) old('patient_id', $appointment->patient_id) === (string) $patient->id)
                                    >
                                        {{ $patient->first_name }}
                                        {{ $patient->last_name }}
                                        @if ($patient->patient_number)
                                            — {{ $patient->patient_number }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('patient_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Branch --}}
                        <div>
                            <label for="branch_id" class="mb-2 block text-sm font-semibold text-[#263550]">
                                Branch
                            </label>

                            <select
                                id="branch_id"
                                name="branch_id"
                                class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="">No branch selected</option>

                                @foreach ($branches as $branch)
                                    <option
                                        value="{{ $branch->id }}"
                                        @selected((string) old('branch_id', $appointment->branch_id) === (string) $branch->id)
                                    >
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('branch_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Appointment Status --}}
                        <div>
                            <label for="status" class="mb-2 block text-sm font-semibold text-[#263550]">
                                Status <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                                class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-4 focus:ring-blue-100"
                            >
                                @foreach ($statusLabels as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(old('status', $appointment->status) === $value)
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="appointment_date" class="mb-2 block text-sm font-semibold text-[#263550]">
                                Appointment Date <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                id="appointment_date"
                                name="appointment_date"
                                value="{{ old('appointment_date', $appointment->appointment_date?->format('Y-m-d')) }}"
                                required
                                class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-4 focus:ring-blue-100"
                            >

                            @error('appointment_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Start Time --}}
                        <div>
                            <label for="start_time" class="mb-2 block text-sm font-semibold text-[#263550]">
                                Start Time <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="time"
                                id="start_time"
                                name="start_time"
                                value="{{ old('start_time', $appointment->start_time ? substr((string) $appointment->start_time, 0, 5) : '') }}"
                                required
                                class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-4 focus:ring-blue-100"
                            >

                            @error('start_time')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- End Time --}}
                        <div>
                            <label for="end_time" class="mb-2 block text-sm font-semibold text-[#263550]">
                                End Time
                            </label>

                            <input
                                type="time"
                                id="end_time"
                                name="end_time"
                                value="{{ old('end_time', $appointment->end_time ? substr((string) $appointment->end_time, 0, 5) : '') }}"
                                class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-4 focus:ring-blue-100"
                            >

                            <p class="mt-1.5 text-xs text-slate-400">
                                Optional. Must be later than the start time.
                            </p>

                            @error('end_time')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Reason --}}
                        <div class="sm:col-span-2">
                            <label for="reason" class="mb-2 block text-sm font-semibold text-[#263550]">
                                Reason for Visit
                            </label>

                            <input
                                type="text"
                                id="reason"
                                name="reason"
                                maxlength="255"
                                value="{{ old('reason', $appointment->reason) }}"
                                placeholder="e.g. Dental check-up, toothache, cleaning"
                                class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-4 focus:ring-blue-100"
                            >

                            @error('reason')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="sm:col-span-2">
                            <label for="notes" class="mb-2 block text-sm font-semibold text-[#263550]">
                                Additional Notes
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                rows="4"
                                placeholder="Add relevant information for this appointment..."
                                class="w-full resize-y rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-4 focus:ring-blue-100"
                            >{{ old('notes', $appointment->notes) }}</textarea>

                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('clinic.appointments.show', $appointment) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-[#DCE7F0] bg-white px-6 py-3 text-sm font-semibold text-[#263550] transition hover:bg-slate-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-[#5B9DF9] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#4388EC] focus:outline-none focus:ring-4 focus:ring-blue-200"
                    >
                        Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>