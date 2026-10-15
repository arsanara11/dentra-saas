<x-app-layout>
    @php
        $statusStyles = [
            'scheduled' => 'bg-[#EAF4FF] text-[#5B9DF9]',
            'confirmed' => 'bg-[#F3EBFF] text-[#8B7CFF]',
            'checked_in' => 'bg-[#E4F7F5] text-[#2BA89B]',
            'completed' => 'bg-[#E9F9F0] text-[#43A96E]',
            'cancelled' => 'bg-[#FCEBF0] text-[#D97791]',
            'no_show' => 'bg-[#FFF4E4] text-[#D99A3D]',
        ];

        $statusLabels = [
            'scheduled' => 'Scheduled',
            'confirmed' => 'Confirmed',
            'checked_in' => 'Checked In',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
        ];

        $patientName = trim(
            ($appointment->patient->first_name ?? '') . ' ' .
            ($appointment->patient->last_name ?? '')
        );

        $canCheckIn = auth()->user()->hasPermission('queues.create', $tenant);
        $canViewQueue = auth()->user()->hasPermission('queues.view', $tenant);

        $today = \Carbon\Carbon::now(
            $tenant->timezone ?: config('app.timezone')
        )->toDateString();

        $canCheckInNow = $canCheckIn
            && in_array($appointment->status, ['scheduled', 'confirmed'], true)
            && $appointment->appointment_date->toDateString() === $today;
    @endphp

    <div class="min-h-screen bg-[#EEF4F8]">
        <main class="w-full space-y-5">

            {{-- HEADER --}}
            <section class="relative overflow-hidden rounded-[34px] border border-white bg-white shadow-[0_18px_50px_rgba(52,75,98,0.07)]">
                <div class="pointer-events-none absolute -right-16 -top-24 h-80 w-80 rounded-full bg-[#E8F4FF]"></div>

                <div class="relative px-6 py-8 sm:px-9 lg:px-11">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('dashboard') }}" class="text-[#94A3B8] hover:text-[#5B9DF9]">
                            Clinic
                        </a>
                        <span class="text-[#B7C1CE]">/</span>
                        <a href="{{ route('clinic.appointments.index') }}" class="text-[#94A3B8] hover:text-[#5B9DF9]">
                            Appointments
                        </a>
                        <span class="text-[#B7C1CE]">/</span>
                        <span class="font-medium text-[#5B9DF9]">Details</span>
                    </div>

                    @if (session('success'))
                        <div class="mt-5 rounded-2xl border border-[#CDEEDB] bg-[#F5FCF8] px-5 py-4 text-sm font-medium text-[#319565]">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-5 rounded-2xl border border-[#F4D5DC] bg-[#FFF8FA] px-5 py-4 text-sm text-[#C45F78]">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-8 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                        <div>
                            <p class="text-sm font-medium text-[#8090A5]">
                                {{ $tenant->name }}
                            </p>

                            <h1 class="mt-2 break-words text-3xl font-semibold tracking-[-0.04em] text-[#25324A] sm:text-[42px]">
                                Appointment Details
                            </h1>

                            <p class="mt-3 text-sm text-[#8A99AC]">
                                {{ $appointment->appointment_number }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('clinic.appointments.index') }}"
                               class="inline-flex items-center justify-center rounded-[14px] border border-[#DCE6EE] bg-white px-5 py-3 text-sm font-semibold text-[#718096] transition hover:bg-[#F7FAFC]">
                                Back to Appointments
                            </a>

                            @if ($canCheckInNow)
                                <form method="POST"
                                      action="{{ route('clinic.appointments.check-in', $appointment) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 rounded-[14px] bg-[#2BA89B] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#249A8E]">
                                        Check In Patient
                                    </button>
                                </form>
                            @endif

                            @if ($appointment->queue && $canViewQueue)
                                <a href="{{ route('clinic.queues.show', $appointment->queue) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-[14px] border border-[#CFEDEA] bg-[#F1FBFA] px-5 py-3 text-sm font-semibold text-[#2BA89B] transition hover:bg-[#E4F7F5]">
                                    Ticket {{ $appointment->queue->ticket_number }}
                                </a>
                            @endif

                            @canDent('appointments.update')
                                @if ($appointment->status !== 'checked_in')
                                <a href="{{ route('clinic.appointments.edit', $appointment) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-[14px] bg-[#5B9DF9] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#4E91F0]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16 4 4 4M4 20l4-.8L19 8a2.1 2.1 0 0 0-3-3L5 16l-1 4z"/>
                                    </svg>
                                    Edit Appointment
                                </a>
                                @endif
                            @endcanDent
                        </div>
                    </div>
                </div>
            </section>

            {{-- APPOINTMENT SUMMARY --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-[25px] border border-[#DCE7F0] bg-white p-6 shadow-sm">
                    <p class="text-sm text-[#8493A7]">Appointment Status</p>
                    <div class="mt-4">
                        <span class="inline-flex items-center gap-2 rounded-full px-3.5 py-2 text-sm font-semibold {{ $statusStyles[$appointment->status] ?? 'bg-[#F1F3F6] text-[#7C8595]' }}">
                            <span class="h-2 w-2 rounded-full bg-current"></span>
                            {{ $statusLabels[$appointment->status] ?? ucfirst(str_replace('_', ' ', $appointment->status)) }}
                        </span>
                    </div>
                </div>

                <div class="rounded-[25px] border border-[#DCE7F0] bg-white p-6 shadow-sm">
                    <p class="text-sm text-[#8493A7]">Appointment Date</p>
                    <p class="mt-3 text-xl font-semibold text-[#263550]">
                        {{ $appointment->appointment_date->format('d M Y') }}
                    </p>
                </div>

                <div class="rounded-[25px] border border-[#DCE7F0] bg-white p-6 shadow-sm">
                    <p class="text-sm text-[#8493A7]">Appointment Time</p>
                    <p class="mt-3 text-xl font-semibold text-[#263550]">
                        {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                        @if ($appointment->end_time)
                            – {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                        @endif
                    </p>
                </div>
            </section>

            {{-- DETAILS --}}
            <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">

                {{-- PATIENT INFORMATION --}}
                <div class="overflow-hidden rounded-[30px] border border-[#D8E4ED] bg-white shadow-sm xl:col-span-2">
                    <div class="flex items-center gap-3 border-b border-[#E9EFF4] px-6 py-5 sm:px-8">
                        <div class="flex h-11 w-11 items-center justify-center rounded-[15px] bg-[#E8F3FF] text-[#5B9DF9]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="12" cy="8" r="4"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                            </svg>
                        </div>

                        <div>
                            <h2 class="font-semibold text-[#263550]">Patient Information</h2>
                            <p class="mt-1 text-sm text-[#8A99AC]">Patient assigned to this appointment</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 px-6 py-7 sm:grid-cols-2 sm:px-8">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9BA8B7]">Patient Name</p>
                            <p class="mt-2 text-base font-semibold text-[#263550]">
                                {{ $patientName ?: 'Unknown patient' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9BA8B7]">Patient Number</p>
                            <p class="mt-2 text-sm font-semibold text-[#526176]">
                                {{ $appointment->patient->patient_number ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9BA8B7]">Clinic Branch</p>
                            <p class="mt-2 text-sm font-semibold text-[#526176]">
                                {{ $appointment->branch->name ?? 'Main branch' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9BA8B7]">Created By</p>
                            <p class="mt-2 text-sm font-semibold text-[#526176]">
                                {{ $appointment->creator->name ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- APPOINTMENT INFORMATION --}}
                <div class="overflow-hidden rounded-[30px] border border-[#D8E4ED] bg-white shadow-sm">
                    <div class="border-b border-[#E9EFF4] px-6 py-5">
                        <h2 class="font-semibold text-[#263550]">Visit Information</h2>
                        <p class="mt-1 text-sm text-[#8A99AC]">Consultation details</p>
                    </div>

                    <div class="space-y-6 px-6 py-6">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9BA8B7]">Reason for Visit</p>
                            <p class="mt-2 break-words text-sm leading-6 text-[#526176]">
                                {{ $appointment->reason ?: 'General consultation' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9BA8B7]">Additional Notes</p>
                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-[#526176]">{{ $appointment->notes ?: 'No additional notes.' }}</p>
                        </div>

                        <div class="border-t border-[#EDF2F6] pt-5">
                            <p class="text-xs font-medium uppercase tracking-wider text-[#9BA8B7]">Created At</p>
                            <p class="mt-2 text-sm text-[#526176]">
                                {{ $appointment->created_at?->format('d M Y, H:i') ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- DANGER ZONE --}}
            @canDent('appointments.delete')
                <section class="rounded-[25px] border border-[#F0DDE2] bg-white p-6 shadow-sm sm:flex sm:items-center sm:justify-between sm:gap-6">
                    <div>
                        <h2 class="font-semibold text-[#263550]">Delete Appointment</h2>
                        <p class="mt-1 text-sm leading-6 text-[#8A99AC]">
                            This removes the appointment from the active directory.
                        </p>
                    </div>

                    <form method="POST"
                          action="{{ route('clinic.appointments.destroy', $appointment) }}"
                          class="mt-4 sm:mt-0"
                          onsubmit="return confirm('Are you sure you want to delete this appointment?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-[13px] border border-[#F0D3DA] bg-[#FFF8FA] px-5 py-3 text-sm font-semibold text-[#C45F78] transition hover:bg-[#FCEBF0]">
                            Delete Appointment
                        </button>
                    </form>
                </section>
            @endcanDent

        </main>
    </div>
</x-app-layout>