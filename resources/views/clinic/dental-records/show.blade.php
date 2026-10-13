<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FB] px-4 py-8 sm:px-6 lg:px-10">
        <div class="mx-auto max-w-5xl">

            {{-- HEADER --}}
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#5B9DF9]">
                        Patient Management
                    </p>

                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-[#263550]">
                        Dental Record Details
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Detail pemeriksaan dan riwayat perawatan pasien.
                    </p>
                </div>

                <a href="{{ route('clinic.dental-records.index') }}"
                   class="inline-flex items-center justify-center rounded-2xl border border-[#DCE7F0] bg-white px-5 py-3 text-sm font-semibold text-[#263550] transition hover:bg-slate-50">
                    ← Back to Records
                </a>
            </div>

            {{-- PATIENT & DOCTOR --}}
            <section class="mb-6 rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-sm sm:p-8">
                <h2 class="mb-5 text-lg font-bold text-[#263550]">
                    Patient Information
                </h2>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Patient Name
                        </p>
                        <p class="mt-2 text-base font-bold text-[#263550]">
                            {{ $record->patient?->first_name ?? '-' }}
                            {{ $record->patient?->last_name ?? '' }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $record->patient?->patient_number ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Examining Doctor
                        </p>
                        <p class="mt-2 text-base font-bold text-[#263550]">
                            {{ $record->doctor?->name ?? '-' }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $record->doctor?->specialization ?? 'No specialization listed' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Examination Date
                        </p>
                        <p class="mt-2 text-sm font-semibold text-[#263550]">
                            {{ $record->examined_at ? \Carbon\Carbon::parse($record->examined_at)->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Related Appointment
                        </p>
                        <p class="mt-2 text-sm font-semibold text-[#263550]">
                            {{ $record->appointment_id ? 'Appointment #' . $record->appointment_id : 'No linked appointment' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- CLINICAL DETAILS --}}
            <section class="rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-sm sm:p-8">
                <h2 class="mb-6 text-lg font-bold text-[#263550]">
                    Clinical Examination
                </h2>

                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Chief Complaint
                        </p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $record->chief_complaint ?: 'No complaint recorded.' }}</p>
                    </div>

                    <div class="border-t border-[#EDF2F7] pt-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Diagnosis
                        </p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $record->diagnosis ?: 'No diagnosis recorded.' }}</p>
                    </div>

                    <div class="border-t border-[#EDF2F7] pt-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Treatment / Procedure
                        </p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $record->treatment ?: 'No treatment recorded.' }}</p>
                    </div>

                    <div class="border-t border-[#EDF2F7] pt-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Prescription
                        </p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $record->prescription ?: 'No prescription recorded.' }}</p>
                    </div>

                    <div class="border-t border-[#EDF2F7] pt-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Clinical Notes
                        </p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $record->clinical_notes ?: 'No additional notes.' }}</p>
                    </div>

                    <div class="border-t border-[#EDF2F7] pt-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Recommended Next Visit
                        </p>
                        <p class="mt-2 text-sm font-semibold text-[#263550]">
                            {{ $record->next_visit_at ? \Carbon\Carbon::parse($record->next_visit_at)->format('d M Y') : 'Not scheduled' }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- ACTIONS --}}
            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('clinic.dental-records.index') }}"
                   class="inline-flex justify-center rounded-xl border border-[#DCE7F0] bg-white px-6 py-3 text-sm font-semibold text-[#263550] hover:bg-slate-50">
                    Back
                </a>

                @can('dental_records.update')
                    <a href="{{ route('clinic.dental-records.edit', $record) }}"
                       class="inline-flex justify-center rounded-xl bg-[#5B9DF9] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#4388EB]">
                        Edit Dental Record →
                    </a>
                @endcan
            </div>

        </div>
    </div>
</x-app-layout>