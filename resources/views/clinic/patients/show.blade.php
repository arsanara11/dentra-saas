<x-app-layout>
    <div class="min-h-screen bg-[#EEF4F8]">

        {{-- PAGE HEADER --}}
        <div class="border-b border-[#E2EAF1] bg-white">
            <div class="mx-auto max-w-[1500px] px-6 py-5 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <div class="mb-2 flex items-center gap-2 text-xs font-medium text-[#9AA6B6]">
                            <a href="{{ route('clinic.patients.index') }}"
                               class="transition hover:text-[#5B9DF9]">
                                Patients
                            </a>

                            <span>/</span>

                            <span class="text-[#5B9DF9]">Patient Details</span>
                        </div>

                        <p class="text-sm font-medium text-[#8B95A7]">
                            {{ $tenant->name }}
                        </p>

                        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-[#273047]">
                            Patient Details
                        </h1>

                        <p class="mt-1 text-sm text-[#8B95A7]">
                            View patient profile and contact information.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('clinic.patients.index') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-[#DCE5ED] bg-white px-4 py-2.5 text-sm font-semibold text-[#59657A] transition hover:bg-[#F7FAFC]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-4 w-4"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Patients
                        </a>

                        @canDent('patients.update')
                            <a href="{{ route('clinic.patients.edit', $patient) }}"
                               class="inline-flex items-center gap-2 rounded-xl bg-[#5B9DF9] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#4D90F0]">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="1.8">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M16.862 4.487l2.651 2.651M7 17l-1 4 4-1L19.5 10.5a2.121 2.121 0 00-3-3L7 17z" />
                                </svg>
                                Edit Patient
                            </a>
                        @endcanDent
                    </div>

                </div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <main class="mx-auto max-w-[1500px] px-6 py-7 lg:px-8">

            {{-- FLASH MESSAGE --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-[#CDEEDB] bg-[#F0FBF5] px-5 py-4 text-sm font-medium text-[#278653]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5 shrink-0"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- PATIENT PROFILE --}}
            <section class="overflow-hidden rounded-[28px] border border-[#D8E4ED] bg-white shadow-[0_12px_32px_rgba(35,48,71,0.04)]">

                <div class="bg-gradient-to-r from-[#EAF4FF] via-[#F4F8FD] to-[#F5F0FF] p-6 sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-[26px] border border-white bg-white text-2xl font-semibold text-[#5B9DF9] shadow-sm">
                            {{ strtoupper(substr($patient->first_name ?? 'P', 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-2xl font-semibold tracking-tight text-[#273047]">
                                    {{ $patient->full_name }}
                                </h2>

                                @if($patient->status === 'active')
                                    <span class="inline-flex items-center gap-2 rounded-full bg-[#E4F8EC] px-3 py-1.5 text-xs font-semibold text-[#329660]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#43B978]"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full bg-[#EDF0F4] px-3 py-1.5 text-xs font-semibold text-[#7C8595]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#9AA3B2]"></span>
                                        Inactive
                                    </span>
                                @endif
                            </div>

                            <p class="mt-2 text-sm text-[#748198]">
                                Patient ID:
                                <span class="font-mono font-semibold text-[#59657A]">
                                    {{ $patient->patient_number ?: 'Not assigned' }}
                                </span>
                            </p>

                            <p class="mt-1 text-sm text-[#8B95A7]">
                                Registered in {{ $tenant->name }}
                            </p>
                        </div>

                    </div>
                </div>

                {{-- PROFILE INFORMATION --}}
                <div class="p-6 sm:p-8">
                    <div class="mb-6">
                        <h3 class="text-base font-semibold text-[#273047]">
                            Personal Information
                        </h3>
                        <p class="mt-1 text-sm text-[#8B95A7]">
                            Basic details associated with this patient.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2 xl:grid-cols-3">

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                First Name
                            </p>
                            <p class="mt-2 text-sm font-semibold text-[#344057]">
                                {{ $patient->first_name ?: '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                Last Name
                            </p>
                            <p class="mt-2 text-sm font-semibold text-[#344057]">
                                {{ $patient->last_name ?: '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                Gender
                            </p>
                            <p class="mt-2 text-sm font-semibold text-[#344057]">
                                @if($patient->gender === 'male')
                                    Male
                                @elseif($patient->gender === 'female')
                                    Female
                                @else
                                    —
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                Date of Birth
                            </p>
                            <p class="mt-2 text-sm font-semibold text-[#344057]">
                                {{ $patient->date_of_birth ? $patient->date_of_birth->format('d M Y') : '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                Phone Number
                            </p>
                            <p class="mt-2 text-sm font-semibold text-[#344057]">
                                {{ $patient->phone ?: '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                Email Address
                            </p>
                            <p class="mt-2 break-words text-sm font-semibold text-[#344057]">
                                {{ $patient->email ?: '—' }}
                            </p>
                        </div>

                        <div class="sm:col-span-2 xl:col-span-3">
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                Address
                            </p>
                            <p class="mt-2 text-sm leading-6 text-[#59657A]">
                                {{ $patient->address ?: 'No address provided' }}
                            </p>

                            @if($patient->city || $patient->province || $patient->postal_code)
                                <p class="mt-1 text-sm text-[#59657A]">
                                    {{ collect([$patient->city, $patient->province, $patient->postal_code])->filter()->implode(', ') }}
                                </p>
                            @endif
                        </div>

                    </div>
                </div>
            </section>

            {{-- ADDITIONAL INFORMATION --}}
            <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">

                {{-- EMERGENCY CONTACT --}}
                <section class="rounded-[26px] border border-[#D8E4ED] bg-white p-6 shadow-[0_10px_28px_rgba(35,48,71,0.035)] sm:p-7">
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#FFF3E5] text-[#D99643]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-semibold text-[#273047]">
                                Emergency Contact
                            </h3>
                            <p class="mt-1 text-sm text-[#8B95A7]">
                                Contact details in case of an emergency.
                            </p>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <p class="text-xs text-[#9AA6B6]">Contact Name</p>
                                    <p class="mt-1 text-sm font-semibold text-[#344057]">
                                        {{ $patient->emergency_contact_name ?: '—' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-[#9AA6B6]">Phone Number</p>
                                    <p class="mt-1 text-sm font-semibold text-[#344057]">
                                        {{ $patient->emergency_contact_phone ?: '—' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-[#9AA6B6]">Relationship</p>
                                    <p class="mt-1 text-sm font-semibold text-[#344057]">
                                        {{ $patient->emergency_contact_relation ?: '—' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- MEDICAL INFORMATION --}}
                <section class="rounded-[26px] border border-[#D8E4ED] bg-white p-6 shadow-[0_10px_28px_rgba(35,48,71,0.035)] sm:p-7">
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#F2EAFE] text-[#8B7CFF]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 6v12m-6-6h12M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-semibold text-[#273047]">
                                Medical Information
                            </h3>
                            <p class="mt-1 text-sm text-[#8B95A7]">
                                Important notes for the clinical team.
                            </p>

                            <div class="mt-5 space-y-5">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                        Allergies
                                    </p>
                                    <div class="mt-2 rounded-2xl border border-[#F4DFD7] bg-[#FFF8F5] p-4 text-sm leading-6 text-[#765B52]">
                                        {{ $patient->allergies ?: 'No allergies recorded.' }}
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                        Medical Notes
                                    </p>
                                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-[#59657A]">
                                        {{ $patient->medical_notes ?: 'No medical notes recorded.' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-[#9AA6B6]">
                                        Additional Notes
                                    </p>
                                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-[#59657A]">
                                        {{ $patient->notes ?: 'No additional notes.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

        </main>
    </div>
</x-app-layout>
