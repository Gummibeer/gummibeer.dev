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
            font-weight: 300;
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
        <div class="mb-[7.5rem] font-logo text-[10rem] leading-none text-brand">{{ $identity->get('brand_name') }}</div>

        <h1 class="m-0 text-[8rem] leading-[1.05] font-black text-black">{{ $title }}</h1>

        @if (isset($date, $readTime))
            <ul class="mt-[4rem] flex list-none justify-center gap-[4rem] p-0 text-[4rem] leading-none text-snow-20">
                <li class="flex items-center gap-4">
                    <x-icon
                        name="ski-calendar"
                        class="size-[4rem]"
                    />
                    <span>{{ $date->format('M jS, Y') }}</span>
                </li>
                <li class="flex items-center gap-4">
                    <x-icon
                        name="ski-clock"
                        class="size-[4rem]"
                    />
                    <span>{{ $readTime }} min read</span>
                </li>
            </ul>
        @endif
    </main>
</body>
</html>
