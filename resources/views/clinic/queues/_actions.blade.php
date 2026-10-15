{{--
    Queue action buttons for one ticket.

    Expects:
      $queue         App\Models\Queue
      $queueService  App\Services\Queue\QueueService

    The caller is responsible for checking the "queues.update" permission.
--}}
@php
    $actionLabels = [
        'call' => 'Call',
        'recall' => 'Recall',
        'serve' => 'Start Service',
        'complete' => 'Complete',
        'skip' => 'Skip',
        'no_show' => 'No-show',
        'cancel' => 'Cancel',
    ];

    $actionStyles = [
        'call' => 'bg-[#E8F3FF] text-[#5B9DF9] hover:bg-[#DDEEFF]',
        'recall' => 'bg-[#F3EBFF] text-[#8B7CFF] hover:bg-[#EBDFFF]',
        'serve' => 'bg-[#E8F3FF] text-[#5B9DF9] hover:bg-[#DDEEFF]',
        'complete' => 'bg-[#E9F9F0] text-[#43A96E] hover:bg-[#DCF3E6]',
        'skip' => 'bg-[#FFF4E4] text-[#D99A3D] hover:bg-[#FFEBCF]',
        'no_show' => 'border border-[#DCE6EE] bg-white text-[#718096] hover:bg-[#F8FAFC]',
        'cancel' => 'border border-[#F4D5DC] bg-white text-[#D97791] hover:bg-[#FFF6F8]',
    ];

    // Actions that ask the user for an optional reason before submitting.
    $actionsWithReason = ['skip', 'no_show', 'cancel'];

    // Primary actions first, destructive actions last.
    $actionOrder = ['call', 'serve', 'complete', 'recall', 'skip', 'no_show', 'cancel'];

    $availableActions = array_values(array_intersect(
        $actionOrder,
        $queueService->availableActions($queue->status)
    ));
@endphp

@forelse ($availableActions as $action)
    <form method="POST"
          action="{{ route('clinic.queues.action', ['queue' => $queue, 'action' => $action]) }}"
          @if (in_array($action, $actionsWithReason, true))
              onsubmit="var r = prompt('Reason for {{ strtolower($actionLabels[$action]) }} (optional):'); if (r === null) { return false; } this.elements.reason.value = r; return true;"
          @endif>
        @csrf
        <input type="hidden" name="reason" value="">

        <button type="submit"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-[9px] px-3 py-2 text-[11px] font-semibold transition {{ $actionStyles[$action] }}">
            {{ $actionLabels[$action] }}
        </button>
    </form>
@empty
    <span class="text-[11px] font-medium text-[#A0ACBA]">No actions</span>
@endforelse