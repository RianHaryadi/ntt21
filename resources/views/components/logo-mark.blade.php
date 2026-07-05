@props(['class' => 'w-9 h-9'])

<svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}" role="img" aria-label="Pesona NTT">
    <defs>
        <clipPath id="logoCircleClip">
            <circle cx="24" cy="24" r="24"/>
        </clipPath>
    </defs>

    <g clip-path="url(#logoCircleClip)">
        <circle cx="24" cy="24" r="24" fill="#1C4750"/>

        <!-- rising sun -->
        <circle cx="24" cy="18" r="8" fill="#D2674A"/>

        <!-- horizon waves -->
        <path d="M-4 28 C 8 22, 16 22, 24 28 C 32 34, 40 34, 52 28 L 52 52 L -4 52 Z" fill="#0F6E63"/>
        <path d="M-4 36 C 8 30, 16 30, 24 36 C 32 42, 40 42, 52 36 L 52 52 L -4 52 Z" fill="#0F6E63" opacity="0.55"/>
    </g>
</svg>
