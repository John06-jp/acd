@extends('layouts.sec')

@section('content')
    <div class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-2">Dashboard</h1>
                <p class="text-muted mb-0">{{ __("You're logged in!") }}</p>
            </div>
        </div>
    </div>
@endsection
