@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
@endpush

@section('banner')
<img src="{{ app(\App\Services\SiteSettingsService::class)->publicUrl('landing.hero_image') }}"
     alt="Assumption College of Davao — Powered by Pantas"
     class="acd-banner-photo"
     width="3905" height="1056"
     loading="eager">
@endsection
