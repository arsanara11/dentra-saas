<x-app-layout>
    @php
        $statusStyles = [
            'scheduled' => 'bg-[#EAF4FF] text-[#5B9DF9]',
            'confirmed' => 'bg-[#F3EBFF] text-[#8B7CFF]',
            'completed' => 'bg-[#E9F9F0] text-[#43A96E]',
            'cancelled' => 'bg-[#FCEBF0] text-[#D97791]',
            'no_show' => 'bg-[#FFF4E4] text-[#D99A3D]',
        ];

        $statusLabels = [
            'scheduled' => 'Scheduled',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
        ];
    @endphp

    <div class="min-h-screen bg-[#EEF4F8]">
        <main class="w-full max-w-none px-0 py-0">

            {{-- HERO --}}
            <section class="relative overflow-hidden rounded-[36px] border border-white/80 bg-white shadow-[0_18px_50px_rgba(52,75,98,0.07)]">
                <div class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="absolute -right-20 -top-28 h-[420px] w-[420px] rounded-full bg-[#E8F4FF] opacity-80 blur-[2px]"></div>
                    <div class="absolute right-[12%] top-[18%] h-[250px] w-[250px] rounded-full bg-[#F2F8FF]"></div>
                    <div class="absolute bottom-[-140px] right-[26%] h-[260px] w-[260px] rounded-full bg-[#EDF7FF]"></div>
                </div>

                <div class="relative z-10 flex min-h-[310px] flex-col justify-between gap-10 px-7 py-8 sm:px-9 lg:flex-row lg:items-center lg:px-11 lg:py-10">
                    <div class="max-w-[720px]">
                        <div class="flex items-center gap-2 text-sm font-medium">
                            <a href="{{ route('dashboard') }}" class="text-[#94A3B8] transition hover:text-[#5B9DF9]">
                                Clinic
                            </a>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#B7C1CE]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                            </svg>

                            <span class="text-[#5B9DF9]">Appointments</span>
                        </div>

                        <p class="mt-8 text-sm font-medium text-[#8090A5]">
                            {{ $tenant->name }}
                        </p>

                        <h1 class="mt-1 text-[42px] font-semibold leading-[1.05] tracking-[-0.045em] text-[#25324A] sm:text-[48px]">
                            Appointments
                        </h1>

                        <p class="mt-4 max-w-[650px] text-[15px] leading-7 text-[#8A99AC]">
                            Manage your clinic's appointment schedules, patient visits,
                            and consultation times in one place.
                        </p>
                    </div>

                    <div class="relative flex shrink-0 items-center justify-end lg:min-w-[400px]">
                        {{-- Decorative dental schedule illustration --}}
                        <div class="pointer-events-none absolute right-[110px] top-1/2 hidden -translate-y-1/2 lg:block">
                            <div class="relative flex h-[190px] w-[190px] items-center justify-center rounded-full bg-[#EAF5FF]">
                                <div class="absolute inset-[22px] rounded-full border border-[#D6EBFF]"></div>
                                <div class="absolute inset-[42px] rounded-full bg-[#F5FAFF]"></div>

                                <svg xmlns="http://www.w3.org/2000/svg" class="relative z-10 h-[72px] w-[72px] text-[#8EC3FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3.5" y="5" width="17" height="16" rx="3"/>
                                    <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.5 15 2.2 2.2 4.8-4.8"/>
                                </svg>

                                <div class="absolute -right-1 top-5 flex h-8 w-8 items-center justify-center rounded-full bg-white text-[#72B1FF] shadow-[0_8px_20px_rgba(91,157,249,0.15)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2l1.8 7.2L21 11l-7.2 1.8L12 20l-1.8-7.2L3 11l7.2-1.8L12 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        @canDent('appointments.create')
                            <a href="{{ route('clinic.appointments.create') }}"
                               class="relative z-20 inline-flex items-center justify-center gap-2.5 rounded-[18px] bg-[#5B9DF9] px-6 py-4 text-sm font-semibold text-white shadow-[0_12px_28px_rgba(91,157,249,0.25)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#4E91F0] hover:shadow-[0_16px_32px_rgba(91,157,249,0.3)]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                </svg>
                                New Appointment
                            </a>
                        @endcanDent
                    </div>
                </div>
            </section>

            {{-- FLASH MESSAGES --}}
            @if (session('success'))
                <div class="mt-5 rounded-2xl border border-[#CDEEDB] bg-white px-5 py-4 text-sm font-medium text-[#319565] shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded-2xl border border-[#F4D5DC] bg-white px-5 py-4 text-sm text-[#C45F78] shadow-sm">
                    <p class="mb-2 font-semibold">Please check the following:</p>
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- STATISTICS --}}
            <section class="mt-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                    {{-- TOTAL --}}
                    <div class="group rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-[0_12px_32px_rgba(52,75,98,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_38px_rgba(52,75,98,0.08)]">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#8493A7]">Total Appointments</p>
                                <p class="mt-3 text-[32px] font-semibold tracking-[-0.045em] text-[#263550]">
                                    {{ $appointments->total() }}
                                </p>
                                <p class="mt-1.5 text-xs text-[#A0ACBA]">Matching current filters</p>
                            </div>

                            <div class="flex h-12 w-12 items-center justify-center rounded-[17px] bg-[#E8F3FF] text-[#5B9DF9] transition group-hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <rect x="3.5" y="5" width="17" height="16" rx="2.5"/>
                                    <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17M8 14h3m-3 3h6"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- SHOWING --}}
                    <div class="group rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-[0_12px_32px_rgba(52,75,98,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_38px_rgba(52,75,98,0.08)]">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#8493A7]">Showing</p>
                                <p class="mt-3 text-[32px] font-semibold tracking-[-0.045em] text-[#263550]">
                                    {{ $appointments->count() }}
                                </p>
                                <p class="mt-1.5 text-xs text-[#A0ACBA]">Appointments on this page</p>
                            </div>

                            <div class="flex h-12 w-12 items-center justify-center rounded-[17px] bg-[#F3EBFF] text-[#8B7CFF] transition group-hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 6h14M5 12h14M5 18h8"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.5 6h.01M3.5 12h.01M3.5 18h.01"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- SCHEDULED --}}
                    <div class="group rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-[0_12px_32px_rgba(52,75,98,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_38px_rgba(52,75,98,0.08)]">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#8493A7]">Scheduled</p>
                                <p class="mt-3 text-[32px] font-semibold tracking-[-0.045em] text-[#263550]">
                                    {{ $appointments->getCollection()->where('status', 'scheduled')->count() }}
                                </p>
                                <p class="mt-1.5 text-xs text-[#A0ACBA]">On current page</p>
                            </div>

                            <div class="flex h-12 w-12 items-center justify-center rounded-[17px] bg-[#E8F3FF] text-[#5B9DF9] transition group-hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- COMPLETED --}}
                    <div class="group rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-[0_12px_32px_rgba(52,75,98,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_38px_rgba(52,75,98,0.08)]">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#8493A7]">Completed</p>
                                <p class="mt-3 text-[32px] font-semibold tracking-[-0.045em] text-[#263550]">
                                    {{ $appointments->getCollection()->where('status', 'completed')->count() }}
                                </p>
                                <p class="mt-1.5 text-xs text-[#A0ACBA]">On current page</p>
                            </div>

                            <div class="flex h-12 w-12 items-center justify-center rounded-[17px] bg-[#E9F9F0] text-[#3EB778] transition group-hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4m5 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- APPOINTMENT DIRECTORY --}}
            <section class="mt-6 overflow-hidden rounded-[34px] border border-[#D8E4ED] bg-white shadow-[0_16px_42px_rgba(52,75,98,0.055)]">

                {{-- DIRECTORY HEADER AND FILTERS --}}
                <div class="px-6 py-7 sm:px-7 lg:px-8">
                    <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">

                        <div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-[14px] bg-[#E8F3FF] text-[#5B9DF9]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <rect x="3.5" y="5" width="17" height="16" rx="2.5"/>
                                        <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17"/>
                                    </svg>
                                </div>

                                <div>
                                    <h2 class="text-xl font-semibold tracking-[-0.025em] text-[#263550]">
                                        Appointment Directory
                                    </h2>
                                    <p class="mt-1 text-sm text-[#8A99AC]">
                                        View and manage your clinic's appointment schedule.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form method="GET"
                              action="{{ route('clinic.appointments.index') }}"
                              class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 xl:w-auto xl:grid-cols-4">

                            <div class="relative sm:col-span-2 xl:col-span-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#A1AFBF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                </svg>

                                <input
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Search appointments..."
                                    class="w-full rounded-[17px] border border-[#DCE6EE] bg-[#F8FAFC] py-3.5 pl-11 pr-4 text-sm font-medium text-[#263550] outline-none transition placeholder:text-[#A1AFBF] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF] xl:w-[250px]"
                                >
                            </div>

                            <select
                                name="status"
                                aria-label="Filter by status"
                                class="w-full rounded-[17px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm font-medium text-[#5F6C7F] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]">
                                <option value="">All statuses</option>

                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($status === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            <input
                                type="date"
                                name="date"
                                value="{{ $date }}"
                                aria-label="Filter by appointment date"
                                class="w-full rounded-[17px] border border-[#DCE6EE] bg-[#F8FAFC] px-4 py-3.5 text-sm font-medium text-[#5F6C7F] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]"
                            >

                            <div class="flex gap-2">
                                <button
                                    type="submit"
                                    class="inline-flex flex-1 items-center justify-center rounded-[14px] bg-[#5B9DF9] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#4E91F0]">
                                    Filter
                                </button>

                                @if ($search || $status || $date)
                                    <a href="{{ route('clinic.appointments.index') }}"
                                       class="inline-flex items-center justify-center rounded-[14px] bg-[#F1F5F9] px-4 py-3 text-sm font-semibold text-[#718096] transition hover:bg-[#E5EBF0]">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto border-t border-[#E9EFF4]">
                    <table class="w-full min-w-[1100px]">

                        <thead>
                            <tr class="bg-[#FAFCFD]">
                                <th class="px-8 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Appointment
                                </th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Patient
                                </th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Date & Time
                                </th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Branch
                                </th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Status
                                </th>
                                <th class="px-8 py-4 text-right text-[11px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#EDF2F6]">
                            @forelse ($appointments as $appointment)
                                @php
                                    $patientName = trim(
                                        ($appointment->patient->first_name ?? '') . ' ' .
                                        ($appointment->patient->last_name ?? '')
                                    );
                                @endphp

                                <tr class="group transition duration-150 hover:bg-[#FBFDFF]">

                                    {{-- APPOINTMENT NUMBER --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[15px] bg-[#E8F3FF] text-[#5B9DF9]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                                    <rect x="3.5" y="5" width="17" height="16" rx="2.5"/>
                                                    <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17"/>
                                                </svg>
                                            </div>

                                            <div class="min-w-0">
                                                <a href="{{ route('clinic.appointments.show', $appointment) }}"
                                                   class="block truncate text-sm font-semibold text-[#263550] transition hover:text-[#5B9DF9]">
                                                    {{ $appointment->appointment_number }}
                                                </a>

                                                <p class="mt-1 max-w-[220px] truncate text-xs text-[#9BA8B7]">
                                                    {{ $appointment->reason ?: 'General consultation' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- PATIENT --}}
                                    <td class="px-6 py-5">
                                        <p class="text-sm font-semibold text-[#263550]">
                                            {{ $patientName ?: 'Unknown patient' }}
                                        </p>

                                        <p class="mt-1 text-xs text-[#9BA8B7]">
                                            {{ $appointment->patient->patient_number ?? '—' }}
                                        </p>
                                    </td>

                                    {{-- DATE AND TIME --}}
                                    <td class="px-6 py-5">
                                        <p class="text-sm font-medium text-[#5F6C7F]">
                                            {{ $appointment->appointment_date->format('d M Y') }}
                                        </p>

                                        <p class="mt-1 text-xs text-[#9BA8B7]">
                                            {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}

                                            @if ($appointment->end_time)
                                                – {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                                            @endif
                                        </p>
                                    </td>

                                    {{-- BRANCH --}}
                                    <td class="px-6 py-5">
                                        <span class="text-sm text-[#5F6C7F]">
                                            {{ $appointment->branch->name ?? 'Main branch' }}
                                        </span>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $statusStyles[$appointment->status] ?? 'bg-[#F1F3F6] text-[#7C8595]' }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ $statusLabels[$appointment->status] ?? ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </span>
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('clinic.appointments.show', $appointment) }}"
                                               class="inline-flex items-center justify-center rounded-[11px] border border-[#DCE6EE] bg-white px-3.5 py-2 text-xs font-semibold text-[#5F6C7F] transition hover:border-[#C9E1FB] hover:bg-[#F4F9FE] hover:text-[#5B9DF9]">
                                                View
                                            </a>

                                            @canDent('appointments.update')
                                                <a href="{{ route('clinic.appointments.edit', $appointment) }}"
                                                   class="inline-flex items-center justify-center rounded-[11px] bg-[#E8F3FF] px-3.5 py-2 text-xs font-semibold text-[#5B9DF9] transition hover:bg-[#DDEEFF]">
                                                    Edit
                                                </a>
                                            @endcanDent
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-24">
                                        <div class="mx-auto max-w-md text-center">
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[21px] bg-[#EEF4F8] text-[#94A3B8]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                    <rect x="3.5" y="5" width="17" height="16" rx="2.5"/>
                                                    <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17"/>
                                                </svg>
                                            </div>

                                            <h3 class="mt-5 text-base font-semibold text-[#263550]">
                                                No appointments found
                                            </h3>

                                            <p class="mt-2 text-sm leading-6 text-[#8A99AC]">
                                                @if ($search || $status || $date)
                                                    No appointments matched your current search or filters.
                                                @else
                                                    Your clinic does not have any appointments yet.
                                                @endif
                                            </p>

                                            @if ($search || $status || $date)
                                                <a href="{{ route('clinic.appointments.index') }}"
                                                   class="mt-5 inline-flex rounded-[11px] bg-[#EEF3F7] px-4 py-2.5 text-sm font-semibold text-[#5F6C7F] transition hover:bg-[#E5EBF0]">
                                                    Clear Filters
                                                </a>
                                            @elseif (auth()->user()->hasPermission('appointments.create'))
                                                <a href="{{ route('clinic.appointments.create') }}"
                                                   class="mt-5 inline-flex rounded-[11px] bg-[#5B9DF9] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#4E91F0]">
                                                    Create First Appointment
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
                @if ($appointments->hasPages())
                    <div class="border-t border-[#E7EDF2] bg-[#FCFDFE] px-6 py-5 sm:px-8">
                        {{ $appointments->links() }}
                    </div>
                @endif

                {{-- TABLE FOOTER --}}
                <div class="flex flex-col gap-2 border-t border-[#E7EDF2] bg-white px-6 py-4 text-xs text-[#9BA8B7] sm:flex-row sm:items-center sm:justify-between sm:px-8">
                    <p>
                        Showing
                        <span class="font-semibold text-[#5F6C7F]">{{ $appointments->firstItem() ?? 0 }}</span>
                        to
                        <span class="font-semibold text-[#5F6C7F]">{{ $appointments->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-semibold text-[#5F6C7F]">{{ $appointments->total() }}</span>
                        appointments
                    </p>

                    <p>{{ $tenant->name }} · Appointment Management</p>
                </div>
            </section>

        </main>
    </div>
</x-app-layout>
