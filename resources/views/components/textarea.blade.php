@props(['disabled' => false])

@php
    $classList = 'rounded-2xl shadow-sm focus:outline-none py-2 px-4 disabled:cursor-not-allowed transition duration-300 resize-none';
    if($errors->has($attributes->get('name'))) {
        $classList .= ' border-red focus:border-red-700 focus:ring-0';
    } else {
		$classList .= ' border-slate-400 focus:border-cyan-500 focus:ring-0';
	}
@endphp

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classList, 'style' => 'border-width: 3px;']) !!}></textarea>

@error($attributes->get('name'))
    <p class="text-red font-bold mt-2 error-messages"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
@enderror
