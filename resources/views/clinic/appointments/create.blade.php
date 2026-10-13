<x-app-layout>
    <div class="min-h-screen bg-[#EEF4F8]">
        <main class="w-full max-w-none space-y-5 px-0 py-0">

            {{-- HERO --}}
            <section class="relative overflow-hidden rounded-[36px] border border-white/80 bg-white shadow-[0_18px_50px_rgba(52,75,98,0.07)]">
                <div class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="absolute -right-16 -top-24 h-[350px] w-[350px] rounded-full bg-[#E8F4FF] opacity-80"></div>
                    <div class="absolute bottom-[-130px] right-[25%] h-[250px] w-[250px] rounded-full bg-[#EDF7FF]"></div>
                </div>

                <div class="relative z-10 px-7 py-8 sm:px-9 lg:px-11 lg:py-10">
                    <div class="flex flex-wrap items-center gap-2 text-sm font-medium">
                        <a href="{{ route('dashboard') }}"
                           class="text-[#94A3B8] transition hover:text-[#5B9DF9]">
                            Clinic
                        </a>

                        <svg class="h-4 w-4 text-[#B7C1CE]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                        </svg>

                        <a href="{{ route('clinic.appointments.index') }}"
                           class="text-[#94A3B8] transition hover:text-[#5B9DF9]">
                            Appointments
                        </a>

                        <svg class="h-4 w-4 text-[#B7C1CE]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                        </svg>

                        <span class="text-[#5B9DF9]">New Appointment</span>
                    </div>

                    <div class="mt-8 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                        <div>
                            <p class="text-sm font-medium text-[#8090A5]">
                                {{ $tenant->name }}
                            </p>

                            <h1 class="mt-2 text-[38px] font-semibold leading-tight tracking-[-0.045em] text-[#25324A] sm:text-[46px]">
                                Create Appointment
                            </h1>

                            <p class="mt-3 max-w-2xl text-sm leading-7 text-[#8A99AC] sm:text-[15px]">
                                Schedule a patient visit and organize the details of their upcoming dental consultation.
                            </p>
                        </div>

                        <div class="hidden h-20 w-20 shrink-0 items-center justify-center rounded-[26px] bg-[#EAF5FF] text-[#5B9DF9] sm:flex">
                            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                                <rect x="3.5" y="5" width="17" height="16" rx="3"/>
                                <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 13v5m-2.5-2.5h5"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ERROR MESSAGES --}}
            @if ($errors->any())
                <section class="rounded-[22px] border border-[#F4D5DC] bg-white px-5 py-4 shadow-sm sm:px-6">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#FCEBF0] text-[#D97791]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" d="M12 7v6m0 4h.01"/>
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-sm font-semibold text-[#C45F78]">
                                Please check your information
                            </h2>

                            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-[#C45F78]">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </section>
            @endif

            @if (session('error'))
                <div class="rounded-[20px] border border-[#F4D5DC] bg-white px-5 py-4 text-sm text-[#C45F78] shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- FORM CARD --}}
            <section class="overflow-hidden rounded-[34px] border border-[#D8E4ED] bg-white shadow-[0_16px_42px_rgba(52,75,98,0.055)]">

                <div class="border-b border-[#E9EFF4] px-6 py-6 sm:px-8">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-[15px] bg-[#E8F3FF] text-[#5B9DF9]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="3.5" y="5" width="17" height="16" rx="2.5"/>
                                <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17"/>
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold tracking-[-0.025em] text-[#263550]">
                                Appointment Information
                            </h2>
                            <p class="mt-1 text-sm text-[#8A99AC]">
                                Fields marked with <span class="font-semibold text-[#D97791]">*</span> are required.
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('clinic.appointments.store') }}">
                    @csrf

                    <div class="space-y-8 px-6 py-7 sm:px-8 sm:py-8">

                        {{-- PATIENT AND BRANCH --}}
                        <div>
                            <div class="mb-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.13em] text-[#5B9DF9]">
                                    01 — Patient & Location
                                </p>
                                <h3 class="mt-2 text-base font-semibold text-[#263550]">
                                    Who is this appointment for?
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                                {{-- PATIENT --}}
                                <div>
                                    <label for="patient_id" class="mb-2 block text-sm font-semibold text-[#526176]">
                                        Patient <span class="text-[#D97791]">*</span>
                                    </label>

                                    <select
                                        id="patient_id"
                                        name="patient_id"
                                        required
                                        class="w-full rounded-[16px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm text-[#263550] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] @error('patient_id') border-[#E7A3B2] @enderror">

                                        <option value="">Select a patient</option>

                                        @foreach ($patients as $patient)
                                            <option
                                                value="{{ $patient->id }}"
                                                @selected((string) old('patient_id') === (string) $patient->id)>
                                                {{ trim($patient->first_name . ' ' . $patient->last_name) }}
                                                — {{ $patient->patient_number }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('patient_id')
                                        <p class="mt-2 text-xs font-medium text-[#D97791]">{{ $message }}</p>
                                    @enderror

                                    @if ($patients->isEmpty())
                                        <p class="mt-2 text-xs leading-5 text-[#D97791]">
                                            No active patients are available. Add or activate a patient before creating an appointment.
                                        </p>
                                    @else
                                        <p class="mt-2 text-xs text-[#9BA8B7]">
                                            Only active patients from this clinic are listed.
                                        </p>
                                    @endif
                                </div>

                                {{-- BRANCH --}}
                                <div>
                                    <label for="branch_id" class="mb-2 block text-sm font-semibold text-[#526176]">
                                        Clinic Branch
                                    </label>

                                    <select
                                        id="branch_id"
                                        name="branch_id"
                                        class="w-full rounded-[16px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm text-[#263550] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] @error('branch_id') border-[#E7A3B2] @enderror">

                                        <option value="">Default / Main branch</option>

                                        @foreach ($branches as $branch)
                                            <option
                                                value="{{ $branch->id }}"
                                                @selected((string) old('branch_id') === (string) $branch->id)>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('branch_id')
                                        <p class="mt-2 text-xs font-medium text-[#D97791]">{{ $message }}</p>
                                    @enderror

                                    <p class="mt-2 text-xs text-[#9BA8B7]">
                                        Optional. Choose the branch where the visit will take place.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-[#EDF2F6]"></div>

                        {{-- DATE AND TIME --}}
                        <div>
                            <div class="mb-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.13em] text-[#5B9DF9]">
                                    02 — Schedule
                                </p>
                                <h3 class="mt-2 text-base font-semibold text-[#263550]">
                                    When is the visit?
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">

                                {{-- DATE --}}
                                <div>
                                    <label for="appointment_date" class="mb-2 block text-sm font-semibold text-[#526176]">
                                        Appointment Date <span class="text-[#D97791]">*</span>
                                    </label>

                                    <input
                                        id="appointment_date"
                                        type="date"
                                        name="appointment_date"
                                        value="{{ old('appointment_date', now()->format('Y-m-d')) }}"
                                        required
                                        class="w-full rounded-[16px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm text-[#263550] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] @error('appointment_date') border-[#E7A3B2] @enderror">

                                    @error('appointment_date')
                                        <p class="mt-2 text-xs font-medium text-[#D97791]">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- START TIME --}}
                                <div>
                                    <label for="start_time" class="mb-2 block text-sm font-semibold text-[#526176]">
                                        Start Time <span class="text-[#D97791]">*</span>
                                    </label>

                                    <input
                                        id="start_time"
                                        type="time"
                                        name="start_time"
                                        value="{{ old('start_time', '09:00') }}"
                                        required
                                        class="w-full rounded-[16px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm text-[#263550] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] @error('start_time') border-[#E7A3B2] @enderror">

                                    @error('start_time')
                                        <p class="mt-2 text-xs font-medium text-[#D97791]">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- END TIME --}}
                                <div>
                                    <label for="end_time" class="mb-2 block text-sm font-semibold text-[#526176]">
                                        End Time
                                    </label>

                                    <input
                                        id="end_time"
                                        type="time"
                                        name="end_time"
                                        value="{{ old('end_time', '09:30') }}"
                                        class="w-full rounded-[16px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm text-[#263550] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] @error('end_time') border-[#E7A3B2] @enderror">

                                    @error('end_time')
                                        <p class="mt-2 text-xs font-medium text-[#D97791]">{{ $message }}</p>
                                    @enderror

                                    <p class="mt-2 text-xs text-[#9BA8B7]">
                                        Optional. Must be later than the start time.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-[#EDF2F6]"></div>

                        {{-- VISIT DETAILS --}}
                        <div>
                            <div class="mb-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.13em] text-[#5B9DF9]">
                                    03 — Visit Details
                                </p>
                                <h3 class="mt-2 text-base font-semibold text-[#263550]">
                                    What is the reason for the visit?
                                </h3>
                            </div>

                            <div class="space-y-5">

                                {{-- REASON --}}
                                <div>
                                    <label for="reason" class="mb-2 block text-sm font-semibold text-[#526176]">
                                        Reason for Visit
                                    </label>

                                    <input
                                        id="reason"
                                        type="text"
                                        name="reason"
                                        value="{{ old('reason') }}"
                                        maxlength="255"
                                        placeholder="e.g. Dental check-up, toothache, cleaning..."
                                        class="w-full rounded-[16px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm text-[#263550] outline-none transition placeholder:text-[#A1AFBF] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] @error('reason') border-[#E7A3B2] @enderror">

                                    @error('reason')
                                        <p class="mt-2 text-xs font-medium text-[#D97791]">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- NOTES --}}
                                <div>
                                    <label for="notes" class="mb-2 block text-sm font-semibold text-[#526176]">
                                        Additional Notes
                                    </label>

                                    <textarea
                                        id="notes"
                                        name="notes"
                                        rows="4"
                                        maxlength="10000"
                                        placeholder="Add any relevant information for this appointment..."
                                        class="w-full resize-y rounded-[16px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm leading-6 text-[#263550] outline-none transition placeholder:text-[#A1AFBF] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] @error('notes') border-[#E7A3B2] @enderror">{{ old('notes') }}</textarea>

                                    @error('notes')
                                        <p class="mt-2 text-xs font-medium text-[#D97791]">{{ $message }}</p>
                                    @enderror

                                    <p class="mt-2 text-xs text-[#9BA8B7]">
                                        Optional. Maximum 10,000 characters.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- STATUS NOTICE --}}
                        <div class="flex items-start gap-3 rounded-[20px] border border-[#DCEBFA] bg-[#F5FAFF] px-5 py-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E4F1FF] text-[#5B9DF9]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" d="M12 11v5m0-8h.01"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-[#344966]">
                                    Appointment status
                                </p>
                                <p class="mt-1 text-sm leading-6 text-[#8192A8]">
                                    New appointments will automatically be saved with the
                                    <span class="font-semibold text-[#5B9DF9]">Scheduled</span>
                                    status. You can manage the status after creating the appointment.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FORM ACTIONS --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-[#E9EFF4] bg-[#FCFDFE] px-6 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                        <a
                            href="{{ route('clinic.appointments.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-[15px] border border-[#DCE6EE] bg-white px-5 py-3.5 text-sm font-semibold text-[#718096] transition hover:border-[#C8D8E5] hover:bg-[#F7FAFC]">

                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                            </svg>

                            Cancel
                        </a>

                        <button
                            type="submit"
                            @disabled($patients->isEmpty())
                            class="inline-flex items-center justify-center gap-2.5 rounded-[15px] bg-[#5B9DF9] px-6 py-3.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(91,157,249,0.22)] transition hover:-translate-y-0.5 hover:bg-[#4E91F0] disabled:cursor-not-allowed disabled:opacity-50">

                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                            </svg>

                            Create Appointment
                        </button>
                    </div>
                </form>
            </section>

            <p class="px-2 pb-4 text-center text-xs text-[#9BA8B7]">
                {{ $tenant->name }} · Appointment Management
            </p>
        </main>
    </div>
</x-app-layout>