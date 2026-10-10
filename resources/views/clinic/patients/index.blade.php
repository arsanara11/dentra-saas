<x-app-layout>

    <div class="min-h-screen bg-[#EEF4F8]">

        {{-- =========================================================
            MAIN PAGE
        ========================================================== --}}
        <main class="w-full max-w-none px-3 py-4 sm:px-4 lg:px-5 lg:py-5">


            {{-- =====================================================
                HERO
            ====================================================== --}}
            <section class="relative overflow-hidden rounded-[36px] border border-white/80 bg-white shadow-[0_18px_50px_rgba(52,75,98,0.07)]">

                {{-- BACKGROUND DECORATION --}}
                <div class="pointer-events-none absolute inset-0 overflow-hidden">

                    <div class="absolute -right-20 -top-28 h-[420px] w-[420px] rounded-full bg-[#E8F4FF] opacity-80 blur-[2px]"></div>

                    <div class="absolute right-[12%] top-[18%] h-[250px] w-[250px] rounded-full bg-[#F2F8FF]"></div>

                    <div class="absolute bottom-[-140px] right-[26%] h-[260px] w-[260px] rounded-full bg-[#EDF7FF]"></div>

                </div>


                {{-- HERO CONTENT --}}
                <div class="relative z-10 flex min-h-[310px] flex-col justify-between gap-10 px-7 py-8 sm:px-9 lg:flex-row lg:items-center lg:px-11 lg:py-10">


                    {{-- LEFT --}}
                    <div class="max-w-[720px]">


                        {{-- BREADCRUMB --}}
                        <div class="flex items-center gap-2 text-sm font-medium">

                            <span class="text-[#94A3B8]">
                                Clinic
                            </span>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-[#B7C1CE]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m9 5 7 7-7 7"
                                />
                            </svg>

                            <span class="text-[#5B9DF9]">
                                Patients
                            </span>

                        </div>


                        {{-- CLINIC --}}
                        <p class="mt-8 text-sm font-medium text-[#8090A5]">
                            {{ $tenant->name }}
                        </p>


                        {{-- TITLE --}}
                        <h1 class="mt-1 text-[42px] font-semibold leading-[1.05] tracking-[-0.045em] text-[#25324A] sm:text-[48px]">
                            Patients
                        </h1>


                        {{-- DESCRIPTION --}}
                        <p class="mt-4 max-w-[650px] text-[15px] leading-7 text-[#8A99AC]">
                            Manage your clinic's patient records, contact information,
                            and patient profiles from one place.
                        </p>

                    </div>


                    {{-- RIGHT HERO --}}
                    <div class="relative flex shrink-0 items-center justify-end lg:min-w-[400px]">


                        {{-- TOOTH DECORATION --}}
                        <div class="pointer-events-none absolute right-[110px] top-1/2 hidden -translate-y-1/2 lg:block">

                            <div class="relative flex h-[190px] w-[190px] items-center justify-center rounded-full bg-[#EAF5FF]">

                                <div class="absolute inset-[22px] rounded-full border border-[#D6EBFF]"></div>

                                <div class="absolute inset-[42px] rounded-full bg-[#F5FAFF]"></div>


                                {{-- TOOTH --}}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 64 64"
                                    class="relative z-10 h-[76px] w-[76px] text-[#8EC3FF]"
                                    fill="none"
                                >
                                    <path
                                        d="M20.7 13.8c-4.8-1.1-9.5 1.6-10.8 6.3-1.6 5.8.7 10.6 3.5 14.6 2.6 3.7 3.4 8.8 4.3 13.2.7 3.4 2 6.5 5 6.5 3.6 0 4.2-5.2 5.2-9.3.9-3.7 1.7-6.1 3.9-6.1s3 2.4 3.9 6.1c1 4.1 1.6 9.3 5.2 9.3 3 0 4.3-3.1 5-6.5.9-4.4 1.7-9.5 4.3-13.2 2.8-4 5.1-8.8 3.5-14.6-1.3-4.7-6-7.4-10.8-6.3-2.5.6-5 2-7.1 2s-4.6-1.4-7.1-2z"
                                        fill="currentColor"
                                        opacity=".32"
                                    />

                                    <path
                                        d="M20.7 13.8c-4.8-1.1-9.5 1.6-10.8 6.3-1.6 5.8.7 10.6 3.5 14.6 2.6 3.7 3.4 8.8 4.3 13.2.7 3.4 2 6.5 5 6.5 3.6 0 4.2-5.2 5.2-9.3.9-3.7 1.7-6.1 3.9-6.1s3 2.4 3.9 6.1c1 4.1 1.6 9.3 5.2 9.3 3 0 4.3-3.1 5-6.5.9-4.4 1.7-9.5 4.3-13.2 2.8-4 5.1-8.8 3.5-14.6-1.3-4.7-6-7.4-10.8-6.3-2.5.6-5 2-7.1 2s-4.6-1.4-7.1-2z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />

                                    <path
                                        d="M23 23c2.2-1.5 5-2.1 9-2.1s6.8.6 9 2.1"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        opacity=".55"
                                    />
                                </svg>


                                {{-- SPARKLE --}}
                                <div class="absolute -right-1 top-5 flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-[0_8px_20px_rgba(91,157,249,0.15)]">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-[#72B1FF]"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                    >
                                        <path d="M12 2l1.8 7.2L21 11l-7.2 1.8L12 20l-1.8-7.2L3 11l7.2-1.8L12 2z"/>
                                    </svg>

                                </div>

                            </div>

                        </div>


                        {{-- ADD BUTTON --}}
                        @canDent('patients.create')

                            <a
                                href="{{ route('clinic.patients.create') }}"
                                class="relative z-20 inline-flex items-center justify-center gap-2.5 rounded-[18px] bg-[#5B9DF9] px-6 py-4 text-sm font-semibold text-white shadow-[0_12px_28px_rgba(91,157,249,0.25)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#4E91F0] hover:shadow-[0_16px_32px_rgba(91,157,249,0.3)]"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 5v14M5 12h14"
                                    />
                                </svg>

                                Add Patient

                            </a>

                        @endcanDent

                    </div>

                </div>

            </section>



            {{-- =====================================================
                STATISTICS
            ====================================================== --}}
            <section class="mt-6">

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">


                    {{-- TOTAL PATIENTS --}}
                    <div class="group rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-[0_12px_32px_rgba(52,75,98,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_38px_rgba(52,75,98,0.08)]">

                        <div class="flex items-start justify-between">

                            <div>

                                <p class="text-sm font-medium text-[#8493A7]">
                                    Total Patients
                                </p>

                                <p class="mt-3 text-[32px] font-semibold tracking-[-0.045em] text-[#263550]">
                                    {{ $patients->total() }}
                                </p>

                                <p class="mt-1.5 text-xs text-[#A0ACBA]">
                                    Registered records
                                </p>

                            </div>


                            <div class="flex h-12 w-12 items-center justify-center rounded-[17px] bg-[#E8F3FF] text-[#5B9DF9] transition duration-200 group-hover:scale-105">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zm8 8a4 4 0 00-4-4m1-5a3 3 0 100-6"
                                    />
                                </svg>

                            </div>

                        </div>

                    </div>


                    {{-- SHOWING --}}
                    <div class="group rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-[0_12px_32px_rgba(52,75,98,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_38px_rgba(52,75,98,0.08)]">

                        <div class="flex items-start justify-between">

                            <div>

                                <p class="text-sm font-medium text-[#8493A7]">
                                    Showing
                                </p>

                                <p class="mt-3 text-[32px] font-semibold tracking-[-0.045em] text-[#263550]">
                                    {{ $patients->count() }}
                                </p>

                                <p class="mt-1.5 text-xs text-[#A0ACBA]">
                                    Patients on this page
                                </p>

                            </div>


                            <div class="flex h-12 w-12 items-center justify-center rounded-[17px] bg-[#F3EBFF] text-[#8B7CFF] transition duration-200 group-hover:scale-105">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 6h14M5 12h14M5 18h8"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.5 6h.01M3.5 12h.01M3.5 18h.01"
                                    />
                                </svg>

                            </div>

                        </div>

                    </div>


                    {{-- ACTIVE --}}
                    <div class="group rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-[0_12px_32px_rgba(52,75,98,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_38px_rgba(52,75,98,0.08)]">

                        <div class="flex items-start justify-between">

                            <div>

                                <p class="text-sm font-medium text-[#8493A7]">
                                    Active Records
                                </p>

                                <p class="mt-3 text-[32px] font-semibold tracking-[-0.045em] text-[#263550]">
                                    {{ $patients->getCollection()->where('status', 'active')->count() }}
                                </p>

                                <p class="mt-1.5 text-xs text-[#A0ACBA]">
                                    On current page
                                </p>

                            </div>


                            <div class="flex h-12 w-12 items-center justify-center rounded-[17px] bg-[#E9F9F0] text-[#3EB778] transition duration-200 group-hover:scale-105">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>

                            </div>

                        </div>

                    </div>


                    {{-- CLINIC --}}
                    <div class="group rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-[0_12px_32px_rgba(52,75,98,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_38px_rgba(52,75,98,0.08)]">

                        <div class="flex items-start justify-between">

                            <div class="min-w-0">

                                <p class="text-sm font-medium text-[#8493A7]">
                                    Clinic
                                </p>

                                <p class="mt-3 truncate text-[21px] font-semibold tracking-[-0.03em] text-[#263550]">
                                    {{ $tenant->name }}
                                </p>

                                <p class="mt-1.5 truncate text-xs text-[#A0ACBA]">
                                    {{ $tenant->city ?? 'Clinic workspace' }}
                                </p>

                            </div>


                            <div class="ml-4 flex h-12 w-12 shrink-0 items-center justify-center rounded-[17px] bg-[#FFF4E4] text-[#E9A24B] transition duration-200 group-hover:scale-105">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 21h18M5 21V6a1 1 0 011-1h12a1 1 0 011 1v15M9 9h1m4 0h1M9 13h1m4 0h1M9 17h1m4 0h1"
                                    />
                                </svg>

                            </div>

                        </div>

                    </div>

                </div>

            </section>



            {{-- =====================================================
                PATIENT DIRECTORY
            ====================================================== --}}
            <section class="mt-6 overflow-hidden rounded-[34px] border border-[#D8E4ED] bg-white shadow-[0_16px_42px_rgba(52,75,98,0.055)]">


                {{-- DIRECTORY TOP --}}
                <div class="px-6 py-7 sm:px-7 lg:px-8">

                    <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">


                        {{-- DIRECTORY TITLE --}}
                        <div>

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-[14px] bg-[#E8F3FF] text-[#5B9DF9]">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M17 20a5 5 0 00-10 0M12 14a4 4 0 100-8 4 4 0 000 8zm8 6a4 4 0 00-4-4"
                                        />
                                    </svg>

                                </div>


                                <div>

                                    <h2 class="text-xl font-semibold tracking-[-0.025em] text-[#263550]">
                                        Patient Directory
                                    </h2>

                                    <p class="mt-1 text-sm text-[#8A99AC]">
                                        View and manage registered patients.
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- SEARCH --}}
                        <form
                            method="GET"
                            action="{{ route('clinic.patients.index') }}"
                            class="w-full xl:w-[400px]"
                        >

                            <div class="relative">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#A1AFBF]"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"
                                    />
                                </svg>

                                <input
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Search patients..."
                                    class="w-full rounded-[17px] border border-[#DCE6EE] bg-[#F8FAFC] py-3.5 pl-11 pr-4 text-sm font-medium text-[#263550] outline-none transition duration-200 placeholder:text-[#A1AFBF] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]"
                                >

                            </div>

                        </form>

                    </div>

                </div>


                {{-- TABLE --}}
                <div class="overflow-x-auto border-t border-[#E9EFF4]">

                    <table class="w-full min-w-[1000px]">


                        {{-- HEAD --}}
                        <thead>

                            <tr class="bg-[#FAFCFD]">

                                <th class="px-8 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Patient
                                </th>

                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Patient ID
                                </th>

                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Contact
                                </th>

                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Gender
                                </th>

                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Status
                                </th>

                                <th class="px-8 py-4 text-right text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        {{-- BODY --}}
                        <tbody class="divide-y divide-[#EDF2F6]">

                            @forelse($patients as $patient)

                                <tr class="group transition duration-150 hover:bg-[#FBFDFF]">


                                    {{-- PATIENT --}}
                                    <td class="px-8 py-5">

                                        <div class="flex items-center gap-4">

                                            @if($patient->gender === 'female')

                                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[15px] bg-[#FCEBF3] text-sm font-semibold text-[#D77FA7]">
                                                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                                                </div>

                                            @else

                                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[15px] bg-[#E8F3FF] text-sm font-semibold text-[#5B9DF9]">
                                                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                                                </div>

                                            @endif


                                            <div class="min-w-0">

                                                <a
                                                    href="{{ route('clinic.patients.show', $patient) }}"
                                                    class="block truncate text-sm font-semibold text-[#263550] transition hover:text-[#5B9DF9]"
                                                >
                                                    {{ $patient->full_name }}
                                                </a>

                                                <p class="mt-1 text-xs text-[#9BA8B7]">
                                                    Registered patient
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- ID --}}
                                    <td class="px-6 py-5">

                                        <span class="inline-flex rounded-[11px] bg-[#F3F6F9] px-3 py-1.5 font-mono text-xs font-medium text-[#5F6C7F]">
                                            {{ $patient->patient_number }}
                                        </span>

                                    </td>


                                    {{-- CONTACT --}}
                                    <td class="px-6 py-5">

                                        @if($patient->phone)

                                            <div class="flex items-center gap-2.5">

                                                <div class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-[#F2F6FA] text-[#8190A3]">

                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.95.68l1.1 3.29a1 1 0 01-.5 1.18l-2.12 1.06a11.05 11.05 0 005.05 5.05l1.06-2.12a1 1 0 011.18-.5l3.29 1.1a1 1 0 01.68.95V18a2 2 0 01-2 2h-1C9.61 20 4 14.39 4 7V6a2 2 0 01-1-1z"
                                                        />
                                                    </svg>

                                                </div>

                                                <span class="text-sm text-[#5F6C7F]">
                                                    {{ $patient->phone }}
                                                </span>

                                            </div>

                                        @elseif($patient->email)

                                            <div class="flex items-center gap-2.5">

                                                <div class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-[#F2F6FA] text-[#8190A3]">

                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M3 8l9 5 9-5M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"
                                                        />
                                                    </svg>

                                                </div>

                                                <span class="max-w-[180px] truncate text-sm text-[#5F6C7F]">
                                                    {{ $patient->email }}
                                                </span>

                                            </div>

                                        @else

                                            <span class="text-sm text-[#A1AFBF]">
                                                No contact
                                            </span>

                                        @endif

                                    </td>


                                    {{-- GENDER --}}
                                    <td class="px-6 py-5">

                                        @if($patient->gender === 'male')

                                            <span class="inline-flex rounded-[11px] bg-[#EAF4FF] px-3 py-1.5 text-xs font-semibold text-[#5B9DF9]">
                                                Male
                                            </span>

                                        @elseif($patient->gender === 'female')

                                            <span class="inline-flex rounded-[11px] bg-[#FCEBF3] px-3 py-1.5 text-xs font-semibold text-[#D77FA7]">
                                                Female
                                            </span>

                                        @else

                                            <span class="text-sm text-[#A1AFBF]">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- STATUS --}}
                                    <td class="px-6 py-5">

                                        @if($patient->status === 'active')

                                            <span class="inline-flex items-center gap-2 rounded-full bg-[#EAF9F0] px-3 py-1.5 text-xs font-semibold text-[#43A96E]">

                                                <span class="h-1.5 w-1.5 rounded-full bg-[#43B978]"></span>

                                                Active

                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-2 rounded-full bg-[#F1F3F6] px-3 py-1.5 text-xs font-semibold text-[#7C8595]">

                                                <span class="h-1.5 w-1.5 rounded-full bg-[#9AA3B2]"></span>

                                                Inactive

                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTION --}}
                                    <td class="px-8 py-5">

                                        <div class="flex items-center justify-end gap-2">

                                            <a
                                                href="{{ route('clinic.patients.show', $patient) }}"
                                                class="inline-flex items-center justify-center rounded-[11px] border border-[#DCE6EE] bg-white px-3.5 py-2 text-xs font-semibold text-[#5F6C7F] transition duration-150 hover:border-[#C9E1FB] hover:bg-[#F4F9FE] hover:text-[#5B9DF9]"
                                            >
                                                View
                                            </a>


                                            @canDent('patients.update')

                                                <a
                                                    href="{{ route('clinic.patients.edit', $patient) }}"
                                                    class="inline-flex items-center justify-center rounded-[11px] bg-[#E8F3FF] px-3.5 py-2 text-xs font-semibold text-[#5B9DF9] transition duration-150 hover:bg-[#DDEEFF]"
                                                >
                                                    Edit
                                                </a>

                                            @endcanDent

                                        </div>

                                    </td>

                                </tr>


                            @empty

                                {{-- EMPTY --}}
                                <tr>

                                    <td colspan="6" class="px-8 py-24">

                                        <div class="mx-auto max-w-md text-center">

                                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[21px] bg-[#EEF4F8] text-[#94A3B8]">

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-7 w-7"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.6"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zm8 8a4 4 0 00-4-4m1-5a3 3 0 100-6"
                                                    />
                                                </svg>

                                            </div>


                                            <h3 class="mt-5 text-base font-semibold text-[#263550]">
                                                No patients found
                                            </h3>


                                            <p class="mt-2 text-sm leading-6 text-[#8A99AC]">

                                                @if($search)

                                                    No patients matched your search for

                                                    <span class="font-medium text-[#5F6C7F]">
                                                        "{{ $search }}"
                                                    </span>.

                                                @else

                                                    Your clinic does not have any registered patients yet.

                                                @endif

                                            </p>


                                            @if($search)

                                                <a
                                                    href="{{ route('clinic.patients.index') }}"
                                                    class="mt-5 inline-flex rounded-[11px] bg-[#EEF3F7] px-4 py-2.5 text-sm font-semibold text-[#5F6C7F] transition hover:bg-[#E5EBF0]"
                                                >
                                                    Clear Search
                                                </a>

                                            @elseif(auth()->user()->hasPermission('patients.create'))

                                                <a
                                                    href="{{ route('clinic.patients.create') }}"
                                                    class="mt-5 inline-flex rounded-[11px] bg-[#5B9DF9] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#4E91F0]"
                                                >
                                                    Add First Patient
                                                </a>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- PAGINATION --}}
                @if($patients->hasPages())

                    <div class="border-t border-[#E7EDF2] bg-[#FCFDFE] px-6 py-5 sm:px-8">

                        {{ $patients->links() }}

                    </div>

                @endif

            </section>


        </main>

    </div>

</x-app-layout>