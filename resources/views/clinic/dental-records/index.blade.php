<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FB] px-4 py-8 sm:px-6 lg:px-10">
        <div class="mx-auto max-w-[1600px] space-y-7">

            {{-- Header --}}
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#5B9DF9]">
                        Clinical Management
                    </p>

                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#263550]">
                        Dental Records
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Kelola riwayat pemeriksaan dan catatan klinis pasien.
                    </p>
                </div>

                @if(auth()->user()->hasPermission('dental_records.create', app(\App\Support\TenantContext::class)->require()))
                    <a href="{{ route('clinic.dental-records.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#5B9DF9] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-200/60 transition hover:bg-[#4388EB]">
                        <span class="text-lg leading-none">+</span>
                        New Dental Record
                    </a>
                @endif
            </div>

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-[28px] border border-[#E1EAF4] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Records</p>
                            <h2 class="mt-3 text-3xl font-bold text-[#263550]">
                                {{ number_format($records->total()) }}
                            </h2>
                            <p class="mt-2 text-xs text-slate-400">Sesuai filter aktif</p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                            🦷
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-[#E1EAF4] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Current Page</p>
                            <h2 class="mt-3 text-3xl font-bold text-[#263550]">
                                {{ $records->count() }}
                            </h2>
                            <p class="mt-2 text-xs text-slate-400">Record pada halaman ini</p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                            📋
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-[#E1EAF4] bg-white p-6 shadow-sm sm:col-span-2 xl:col-span-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Records Per Page</p>
                            <h2 class="mt-3 text-3xl font-bold text-[#263550]">10</h2>
                            <p class="mt-2 text-xs text-slate-400">Pagination otomatis</p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-2xl">
                            📁
                        </div>
                    </div>
                </div>
            </div>

            {{-- Records Table --}}
            <div class="overflow-hidden rounded-[28px] border border-[#E1EAF4] bg-white shadow-sm">

                {{-- Table Header --}}
                <div class="border-b border-[#E8EEF5] px-6 py-6 sm:px-8">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-[#263550]">
                                Examination History
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Daftar pemeriksaan klinis pasien.
                            </p>
                        </div>

                        <span class="w-fit rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-[#4388EB]">
                            {{ $records->total() }} records found
                        </span>
                    </div>
                </div>

                {{-- Search & Filters --}}
                <form method="GET"
                      action="{{ route('clinic.dental-records.index') }}"
                      class="grid grid-cols-1 gap-3 border-b border-[#E8EEF5] bg-[#FBFCFE] p-5 sm:grid-cols-2 xl:grid-cols-4">

                    <div class="xl:col-span-2">
                        <label for="search" class="mb-2 block text-xs font-semibold text-slate-600">
                            Search Records
                        </label>

                        <input id="search"
                               type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Nama pasien, kode pasien, dokter, diagnosis..."
                               class="w-full rounded-xl border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-[#5B9DF9]">
                    </div>

                    <div>
                        <label for="date_from" class="mb-2 block text-xs font-semibold text-slate-600">
                            From Date
                        </label>

                        <input id="date_from"
                               type="date"
                               name="date_from"
                               value="{{ request('date_from') }}"
                               class="w-full rounded-xl border-[#DCE7F0] bg-white px-4 py-3 text-sm focus:border-[#5B9DF9] focus:ring-[#5B9DF9]">
                    </div>

                    <div>
                        <label for="date_to" class="mb-2 block text-xs font-semibold text-slate-600">
                            To Date
                        </label>

                        <input id="date_to"
                               type="date"
                               name="date_to"
                               value="{{ request('date_to') }}"
                               class="w-full rounded-xl border-[#DCE7F0] bg-white px-4 py-3 text-sm focus:border-[#5B9DF9] focus:ring-[#5B9DF9]">
                    </div>

                    <div class="flex flex-wrap items-center gap-2 sm:col-span-2 xl:col-span-4">
                        <button type="submit"
                                class="rounded-xl bg-[#263550] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#354968]">
                            Apply Filters
                        </button>

                        <a href="{{ route('clinic.dental-records.index') }}"
                           class="rounded-xl border border-[#DCE7F0] bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>

                {{-- Responsive Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] text-left">
                        <thead>
                            <tr class="border-b border-[#E8EEF5] bg-white">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Examination
                                </th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Patient
                                </th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Dentist
                                </th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Diagnosis
                                </th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Next Visit
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#EEF2F7]">
                            @forelse($records as $record)
                                <tr class="transition hover:bg-[#F8FAFD]">
                                    <td class="px-6 py-5">
                                        <p class="text-sm font-semibold text-[#263550]">
                                            {{ $record->examined_at?->format('d M Y') ?? '—' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $record->examined_at?->format('H:i') ?? '' }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-5">
                                        <p class="text-sm font-semibold text-[#263550]">
                                            {{ $record->patient?->name ?? 'Patient unavailable' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $record->patient?->patient_code ?? 'No patient code' }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-5">
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $record->doctor?->name ?? '—' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $record->doctor?->specialization ?? 'Dentist' }}
                                        </p>
                                    </td>

                                    <td class="max-w-[260px] px-6 py-5">
                                        <p class="line-clamp-2 text-sm text-slate-600">
                                            {{ $record->diagnosis ?: 'Belum ada diagnosis' }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-5">
                                        @if($record->next_visit_at)
                                            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                                                {{ $record->next_visit_at->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-sm text-slate-400">Not scheduled</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('clinic.dental-records.show', $record) }}"
                                               class="rounded-lg border border-[#DCE7F0] px-3 py-2 text-xs font-semibold text-[#4388EB] transition hover:bg-blue-50">
                                                View
                                            </a>

                                            @if(auth()->user()->hasPermission('dental_records.update', app(\App\Support\TenantContext::class)->require()))
                                                <a href="{{ route('clinic.dental-records.edit', $record) }}"
                                                   class="rounded-lg border border-[#DCE7F0] px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-3xl">
                                            🦷
                                        </div>

                                        <h3 class="mt-4 text-base font-bold text-[#263550]">
                                            No dental records found
                                        </h3>

                                        <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                                            Belum ada rekam medis yang cocok dengan pencarian lu.
                                            Coba ubah filter atau buat record baru.
                                        </p>

                                        @if(auth()->user()->hasPermission('dental_records.create', app(\App\Support\TenantContext::class)->require()))
                                            <a href="{{ route('clinic.dental-records.create') }}"
                                               class="mt-5 inline-flex rounded-xl bg-[#5B9DF9] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#4388EB]">
                                                Create First Record
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($records->hasPages())
                    <div class="border-t border-[#E8EEF5] px-6 py-5 sm:px-8">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>

            <p class="px-1 text-xs text-slate-400">
                DENTRA Clinical Management · Patient records are restricted to your active clinic.
            </p>
        </div>
    </div>
</x-app-layout>