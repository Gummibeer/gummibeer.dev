<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    />
    <title>Generated Open Graph image</title>
    <style>
        {!! $stylesheet !!}

        @font-face {
            font-family: 'OG Inter';
            font-style: normal;
            font-weight: 100 900;
            font-display: block;
            src: url('{{ $interFont }}') format('woff2');
        }

        @font-face {
            font-family: 'OG Permanent Marker';
            font-style: normal;
            font-weight: 400;
            font-display: block;
            src: url('{{ $logoFont }}') format('woff2');
        }

        :root {
            --font-sans: 'OG Inter', ui-sans-serif, system-ui, sans-serif;
            --font-logo: 'OG Permanent Marker', cursive;
        }
    </style>
</head>
<body
    class="m-0 flex h-screen w-screen items-center justify-center overflow-hidden bg-white text-center font-sans text-night-0"
    style="background-image: radial-gradient(circle at 25px 25px, lightgray 2%, transparent 0%), radial-gradient(circle at 75px 75px, lightgray 2%, transparent 0%); background-size: 100px 100px"
>
    <main class="mx-auto w-full max-w-[1800px] px-24">
        <div class="mb-[7.5rem] font-logo text-[10rem] leading-none text-brand">Tom Herrmann</div>

        <h1 class="m-0 text-[8rem] leading-[1.05] font-black text-black">{{ $title }}</h1>

        @if (isset($date, $readTime))
            <ul class="mt-[4rem] flex list-none justify-center gap-[4rem] p-0 text-[4rem] leading-none text-snow-20">
                <li class="flex items-center gap-4">
                    <svg
                        class="size-[4rem] shrink-0"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5" />
                        <path d="M5.25 4.5h13.5a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V6a1.5 1.5 0 0 1 1.5-1.5Z" />
                    </svg>
                    <span>{{ $date->format('M jS, Y') }}</span>
                </li>
                <li class="flex items-center gap-4">
                    <svg
                        class="size-[4rem] shrink-0"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <circle cx="12" cy="12" r="9.25" />
                        <path d="M12 6.75V12l3.5 2" />
                    </svg>
                    <span>{{ $readTime }} min read</span>
                </li>
            </ul>
        @endif
    </main>
</body>
</html>
