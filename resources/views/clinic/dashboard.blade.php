<x-app-layout>

    <div class="space-y-8">

        {{-- HEADER --}}
        <div class="flex items-end justify-between">

            <div>
                <p class="text-sm font-medium text-[#8A94A6]">
                    Good Morning 👋
                </p>

                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#273047]">
                    Here's what's happening today.
                </h1>

                <p class="mt-2 text-sm text-[#9AA3B3]">
                    {{ app(\App\Support\TenantContext::class)->get()?->name ?? 'Dental Clinic' }}
                </p>
            </div>

            <div class="hidden text-right sm:block">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#A0A8B6]">
                    {{ now()->format('l') }}
                </p>

                <p class="mt-1 text-sm font-semibold text-[#59657A]">
                    {{ now()->format('d F Y') }}
                </p>
            </div>

        </div>


        {{-- STAT CARDS --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-[#EEF1F5]">

                <p class="text-sm font-medium text-[#8993A5]">
                    Today's Patients
                </p>

                <div class="mt-4 flex items-end justify-between">

                    <p class="text-4xl font-semibold text-[#273047]">
                        42
                    </p>

                    <span class="rounded-full bg-[#EAF5FF] px-3 py-1 text-xs font-semibold text-[#5B9DF9]">
                        +12%
                    </span>

                </div>

            </div>


            <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-[#EEF1F5]">

                <p class="text-sm font-medium text-[#8993A5]">
                    Appointments
                </p>

                <div class="mt-4 flex items-end justify-between">

                    <p class="text-4xl font-semibold text-[#273047]">
                        18
                    </p>

                    <span class="rounded-full bg-[#EEF9F3] px-3 py-1 text-xs font-semibold text-[#54A77A]">
                        Today
                    </span>

                </div>

            </div>


            <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-[#EEF1F5]">

                <p class="text-sm font-medium text-[#8993A5]">
                    Waiting Queue
                </p>

                <div class="mt-4 flex items-end justify-between">

                    <p class="text-4xl font-semibold text-[#273047]">
                        07
                    </p>

                    <span class="rounded-full bg-[#FFF5E7] px-3 py-1 text-xs font-semibold text-[#C79546]">
                        Waiting
                    </span>

                </div>

            </div>


            <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-[#EEF1F5]">

                <p class="text-sm font-medium text-[#8993A5]">
                    Completed
                </p>

                <div class="mt-4 flex items-end justify-between">

                    <p class="text-4xl font-semibold text-[#273047]">
                        24
                    </p>

                    <span class="rounded-full bg-[#F4EEFF] px-3 py-1 text-xs font-semibold text-[#8B70C7]">
                        57%
                    </span>

                </div>

            </div>

        </div>


        {{-- MAIN CONTENT --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1.4fr_0.8fr]">


            {{-- PATIENTS --}}
            <div class="rounded-[30px] bg-white p-6 shadow-sm ring-1 ring-[#EEF1F5]">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-[#30394D]">
                            Today's Patients
                        </h2>

                        <p class="mt-1 text-xs text-[#9AA3B3]">
                            Patients scheduled for today
                        </p>
                    </div>

                    <button class="rounded-xl bg-[#F3F8FD] px-4 py-2 text-xs font-semibold text-[#5B9DF9]">
                        View all
                    </button>

                </div>


                <div class="mt-6 space-y-2">

                    @foreach([
                        ['name' => 'Alya Putri', 'service' => 'Dental Checkup', 'time' => '08:00', 'status' => 'Serving'],
                        ['name' => 'John Anderson', 'service' => 'Teeth Cleaning', 'time' => '09:30', 'status' => 'Waiting'],
                        ['name' => 'Sarah Wilson', 'service' => 'Whitening', 'time' => '11:00', 'status' => 'Waiting'],
                        ['name' => 'Michael Lee', 'service' => 'Consultation', 'time' => '13:30', 'status' => 'Scheduled'],
                    ] as $patient)

                        <div class="flex items-center gap-4 rounded-[20px] p-3 transition hover:bg-[#F8FAFC]">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#EAF4FF] text-sm font-bold text-[#5B9DF9]">
                                {{ strtoupper(substr($patient['name'], 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">

                                <p class="text-sm font-semibold text-[#3B455A]">
                                    {{ $patient['name'] }}
                                </p>

                                <p class="mt-1 text-xs text-[#9AA3B3]">
                                    {{ $patient['service'] }}
                                </p>

                            </div>

                            <div class="hidden text-right sm:block">

                                <p class="text-xs font-semibold text-[#59657A]">
                                    {{ $patient['time'] }}
                                </p>

                                <p class="mt-1 text-[10px] text-[#9AA3B3]">
                                    {{ $patient['status'] }}
                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- RIGHT SIDE --}}
            <div class="space-y-5">


                {{-- QUEUE --}}
                <div class="rounded-[30px] bg-[#EAF5FF] p-6">

                    <div class="flex items-start justify-between">

                        <div>

                            <p class="text-sm font-medium text-[#66809D]">
                                Current Queue
                            </p>

                            <p class="mt-2 text-5xl font-semibold tracking-tight text-[#273047]">
                                A-07
                            </p>

                            <p class="mt-2 text-xs text-[#8292A5]">
                                7 patients waiting
                            </p>

                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-[#5B9DF9] shadow-sm">

                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" d="M7 6h10M7 12h10M7 18h6"/>
                            </svg>

                        </div>

                    </div>

                    <button class="mt-6 w-full rounded-2xl bg-[#5B9DF9] py-3 text-sm font-semibold text-white shadow-sm">
                        Manage Queue
                    </button>

                </div>


                {{-- SCHEDULE --}}
                <div class="rounded-[30px] bg-white p-6 shadow-sm ring-1 ring-[#EEF1F5]">

                    <div class="flex items-center justify-between">

                        <div>
                            <h2 class="text-lg font-semibold text-[#30394D]">
                                Upcoming
                            </h2>

                            <p class="mt-1 text-xs text-[#9AA3B3]">
                                Next appointments
                            </p>
                        </div>

                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F4F7FA] text-[#8993A5]">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <rect x="4" y="5" width="16" height="15" rx="3"/>
                                <path stroke-linecap="round" d="M8 3v4M16 3v4M4 10h16"/>
                            </svg>

                        </div>

                    </div>


                    <div class="mt-5 space-y-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#EAF4FF] text-xs font-bold text-[#5B9DF9]">
                                10
                            </div>

                            <div class="flex-1">

                                <p class="text-xs font-semibold text-[#4A556C]">
                                    Dental Checkup
                                </p>

                                <p class="mt-1 text-[10px] text-[#9AA3B3]">
                                    John Anderson
                                </p>

                            </div>

                            <span class="text-[10px] font-semibold text-[#8993A5]">
                                10:00
                            </span>

                        </div>


                        <div class="flex items-center gap-3">

                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#FFF0F5] text-xs font-bold text-[#C77A9C]">
                                11
                            </div>

                            <div class="flex-1">

                                <p class="text-xs font-semibold text-[#4A556C]">
                                    Whitening
                                </p>

                                <p class="mt-1 text-[10px] text-[#9AA3B3]">
                                    Sarah Wilson
                                </p>

                            </div>

                            <span class="text-[10px] font-semibold text-[#8993A5]">
                                11:00
                            </span>

                        </div>


                        <div class="flex items-center gap-3">

                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#F4EEFF] text-xs font-bold text-[#8B70C7]">
                                12
                            </div>

                            <div class="flex-1">

                                <p class="text-xs font-semibold text-[#4A556C]">
                                    Consultation
                                </p>

                                <p class="mt-1 text-[10px] text-[#9AA3B3]">
                                    Michael Lee
                                </p>

                            </div>

                            <span class="text-[10px] font-semibold text-[#8993A5]">
                                13:30
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- BOTTOM --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">


            <div class="rounded-[30px] bg-white p-6 shadow-sm ring-1 ring-[#EEF1F5]">

                <p class="text-sm font-medium text-[#8993A5]">
                    Today's Revenue
                </p>

                <p class="mt-3 text-3xl font-semibold text-[#273047]">
                    Rp 8.450.000
                </p>

                <div class="mt-4 h-2 overflow-hidden rounded-full bg-[#EEF2F6]">

                    <div class="h-full w-[72%] rounded-full bg-[#6EA9F5]"></div>

                </div>

                <p class="mt-2 text-[10px] text-[#9AA3B3]">
                    72% of daily target
                </p>

            </div>


            <div class="rounded-[30px] bg-white p-6 shadow-sm ring-1 ring-[#EEF1F5]">

                <p class="text-sm font-medium text-[#8993A5]">
                    Active Doctors
                </p>

                <p class="mt-3 text-3xl font-semibold text-[#273047]">
                    4
                </p>

                <p class="mt-2 text-[10px] text-[#54A77A]">
                    All doctors are available today
                </p>

            </div>


            <div class="rounded-[30px] bg-gradient-to-br from-[#F8F1FF] to-[#EEF6FF] p-6">

                <p class="text-sm font-medium text-[#7E7195]">
                    Clinic Status
                </p>

                <div class="mt-4 flex items-center gap-3">

                    <span class="h-3 w-3 rounded-full bg-[#58B47E]"></span>

                    <span class="text-lg font-semibold text-[#3B455A]">
                        Clinic is Open
                    </span>

                </div>

                <p class="mt-2 text-xs text-[#9AA3B3]">
                    Bogor Main Branch · {{ now()->format('H:i') }}
                </p>

            </div>

        </div>

    </div>

</x-app-layout>