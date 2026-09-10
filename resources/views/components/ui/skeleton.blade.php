@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'animate-pulse rounded-md bg-gray-200 dark:bg-dark-700 ' . $class]) }} />
