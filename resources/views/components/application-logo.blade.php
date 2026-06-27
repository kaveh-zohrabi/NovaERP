<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 48" {{ $attributes }}>
    <defs>
        <linearGradient id="logo-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#4F46E5" />
            <stop offset="100%" style="stop-color:#7C3AED" />
        </linearGradient>
    </defs>
    <rect x="2" y="8" width="32" height="32" rx="8" fill="url(#logo-gradient)" />
    <path d="M10 16h4l3 4-3 4h-4l3-4-3-4z" fill="white" opacity="0.9"/>
    <path d="M18 16h4l3 4-3 4h-4l3-4-3-4z" fill="white" opacity="0.6"/>
    <text x="44" y="31" font-family="Figtree, sans-serif" font-size="22" font-weight="700" fill="#111827">
        {{ config('app.name', 'ERP') }}
    </text>
</svg>
