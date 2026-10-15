<x-app-layout>

    @php
        $queueStatusStyles = [
            'waiting' => 'bg-[#FFF4E4] text-[#D99A3D]',
            'called' => 'bg-[#EAF4FF] text-[#5B9DF9]',
            'serving' => 'bg-[#F3EBFF] text-[#8B7CFF]',
            'completed' => 'bg-[#E9F9F0] text-[#43A96E]',
            'skipped' => 'bg-[#FFF1E6] text-[#E8833A]',
            'no_show' => 'bg-[#F1F3F6] text-[#7C8595]',
            'cancelled' => 'bg-[#FCEBF0] text-[#D97791]',
        ];

        $queueStatusLabels = [
            'waiting' => 'Waiting',
            'called' => 'Called',
            'serving' => 'In Service',
            'completed' => 'Completed',
            'skipped' => 'Skipped',
            'no_show' => 'No-show',
            'cancelled' => 'Cancelled',
        ];

        $liveTiles = [
            [
                'label' => 'Waiting',
                'value' => $queueSummary['waiting'],
                'tone' => 'bg-[#EAF4FF] text-[#5B9DF9]',
                'icon' => 'M12 7v5l3 2M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z',
            ],
            [
                'label' => 'Called',
                'value' => $queueSummary['called'],
                'tone' => 'bg-[#F7ECFF] text-[#9B72E9]',
                'icon' => 'M3 10v4h4l5 4V6l-5 4H3zM16 9a5 5 0 0 1 0 6',
            ],
            [
                'label' => 'In Service',
                'value' => $queueSummary['serving'],
                'tone' => 'bg-[#FFF1F5] text-[#D985AA]',
                'icon' => 'M5 13l4 4L19 7',
            ],
        ];
    @endphp

    <div class="mx-auto w-full max-w-[1550px]">

        {{-- =========================================================
             GREETING
        ========================================================== --}}

        <div class="mb-6 flex items-end justify-between gap-5">

            <div>

                <p class="mb-1 text-[13px] font-medium text-[#8C98AA]">
                    {{ $tenant->name }}
                </p>

                <h1 class="text-[30px] font-semibold tracking-[-0.04em] text-[#273047] sm:text-[34px]">
                    {{ $greeting }},
                    <span class="text-[#4E91EF]">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="inline-block">👋</span>
                </h1>

            </div>


            {{-- DATE --}}

            <div class="hidden rounded-[16px] border border-[#E9EEF4] bg-white px-4 py-2.5 shadow-[0_4px_18px_rgba(65,85,110,0.04)] sm:block">

                <p class="text-[11px] font-medium uppercase tracking-[0.12em] text-[#A0A9B8]">
                    Today
                </p>

                <p class="mt-0.5 text-sm font-semibold text-[#344057]">
                    {{ $today->format('d F Y') }}
                </p>

            </div>

        </div>


        {{-- =========================================================
             DASHBOARD GRID
        ========================================================== --}}

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1.75fr)_minmax(320px,0.72fr)]">


            {{-- =====================================================
                 LEFT COLUMN
            ====================================================== --}}

            <div class="space-y-5">


                {{-- =================================================
                     TODAY'S PATIENT VISITS
                ================================================== --}}

                <section
                    class="relative min-h-[285px] overflow-hidden rounded-[28px] border border-[#E6EDF4] bg-gradient-to-br from-[#F4FAFF] via-[#EEF7FF] to-[#F8FBFD] p-7 shadow-[0_8px_30px_rgba(66,95,125,0.05)]"
                >

                    {{-- Decorative circles --}}

                    <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-[#DDEEFF]/70 blur-2xl"></div>

                    <div class="pointer-events-none absolute -bottom-24 left-1/3 h-52 w-52 rounded-full bg-[#E8F5FF] blur-3xl"></div>


                    <div class="relative z-10 flex h-full flex-col justify-between">


                        <div>

                            <p class="text-[15px] font-medium text-[#344057]">
                                Today's Patient Visits
                            </p>

                            <div class="mt-1 flex items-end gap-2">

                                <span class="text-[58px] font-semibold leading-none tracking-[-0.06em] text-[#273047]">
                                    {{ $todayVisits }}
                                </span>

                                <span class="mb-2 text-xs text-[#8995A7]">
                                    /person
                                </span>

                            </div>

                        </div>


                        {{-- PATIENT TYPES --}}

                        <div class="mt-7 flex flex-wrap gap-3">


                            {{-- NEW PATIENTS --}}

                            <div
                                class="min-w-[155px] rounded-[18px] bg-[#6CA4F2] px-4 py-3.5 text-white shadow-[0_8px_20px_rgba(91,157,249,0.16)]"
                            >

                                <div class="text-xs font-medium text-white/90">
                                    New Patients
                                </div>

                                <div class="mt-1 flex items-end justify-between gap-3">

                                    <span class="text-[28px] font-semibold leading-none">
                                        {{ $newPatients }}
                                    </span>

                                    <span class="rounded-lg bg-white/20 px-2 py-1 text-[10px] font-semibold">
                                        Today
                                    </span>

                                </div>

                            </div>


                            {{-- RETURNING PATIENTS --}}

                            <div
                                class="min-w-[155px] rounded-[18px] bg-[#D985AA] px-4 py-3.5 text-white shadow-[0_8px_20px_rgba(217,133,170,0.14)]"
                            >

                                <div class="text-xs font-medium text-white/90">
                                    Returning Patients
                                </div>

                                <div class="mt-1 flex items-end justify-between gap-3">

                                    <span class="text-[28px] font-semibold leading-none">
                                        {{ $returningPatients }}
                                    </span>

                                    <span class="rounded-lg bg-white/20 px-2 py-1 text-[10px] font-semibold">
                                        Active
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- TOOTH ILLUSTRATION --}}

                    <div class="pointer-events-none absolute bottom-0 right-3 hidden h-[245px] w-[285px] items-center justify-center xl:flex">

                        <div class="relative flex h-[205px] w-[205px] items-center justify-center rounded-full border border-white/70 bg-white/35 shadow-[0_20px_50px_rgba(91,157,249,0.08)] backdrop-blur-sm">

                            {{-- Tooth --}}

                            <svg
                                viewBox="0 0 200 200"
                                class="h-[150px] w-[150px] drop-shadow-[0_15px_15px_rgba(75,95,120,0.16)]"
                            >

                                <defs>

                                    <linearGradient
                                        id="toothGradient"
                                        x1="0"
                                        y1="0"
                                        x2="1"
                                        y2="1"
                                    >

                                        <stop
                                            offset="0%"
                                            stop-color="#FFFFFF"
                                        />

                                        <stop
                                            offset="100%"
                                            stop-color="#DCE7EF"
                                        />

                                    </linearGradient>

                                </defs>


                                <path
                                    d="M100 21
                                       C79 7 56 14 48 34
                                       C40 54 47 70 51 88
                                       C55 107 52 135 62 157
                                       C68 171 79 166 85 149
                                       L95 119
                                       C98 110 102 110 105 119
                                       L115 149
                                       C121 166 132 171 138 157
                                       C148 135 145 107 149 88
                                       C153 70 160 54 152 34
                                       C144 14 121 7 100 21Z"
                                    fill="url(#toothGradient)"
                                    stroke="#C7D6E1"
                                    stroke-width="3"
                                />

                                <path
                                    d="M70 44 C78 33 89 32 100 40 C111 32 122 33 130 44"
                                    fill="none"
                                    stroke="#EEF5F9"
                                    stroke-width="8"
                                    stroke-linecap="round"
                                />

                            </svg>


                            {{-- Small dental tools --}}

                            <div class="absolute -left-8 top-8 h-20 w-[2px] rotate-[35deg] rounded-full bg-[#AFC2D2]"></div>

                            <div class="absolute -right-7 top-7 h-16 w-[2px] -rotate-[32deg] rounded-full bg-[#AFC2D2]"></div>

                            <div class="absolute -bottom-2 left-8 h-16 w-[2px] rotate-[65deg] rounded-full bg-[#B8C9D6]"></div>

                            <div class="absolute -bottom-2 right-8 h-16 w-[2px] -rotate-[65deg] rounded-full bg-[#B8C9D6]"></div>

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     PATIENT LIST + CONSULTATION
                ================================================== --}}

                <section
                    class="rounded-[28px] border border-[#D9E3EC] bg-[#EEF4F8] p-5 shadow-[0_10px_30px_rgba(66,95,125,0.08)] sm:p-6"
                >

                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(300px,0.82fr)]">


                        {{-- TODAY'S QUEUE --}}

                        <div>

                            <div class="mb-4 flex items-center justify-between">

                                <h2 class="text-[17px] font-semibold text-[#273047]">
                                    Today's Queue
                                </h2>

                                @if ($canViewQueue)
                                    <a
                                        href="{{ route('clinic.queues.index') }}"
                                        class="flex items-center gap-2 rounded-xl border border-[#E9EEF4] bg-[#FAFBFD] px-3 py-2 text-xs font-medium text-[#59657A] transition hover:bg-white"
                                    >
                                        View queue
                                    </a>
                                @endif

                            </div>


                            <div class="rounded-[22px] border border-[#D6E2EC] bg-[#F7FAFC] p-3 shadow-[inset_0_1px_0_rgba(255,255,255,0.8)]">


                                @forelse($queueToday as $ticket)

                                    @php
                                        $ticketPatient = $ticket->patient;
                                        $ticketName = $ticketPatient->full_name ?? 'Unknown patient';
                                    @endphp

                                    <a
                                        href="{{ route('clinic.queues.show', $ticket) }}"
                                        class="group mb-2 flex items-center justify-between gap-3 rounded-[15px] border border-[#E1E9F0] bg-white px-3 py-3 shadow-[0_3px_10px_rgba(55,80,105,0.035)] transition hover:border-[#A8CEF5] hover:shadow-[0_5px_15px_rgba(91,157,249,0.10)] last:mb-0"
                                    >

                                        <div class="flex min-w-0 items-center gap-3">

                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EAF4FF] text-xs font-semibold text-[#5B9DF9]"
                                            >
                                                {{ strtoupper(substr($ticketPatient->first_name ?? '?', 0, 1)) }}
                                            </div>


                                            <div class="min-w-0">

                                                <p class="truncate text-sm font-semibold text-[#344057]">
                                                    {{ $ticketName }}
                                                </p>

                                                <p class="mt-0.5 truncate text-[10px] text-[#91A0B2]">
                                                    {{ $ticket->ticket_number }} · {{ $ticketPatient->patient_number ?? '—' }}
                                                </p>

                                            </div>

                                        </div>


                                        <div class="flex shrink-0 items-center gap-2">

                                            <span
                                                class="hidden rounded-full px-2 py-1 text-[9px] font-semibold sm:inline-flex {{ $queueStatusStyles[$ticket->status] ?? 'bg-[#F1F3F6] text-[#7C8595]' }}"
                                            >
                                                {{ $queueStatusLabels[$ticket->status] ?? ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>

                                            <span
                                                class="rounded-lg bg-[#29354A] px-2.5 py-1.5 text-[10px] font-semibold text-white"
                                            >
                                                View
                                            </span>

                                        </div>

                                    </a>

                                @empty

                                    <div class="flex min-h-[220px] flex-col items-center justify-center rounded-[17px] border border-[#D9E5EE] bg-white px-4 text-center">

                                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[#EAF4FF] text-[#5B9DF9]">

                                            <svg
                                                class="h-5 w-5"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                                viewBox="0 0 24 24"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20"
                                                />

                                                <circle
                                                    cx="10"
                                                    cy="7"
                                                    r="3"
                                                />

                                            </svg>

                                        </div>

                                        <p class="text-sm font-semibold text-[#344057]">
                                            No patients in the queue today
                                        </p>

                                        <p class="mt-1 text-xs text-[#96A1B1]">
                                            @if ($canViewQueue)
                                                Patients who arrive today will appear here.
                                            @else
                                                You do not have access to the queue.
                                            @endif
                                        </p>

                                    </div>

                                @endforelse


                            </div>

                        </div>


                        {{-- LIVE QUEUE --}}

                        <div>

                            <div class="mb-4 flex items-center justify-between">

                                <h2 class="text-[17px] font-semibold text-[#273047]">
                                    Live Queue
                                </h2>

                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full border border-[#E6EBF1] text-[#758196]"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="8"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            d="M9.5 9.5h.01M14.5 9.5h.01M8.5 14c1.8 1.7 5.2 1.7 7 0"
                                        />
                                    </svg>
                                </span>

                            </div>


                            <div class="rounded-[22px] border border-[#D6E2EC] bg-[#F7FAFC] p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.8)]">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#C9DFF5] bg-[#DCEEFF] text-[11px] font-bold text-[#4E91EF]">
                                        {{ $nowServing?->ticket_number ?? '—' }}
                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-[#344057]">
                                            Today's Queue
                                        </p>

                                        <p class="mt-0.5 text-[10px] text-[#96A1B1]">
                                            Live status
                                        </p>

                                    </div>

                                </div>


                                <div class="mt-5 grid grid-cols-3 gap-2">

                                    @foreach($liveTiles as $tile)

                                        <div class="rounded-[15px] border border-[#E6EDF3] bg-white p-3 text-center shadow-[0_3px_10px_rgba(55,80,105,0.03)]">

                                            <div class="mx-auto flex h-8 w-8 items-center justify-center rounded-lg {{ $tile['tone'] }}">
                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="{{ $tile['icon'] }}"
                                                    />
                                                </svg>
                                            </div>

                                            <p class="mt-2 text-lg font-semibold leading-none text-[#273047]">
                                                {{ $tile['value'] }}
                                            </p>

                                            <p class="mt-1.5 text-[10px] text-[#8995A7]">
                                                {{ $tile['label'] }}
                                            </p>

                                        </div>

                                    @endforeach

                                </div>


                                <div class="mt-5 border-t border-dashed border-[#DDE4EC] pt-4">

                                    <div class="flex items-center justify-between gap-3">

                                        <span class="text-[10px] text-[#8D99AA]">
                                            Now serving
                                        </span>

                                        @if ($nowServing)
                                            <span class="flex min-w-0 items-center gap-1.5 text-[10px] font-semibold text-[#8B7CFF]">
                                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-[#8B7CFF]"></span>
                                                <span class="truncate">
                                                    {{ $nowServing->ticket_number }}
                                                    @if ($nowServing->patient)
                                                        · {{ $nowServing->patient->full_name }}
                                                    @endif
                                                </span>
                                            </span>
                                        @else
                                            <span class="flex items-center gap-1.5 text-[10px] font-semibold text-[#9AA5B4]">
                                                <span class="h-1.5 w-1.5 rounded-full bg-[#C4CCD8]"></span>
                                                No one in service
                                            </span>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </section>

            </div>


            {{-- =====================================================
                 RIGHT COLUMN
            ====================================================== --}}

            <div class="space-y-5">


                {{-- =================================================
                     YOUR SCHEDULE
                ================================================== --}}

                <section
                    class="rounded-[28px] border border-[#E6EDF4] bg-white p-6 shadow-[0_8px_30px_rgba(66,95,125,0.045)]"
                >

                    <div class="flex items-center justify-between">

                        <h2 class="text-[17px] font-semibold text-[#273047]">
                            Your Schedule
                        </h2>

                        <div class="flex gap-1">

                            <a
                                href="{{ route('dashboard', ['month' => $calendar['prev']]) }}"
                                aria-label="Previous month"
                                class="flex h-7 w-7 items-center justify-center rounded-lg text-[#8995A7] hover:bg-[#F4F7FA]"
                            >
                                ‹
                            </a>

                            <a
                                href="{{ route('dashboard', ['month' => $calendar['next']]) }}"
                                aria-label="Next month"
                                class="flex h-7 w-7 items-center justify-center rounded-lg text-[#8995A7] hover:bg-[#F4F7FA]"
                            >
                                ›
                            </a>

                        </div>

                    </div>


                    <p class="mt-2 text-xs text-[#8995A7]">
                        {{ $calendar['label'] }}
                    </p>


                    {{-- CALENDAR --}}

                    <div class="mt-5">

                        <div class="grid grid-cols-7 gap-1 text-center">

                            @foreach(['SUN','MON','TUE','WED','THU','FRI','SAT'] as $day)
                                <span class="py-1 text-[8px] font-semibold tracking-wide text-[#A1AAB8]">
                                    {{ $day }}
                                </span>
                            @endforeach

                            @for($i = 0; $i < $calendar['firstDay']; $i++)
                                <span class="h-8"></span>
                            @endfor

                            @for($day = 1; $day <= $calendar['daysInMonth']; $day++)

                                @php
                                    $isToday = $day === $calendar['todayDay'];
                                    $busyCount = $calendar['busy'][$day] ?? 0;
                                    $dayClass = $isToday
                                        ? 'bg-[#5B9DF9] text-white shadow-[0_4px_10px_rgba(91,157,249,0.22)]'
                                        : 'text-[#4F5A6E] hover:bg-[#F3F7FB]';
                                    $dotClass = $isToday ? 'bg-white' : 'bg-[#5B9DF9]';
                                @endphp

                                <div class="flex h-8 items-center justify-center">

                                    @if ($canViewAppointments)
                                        <a
                                            href="{{ route('clinic.appointments.index', ['date' => $calendar['month'] . '-' . str_pad($day, 2, '0', STR_PAD_LEFT)]) }}"
                                            @if ($busyCount > 0) title="{{ $busyCount }} {{ \Illuminate\Support\Str::plural('appointment', $busyCount) }}" @endif
                                            class="relative flex h-7 w-7 items-center justify-center rounded-lg text-[10px] font-medium {{ $dayClass }}"
                                        >
                                            {{ $day }}

                                            @if ($busyCount > 0)
                                                <span class="absolute bottom-0.5 h-1 w-1 rounded-full {{ $dotClass }}"></span>
                                            @endif
                                        </a>
                                    @else
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg text-[10px] font-medium {{ $dayClass }}">
                                            {{ $day }}
                                        </span>
                                    @endif

                                </div>

                            @endfor

                        </div>

                    </div>


                    {{-- UPCOMING --}}

                    <div class="mt-5 border-t border-[#EEF1F5] pt-5">

                        <div class="mb-3 flex items-center justify-between">

                            <h3 class="text-[16px] font-semibold text-[#273047]">
                                Upcoming
                            </h3>

                            @if ($canViewAppointments)
                                <a
                                    href="{{ route('clinic.appointments.index') }}"
                                    class="text-[10px] font-medium text-[#4E91EF] hover:underline"
                                >
                                    View All
                                </a>
                            @endif

                        </div>


                        @forelse($upcoming as $item)

                            <a
                                href="{{ $item['url'] }}"
                                class="mb-2.5 flex items-center gap-3 rounded-[16px] bg-[#EDF6FF] px-3 py-3 transition hover:bg-[#E3F1FF] last:mb-0"
                            >

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-[#5B9DF9]">
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        viewBox="0 0 24 24"
                                    >
                                        <rect
                                            x="4"
                                            y="5"
                                            width="16"
                                            height="15"
                                            rx="3"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            d="M8 3v4M16 3v4M4 10h16"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="truncate text-[11px] font-semibold text-[#344057]">
                                        {{ $item['title'] }}
                                    </p>

                                    <p class="mt-0.5 truncate text-[9px] text-[#8A98AA]">
                                        {{ $item['meta'] }}
                                    </p>

                                </div>

                            </a>

                        @empty

                            <p class="rounded-[16px] bg-[#F7FAFC] px-3 py-4 text-center text-[11px] text-[#8A98AA]">
                                @if ($canViewAppointments)
                                    No upcoming appointments.
                                @else
                                    You do not have access to appointments.
                                @endif
                            </p>

                        @endforelse

                    </div>

                </section>


                {{-- =================================================
                     DENTIST NOTES
                ================================================== --}}

                <section
                    class="relative overflow-hidden rounded-[28px] border border-[#E6EDF4] bg-white p-6 shadow-[0_8px_30px_rgba(66,95,125,0.045)]"
                >

                    <div class="flex items-center justify-between">

                        <h2 class="text-[17px] font-semibold text-[#273047]">
                            Today's Reminders
                        </h2>

                        @if ($canViewQueue)
                            <a
                                href="{{ route('clinic.queues.index') }}"
                                class="rounded-xl border border-[#E6EDF4] bg-white px-3 py-1.5 text-[10px] font-semibold text-[#59657A] transition hover:bg-[#F7FAFC]"
                            >
                                Open queue
                            </a>
                        @endif

                    </div>


                    {{-- DENTAL ILLUSTRATION --}}

                    <div class="relative mt-5 h-[130px] overflow-hidden rounded-[20px] bg-gradient-to-br from-[#EEF8FF] to-[#F8FBFD]">

                        <div class="absolute -right-5 -top-5 h-32 w-32 rounded-full bg-[#DDEEFF] blur-2xl"></div>


                        <div class="absolute bottom-[-18px] right-6">

                            <svg
                                width="120"
                                height="120"
                                viewBox="0 0 120 120"
                                fill="none"
                            >

                                {{-- Tooth body --}}

                                <path
                                    d="M60 17
                                       C47 8 34 12 30 25
                                       C26 38 31 48 33 60
                                       C35 72 34 90 41 99
                                       C46 106 52 101 55 91
                                       L59 74
                                       C60 70 60 70 61 74
                                       L65 91
                                       C68 101 74 106 79 99
                                       C86 90 85 72 87 60
                                       C89 48 94 38 90 25
                                       C86 12 73 8 60 17Z"
                                    fill="#FFFFFF"
                                    stroke="#BCD0DD"
                                    stroke-width="2"
                                />

                                {{-- Face --}}

                                <circle
                                    cx="50"
                                    cy="48"
                                    r="2"
                                    fill="#6B8295"
                                />

                                <circle
                                    cx="70"
                                    cy="48"
                                    r="2"
                                    fill="#6B8295"
                                />

                                <path
                                    d="M53 57c4 4 10 4 14 0"
                                    stroke="#6B8295"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />

                                {{-- Tooth brush --}}

                                <path
                                    d="M15 91 38 68"
                                    stroke="#6EA9EE"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                />

                                <path
                                    d="m12 94 6 6"
                                    stroke="#D58AAD"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                />

                            </svg>

                        </div>


                        {{-- NOTE CARD --}}

                        <div class="absolute bottom-4 left-4 max-w-[180px]">

                            <div class="rounded-xl bg-white/85 px-3 py-2 shadow-sm backdrop-blur">

                                <p class="text-[9px] font-semibold text-[#344057]">
                                    Today's overview
                                </p>

                                <p class="mt-1 text-[8px] leading-relaxed text-[#8B97A8]">
                                    {{ $todayVisits }} {{ \Illuminate\Support\Str::plural('patient', $todayVisits) }} visiting today.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- REMINDERS LIST --}}

                    <div class="mt-4 space-y-2">

                        @forelse($reminders as $reminder)

                            <a
                                href="{{ $reminder['url'] }}"
                                class="flex items-center gap-3 rounded-[14px] bg-[#F9FBFD] px-3 py-2.5 transition hover:bg-[#F1F6FB]"
                            >

                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#EAF4FF] text-[#5B9DF9]">
                                    <svg
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            d="M8 9h8M8 13h6"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-[10px] font-semibold text-[#4A566B]">
                                        {{ $reminder['title'] }}
                                    </p>

                                    <p class="mt-0.5 text-[8px] text-[#9AA5B4]">
                                        {{ $reminder['meta'] }}
                                    </p>

                                </div>

                            </a>

                        @empty

                            <p class="rounded-[14px] bg-[#F9FBFD] px-3 py-4 text-center text-[10px] text-[#8A98AA]">
                                All clear. Nothing needs attention right now.
                            </p>

                        @endforelse

                    </div>

                </section>

            </div>

        </div>

    </div>

</x-app-layout>