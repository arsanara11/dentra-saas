<x-app-layout>
    @php
        $statusStyles = [
            'waiting' => 'bg-[#FFF4E4] text-[#D99A3D]',
            'called' => 'bg-[#EAF4FF] text-[#5B9DF9]',
            'serving' => 'bg-[#F3EBFF] text-[#8B7CFF]',
            'completed' => 'bg-[#E9F9F0] text-[#43A96E]',
            'cancelled' => 'bg-[#FCEBF0] text-[#D97791]',
        ];

        $statusLabels = [
            'waiting' => 'Waiting',
            'called' => 'Called',
            'serving' => 'In Service',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $queueTotal = $summary->total ?? 0;
        $waitingTotal = $summary->waiting ?? 0;
        $calledTotal = $summary->called ?? 0;
        $servingTotal = $summary->serving ?? 0;
    @endphp

    <div class="min-h-screen bg-[#EEF4F8]">
        <main class="w-full max-w-none space-y-4 px-0 py-0">

            {{-- HERO --}}
            <section class="relative overflow-hidden rounded-[26px] border border-white bg-white shadow-[0_6px_24px_rgba(52,75,98,0.04)]">
                <div class="pointer-events-none absolute inset-y-0 right-0 w-[24%] overflow-hidden">
                    <div class="absolute -right-20 -top-32 h-[290px] w-[290px] rounded-full bg-[#E8F4FF]/80"></div>
                    <div class="absolute -right-5 top-7 h-[180px] w-[180px] rounded-full bg-[#F2F8FF]"></div>
                    <div class="absolute -bottom-28 right-[35%] h-[190px] w-[190px] rounded-full bg-[#EDF7FF]/70"></div>
                </div>

                <div class="relative z-10 flex flex-col gap-5 px-5 py-5 sm:px-7 sm:py-6 lg:flex-row lg:items-center lg:justify-between lg:gap-8">
                    <div class="min-w-0">
                        <nav class="flex items-center gap-2 text-[13px] font-medium">
                            <a href="{{ route('dashboard') }}"
                               class="text-[#94A3B8] transition hover:text-[#5B9DF9]">
                                Clinic
                            </a>

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-3.5 w-3.5 text-[#B7C1CE]"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                            </svg>

                            <span class="text-[#5B9DF9]">Queue Management</span>
                        </nav>

                        <p class="mt-4 text-[13px] font-medium text-[#8090A5]">
                            {{ $tenant->name }}
                        </p>

                        <h1 class="mt-1 text-[32px] font-semibold leading-tight tracking-[-0.045em] text-[#25324A] sm:text-[38px]">
                            Queue Management
                        </h1>

                        <p class="mt-2 max-w-[560px] text-sm leading-6 text-[#8A99AC]">
                            Manage patient arrivals, call the next patient, and monitor the treatment queue.
                        </p>
                    </div>

                    @canDent('queues.create')
                        <div class="relative z-10 shrink-0">
                            <a href="#add-queue"
                               class="inline-flex items-center justify-center gap-2 rounded-[14px] bg-[#5B9DF9] px-5 py-3 text-sm font-semibold text-white shadow-[0_6px_16px_rgba(91,157,249,0.20)] transition hover:bg-[#4E91F0]">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-[18px] w-[18px]" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                </svg>
                                Add to Queue
                            </a>
                        </div>
                    @endcanDent
                </div>
            </section>

            {{-- FLASH MESSAGES --}}
            @if (session('success'))
                <div class="rounded-2xl border border-[#CDEEDB] bg-white px-5 py-3.5 text-sm font-medium text-[#319565] shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-[#F4D5DC] bg-white px-5 py-3.5 text-sm text-[#C45F78] shadow-sm">
                    <p class="mb-2 font-semibold">Please check the following:</p>
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- STATISTICS --}}
            <section>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

                    {{-- TOTAL --}}
                    <div class="rounded-[22px] border border-[#DCE7F0] bg-white p-5 shadow-[0_5px_20px_rgba(52,75,98,0.035)] transition hover:shadow-[0_9px_25px_rgba(52,75,98,0.06)]">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[13px] font-medium text-[#8493A7]">Total Queue</p>
                                <p class="mt-2 text-[30px] font-semibold leading-none tracking-[-0.045em] text-[#263550]">
                                    {{ $queueTotal }}
                                </p>
                                <p class="mt-2 text-xs text-[#A0ACBA]">For selected date</p>
                            </div>
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] bg-[#E8F3FF] text-[#5B9DF9]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- WAITING --}}
                    <div class="rounded-[22px] border border-[#DCE7F0] bg-white p-5 shadow-[0_5px_20px_rgba(52,75,98,0.035)] transition hover:shadow-[0_9px_25px_rgba(52,75,98,0.06)]">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[13px] font-medium text-[#8493A7]">Waiting</p>
                                <p class="mt-2 text-[30px] font-semibold leading-none tracking-[-0.045em] text-[#263550]">
                                    {{ $waitingTotal }}
                                </p>
                                <p class="mt-2 text-xs text-[#A0ACBA]">Patients in line</p>
                            </div>
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] bg-[#FFF4E4] text-[#D99A3D]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- CALLED --}}
                    <div class="rounded-[22px] border border-[#DCE7F0] bg-white p-5 shadow-[0_5px_20px_rgba(52,75,98,0.035)] transition hover:shadow-[0_9px_25px_rgba(52,75,98,0.06)]">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[13px] font-medium text-[#8493A7]">Called</p>
                                <p class="mt-2 text-[30px] font-semibold leading-none tracking-[-0.045em] text-[#263550]">
                                    {{ $calledTotal }}
                                </p>
                                <p class="mt-2 text-xs text-[#A0ACBA]">Awaiting response</p>
                            </div>
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] bg-[#E8F3FF] text-[#5B9DF9]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10v4h4l5 4V6l-5 4H3z"/>
                                    <path stroke-linecap="round" d="M16 9a5 5 0 0 1 0 6m2-9a9 9 0 0 1 0 12"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- IN SERVICE --}}
                    <div class="rounded-[22px] border border-[#DCE7F0] bg-white p-5 shadow-[0_5px_20px_rgba(52,75,98,0.035)] transition hover:shadow-[0_9px_25px_rgba(52,75,98,0.06)]">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[13px] font-medium text-[#8493A7]">In Service</p>
                                <p class="mt-2 text-[30px] font-semibold leading-none tracking-[-0.045em] text-[#263550]">
                                    {{ $servingTotal }}
                                </p>
                                <p class="mt-2 text-xs text-[#A0ACBA]">Currently being treated</p>
                            </div>
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] bg-[#F3EBFF] text-[#8B7CFF]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12m-8.48 8.48-2.12 2.12"/>
                                    <circle cx="12" cy="12" r="4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ADD QUEUE FORM --}}
            @canDent('queues.create')
                <section id="add-queue" class="scroll-mt-5 overflow-hidden rounded-[24px] border border-[#D8E4ED] bg-white shadow-[0_6px_24px_rgba(52,75,98,0.035)]">
                    <div class="flex items-center gap-3 border-b border-[#E9EFF4] px-5 py-4 sm:px-6">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[12px] bg-[#E8F3FF] text-[#5B9DF9]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold tracking-[-0.025em] text-[#263550]">
                                Register Patient
                            </h2>
                            <p class="mt-0.5 text-xs text-[#8A99AC]">
                                Add a patient to the queue.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('clinic.queues.store') }}" class="p-5 sm:p-6">
                        @csrf

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <div>
                                <label for="patient_id" class="mb-1.5 block text-[13px] font-semibold text-[#5F6C7F]">
                                    Patient <span class="text-[#D97791]">*</span>
                                </label>
                                <select id="patient_id" name="patient_id" required
                                        class="w-full rounded-[12px] border border-[#DCE6EE] bg-[#F8FAFC] px-3.5 py-3 text-sm text-[#263550] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]">
                                    <option value="">Select a patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}" @selected((string) old('patient_id') === (string) $patient->id)>
                                            {{ trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '')) }}
                                            — {{ $patient->patient_number ?? 'No patient number' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($patients->isEmpty())
                                    <p class="mt-1.5 text-xs text-[#D97791]">
                                        No active patients found. Register a patient first.
                                    </p>
                                @endif
                            </div>

                            <div>
                                <label for="branch_id" class="mb-1.5 block text-[13px] font-semibold text-[#5F6C7F]">
                                    Branch
                                </label>
                                <select id="branch_id" name="branch_id"
                                        class="w-full rounded-[12px] border border-[#DCE6EE] bg-[#F8FAFC] px-3.5 py-3 text-sm text-[#263550] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]">
                                    <option value="">Main branch / No branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="queue_date" class="mb-1.5 block text-[13px] font-semibold text-[#5F6C7F]">
                                    Queue Date <span class="text-[#D97791]">*</span>
                                </label>
                                <input id="queue_date" type="date" name="queue_date"
                                       value="{{ old('queue_date', $date) }}" required
                                       class="w-full rounded-[12px] border border-[#DCE6EE] bg-[#F8FAFC] px-3.5 py-3 text-sm text-[#263550] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]">
                            </div>

                            <div class="flex items-end">
                                <button type="submit" @disabled($patients->isEmpty())
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-[12px] bg-[#5B9DF9] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#4E91F0] disabled:cursor-not-allowed disabled:opacity-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                    </svg>
                                    Add to Queue
                                </button>
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-[#9BA8B7]">
                            Queue numbers are assigned automatically for each date within the clinic.
                        </p>
                    </form>
                </section>
            @endcanDent

            {{-- QUEUE DIRECTORY --}}
            <section class="overflow-hidden rounded-[26px] border border-[#D8E4ED] bg-white shadow-[0_6px_24px_rgba(52,75,98,0.04)]">

                {{-- DIRECTORY HEADER --}}
                <div class="flex flex-col gap-4 px-5 py-5 xl:flex-row xl:items-center xl:justify-between sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[13px] bg-[#E8F3FF] text-[#5B9DF9]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold tracking-[-0.025em] text-[#263550]">
                                Queue Directory
                            </h2>
                            <p class="mt-0.5 text-[13px] text-[#8A99AC]">
                                Track patient positions and manage service flow.
                            </p>
                        </div>
                    </div>

                    {{-- FILTERS --}}
                    <form method="GET" action="{{ route('clinic.queues.index') }}"
                          class="grid w-full grid-cols-1 gap-2.5 sm:grid-cols-2 xl:w-auto xl:grid-cols-4">

                        <div class="relative sm:col-span-2 xl:col-span-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#A1AFBF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Search patient or queue..."
                                   class="w-full rounded-[12px] border border-[#DCE6EE] bg-[#F8FAFC] py-3 pl-10 pr-3 text-[13px] text-[#263550] outline-none transition placeholder:text-[#A1AFBF] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] xl:w-[220px]">
                        </div>

                        <select name="status" aria-label="Filter by queue status"
                                class="w-full rounded-[12px] border border-[#DCE6EE] bg-[#F8FAFC] px-3 py-3 text-[13px] text-[#5F6C7F] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]">
                            <option value="">All statuses</option>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected($status === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        <input type="date" name="date" value="{{ $date }}"
                               aria-label="Filter by queue date"
                               class="w-full rounded-[12px] border border-[#DCE6EE] bg-[#F8FAFC] px-3 py-3 text-[13px] text-[#5F6C7F] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]">

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="inline-flex flex-1 items-center justify-center rounded-[12px] bg-[#5B9DF9] px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-[#4E91F0]">
                                Filter
                            </button>
                            @if ($search || $status)
                                <a href="{{ route('clinic.queues.index', ['date' => $date]) }}"
                                   class="inline-flex items-center justify-center rounded-[12px] bg-[#F1F5F9] px-3.5 py-3 text-[13px] font-semibold text-[#718096] transition hover:bg-[#E5EBF0]">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto border-t border-[#E9EFF4]">
                    <table class="w-full min-w-[1000px]">
                        <thead>
                            <tr class="bg-[#FAFCFD]">
                                <th class="px-6 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Queue
                                </th>
                                <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Patient
                                </th>
                                <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Branch
                                </th>
                                <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Appointment
                                </th>
                                <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Status
                                </th>
                                <th class="px-6 py-3.5 text-right text-[10px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#EDF2F6]">
                            @forelse ($queues as $queue)
                                @php
                                    $patientName = trim(
                                        ($queue->patient->first_name ?? '') . ' ' .
                                        ($queue->patient->last_name ?? '')
                                    );

                                    $nextStatus = match ($queue->status) {
                                        'waiting' => 'called',
                                        'called' => 'serving',
                                        'serving' => 'completed',
                                        default => null,
                                    };

                                    $nextStatusLabels = [
                                        'called' => 'Call Patient',
                                        'serving' => 'Start Service',
                                        'completed' => 'Complete',
                                    ];
                                @endphp

                                <tr class="transition hover:bg-[#FBFDFF]">

                                    {{-- QUEUE NUMBER --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#E8F3FF] text-[#5B9DF9]">
                                                <span class="text-xs font-bold">
                                                    {{ str_pad($queue->queue_number, 3, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-[13px] font-semibold text-[#263550]">
                                                    Queue #{{ str_pad($queue->queue_number, 3, '0', STR_PAD_LEFT) }}
                                                </p>
                                                <p class="mt-1 text-[11px] text-[#9BA8B7]">
                                                    {{ $queue->queue_date->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- PATIENT --}}
                                    <td class="px-5 py-4">
                                        <p class="text-[13px] font-semibold text-[#263550]">
                                            {{ $patientName ?: 'Unknown patient' }}
                                        </p>
                                        <p class="mt-1 text-[11px] text-[#9BA8B7]">
                                            {{ $queue->patient->patient_number ?? '—' }}
                                        </p>
                                    </td>

                                    {{-- BRANCH --}}
                                    <td class="px-5 py-4">
                                        <span class="text-[13px] text-[#5F6C7F]">
                                            {{ $queue->branch->name ?? 'Main branch' }}
                                        </span>
                                    </td>

                                    {{-- APPOINTMENT --}}
                                    <td class="px-5 py-4">
                                        @if ($queue->appointment)
                                            <p class="text-[13px] font-medium text-[#263550]">
                                                {{ $queue->appointment->appointment_number }}
                                            </p>
                                            <p class="mt-1 text-[11px] text-[#9BA8B7]">
                                                {{ $queue->appointment->appointment_date?->format('d M Y') ?? '—' }}
                                            </p>
                                        @else
                                            <span class="text-[13px] text-[#9BA8B7]">Walk-in</span>
                                        @endif
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-[11px] font-semibold {{ $statusStyles[$queue->status] ?? 'bg-[#F1F3F6] text-[#7C8595]' }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ $statusLabels[$queue->status] ?? ucfirst(str_replace('_', ' ', $queue->status)) }}
                                        </span>
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @canDent('queues.update')
                                                @if ($nextStatus)
                                                    <form method="POST" action="{{ route('clinic.queues.status', $queue) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $nextStatus }}">

                                                        <button type="submit"
                                                                class="inline-flex items-center justify-center whitespace-nowrap rounded-[9px] bg-[#E8F3FF] px-3 py-2 text-[11px] font-semibold text-[#5B9DF9] transition hover:bg-[#DDEEFF]">
                                                            {{ $nextStatusLabels[$nextStatus] }}
                                                        </button>
                                                    </form>
                                                @endif

                                                @if (in_array($queue->status, ['waiting', 'called'], true))
                                                    <form method="POST"
                                                          action="{{ route('clinic.queues.status', $queue) }}"
                                                          onsubmit="return confirm('Cancel this queue entry?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="cancelled">

                                                        <button type="submit"
                                                                class="inline-flex items-center justify-center rounded-[9px] border border-[#F4D5DC] bg-white px-2.5 py-2 text-[11px] font-semibold text-[#D97791] transition hover:bg-[#FFF6F8]">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcanDent

                                            @if (in_array($queue->status, ['completed', 'cancelled'], true))
                                                <span class="text-[11px] font-medium text-[#A0ACBA]">No actions</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16">
                                        <div class="mx-auto max-w-md text-center">
                                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-[18px] bg-[#EEF4F8] text-[#94A3B8]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                                </svg>
                                            </div>

                                            <h3 class="mt-4 text-sm font-semibold text-[#263550]">
                                                No queue entries found
                                            </h3>

                                            <p class="mt-1.5 text-[13px] leading-6 text-[#8A99AC]">
                                                @if ($search || $status)
                                                    No queue entries matched your current search or filters.
                                                @else
                                                    There are no patients in the queue for this date yet. Add a patient to get started.
                                                @endif
                                            </p>

                                            @if ($search || $status)
                                                <a href="{{ route('clinic.queues.index', ['date' => $date]) }}"
                                                   class="mt-4 inline-flex rounded-[10px] bg-[#EEF3F7] px-4 py-2 text-xs font-semibold text-[#5F6C7F] transition hover:bg-[#E5EBF0]">
                                                    Clear Filters
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
                @if ($queues->hasPages())
                    <div class="border-t border-[#E7EDF2] bg-[#FCFDFE] px-5 py-4 sm:px-6">
                        {{ $queues->links() }}
                    </div>
                @endif

                {{-- FOOTER --}}
                <div class="flex flex-col gap-1.5 border-t border-[#E7EDF2] bg-white px-5 py-3.5 text-[11px] text-[#9BA8B7] sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p>
                        Showing
                        <span class="font-semibold text-[#5F6C7F]">{{ $queues->firstItem() ?? 0 }}</span>
                        to
                        <span class="font-semibold text-[#5F6C7F]">{{ $queues->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-semibold text-[#5F6C7F]">{{ $queues->total() }}</span>
                        queue entries
                    </p>
                    <p>{{ $tenant->name }} · Queue Management</p>
                </div>
            </section>

        </main>
    </div>
</x-app-layout>
