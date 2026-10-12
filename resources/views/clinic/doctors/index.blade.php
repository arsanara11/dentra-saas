<x-app-layout>
    <main class="w-full space-y-6 px-0 py-0">

        {{-- Header --}}
        <section class="relative overflow-hidden rounded-[28px] border border-[#DCE7F0] bg-white p-6 sm:p-8">
            <div class="absolute -right-12 -top-16 h-48 w-48 rounded-full bg-[#DCEBFF] opacity-60 blur-3xl"></div>

            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#5B9DF9]">
                        DENTRA / CLINIC MANAGEMENT
                    </p>

                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-[#263550] sm:text-4xl">
                        Doctors
                    </h1>

                    <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">
                        Manage your dental team, professional information, and clinic assignments.
                    </p>
                </div>

                @if (auth()->user()->hasPermission('doctors.manage', $tenant))
                    <a href="{{ route('clinic.doctors.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#5B9DF9] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-[#4789E8]">
                        <span class="text-lg leading-none">+</span>
                        Add Doctor
                    </a>
                @endif
            </div>
        </section>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                <p class="font-bold">Something went wrong.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Statistics --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-[24px] border border-[#DCE7F0] bg-white p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Total Doctors</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-lg">🩺</span>
                </div>

                <p class="mt-4 text-3xl font-bold tracking-tight text-[#263550]">
                    {{ number_format($summary['total']) }}
                </p>

                <p class="mt-1 text-xs text-slate-400">Registered in this clinic</p>
            </div>

            <div class="rounded-[24px] border border-[#DCE7F0] bg-white p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Active Doctors</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-lg">✓</span>
                </div>

                <p class="mt-4 text-3xl font-bold tracking-tight text-emerald-600">
                    {{ number_format($summary['active']) }}
                </p>

                <p class="mt-1 text-xs text-slate-400">Available doctor profiles</p>
            </div>

            <div class="rounded-[24px] border border-[#DCE7F0] bg-white p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Inactive Doctors</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-lg">—</span>
                </div>

                <p class="mt-4 text-3xl font-bold tracking-tight text-slate-500">
                    {{ number_format($summary['inactive']) }}
                </p>

                <p class="mt-1 text-xs text-slate-400">Currently inactive</p>
            </div>
        </section>

        {{-- Doctor Directory --}}
        <section class="overflow-hidden rounded-[28px] border border-[#DCE7F0] bg-white">

            <div class="border-b border-[#E8EEF4] p-5 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-[#263550]">Doctor Directory</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Search and manage registered doctors.
                        </p>
                    </div>

                    <form action="{{ route('clinic.doctors.index') }}"
                          method="GET"
                          class="flex flex-col gap-3 sm:flex-row">

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search name, code, specialty..."
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100 sm:w-72"
                        >

                        <select
                            name="status"
                            class="rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">
                            <option value="">All statuses</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                        </select>

                        <button type="submit"
                                class="rounded-xl bg-[#263550] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#354767]">
                            Search
                        </button>

                        @if ($search !== '' || $status !== '')
                            <a href="{{ route('clinic.doctors.index') }}"
                               class="rounded-xl border border-[#DCE7F0] px-4 py-3 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="bg-[#F7F9FC]">
                        <tr class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-400">
                            <th class="px-6 py-4">Doctor</th>
                            <th class="px-6 py-4">Specialization</th>
                            <th class="px-6 py-4">Branch</th>
                            <th class="px-6 py-4">Contact</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#EDF1F5]">
                        @forelse ($doctors as $doctor)
                            <tr class="transition hover:bg-[#FAFCFF]">

                                {{-- Doctor Identity --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#EAF2FF] text-sm font-bold text-[#4789E8]">
                                            {{ collect(explode(' ', trim($doctor->name)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}
                                        </div>

                                        <div>
                                            <a href="{{ route('clinic.doctors.show', $doctor) }}"
                                               class="font-bold text-[#263550] transition hover:text-[#4789E8]">
                                                {{ $doctor->name }}
                                            </a>

                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $doctor->doctor_code }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Specialization --}}
                                <td class="px-6 py-5">
                                    <span class="text-sm font-medium text-slate-600">
                                        {{ $doctor->specialization ?: 'General Dentist' }}
                                    </span>

                                    @if ($doctor->license_number)
                                        <p class="mt-1 text-xs text-slate-400">
                                            License: {{ $doctor->license_number }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Branch --}}
                                <td class="px-6 py-5 text-sm text-slate-600">
                                    {{ $doctor->branch?->name ?? 'All / Unassigned' }}
                                </td>

                                {{-- Contact --}}
                                <td class="px-6 py-5">
                                    <p class="text-sm text-slate-600">
                                        {{ $doctor->email ?: '—' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $doctor->phone ?: 'No phone' }}
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-5">
                                    @if ($doctor->status === 'active')
                                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-end gap-2">

                                        @if (auth()->user()->hasPermission('doctors.view', $tenant))
                                            <a href="{{ route('clinic.doctors.show', $doctor) }}"
                                               class="rounded-lg border border-[#DCE7F0] px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-blue-50 hover:text-[#4789E8]">
                                                View
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasPermission('doctors.manage', $tenant))
                                            <a href="{{ route('clinic.doctors.edit', $doctor) }}"
                                               class="rounded-lg bg-[#EAF2FF] px-3 py-2 text-xs font-semibold text-[#4789E8] transition hover:bg-blue-100">
                                                Edit
                                            </a>

                                            <form action="{{ route('clinic.doctors.destroy', $doctor) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete this doctor profile?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="rounded-lg border border-rose-100 px-3 py-2 text-xs font-semibold text-rose-500 transition hover:bg-rose-50">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F1F5F9] text-2xl">
                                        🩺
                                    </div>

                                    <h3 class="mt-4 font-bold text-[#263550]">
                                        No doctors found
                                    </h3>

                                    <p class="mt-2 text-sm text-slate-500">
                                        @if ($search !== '' || $status !== '')
                                            Try changing your search or filter.
                                        @else
                                            Add your first doctor to start building your clinic directory.
                                        @endif
                                    </p>

                                    @if (
                                        $search === '' &&
                                        $status === '' &&
                                        auth()->user()->hasPermission('doctors.manage', $tenant)
                                    )
                                        <a href="{{ route('clinic.doctors.create') }}"
                                           class="mt-5 inline-flex rounded-xl bg-[#5B9DF9] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#4789E8]">
                                            Add First Doctor
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($doctors->hasPages())
                <div class="border-t border-[#E8EEF4] px-5 py-4 sm:px-6">
                    {{ $doctors->links() }}
                </div>
            @endif

        </section>
    </main>
</x-app-layout>