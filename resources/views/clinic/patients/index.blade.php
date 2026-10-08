<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Patients
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage patients for {{ $tenant->name }}
                </p>
            </div>

            @canDent('patients.create')
                <a
                    href="{{ route('clinic.patients.create') }}"
                    class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700"
                >
                    + Add Patient
                </a>
            @endcanDent
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header Card --}}
            <div class="mb-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                <div class="p-6">

                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Patient Directory
                            </p>

                            <h3 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">
                                {{ $patients->total() }}
                                <span class="text-gray-400">
                                    patients
                                </span>
                            </h3>
                        </div>

                        {{-- Search --}}
                        <form
                            method="GET"
                            action="{{ route('clinic.patients.index') }}"
                            class="w-full lg:w-[380px]"
                        >
                            <div class="relative">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Search patient..."
                                    class="w-full rounded-2xl border-gray-200 bg-gray-50 px-4 py-3 pr-24 text-sm text-gray-900 outline-none transition focus:border-gray-400 focus:ring-0"
                                >

                                <button
                                    type="submit"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-xl bg-gray-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-gray-700"
                                >
                                    Search
                                </button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>

            {{-- Patients Table --}}
            <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-100">

                        <thead class="bg-gray-50">
                            <tr>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Patient
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Patient No.
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Gender
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Phone
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse ($patients as $patient)

                                <tr class="transition hover:bg-gray-50">

                                    {{-- Patient --}}
                                    <td class="whitespace-nowrap px-6 py-5">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-sm font-semibold text-gray-700">
                                                {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                                            </div>

                                            <div>
                                                <p class="font-semibold text-gray-900">
                                                    {{ $patient->full_name }}
                                                </p>

                                                @if ($patient->email)
                                                    <p class="mt-0.5 text-xs text-gray-500">
                                                        {{ $patient->email }}
                                                    </p>
                                                @endif
                                            </div>

                                        </div>

                                    </td>

                                    {{-- Patient Number --}}
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <span class="font-mono text-sm text-gray-600">
                                            {{ $patient->patient_number }}
                                        </span>
                                    </td>

                                    {{-- Gender --}}
                                    <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-600">
                                        {{ $patient->gender ? ucfirst($patient->gender) : '—' }}
                                    </td>

                                    {{-- Phone --}}
                                    <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-600">
                                        {{ $patient->phone ?: '—' }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-6 py-5">

                                        @if ($patient->status === 'active')

                                            <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                                Inactive
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Action --}}
                                    <td class="whitespace-nowrap px-6 py-5 text-right">

                                        @canDent('patients.view')
                                            <a
                                                href="{{ route('clinic.patients.show', $patient) }}"
                                                class="text-sm font-semibold text-gray-700 transition hover:text-gray-900"
                                            >
                                                View
                                            </a>
                                        @endcanDent

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">

                                        <div class="mx-auto max-w-sm">

                                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-xl">
                                                👤
                                            </div>

                                            <h3 class="mt-4 text-base font-semibold text-gray-900">
                                                No patients found
                                            </h3>

                                            <p class="mt-1 text-sm text-gray-500">
                                                Start by adding your first patient to {{ $tenant->name }}.
                                            </p>

                                            @canDent('patients.create')
                                                <a
                                                    href="{{ route('clinic.patients.create') }}"
                                                    class="mt-5 inline-flex items-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700"
                                                >
                                                    Add First Patient
                                                </a>
                                            @endcanDent

                                        </div>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- Pagination --}}
                @if ($patients->hasPages())

                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $patients->links() }}
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>