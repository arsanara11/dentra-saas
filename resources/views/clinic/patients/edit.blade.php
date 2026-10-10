<x-app-layout>
    <div class="min-h-screen bg-[#EEF4F8]">

        {{-- PAGE HEADER --}}
        <div class="border-b border-[#E4EAF0] bg-white">
            <div class="mx-auto max-w-[1500px] px-6 py-6 lg:px-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-medium text-[#96A2B3]">
                            <a href="{{ route('clinic.patients.index') }}"
                               class="transition hover:text-[#5B9DF9]">
                                Patients
                            </a>

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-3.5 w-3.5"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m9 5 7 7-7 7"/>
                            </svg>

                            <a href="{{ route('clinic.patients.show', $patient) }}"
                               class="transition hover:text-[#5B9DF9]">
                                Patient Details
                            </a>

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-3.5 w-3.5"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m9 5 7 7-7 7"/>
                            </svg>

                            <span class="text-[#5B9DF9]">Edit Patient</span>
                        </div>

                        <p class="mt-3 text-sm font-medium text-[#8996A8]">
                            {{ $tenant->name }}
                        </p>

                        <h1 class="mt-1 text-2xl font-semibold tracking-[-0.035em] text-[#273047] sm:text-[30px]">
                            Edit Patient
                        </h1>

                        <p class="mt-2 text-sm leading-6 text-[#8996A8]">
                            Update patient contact, personal, and medical information.
                        </p>
                    </div>

                    <a href="{{ route('clinic.patients.show', $patient) }}"
                       class="inline-flex items-center justify-center gap-2 self-start rounded-xl border border-[#DCE5ED] bg-white px-4 py-2.5 text-sm font-semibold text-[#59657A] transition hover:bg-[#F7FAFC] sm:self-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-4 w-4"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 12H5m7 7-7-7 7-7"/>
                        </svg>
                        Back to Patient Details
                    </a>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <main class="mx-auto max-w-[1500px] px-5 py-7 sm:px-6 lg:px-8 lg:py-8">

            {{-- FORM INTRO --}}
            <div class="mb-6 flex items-start gap-4 rounded-[24px] border border-[#DCE8F1] bg-[#F5FAFF] p-5 sm:p-6">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-[#5B9DF9] shadow-sm ring-1 ring-[#E1EDFA]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2m2-1h4a2 2 0 012 2v2h-8V3a2 2 0 012-2z"/>
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-[#344057]">
                        Patient Information
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-[#8492A6]">
                        Update the information below and save your changes.
                        Fields marked
                        <span class="font-semibold text-[#E47777]">*</span>
                        are required.
                    </p>

                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-lg border border-[#DCE8F1] bg-white px-3 py-2 font-medium text-[#59657A]">
                            Patient No.
                            <span class="ml-1 font-semibold text-[#273047]">
                                {{ $patient->patient_number }}
                            </span>
                        </span>

                        <span class="rounded-lg border border-[#DCE8F1] bg-white px-3 py-2 font-medium text-[#59657A]">
                            Status:
                            <span class="ml-1 font-semibold {{ $patient->status === 'active' ? 'text-[#279B65]' : 'text-[#8996A8]' }}">
                                {{ ucfirst($patient->status) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-[#F2D4D4] bg-[#FFF7F7] p-5">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="mt-0.5 h-5 w-5 shrink-0 text-[#D96B6B]"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.7 3.86a2 2 0 00-3.4 0z"/>
                        </svg>

                        <div>
                            <p class="text-sm font-semibold text-[#A84444]">
                                Please check the form
                            </p>

                            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-[#B45C5C]">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- PATIENT FORM --}}
            <form action="{{ route('clinic.patients.update', $patient) }}"
                  method="POST"
                  class="space-y-6">

                @csrf
                @method('PUT')

                {{-- PERSONAL INFORMATION --}}
                <section class="overflow-hidden rounded-[26px] border border-[#DCE6EE] bg-white shadow-[0_10px_30px_rgba(35,48,71,0.035)]">
                    <div class="flex items-center gap-3 border-b border-[#EDF1F5] px-5 py-5 sm:px-7">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#E7F2FD] text-[#5B9DF9]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 19a4 4 0 00-8 0M11 11a4 4 0 100-8 4 4 0 000 8zm8 8a4 4 0 00-4-4m1-5a3 3 0 100-6"/>
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-[#273047]">Personal Information</h2>
                            <p class="mt-1 text-xs text-[#96A2B3]">Patient identity and basic details</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 px-5 py-6 sm:grid-cols-2 sm:px-7 lg:grid-cols-3">
                        <div>
                            <label for="first_name" class="mb-2 block text-sm font-medium text-[#48566B]">
                                First Name <span class="text-[#E47777]">*</span>
                            </label>
                            <input id="first_name" name="first_name" type="text"
                                   value="{{ old('first_name', $patient->first_name) }}"
                                   required maxlength="100" autocomplete="given-name"
                                   placeholder="Enter first name"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('first_name')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="mb-2 block text-sm font-medium text-[#48566B]">Last Name</label>
                            <input id="last_name" name="last_name" type="text"
                                   value="{{ old('last_name', $patient->last_name) }}"
                                   maxlength="100" autocomplete="family-name"
                                   placeholder="Enter last name"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('last_name')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="mb-2 block text-sm font-medium text-[#48566B]">Gender</label>
                            <select id="gender" name="gender"
                                    class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                                <option value="">Select gender</option>
                                <option value="male" @selected(old('gender', $patient->gender) === 'male')>Male</option>
                                <option value="female" @selected(old('gender', $patient->gender) === 'female')>Female</option>
                            </select>
                            @error('gender')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="mb-2 block text-sm font-medium text-[#48566B]">Date of Birth</label>
                            <input id="date_of_birth" name="date_of_birth" type="date"
                                   value="{{ old('date_of_birth', $patient->date_of_birth ? \Illuminate\Support\Carbon::parse($patient->date_of_birth)->format('Y-m-d') : '') }}"
                                   max="{{ now()->format('Y-m-d') }}" autocomplete="bday"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('date_of_birth')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-2 block text-sm font-medium text-[#48566B]">Phone Number</label>
                            <input id="phone" name="phone" type="tel"
                                   value="{{ old('phone', $patient->phone) }}"
                                   maxlength="30" autocomplete="tel"
                                   placeholder="e.g. 081234567890"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('phone')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-[#48566B]">Email Address</label>
                            <input id="email" name="email" type="email"
                                   value="{{ old('email', $patient->email) }}"
                                   maxlength="255" autocomplete="email"
                                   placeholder="patient@example.com"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- ADDRESS INFORMATION --}}
                <section class="overflow-hidden rounded-[26px] border border-[#DCE6EE] bg-white shadow-[0_10px_30px_rgba(35,48,71,0.035)]">
                    <div class="flex items-center gap-3 border-b border-[#EDF1F5] px-5 py-5 sm:px-7">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F2E7FC] text-[#8B7CFF]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z"/>
                                <circle cx="12" cy="10" r="2.5"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-[#273047]">Address Information</h2>
                            <p class="mt-1 text-xs text-[#96A2B3]">Where the patient lives</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 px-5 py-6 sm:grid-cols-2 sm:px-7 lg:grid-cols-3">
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label for="address" class="mb-2 block text-sm font-medium text-[#48566B]">Street Address</label>
                            <textarea id="address" name="address" rows="3"
                                      placeholder="Enter street, building, or house address"
                                      class="w-full resize-y rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">{{ old('address', $patient->address) }}</textarea>
                            @error('address')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="mb-2 block text-sm font-medium text-[#48566B]">City</label>
                            <input id="city" name="city" type="text"
                                   value="{{ old('city', $patient->city) }}"
                                   maxlength="100" placeholder="e.g. Bogor"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('city')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="province" class="mb-2 block text-sm font-medium text-[#48566B]">Province</label>
                            <input id="province" name="province" type="text"
                                   value="{{ old('province', $patient->province) }}"
                                   maxlength="100" placeholder="e.g. West Java"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('province')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="mb-2 block text-sm font-medium text-[#48566B]">Postal Code</label>
                            <input id="postal_code" name="postal_code" type="text"
                                   value="{{ old('postal_code', $patient->postal_code) }}"
                                   maxlength="10" autocomplete="postal-code"
                                   placeholder="e.g. 16128"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('postal_code')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- EMERGENCY CONTACT --}}
                <section class="overflow-hidden rounded-[26px] border border-[#DCE6EE] bg-white shadow-[0_10px_30px_rgba(35,48,71,0.035)]">
                    <div class="flex items-center gap-3 border-b border-[#EDF1F5] px-5 py-5 sm:px-7">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#FFF3E3] text-[#E9A24B]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 8v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.7 3.86a2 2 0 00-3.4 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-[#273047]">Emergency Contact</h2>
                            <p class="mt-1 text-xs text-[#96A2B3]">A person to contact when needed</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 px-5 py-6 sm:grid-cols-2 sm:px-7 lg:grid-cols-3">
                        <div>
                            <label for="emergency_contact_name" class="mb-2 block text-sm font-medium text-[#48566B]">Contact Name</label>
                            <input id="emergency_contact_name" name="emergency_contact_name" type="text"
                                   value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                                   maxlength="150" placeholder="Full name"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('emergency_contact_name')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="emergency_contact_phone" class="mb-2 block text-sm font-medium text-[#48566B]">Contact Phone</label>
                            <input id="emergency_contact_phone" name="emergency_contact_phone" type="tel"
                                   value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                                   maxlength="30" placeholder="Phone number"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('emergency_contact_phone')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="emergency_contact_relation" class="mb-2 block text-sm font-medium text-[#48566B]">Relationship</label>
                            <input id="emergency_contact_relation" name="emergency_contact_relation" type="text"
                                   value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation) }}"
                                   maxlength="100" placeholder="e.g. Parent, spouse"
                                   class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">
                            @error('emergency_contact_relation')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- MEDICAL INFORMATION --}}
                <section class="overflow-hidden rounded-[26px] border border-[#DCE6EE] bg-white shadow-[0_10px_30px_rgba(35,48,71,0.035)]">
                    <div class="flex items-center gap-3 border-b border-[#EDF1F5] px-5 py-5 sm:px-7">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#EAF9F0] text-[#43B978]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6v12m-6-6h12"/>
                                <circle cx="12" cy="12" r="9"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-[#273047]">Medical Information</h2>
                            <p class="mt-1 text-xs text-[#96A2B3]">Important health details for the clinic</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 px-5 py-6 sm:px-7">
                        <div>
                            <label for="allergies" class="mb-2 block text-sm font-medium text-[#48566B]">Known Allergies</label>
                            <textarea id="allergies" name="allergies" rows="2"
                                      placeholder="List any known allergies, or leave blank if not known"
                                      class="w-full resize-y rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">{{ old('allergies', $patient->allergies) }}</textarea>
                            @error('allergies')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="medical_notes" class="mb-2 block text-sm font-medium text-[#48566B]">Medical Notes</label>
                            <textarea id="medical_notes" name="medical_notes" rows="3"
                                      placeholder="Relevant medical history or other health information"
                                      class="w-full resize-y rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">{{ old('medical_notes', $patient->medical_notes) }}</textarea>
                            @error('medical_notes')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="mb-2 block text-sm font-medium text-[#48566B]">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="3"
                                      placeholder="Any additional information about this patient"
                                      class="w-full resize-y rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition placeholder:text-[#A5AFBD] focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD]">{{ old('notes', $patient->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- PATIENT STATUS --}}
                <section class="overflow-hidden rounded-[26px] border border-[#DCE6EE] bg-white shadow-[0_10px_30px_rgba(35,48,71,0.035)]">
                    <div class="px-5 py-5 sm:px-7">
                        <label for="status" class="mb-2 block text-sm font-medium text-[#48566B]">
                            Patient Status <span class="text-[#E47777]">*</span>
                        </label>
                        <p class="mb-3 text-xs leading-5 text-[#96A2B3]">
                            Inactive patients remain in the records but can be distinguished from active patients.
                        </p>
                        <select id="status" name="status" required
                                class="w-full rounded-xl border border-[#DCE5ED] bg-[#FAFBFD] px-4 py-3 text-sm text-[#273047] outline-none transition focus:border-[#9CC7FF] focus:bg-white focus:ring-4 focus:ring-[#E7F2FD] sm:max-w-sm">
                            <option value="active" @selected(old('status', $patient->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $patient->status) === 'inactive')>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                {{-- FORM ACTIONS --}}
                <div class="flex flex-col-reverse gap-3 rounded-[24px] border border-[#DCE6EE] bg-white p-5 shadow-[0_10px_30px_rgba(35,48,71,0.025)] sm:flex-row sm:items-center sm:justify-between sm:px-7">
                    <p class="text-xs leading-5 text-[#96A2B3]">
                        Changes will be saved to
                        <span class="font-medium text-[#59657A]">{{ $tenant->name }}</span>.
                    </p>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row">
                        <a href="{{ route('clinic.patients.show', $patient) }}"
                           class="inline-flex items-center justify-center rounded-xl border border-[#DCE5ED] bg-white px-5 py-3 text-sm font-semibold text-[#59657A] transition hover:bg-[#F7FAFC]">
                            Cancel
                        </a>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#5B9DF9] px-6 py-3 text-sm font-semibold text-white shadow-[0_7px_18px_rgba(91,157,249,0.2)] transition hover:bg-[#4D90F0] hover:shadow-[0_10px_22px_rgba(91,157,249,0.25)]">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-4 w-4"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.9">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m5 12 4 4L19 6"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
</x-app-layout>

