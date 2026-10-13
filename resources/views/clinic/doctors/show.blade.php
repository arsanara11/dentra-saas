<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FB] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl">

            {{-- Header --}}
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-[0.25em] text-[#5B9DF9]">
                        DENTRA / DOCTORS
                    </p>

                    <h1 class="text-3xl font-bold tracking-tight text-[#263550]">
                        Doctor Profile
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Doctor information and professional details.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('clinic.doctors.index') }}"
                       class="rounded-xl border border-[#DCE7F0] bg-white px-5 py-3 text-sm font-semibold text-[#263550] transition hover:bg-slate-50">
                        Back to Doctors
                    </a>

                    @if(auth()->user()->hasPermission('doctors.manage', $tenant))
                        <a href="{{ route('clinic.doctors.edit', $doctor) }}"
                           class="rounded-xl bg-[#5B9DF9] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-600">
                            Edit Doctor
                        </a>
                    @endif
                </div>
            </div>

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Profile Card --}}
            <div class="overflow-hidden rounded-3xl border border-[#DCE7F0] bg-white shadow-sm">

                <div class="bg-gradient-to-r from-[#EAF3FF] to-[#F7FAFF] p-6 sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-[#5B9DF9] text-3xl font-bold text-white shadow-lg shadow-blue-100">
                            {{ strtoupper(substr($doctor->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="mb-2 flex flex-wrap items-center gap-3">
                                <h2 class="text-2xl font-bold text-[#263550]">
                                    {{ $doctor->name }}
                                </h2>

                                @if($doctor->status === 'active')
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                        Active
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        Inactive
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-slate-500">
                                {{ $doctor->specialization ?: 'General Dentist' }}
                            </p>

                            <p class="mt-2 text-sm font-semibold text-[#5B9DF9]">
                                {{ $doctor->doctor_code }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <div class="p-6 sm:p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-[#263550]">
                            Professional Information
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Registered information for this doctor.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                        <div class="rounded-2xl border border-[#DCE7F0] p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Doctor Code
                            </p>
                            <p class="mt-2 break-words font-semibold text-[#263550]">
                                {{ $doctor->doctor_code }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-[#DCE7F0] p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Specialization
                            </p>
                            <p class="mt-2 break-words font-semibold text-[#263550]">
                                {{ $doctor->specialization ?: 'Not specified' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-[#DCE7F0] p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Professional License Number
                            </p>
                            <p class="mt-2 break-words font-semibold text-[#263550]">
                                {{ $doctor->license_number ?: 'Not provided' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-[#DCE7F0] p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Education
                            </p>
                            <p class="mt-2 break-words font-semibold text-[#263550]">
                                {{ $doctor->education ?: 'Not specified' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-[#DCE7F0] p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Email Address
                            </p>
                            <p class="mt-2 break-words font-semibold text-[#263550]">
                                {{ $doctor->email ?: 'Not provided' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-[#DCE7F0] p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Phone Number
                            </p>
                            <p class="mt-2 break-words font-semibold text-[#263550]">
                                {{ $doctor->phone ?: 'Not provided' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-[#DCE7F0] p-5 sm:col-span-2 lg:col-span-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Clinic Branch
                            </p>
                            <p class="mt-2 break-words font-semibold text-[#263550]">
                                {{ $doctor->branch?->name ?? 'Not assigned' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-[#DCE7F0] p-5 sm:col-span-2 lg:col-span-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Biography
                            </p>
                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-7 text-slate-600">{{ $doctor->bio ?: 'No biography has been added.' }}</p>
                        </div>

                    </div>

                    {{-- Timestamps --}}
                    <div class="mt-8 flex flex-col gap-2 border-t border-[#DCE7F0] pt-5 text-xs text-slate-400 sm:flex-row sm:justify-between">
                        <p>
                            Created:
                            {{ $doctor->created_at?->format('d M Y, H:i') ?? '-' }}
                        </p>

                        <p>
                            Last updated:
                            {{ $doctor->updated_at?->format('d M Y, H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
