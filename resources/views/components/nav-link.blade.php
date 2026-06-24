@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-primary text-sm font-medium leading-5 text-app-dark focus:outline-none focus:border-primary transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-app-muted hover:text-app-dark hover:border-app-border focus:outline-none focus:text-app-dark focus:border-app-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
