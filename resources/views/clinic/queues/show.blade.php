<x-app-layout>
    @php
        $statusStyles = [
            'waiting' => 'bg-[#FFF4E4] text-[#D99A3D]',
            'called' => 'bg-[#EAF4FF] text-[#5B9DF9]',
            'serving' => 'bg-[#F3EBFF] text-[#8B7CFF]',
            'completed' => 'bg-[#E9F9F0] text-[#43A96E]',
            'skipped' => 'bg-[#FFF1E6] text-[#E8833A]',
            'no_show' => 'bg-[#F1F3F6] text-[#7C8595]',
            'cancelled' => 'bg-[#FCEBF0] text-[#D97791]',
        ];

        $statusLabels = [
            'waiting' => 'Waiting',
            'called' => 'Called',
            'serving' => 'In Service',
            'completed' => 'Completed',
            'skipped' => 'Skipped',
            'no_show' => 'No-show',
            'cancelled' => 'Cancelled',
        ];

        $priorityStyles = [
            'normal' => 'bg-[#F1F3F6] text-[#7C8595]',
            'priority' => 'bg-[#FFF4E4] text-[#D99A3D]',
            'emergency' => 'bg-[#FCEBF0] text-[#D97791]',
        ];

        $eventLabels = [
            'created' => 'Ticket issued',
            'called' => 'Patient called',
            'recalled' => 'Patient called again',
            'serving' => 'Service started',
            'completed' => 'Service completed',
            'skipped' => 'Ticket skipped',
            'no_show' => 'Marked as no-show',
            'cancelled' => 'Ticket cancelled',
        ];

        $eventDots = [
            'created' => 'bg-[#5B9DF9]',
            'called' => 'bg-[#5B9DF9]',
            'recalled' => 'bg-[#8B7CFF]',
            'serving' => 'bg-[#8B7CFF]',
            'completed' => 'bg-[#43A96E]',
            'skipped' => 'bg-[#E8833A]',
            'no_show' => 'bg-[#7C8595]',
            'cancelled' => 'bg-[#D97791]',
        ];

        $canUpdate = auth()->user()->hasPermission('queues.update');
        $canViewPatient = auth()->user()->hasPermission('patients.view');
        $canViewAppointment = auth()->user()->hasPermission('appointments.view');

        $timezone = $tenant->timezone ?: config('app.timezone');

        $formatTime = fn ($date) => $date
            ? $date->copy()->timezone($timezone)->format('d M Y, H:i')
            : '—';

        $minutesBetween = fn ($from, $to) => ($from && $to)
            ? (int) round($from->diffInMinutes($to, true))
            : null;

        $waitingMinutes = $minutesBetween($queue->created_at, $queue->called_at);
        $serviceMinutes = $minutesBetween($queue->started_at, $queue->completed_at);

        $patientName = trim(
            ($queue->patient->first_name ?? '') . ' ' .
            ($queue->patient->last_name ?? '')
        );

        $cardClass = 'overflow-hidden rounded-[24px] border border-[#D8E4ED] bg-white shadow-[0_6px_24px_rgba(52,75,98,0.035)]';
        $labelClass = 'text-[11px] font-semibold uppercase tracking-[0.08em] text-[#91A0B2]';
        $valueClass = 'mt-1 text-[13px] font-medium text-[#263550]';
    @endphp

    <div class="min-h-screen bg-[#EEF4F8]">
        <main class="w-full max-w-none space-y-4 px-0 py-0">

            {{-- HEADER --}}
            <section class="relative overflow-hidden rounded-[26px] border border-white bg-white shadow-[0_6px_24px_rgba(52,75,98,0.04)]">
                <div class="pointer-events-none absolute inset-y-0 right-0 w-[24%] overflow-hidden">
                    <div class="absolute -right-20 -top-32 h-[290px] w-[290px] rounded-full bg-[#E8F4FF]/80"></div>
                    <div class="absolute -right-5 top-7 h-[180px] w-[180px] rounded-full bg-[#F2F8FF]"></div>
                </div>

                <div class="relative z-10 flex flex-col gap-5 px-5 py-5 sm:px-7 sm:py-6 lg:flex-row lg:items-center lg:justify-between lg:gap-8">
                    <div class="min-w-0">
                        <nav class="flex items-center gap-2 text-[13px] font-medium">
                            <a href="{{ route('dashboard') }}"
                               class="text-[#94A3B8] transition hover:text-[#5B9DF9]">
                                Clinic
                            </a>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#B7C1CE]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                            </svg>

                            <a href="{{ route('clinic.queues.index', ['date' => $queue->queue_date->toDateString()]) }}"
                               class="text-[#94A3B8] transition hover:text-[#5B9DF9]">
                                Queue Management
                            </a>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#B7C1CE]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                            </svg>

                            <span class="text-[#5B9DF9]">{{ $queue->ticket_number }}</span>
                        </nav>

                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <h1 class="text-[32px] font-semibold leading-tight tracking-[-0.045em] text-[#25324A] sm:text-[38px]">
                                Ticket {{ $queue->ticket_number }}
                            </h1>

                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold {{ $statusStyles[$queue->status] ?? 'bg-[#F1F3F6] text-[#7C8595]' }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                {{ $statusLabels[$queue->status] ?? ucfirst(str_replace('_', ' ', $queue->status)) }}
                            </span>

                            @if ($queue->priority !== 'normal')
                                <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold {{ $priorityStyles[$queue->priority] ?? $priorityStyles['normal'] }}">
                                    {{ ucfirst($queue->priority) }}
                                </span>
                            @endif
                        </div>

                        <p class="mt-2 text-sm text-[#8A99AC]">
                            {{ $patientName ?: 'Unknown patient' }}
                            · {{ $queue->queue_date->format('d M Y') }}
                            · {{ $tenant->name }}
                        </p>
                    </div>

                    <div class="relative z-10 flex shrink-0 flex-wrap items-center gap-1.5">
                        @if ($canUpdate)
                            @include('clinic.queues._actions', [
                                'queue' => $queue,
                                'queueService' => $queueService,
                            ])
                        @endif

                        <a href="{{ route('clinic.queues.index', ['date' => $queue->queue_date->toDateString()]) }}"
                           class="inline-flex items-center justify-center whitespace-nowrap rounded-[9px] border border-[#DCE6EE] bg-white px-3 py-2 text-[11px] font-semibold text-[#5F6C7F] transition hover:bg-[#F8FAFC]">
                            Back to Queue
                        </a>
                    </div>
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

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

                {{-- DETAILS --}}
                <section class="{{ $cardClass }} xl:col-span-1">
                    <div class="border-b border-[#E9EFF4] px-5 py-4 sm:px-6">
                        <h2 class="text-base font-semibold tracking-[-0.025em] text-[#263550]">
                            Ticket Details
                        </h2>
                    </div>

                    <dl class="space-y-4 p-5 sm:p-6">
                        <div>
                            <dt class="{{ $labelClass }}">Patient</dt>
                            <dd class="{{ $valueClass }}">
                                @if ($queue->patient && $canViewPatient)
                                    <a href="{{ route('clinic.patients.show', $queue->patient) }}"
                                       class="text-[#5B9DF9] transition hover:text-[#4E91F0]">
                                        {{ $patientName ?: 'Unknown patient' }}
                                    </a>
                                @else
                                    {{ $patientName ?: 'Unknown patient' }}
                                @endif
                                <span class="block text-[11px] font-normal text-[#9BA8B7]">
                                    {{ $queue->patient->patient_number ?? '—' }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="{{ $labelClass }}">Branch</dt>
                            <dd class="{{ $valueClass }}">{{ $queue->branch->name ?? 'Main branch' }}</dd>
                        </div>

                        <div>
                            <dt class="{{ $labelClass }}">Doctor</dt>
                            <dd class="{{ $valueClass }}">{{ $queue->doctor->name ?? 'Any doctor' }}</dd>
                        </div>

                        <div>
                            <dt class="{{ $labelClass }}">Appointment</dt>
                            <dd class="{{ $valueClass }}">
                                @if ($queue->appointment)
                                    @if ($canViewAppointment)
                                        <a href="{{ route('clinic.appointments.show', $queue->appointment) }}"
                                           class="text-[#5B9DF9] transition hover:text-[#4E91F0]">
                                            {{ $queue->appointment->appointment_number }}
                                        </a>
                                    @else
                                        {{ $queue->appointment->appointment_number }}
                                    @endif
                                    <span class="block text-[11px] font-normal text-[#9BA8B7]">
                                        {{ $queue->appointment->appointment_date?->format('d M Y') ?? '—' }}
                                    </span>
                                @else
                                    Walk-in
                                @endif
                            </dd>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-[#EDF2F6] pt-4">
                            <div>
                                <dt class="{{ $labelClass }}">Waiting time</dt>
                                <dd class="{{ $valueClass }}">
                                    {{ $waitingMinutes !== null ? $waitingMinutes . ' min' : '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="{{ $labelClass }}">Service time</dt>
                                <dd class="{{ $valueClass }}">
                                    {{ $serviceMinutes !== null ? $serviceMinutes . ' min' : '—' }}
                                </dd>
                            </div>
                        </div>

                        <div class="space-y-3 border-t border-[#EDF2F6] pt-4">
                            <div class="flex items-center justify-between gap-3 text-[12px]">
                                <span class="text-[#8A99AC]">Issued</span>
                                <span class="font-medium text-[#263550]">{{ $formatTime($queue->created_at) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 text-[12px]">
                                <span class="text-[#8A99AC]">Called</span>
                                <span class="font-medium text-[#263550]">{{ $formatTime($queue->called_at) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 text-[12px]">
                                <span class="text-[#8A99AC]">Service started</span>
                                <span class="font-medium text-[#263550]">{{ $formatTime($queue->started_at) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 text-[12px]">
                                <span class="text-[#8A99AC]">Completed</span>
                                <span class="font-medium text-[#263550]">{{ $formatTime($queue->completed_at) }}</span>
                            </div>
                            @if ($queue->skipped_at)
                                <div class="flex items-center justify-between gap-3 text-[12px]">
                                    <span class="text-[#8A99AC]">Skipped</span>
                                    <span class="font-medium text-[#263550]">{{ $formatTime($queue->skipped_at) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3 border-t border-[#EDF2F6] pt-4">
                            <div class="flex items-center justify-between gap-3 text-[12px]">
                                <span class="text-[#8A99AC]">Created by</span>
                                <span class="font-medium text-[#263550]">{{ $queue->creator->name ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 text-[12px]">
                                <span class="text-[#8A99AC]">Last updated by</span>
                                <span class="font-medium text-[#263550]">{{ $queue->updater->name ?? '—' }}</span>
                            </div>
                        </div>
                    </dl>
                </section>

                {{-- HISTORY --}}
                <section class="{{ $cardClass }} xl:col-span-2">
                    <div class="flex items-center justify-between gap-3 border-b border-[#E9EFF4] px-5 py-4 sm:px-6">
                        <div>
                            <h2 class="text-base font-semibold tracking-[-0.025em] text-[#263550]">
                                Activity History
                            </h2>
                            <p class="mt-0.5 text-xs text-[#8A99AC]">
                                Every change to this ticket is recorded and cannot be edited.
                            </p>
                        </div>
                        <span class="rounded-full bg-[#EEF4F8] px-2.5 py-1 text-[11px] font-semibold text-[#5F6C7F]">
                            {{ $queue->events->count() }} events
                        </span>
                    </div>

                    <div class="p-5 sm:p-6">
                        @forelse ($queue->events as $event)
                            @php
                                $metadata = $event->metadata ?? [];
                            @endphp

                            <div class="relative flex gap-4 pb-6 last:pb-0">
                                @unless ($loop->last)
                                    <span class="absolute left-[7px] top-4 h-full w-px bg-[#E3EBF2]"></span>
                                @endunless

                                <span class="relative mt-1 h-[15px] w-[15px] shrink-0 rounded-full border-[3px] border-white shadow-[0_0_0_1px_#DCE6EE] {{ $eventDots[$event->event_type] ?? 'bg-[#94A3B8]' }}"></span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="text-[13px] font-semibold text-[#263550]">
                                            {{ $eventLabels[$event->event_type] ?? ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                        </p>
                                        <p class="text-[11px] text-[#9BA8B7]">
                                            {{ $formatTime($event->created_at) }}
                                        </p>
                                    </div>

                                    <p class="mt-1 text-[12px] text-[#8A99AC]">
                                        by
                                        <span class="font-medium text-[#5F6C7F]">{{ $event->actor->name ?? 'System' }}</span>

                                        @if ($event->from_status && $event->to_status && $event->from_status !== $event->to_status)
                                            ·
                                            {{ $statusLabels[$event->from_status] ?? $event->from_status }}
                                            →
                                            {{ $statusLabels[$event->to_status] ?? $event->to_status }}
                                        @endif
                                    </p>

                                    @if (! empty($metadata['reason']))
                                        <p class="mt-2 rounded-[10px] bg-[#F8FAFC] px-3 py-2 text-[12px] text-[#5F6C7F]">
                                            Reason: {{ $metadata['reason'] }}
                                        </p>
                                    @endif

                                    @if (! empty($metadata['recall_number']))
                                        <p class="mt-1 text-[11px] text-[#9BA8B7]">
                                            Call attempt #{{ $metadata['recall_number'] + 1 }}
                                        </p>
                                    @endif

                                    @if (! empty($metadata['backfilled']))
                                        <p class="mt-1 text-[11px] text-[#9BA8B7]">
                                            Imported from data created before activity tracking was enabled.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="py-10 text-center text-[13px] text-[#8A99AC]">
                                No activity has been recorded for this ticket.
                            </p>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>
    </div>
</x-app-layout>