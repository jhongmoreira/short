@props(['text', 'label' => 'Copiar'])
<button type="button" data-copy="{{ $text }}" {{ $attributes->merge(['class' => 'btn-ghost shrink-0 data-[copied]:border-accent data-[copied]:text-accent']) }}>
    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>
    </svg>
    <span data-copy-label>{{ $label }}</span>
</button>
