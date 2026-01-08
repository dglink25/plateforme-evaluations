@props(['variant' => 'primary', 'size' => 'md', 'pill' => false])

@php
    $baseClasses = 'inline-flex items-center font-medium';
    
    $variantClasses = [
        'primary' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300',
        'secondary' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'success' => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-300',
        'danger' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-300',
        'warning' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-300',
        'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    ];
    
    $sizeClasses = [
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ];
    
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
    
    if ($pill) {
        $classes .= ' rounded-full';
    } else {
        $classes .= ' rounded-md';
    }
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>