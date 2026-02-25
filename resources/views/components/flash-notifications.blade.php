@props([])

@php
    $message = session('success') ?? session('warning') ?? session('error');
    $type = session('success') ? 'success' : (session('warning') ? 'warning' : (session('error') ? 'error' : null));
@endphp
@if ($message && $type)
    @php
        $styles = [
            'success' => 'bg-green-600 text-white hover:bg-green-500',
            'warning' => 'bg-amber-500 text-white hover:bg-amber-400',
            'error'   => 'bg-red-600 text-white hover:bg-red-500',
        ];
        $icons = [
            'success' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
            'warning' => '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
            'error'   => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
        ];
    @endphp
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-4"
         class="fixed bottom-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-3 {{ $styles[$type] }}">
            <span class="flex-shrink-0">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">{!! $icons[$type] !!}</svg>
            </span>
            <p class="flex-1 text-sm font-medium">{{ $message }}</p>
            <button @click="show = false" class="flex-shrink-0 p-1 rounded opacity-90 hover:opacity-100 focus:outline-none">
                <span class="sr-only">Fechar</span>
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    </div>
@endif
