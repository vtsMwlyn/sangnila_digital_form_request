@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="container-draft bg-[#FEFEFEB2] p-6 rounded-2xl w-full shadow-lg">
        <x-back-button onclick="history.back();" />

        <h2 class="text-[#042E66] text-3xl font-black mt-2 mb-1">
            Edit Profile
        </h2>
        <x-separator-line/>

        <div class="w-full grid grid-cols-1 xl:grid-cols-2 gap-10 mt-5">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
        </div>
    </div>
@endsection
