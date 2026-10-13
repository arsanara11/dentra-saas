<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FB] px-4 py-8 sm:px-6 lg:px-10">
        <div class="mx-auto max-w-5xl">

            {{-- HEADER --}}
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#5B9DF9]">
                        Patient Management
                    </p>

                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-[#263550]">
                        New Dental Record
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Catat hasil pemeriksaan dan tindakan perawatan pasien.
                    </p>
                </div>

                <a href="{{ route('clinic.dental-records.index') }}"
                   class="inline-flex items-center justify-center rounded-2xl border border-[#DCE7F0] bg-white px-5 py-3 text-sm font-semibold text-[#263550] transition hover:bg-slate-50">
                    ← Back to Records
                </a>
            </div>

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
                    <p class="font-bold">Data belum berhasil disimpan.</p>

                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM --}}
            <form action="{{ route('clinic.dental-records.store') }}"
                  method="POST"
                  class="space-y-6">
                @csrf

                {{-- PATIENT INFORMATION --}}
                <section class="rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-[#263550]">
                            Patient Information
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Pilih pasien dan dokter yang melakukan pemeriksaan.
                        </p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">

                        {{-- PATIENT --}}
                        <div>
                            <label for="patient_id" class="mb-2 block text-sm font-semibold text-slate-700">
                                Patient <span class="text-red-500">*</span>
                            </label>

                            <select id="patient_id"
                                    name="patient_id"
                                    required
                                    class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">
                                <option value="">Select patient</option>

                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}"
                                        @selected(old('patient_id') == $patient->id)>
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                        — {{ $patient->patient_number }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($patients->isEmpty())
                                <p class="mt-2 text-xs text-amber-600">
                                    Belum ada pasien. Tambahkan pasien terlebih dahulu.
                                </p>
                            @endif

                            @error('patient_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- DOCTOR --}}
                        <div>
                            <label for="doctor_id" class="mb-2 block text-sm font-semibold text-slate-700">
                                Examining Doctor <span class="text-red-500">*</span>
                            </label>

                            <select id="doctor_id"
                                    name="doctor_id"
                                    required
                                    class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">
                                <option value="">Select doctor</option>

                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        @selected(old('doctor_id') == $doctor->id)>
                                        {{ $doctor->name }}
                                        @if (!empty($doctor->specialization))
                                            — {{ $doctor->specialization }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @if ($doctors->isEmpty())
                                <p class="mt-2 text-xs text-amber-600">
                                    Belum ada dokter aktif. Tambahkan atau aktifkan dokter terlebih dahulu.
                                </p>
                            @endif

                            @error('doctor_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- APPOINTMENT --}}
                        <div>
                            <label for="appointment_id" class="mb-2 block text-sm font-semibold text-slate-700">
                                Related Appointment
                            </label>

                            <select id="appointment_id"
                                    name="appointment_id"
                                    class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">
                                <option value="">No linked appointment</option>

                                @foreach ($appointments as $appointment)
                                    <option value="{{ $appointment->id }}"
                                        @selected(old('appointment_id') == $appointment->id)>
                                        Appointment #{{ $appointment->id }}
                                    </option>
                                @endforeach
                            </select>

                            @error('appointment_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- EXAMINATION DATE --}}
                        <div>
                            <label for="examined_at" class="mb-2 block text-sm font-semibold text-slate-700">
                                Examination Date &amp; Time <span class="text-red-500">*</span>
                            </label>

                            <input type="datetime-local"
                                   id="examined_at"
                                   name="examined_at"
                                   value="{{ old('examined_at', now()->format('Y-m-d\TH:i')) }}"
                                   required
                                   class="w-full rounded-xl border border-[#DCE7F0] bg-white px-4 py-3 text-sm text-[#263550] outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                            @error('examined_at')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- CLINICAL EXAMINATION --}}
                <section class="rounded-[28px] border border-[#DCE7F0] bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-[#263550]">
                            Clinical Examination
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Masukkan keluhan, diagnosis, dan tindakan yang dilakukan.
                        </p>
                    </div>

                    <div class="space-y-5">

                        {{-- CHIEF COMPLAINT --}}
                        <div>
                            <label for="chief_complaint" class="mb-2 block text-sm font-semibold text-slate-700">
                                Chief Complaint
                            </label>

                            <textarea id="chief_complaint"
                                      name="chief_complaint"
                                      rows="3"
                                      placeholder="Keluhan utama pasien..."
                                      class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">{{ old('chief_complaint') }}</textarea>

                            @error('chief_complaint')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- DIAGNOSIS --}}
                        <div>
                            <label for="diagnosis" class="mb-2 block text-sm font-semibold text-slate-700">
                                Diagnosis
                            </label>

                            <textarea id="diagnosis"
                                      name="diagnosis"
                                      rows="3"
                                      placeholder="Hasil diagnosis dokter..."
                                      class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">{{ old('diagnosis') }}</textarea>

                            @error('diagnosis')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- TREATMENT --}}
                        <div>
                            <label for="treatment" class="mb-2 block text-sm font-semibold text-slate-700">
                                Treatment / Procedure
                            </label>

                            <textarea id="treatment"
                                      name="treatment"
                                      rows="3"
                                      placeholder="Tindakan atau prosedur perawatan..."
                                      class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">{{ old('treatment') }}</textarea>

                            @error('treatment')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- PRESCRIPTION --}}
                        <div>
                            <label for="prescription" class="mb-2 block text-sm font-semibold text-slate-700">
                                Prescription
                            </label>

                            <textarea id="prescription"
                                      name="prescription"
                                      rows="3"
                                      placeholder="Obat dan instruksi penggunaannya..."
                                      class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">{{ old('prescription') }}</textarea>

                            @error('prescription')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CLINICAL NOTES --}}
                        <div>
                            <label for="clinical_notes" class="mb-2 block text-sm font-semibold text-slate-700">
                                Clinical Notes
                            </label>

                            <textarea id="clinical_notes"
                                      name="clinical_notes"
                                      rows="4"
                                      placeholder="Catatan klinis tambahan..."
                                      class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">{{ old('clinical_notes') }}</textarea>

                            @error('clinical_notes')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NEXT VISIT --}}
                        <div>
                            <label for="next_visit_at" class="mb-2 block text-sm font-semibold text-slate-700">
                                Recommended Next Visit
                            </label>

                            <input type="date"
                                   id="next_visit_at"
                                   name="next_visit_at"
                                   value="{{ old('next_visit_at') }}"
                                   class="w-full rounded-xl border border-[#DCE7F0] px-4 py-3 text-sm outline-none focus:border-[#5B9DF9] focus:ring-2 focus:ring-blue-100">

                            @error('next_visit_at')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- ACTIONS --}}
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('clinic.dental-records.index') }}"
                       class="inline-flex justify-center rounded-xl border border-[#DCE7F0] bg-white px-6 py-3 text-sm font-semibold text-[#263550] hover:bg-slate-50">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex justify-center rounded-xl bg-[#5B9DF9] px-7 py-3 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-[#4388EB]">
                        Save Dental Record →
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>