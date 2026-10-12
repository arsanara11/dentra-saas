<x-app-layout>
    <main class="w-full space-y-6">

        {{-- Header --}}
        <section class="relative overflow-hidden rounded-[28px] border border-[#DCE7F0] bg-white p-6 sm:p-8">
            <div class="absolute -right-12 -top-16 h-48 w-48 rounded-full bg-[#DCEBFF] opacity-60 blur-3xl"></div>

            <div class="relative">
                <a href="{{ route('clinic.doctors.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-[#4789E8]">
                    <span>←</span> Back to Doctors
                </a>

                <p class="mt-5 text-xs font-bold uppercase tracking-[0.24em] text-[#5B9DF9]">
                    DENTRA / CLINIC MANAGEMENT
                </p>

                <h1 class="mt-3 text-3xl font-bold tracking-tight text-[#263550] sm:text-4xl">
                    Add New Doctor
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">
                    Register a dental professional and configure their clinic assignment.
                </p>
            </div>
        </section>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <section class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                <p class="font-bold">Please review the following information:</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Form --}}
        <form action="{{ route('clinic.doctors.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Professional Information --}}
            <section class="overflow-hidden rounded-[28px] border border-[#DCE7F0] bg-white">
                <div class="border-b border-[#E8EEF4] px-6 py-5 sm:px-8">
                    <h2 class="text-lg font-bold text-[#263550]">Professional Information</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Basic identity and professional credentials.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2 sm:p-8">

                    {{-- Doctor Code --}}
                    <div>
                        <label for="doctor_code" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Doctor Code <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="doctor_code"
                            name="doctor_code"
                            type="text"
                            value="{{ old('doctor_code') }}"
                            maxlength="30"
                            required
                            placeholder="e.g. DCT-001"
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                        @error('doctor_code')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            maxlength="255"
                            required
                            placeholder="e.g. drg. Amanda Putri"
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                        @error('name')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Specialization --}}
                    <div>
                        <label for="specialization" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Specialization
                        </label>
                        <input
                            id="specialization"
                            name="specialization"
                            type="text"
                            value="{{ old('specialization') }}"
                            maxlength="255"
                            placeholder="e.g. Orthodontics, General Dentistry"
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                        @error('specialization')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- License --}}
                    <div>
                        <label for="license_number" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Professional License Number
                        </label>
                        <input
                            id="license_number"
                            name="license_number"
                            type="text"
                            value="{{ old('license_number') }}"
                            maxlength="100"
                            placeholder="Enter license number"
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                        @error('license_number')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Education --}}
                    <div class="sm:col-span-2">
                        <label for="education" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Education
                        </label>
                        <input
                            id="education"
                            name="education"
                            type="text"
                            value="{{ old('education') }}"
                            maxlength="255"
                            placeholder="e.g. Bachelor of Dental Surgery"
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                        @error('education')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Biography --}}
                    <div class="sm:col-span-2">
                        <label for="bio" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Professional Biography
                        </label>
                        <textarea
                            id="bio"
                            name="bio"
                            rows="4"
                            maxlength="5000"
                            placeholder="Write a short professional introduction..."
                            class="w-full resize-y rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >{{ old('bio') }}</textarea>
                        @error('bio')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Contact and Assignment --}}
            <section class="overflow-hidden rounded-[28px] border border-[#DCE7F0] bg-white">
                <div class="border-b border-[#E8EEF4] px-6 py-5 sm:px-8">
                    <h2 class="text-lg font-bold text-[#263550]">Contact & Clinic Assignment</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Contact details and the branch where the doctor works.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2 sm:p-8">

                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Email Address
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            maxlength="255"
                            placeholder="doctor@example.com"
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                        @error('email')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Phone Number
                        </label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone') }}"
                            maxlength="30"
                            placeholder="e.g. 081234567890"
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition placeholder:text-slate-400 focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                        @error('phone')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Branch --}}
                    <div>
                        <label for="branch_id" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Clinic Branch
                        </label>
                        <select
                            id="branch_id"
                            name="branch_id"
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">Unassigned / All Branches</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>
                                    {{ $branch->name }}{{ $branch->is_main ? ' (Main Branch)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="mb-2 block text-sm font-semibold text-[#263550]">
                            Profile Status <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            required
                            class="w-full rounded-xl border border-[#DCE7F0] bg-[#F8FAFC] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="active" @selected(old('status', 'active') === 'active')>
                                Active
                            </option>
                            <option value="inactive" @selected(old('status') === 'inactive')>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Actions --}}
            <section class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('clinic.doctors.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-[#DCE7F0] bg-white px-6 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Cancel
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#5B9DF9] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-[#4789E8] focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2"
                >
                    <span>+</span>
                    Save Doctor
                </button>
            </section>

        </form>
    </main>
</x-app-layout>