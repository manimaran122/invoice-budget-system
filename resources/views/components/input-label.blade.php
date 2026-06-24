@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-app-dark']) }}>
    {{ $value ?? $slot }}
</label>
