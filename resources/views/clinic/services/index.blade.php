<x-app-layout>
    @php
        $canManage = auth()->user()->hasPermission('services.manage');

        $currency = $tenant->currency ?: 'IDR';
        $decimals = in_array($currency, ['IDR', 'JPY', 'KRW', 'VND'], true) ? 0 : 2;
        $money = fn ($amount) => $currency . ' ' . number_format((float) $amount, $decimals, ',', '.');

        $statusStyles = [
            'active' => 'bg-[#E9F9F0] text-[#43A96E]',
            'inactive' => 'bg-[#F1F3F6] text-[#7C8595]',
        ];

        $inputClass = 'w-full rounded-[12px] border border-[#DCE6EE] bg-[#F8FAFC] px-3 py-3 text-[13px] text-[#5F6C7F] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]';
    @endphp

    <div class="min-h-screen bg-[#EEF4F8]">
        <main class="w-full max-w-none space-y-4 px-0 py-0">

            {{-- HERO --}}
            <section class="relative overflow-hidden rounded-[26px] border border-white bg-white shadow-[0_6px_24px_rgba(52,75,98,0.04)]">
                <div class="pointer-events-none absolute inset-y-0 right-0 w-[24%] overflow-hidden">
                    <div class="absolute -right-20 -top-32 h-[290px] w-[290px] rounded-full bg-[#E8F4FF]/80"></div>
                    <div class="absolute -right-5 top-7 h-[180px] w-[180px] rounded-full bg-[#F2F8FF]"></div>
                </div>

                <div class="relative z-10 flex flex-col gap-5 px-5 py-5 sm:px-7 sm:py-6 lg:flex-row lg:items-center lg:justify-between lg:gap-8">
                    <div class="min-w-0">
                        <nav class="flex items-center gap-2 text-[13px] font-medium">
                            <a href="{{ route('dashboard') }}" class="text-[#94A3B8] transition hover:text-[#5B9DF9]">Clinic</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#B7C1CE]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                            </svg>
                            <span class="text-[#5B9DF9]">Services</span>
                        </nav>

                        <p class="mt-4 text-[13px] font-medium text-[#8090A5]">{{ $tenant->name }}</p>

                        <h1 class="mt-1 text-[32px] font-semibold leading-tight tracking-[-0.045em] text-[#25324A] sm:text-[38px]">
                            Service Catalogue
                        </h1>

                        <p class="mt-2 max-w-[560px] text-sm leading-6 text-[#8A99AC]">
                            Treatments your clinic offers, with their default duration and price.
                        </p>
                    </div>

                    @if ($canManage)
                        <div class="relative z-10 shrink-0">
                            <a href="{{ route('clinic.services.create') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-[14px] bg-[#5B9DF9] px-5 py-3 text-sm font-semibold text-white shadow-[0_6px_16px_rgba(91,157,249,0.20)] transition hover:bg-[#4E91F0]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                </svg>
                                New Service
                            </a>
                        </div>
                    @endif
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
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- STATISTICS --}}
            <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                @foreach ([
                    ['label' => 'Total Services', 'value' => $statistics['total'], 'hint' => 'In the catalogue', 'tone' => 'bg-[#E8F3FF] text-[#5B9DF9]'],
                    ['label' => 'Active', 'value' => $statistics['active'], 'hint' => 'Available for booking', 'tone' => 'bg-[#E9F9F0] text-[#43A96E]'],
                    ['label' => 'Inactive', 'value' => $statistics['inactive'], 'hint' => 'Hidden from booking', 'tone' => 'bg-[#F1F3F6] text-[#7C8595]'],
                ] as $card)
                    <div class="rounded-[22px] border border-[#DCE7F0] bg-white p-5 shadow-[0_5px_20px_rgba(52,75,98,0.035)]">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[13px] font-medium text-[#8493A7]">{{ $card['label'] }}</p>
                                <p class="mt-2 text-[30px] font-semibold leading-none tracking-[-0.045em] text-[#263550]">{{ $card['value'] }}</p>
                                <p class="mt-2 text-xs text-[#A0ACBA]">{{ $card['hint'] }}</p>
                            </div>
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] {{ $card['tone'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6M9 3h6a1 1 0 0 1 1 1v1H8V4a1 1 0 0 1 1-1ZM7 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1M9 14l2 2 4-4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- DIRECTORY --}}
            <section class="overflow-hidden rounded-[26px] border border-[#D8E4ED] bg-white shadow-[0_6px_24px_rgba(52,75,98,0.04)]">

                <div class="flex flex-col gap-4 px-5 py-5 sm:px-6 2xl:flex-row 2xl:items-center 2xl:justify-between">
                    <div class="shrink-0">
                        <h2 class="text-lg font-semibold tracking-[-0.025em] text-[#263550]">All Services</h2>
                        <p class="mt-0.5 text-[13px] text-[#8A99AC]">Search, edit, or deactivate a service.</p>
                    </div>

                    <form method="GET" action="{{ route('clinic.services.index') }}"
                          class="grid w-full grid-cols-1 gap-2.5 sm:grid-cols-2 xl:grid-cols-[minmax(0,1.8fr)_minmax(0,1fr)_auto] 2xl:w-[640px] 2xl:shrink-0">

                        <div class="relative min-w-0 sm:col-span-2 xl:col-span-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#A1AFBF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Search name, code or category..."
                                   class="w-full rounded-[12px] border border-[#DCE6EE] bg-[#F8FAFC] py-3 pl-10 pr-3 text-[13px] text-[#263550] outline-none transition placeholder:text-[#A1AFBF] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#EAF4FF]">
                        </div>

                        <select name="status" aria-label="Filter by status" class="{{ $inputClass }}">
                            <option value="">All statuses</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                        </select>

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="inline-flex flex-1 items-center justify-center rounded-[12px] bg-[#5B9DF9] px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-[#4E91F0]">
                                Filter
                            </button>
                            @if ($search || $status)
                                <a href="{{ route('clinic.services.index') }}"
                                   class="inline-flex items-center justify-center rounded-[12px] bg-[#F1F5F9] px-3.5 py-3 text-[13px] font-semibold text-[#718096] transition hover:bg-[#E5EBF0]">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto border-t border-[#E9EFF4]">
                    <table class="w-full min-w-[960px]">
                        <thead>
                            <tr class="bg-[#FAFCFD]">
                                @foreach (['Service', 'Duration', 'Price', 'Used', 'Status'] as $heading)
                                    <th class="px-6 py-3.5 text-left text-[10px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">{{ $heading }}</th>
                                @endforeach
                                <th class="px-6 py-3.5 text-right text-[10px] font-semibold uppercase tracking-[0.1em] text-[#91A0B2]">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#EDF2F6]">
                            @forelse ($services as $service)
                                <tr class="transition hover:bg-[#FBFDFF]">

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 min-w-[64px] shrink-0 items-center justify-center rounded-[12px] bg-[#E8F3FF] px-2 text-[#5B9DF9]">
                                                <span class="text-[11px] font-bold">{{ $service->code }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-[13px] font-semibold text-[#263550]">{{ $service->name }}</p>
                                                <p class="mt-1 text-[11px] text-[#9BA8B7]">{{ $service->category ?: 'Uncategorised' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-[13px] text-[#5F6C7F]">
                                        {{ $service->duration_minutes }} min
                                    </td>

                                    <td class="px-6 py-4 text-[13px] font-medium text-[#263550]">
                                        {{ $money($service->price) }}
                                    </td>

                                    <td class="px-6 py-4 text-[13px] text-[#5F6C7F]">
                                        {{ $service->appointments_count }}
                                        {{ $service->appointments_count === 1 ? 'appointment' : 'appointments' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-[11px] font-semibold {{ $statusStyles[$service->status] ?? $statusStyles['inactive'] }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ ucfirst($service->status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap items-center justify-end gap-1.5">
                                            @if ($canManage)
                                                <a href="{{ route('clinic.services.edit', $service) }}"
                                                   class="inline-flex items-center justify-center rounded-[9px] bg-[#E8F3FF] px-3 py-2 text-[11px] font-semibold text-[#5B9DF9] transition hover:bg-[#DDEEFF]">
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('clinic.services.toggle', $service) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-[9px] border border-[#DCE6EE] bg-white px-3 py-2 text-[11px] font-semibold text-[#718096] transition hover:bg-[#F8FAFC]">
                                                        {{ $service->isActive() ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('clinic.services.destroy', $service) }}"
                                                      onsubmit="return confirm('Delete this service permanently?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center rounded-[9px] border border-[#F4D5DC] bg-white px-3 py-2 text-[11px] font-semibold text-[#D97791] transition hover:bg-[#FFF6F8]">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[11px] font-medium text-[#A0ACBA]">View only</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16">
                                        <div class="mx-auto max-w-md text-center">
                                            <h3 class="text-sm font-semibold text-[#263550]">No services found</h3>
                                            <p class="mt-1.5 text-[13px] leading-6 text-[#8A99AC]">
                                                @if ($search || $status)
                                                    No services matched your search or filter.
                                                @else
                                                    Add the treatments your clinic offers, such as scaling, fillings or extractions.
                                                @endif
                                            </p>

                                            @if ($canManage && ! $search && ! $status)
                                                <a href="{{ route('clinic.services.create') }}"
                                                   class="mt-4 inline-flex rounded-[10px] bg-[#5B9DF9] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#4E91F0]">
                                                    Add your first service
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($services->hasPages())
                    <div class="border-t border-[#E7EDF2] bg-[#FCFDFE] px-5 py-4 sm:px-6">
                        {{ $services->links() }}
                    </div>
                @endif

                <div class="flex flex-col gap-1.5 border-t border-[#E7EDF2] bg-white px-5 py-3.5 text-[11px] text-[#9BA8B7] sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p>
                        Showing
                        <span class="font-semibold text-[#5F6C7F]">{{ $services->firstItem() ?? 0 }}</span>
                        to
                        <span class="font-semibold text-[#5F6C7F]">{{ $services->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-semibold text-[#5F6C7F]">{{ $services->total() }}</span>
                        services
                    </p>
                    <p>{{ $tenant->name }} · Service Catalogue</p>
                </div>
            </section>
        </main>
    </div>
</x-app-layout>