<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FB] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">

            {{-- Header --}}
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-[0.25em] text-[#5B9DF9]">
                        DENTRA / DOCTORS
                    </p>

                    <h1 class="text-3xl font-bold tracking-tight text-[#263550]">
                        Edit Doctor
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Update the doctor's professional and contact information.
                    </p>
                </div>

                <a href="{{ route('clinic.doctors.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-[#DCE7F0] bg-white px-5 py-3 text-sm font-semibold text-[#263550] transition hover:bg-slate-50">
                    Back to Doctors
                </a>
            </div>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5">
                    <p class="mb-2 font-semibold text-red-700">
                        Please correct the following errors:
                    </p>

                    <ul class="list-inside list-disc space-y-1 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('clinic.doctors.update', $doctor) }}"
                  method="POST"
                  class="overflow-hidden rounded-3xl border border-[#DCE7F0] bg-white shadow-sm">

                @csrf
                @method('PUT')

                {{-- Form Heading --}}
                <div class="border-b border-[#DCE7F0] bg-gradient-to-r from-[#EAF3FF] to-[#F7FAFF] px-6 py-6 sm:px-8">
                    <h2 class="text-lg font-bold text-[#263550]">
                        Professional Information
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Update the information below and save your changes.
                    </p>
                </div>

                <div class="space-y-8 p-6 sm:p-8">

                    {{-- Identity --}}
                    <section>
                        <h3 class="mb-5 text-sm font-bold uppercase tracking-wider text-[#5B9DF9]">
                            Doctor Details
                        </h3>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <div>
                                <label for="doctor_code"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Doctor Code <span class="text-red-500">*</span>
                                </label>

                                <input id="doctor_code"
                                       name="doctor_code"
                                       type="text"
                                       maxlength="30"
                                       required
                                       value="{{ old('doctor_code', $doctor->doctor_code) }}"
                                       class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                @error('doctor_code')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="name"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Full Name <span class="text-red-500">*</span>
                                </label>

                                <input id="name"
                                       name="name"
                                       type="text"
                                       maxlength="255"
                                       required
                                       value="{{ old('name', $doctor->name) }}"
                                       class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="specialization"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Specialization
                                </label>

                                <input id="specialization"
                                       name="specialization"
                                       type="text"
                                       maxlength="255"
                                       value="{{ old('specialization', $doctor->specialization) }}"
                                       placeholder="e.g. General Dentist"
                                       class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                @error('specialization')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="license_number"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Professional License Number
                                </label>

                                <input id="license_number"
                                       name="license_number"
                                       type="text"
                                       maxlength="100"
                                       value="{{ old('license_number', $doctor->license_number) }}"
                                       placeholder="Enter license number"
                                       class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                @error('license_number')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="education"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Education
                                </label>

                                <input id="education"
                                       name="education"
                                       type="text"
                                       maxlength="255"
                                       value="{{ old('education', $doctor->education) }}"
                                       placeholder="e.g. Bachelor of Dentistry, Professional Dentist Program"
                                       class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                @error('education')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </section>

                    {{-- Contact --}}
                    <section class="border-t border-[#DCE7F0] pt-8">
                        <h3 class="mb-5 text-sm font-bold uppercase tracking-wider text-[#5B9DF9]">
                            Contact & Branch
                        </h3>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <div>
                                <label for="email"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Email Address
                                </label>

                                <input id="email"
                                       name="email"
                                       type="email"
                                       maxlength="255"
                                       value="{{ old('email', $doctor->email) }}"
                                       placeholder="doctor@example.com"
                                       class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Phone Number
                                </label>

                                <input id="phone"
                                       name="phone"
                                       type="text"
                                       maxlength="30"
                                       value="{{ old('phone', $doctor->phone) }}"
                                       placeholder="Enter phone number"
                                       class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="branch_id"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Clinic Branch
                                </label>

                                <select id="branch_id"
                                        name="branch_id"
                                        class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                    <option value="">No branch assigned</option>

                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            @selected((string) old('branch_id', $doctor->branch_id) === (string) $branch->id)>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('branch_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status"
                                       class="mb-2 block text-sm font-semibold text-[#263550]">
                                    Account Status <span class="text-red-500">*</span>
                                </label>

                                <select id="status"
                                        name="status"
                                        required
                                        class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                                    <option value="active"
                                        @selected(old('status', $doctor->status) === 'active')>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        @selected(old('status', $doctor->status) === 'inactive')>
                                        Inactive
                                    </option>
                                </select>

                                @error('status')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </section>

                    {{-- Biography --}}
                    <section class="border-t border-[#DCE7F0] pt-8">
                        <h3 class="mb-5 text-sm font-bold uppercase tracking-wider text-[#5B9DF9]">
                            Additional Information
                        </h3>

                        <label for="bio"
                               class="mb-2 block text-sm font-semibold text-[#263550]">
                            Biography
                        </label>

                        <textarea id="bio"
                                  name="bio"
                                  rows="5"
                                  maxlength="5000"
                                  placeholder="Write a short professional biography..."
                                  class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm text-[#263550] outline-none transition focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">{{ old('bio', $doctor->bio) }}</textarea>

                        @error('bio')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </section>

                </div>

                {{-- Actions --}}
                <div class="flex flex-col-reverse justify-end gap-3 border-t border-[#DCE7F0] bg-slate-50/70 px-6 py-5 sm:flex-row sm:px-8">
                    <a href="{{ route('clinic.doctors.index') }}"
                       class="rounded-xl border border-[#DCE7F0] bg-white px-6 py-3 text-center text-sm font-semibold text-[#263550] transition hover:bg-slate-50">
                        Cancel
                    </a>

                    <button type="submit"
                            class="rounded-xl bg-[#5B9DF9] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
