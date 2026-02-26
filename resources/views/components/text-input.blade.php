@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm']) }}>
