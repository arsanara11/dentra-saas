<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-gray-500">
                Clinic Workspace
            </p>

            <h2 class="mt-1 text-2xl font-semibold text-gray-900">
                {{ $tenant->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl bg-gray-950 p-8 text-white shadow-xl">

                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                    <div>
                        <p class="text-sm font-medium text-indigo-400">
                            DENTRA CLINIC
                        </p>

                        <h1 class="mt-2 text-3xl font-bold tracking-tight">
                            {{ $tenant->name }}
                        </h1>

                        <p class="mt-2 text-gray-400">
                            Your dental clinic workspace is ready.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
                        <p class="text-xs uppercase tracking-wider text-gray-400">
                            Active Tenant
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $tenant->name }}
                        </p>
                    </div>

                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-gray-400">
                            Status
                        </p>

                        <p class="mt-2 text-xl font-semibold capitalize">
                            {{ $tenant->status }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-gray-400">
                            Currency
                        </p>

                        <p class="mt-2 text-xl font-semibold">
                            {{ $tenant->currency }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-gray-400">
                            Active Branches
                        </p>

                        <p class="mt-2 text-xl font-semibold">
                            {{ $branches->count() }}
                        </p>
                    </div>

                </div>
            </div>

            <div class="mt-8">

                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Clinic Branches
                    </h3>

                    <p class="text-sm text-gray-500">
                        Branches belonging to this clinic.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">

                    @forelse ($branches as $branch)

                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">

                            <div class="flex items-start justify-between gap-4">

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500">
                                        {{ $branch->code }}
                                    </p>

                                    <h4 class="mt-2 text-lg font-semibold text-gray-900">
                                        {{ $branch->name }}
                                    </h4>
                                </div>

                                @if ($branch->is_main)
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600">
                                        Main
                                    </span>
                                @endif

                            </div>

                            <div class="mt-5 space-y-2 text-sm text-gray-500">

                                <p>
                                    {{ $branch->address }}
                                </p>

                                <p>
                                    {{ $branch->city }}, {{ $branch->province }}
                                </p>

                                <p>
                                    {{ $branch->phone ?? 'No phone number' }}
                                </p>

                            </div>

                        </div>

                    @empty

                        <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-8 text-center md:col-span-2 lg:col-span-3">
                            <p class="font-medium text-gray-700">
                                No active branches found.
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Add a branch to start managing this clinic.
                            </p>
                        </div>

                    @endforelse

                </div>

            </div>

        </div>
    </div>
</x-app-layout>