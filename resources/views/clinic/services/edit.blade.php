<x-app-layout>
    <div class="min-h-screen bg-[#EEF4F8]">
        <main class="w-full max-w-none space-y-4 px-0 py-0">

            {{-- HERO --}}
            <section class="relative overflow-hidden rounded-[26px] border border-white bg-white shadow-[0_6px_24px_rgba(52,75,98,0.04)]">
                <div class="pointer-events-none absolute inset-y-0 right-0 w-[24%] overflow-hidden">
                    <div class="absolute -right-20 -top-32 h-[290px] w-[290px] rounded-full bg-[#E8F4FF]/80"></div>
                    <div class="absolute -right-5 top-7 h-[180px] w-[180px] rounded-full bg-[#F2F8FF]"></div>
                </div>

                <div class="relative z-10 px-5 py-5 sm:px-7 sm:py-6">
                    <nav class="flex items-center gap-2 text-[13px] font-medium">
                        <a href="{{ route('dashboard') }}" class="text-[#94A3B8] transition hover:text-[#5B9DF9]">Clinic</a>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#B7C1CE]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                        </svg>
                        <a href="{{ route('clinic.services.index') }}" class="text-[#94A3B8] transition hover:text-[#5B9DF9]">Services</a>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#B7C1CE]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                        </svg>
                        <span class="text-[#5B9DF9]">Edit</span>
                    </nav>

                    <h1 class="mt-4 text-[32px] font-semibold leading-tight tracking-[-0.045em] text-[#25324A] sm:text-[38px]">
                        Edit Service
                    </h1>

                    <p class="mt-2 max-w-[620px] text-sm leading-6 text-[#8A99AC]">
                        Changes apply to future bookings. Existing appointments keep their details.
                    </p>
                </div>
            </section>

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

            <section class="overflow-hidden rounded-[26px] border border-[#D8E4ED] bg-white shadow-[0_6px_24px_rgba(52,75,98,0.04)]">
                @include('clinic.services._form', [
                    'service' => $service,
                    'categories' => $categories,
                    'tenant' => $tenant,
                    'action' => route('clinic.services.update', $service),
                    'method' => 'PUT',
                    'submitLabel' => 'Save Changes',
                ])
            </section>

        </main>
    </div>
</x-app-layout>