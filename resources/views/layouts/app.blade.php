<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DENTRA</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },

                    colors: {
                        dentra: {
                            blue: '#5B9DF9',
                            dark: '#273047',
                            muted: '#8D98AA',
                            soft: '#F4F7FA',
                        }
                    },

                    boxShadow: {
                        soft: '0 10px 35px rgba(74, 104, 140, 0.08)',
                        panel: '0 20px 60px rgba(74, 104, 140, 0.08)',
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>


<body class="min-h-screen bg-[#F4F7FA] font-sans text-[#273047] antialiased">

    <div class="min-h-screen p-3 sm:p-5 lg:p-7">

        <div class="mx-auto flex min-h-[calc(100vh-2rem)] max-w-[1500px] overflow-hidden rounded-[38px] border border-white bg-white shadow-panel">


            {{-- =====================================================
                 SIDEBAR
            ====================================================== --}}

            <aside class="hidden w-[92px] shrink-0 flex-col items-center border-r border-[#EEF1F5] bg-white py-7 lg:flex">

                {{-- LOGO --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="mb-10 flex flex-col items-center"
                >

                    <div class="flex h-11 w-11 items-center justify-center rounded-[15px] bg-[#EAF4FF]">

                        <svg
                            class="h-6 w-6 text-[#5B9DF9]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M7.2 4.4c1.1-.8 2.3-.7 3.2-.2.5.3 1 .3 1.6 0 .9-.5 2.1-.6 3.2.2 1.6 1.2 2.1 3.2 1.7 5.1-.4 2.2-1.5 4.2-2.3 6.4-.5 1.4-.8 3.4-1.9 3.4-1.1 0-1.2-1.5-2.4-1.5s-1.3 1.5-2.4 1.5c-1.1 0-1.4-2-1.9-3.4-.8-2.2-1.9-4.2-2.3-6.4-.4-1.9.1-3.9 1.7-5.1Z"
                            />

                            <path
                                stroke-linecap="round"
                                d="M9 7.2c.7-.5 1.5-.6 2.2-.3M15 7.2c-.7-.5-1.5-.6-2.2-.3"
                            />
                        </svg>

                    </div>

                    <span class="mt-2 text-[9px] font-bold tracking-[0.22em] text-[#566176]">
                        DENTRA
                    </span>

                </a>


                {{-- NAVIGATION --}}

                <nav class="flex flex-1 flex-col items-center gap-3">


                    {{-- DASHBOARD --}}

                    <a
                        href="{{ route('dashboard') }}"
                        class="group relative flex h-12 w-12 items-center justify-center rounded-[17px] bg-[#EAF4FF] text-[#5B9DF9]"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 11.5 12 4l8 7.5"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.5 10.5V20h11v-9.5"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10 20v-5h4v5"
                            />
                        </svg>

                        <span class="pointer-events-none absolute left-16 whitespace-nowrap rounded-lg bg-white px-3 py-2 text-xs font-medium text-[#566176] opacity-0 shadow-soft transition group-hover:opacity-100">
                            Dashboard
                        </span>

                    </a>


                    {{-- PATIENTS --}}

                    <a
                        href="#"
                        class="group relative flex h-12 w-12 items-center justify-center rounded-[17px] text-[#8993A5] transition hover:bg-[#F1F7FD] hover:text-[#5B9DF9]"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20"
                            />

                            <circle
                                cx="10"
                                cy="7"
                                r="3"
                            />

                            <path
                                stroke-linecap="round"
                                d="M16 11a3 3 0 0 1 3 3v1"
                            />
                        </svg>

                        <span class="pointer-events-none absolute left-16 whitespace-nowrap rounded-lg bg-white px-3 py-2 text-xs font-medium text-[#566176] opacity-0 shadow-soft transition group-hover:opacity-100">
                            Patients
                        </span>

                    </a>


                    {{-- APPOINTMENTS --}}

                    <a
                        href="#"
                        class="group relative flex h-12 w-12 items-center justify-center rounded-[17px] text-[#8993A5] transition hover:bg-[#F1F7FD] hover:text-[#5B9DF9]"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <rect
                                x="4"
                                y="5"
                                width="16"
                                height="15"
                                rx="3"
                            />

                            <path
                                stroke-linecap="round"
                                d="M8 3v4M16 3v4M4 10h16"
                            />

                            <path
                                stroke-linecap="round"
                                d="M8 14h3M8 17h2"
                            />
                        </svg>

                        <span class="pointer-events-none absolute left-16 whitespace-nowrap rounded-lg bg-white px-3 py-2 text-xs font-medium text-[#566176] opacity-0 shadow-soft transition group-hover:opacity-100">
                            Appointments
                        </span>

                    </a>


                    {{-- QUEUE --}}

                    <a
                        href="#"
                        class="group relative flex h-12 w-12 items-center justify-center rounded-[17px] text-[#8993A5] transition hover:bg-[#F1F7FD] hover:text-[#5B9DF9]"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                d="M7 6h10M7 12h10M7 18h6"
                            />

                            <circle
                                cx="4"
                                cy="6"
                                r="1"
                            />

                            <circle
                                cx="4"
                                cy="12"
                                r="1"
                            />

                            <circle
                                cx="4"
                                cy="18"
                                r="1"
                            />
                        </svg>

                        <span class="pointer-events-none absolute left-16 whitespace-nowrap rounded-lg bg-white px-3 py-2 text-xs font-medium text-[#566176] opacity-0 shadow-soft transition group-hover:opacity-100">
                            Queue
                        </span>

                    </a>


                    {{-- DOCTORS --}}

                    <a
                        href="#"
                        class="group relative flex h-12 w-12 items-center justify-center rounded-[17px] text-[#8993A5] transition hover:bg-[#F1F7FD] hover:text-[#5B9DF9]"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                cx="12"
                                cy="7"
                                r="3"
                            />

                            <path
                                stroke-linecap="round"
                                d="M5 20a7 7 0 0 1 14 0"
                            />
                        </svg>

                        <span class="pointer-events-none absolute left-16 whitespace-nowrap rounded-lg bg-white px-3 py-2 text-xs font-medium text-[#566176] opacity-0 shadow-soft transition group-hover:opacity-100">
                            Doctors
                        </span>

                    </a>


                    {{-- RECORDS --}}

                    <a
                        href="#"
                        class="group relative flex h-12 w-12 items-center justify-center rounded-[17px] text-[#8993A5] transition hover:bg-[#F1F7FD] hover:text-[#5B9DF9]"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 4h12a2 2 0 0 1 2 2v14H4V6a2 2 0 0 1 2-2Z"
                            />

                            <path
                                stroke-linecap="round"
                                d="M8 9h8M8 13h8M8 17h5"
                            />
                        </svg>

                        <span class="pointer-events-none absolute left-16 whitespace-nowrap rounded-lg bg-white px-3 py-2 text-xs font-medium text-[#566176] opacity-0 shadow-soft transition group-hover:opacity-100">
                            Dental Records
                        </span>

                    </a>

                </nav>


                {{-- BOTTOM --}}

                <div class="flex flex-col items-center gap-3">

                    <a
                        href="#"
                        class="flex h-12 w-12 items-center justify-center rounded-[17px] text-[#8993A5] transition hover:bg-[#F1F7FD] hover:text-[#5B9DF9]"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="3.5"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 1.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.1h-2.5V20a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-1.8-1.8.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H6v-2.5h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 1.8-1.8.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V5h2.5v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 1.8 1.8-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1v2.5h-.1a1.7 1.7 0 0 0-1.6 1Z"
                            />
                        </svg>

                    </a>


                    <a
                        href="#"
                        class="flex h-12 w-12 items-center justify-center rounded-[17px] text-[#8993A5] transition hover:bg-[#F1F7FD] hover:text-[#5B9DF9]"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                            />

                            <path
                                stroke-linecap="round"
                                d="M9.5 9a2.5 2.5 0 1 1 4.3 1.8c-.9.9-1.8 1.2-1.8 2.7"
                            />

                            <circle
                                cx="12"
                                cy="17"
                                r=".7"
                                fill="currentColor"
                                stroke="none"
                            />
                        </svg>

                    </a>

                </div>

            </aside>


            {{-- =====================================================
                 MAIN
            ====================================================== --}}

            <main class="flex min-w-0 flex-1 flex-col bg-[#F8FAFC]">

                {{-- TOPBAR --}}

                <header class="flex h-[88px] shrink-0 items-center justify-between border-b border-[#F0F2F5] bg-white px-6 sm:px-8 lg:px-10">

                    {{-- MOBILE LOGO --}}

                    <div class="flex items-center gap-3 lg:hidden">

                        <div class="flex h-10 w-10 items-center justify-center rounded-[13px] bg-[#EAF4FF] text-[#5B9DF9]">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M7.2 4.4c1.1-.8 2.3-.7 3.2-.2.5.3 1 .3 1.6 0 .9-.5 2.1-.6 3.2.2 1.6 1.2 2.1 3.2 1.7 5.1-.4 2.2-1.5 4.2-2.3 6.4-.5 1.4-.8 3.4-1.9 3.4-1.1 0-1.2-1.5-2.4-1.5s-1.3 1.5-2.4 1.5c-1.1 0-1.4-2-1.9-3.4-.8-2.2-1.9-4.2-2.3-6.4-.4-1.9.1-3.9 1.7-5.1Z"
                                />
                            </svg>

                        </div>

                        <span class="text-sm font-bold tracking-[0.2em] text-[#566176]">
                            DENTRA
                        </span>

                    </div>


                    {{-- SEARCH --}}

                    <div class="relative hidden w-full max-w-[560px] md:block">

                        <svg
                            class="absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-[#A2ABBA]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path
                                stroke-linecap="round"
                                d="m20 20-4-4"
                            />
                        </svg>

                        <input
                            type="text"
                            placeholder="Find Patients or Appointments"
                            class="h-12 w-full rounded-[18px] border border-[#EEF1F5] bg-[#F9FAFC] px-12 text-sm text-[#273047] outline-none placeholder:text-[#A3ACBA] focus:border-[#CFE3F9] focus:ring-4 focus:ring-[#EAF4FF]"
                        >

                    </div>


                    {{-- RIGHT --}}

                    <div class="ml-auto flex items-center gap-3">

                        <button class="relative flex h-11 w-11 items-center justify-center rounded-full border border-[#EEF1F5] bg-white text-[#68748A]">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                                />

                                <path
                                    stroke-linecap="round"
                                    d="M10 21h4"
                                />
                            </svg>

                            <span class="absolute right-2.5 top-2 h-2 w-2 rounded-full bg-[#F27D8A]"></span>

                        </button>


                        <div class="flex h-11 w-11 items-center justify-center rounded-full border-4 border-white bg-[#DCEEFF] text-sm font-bold text-[#4387E8] shadow-sm">

                            {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 1)) }}

                        </div>

                    </div>

                </header>


                {{-- PAGE CONTENT --}}

                <div class="min-h-0 flex-1 overflow-y-auto px-6 pb-8 pt-7 sm:px-8 lg:px-10">

                    {{ $slot }}

                </div>

            </main>

        </div>

    </div>

    @stack('scripts')

</body>
</html>