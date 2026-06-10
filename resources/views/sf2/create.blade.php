@extends('layouts.app')

@section('title', 'Create SF2 Report')

@section('styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/sf2-form.css') }}">
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('sf2.index') }}" class="sf2-back-link">&larr; Back to SF2 list</a>
    <h4 class="mt-2 mb-1">Create SF2 report</h4>
    <p class="text-muted small">Enter school details and learner names, then absent/tardy dates. Download the DepEd-style PDF when done.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('sf2.store') }}" id="sf2-form">
    @csrf

    @include('sf2.partials.form-fields', ['defaults' => $defaults])

    <div class="card mb-4">
        <div class="card-header fw-semibold d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Learners</span>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <input type="number" id="sf2-student-count" class="form-control form-control-sm" style="width:5rem" min="1" max="80" placeholder="#">
                <button type="button" id="sf2-generate-rows" class="btn btn-sm btn-outline-primary">Generate rows</button>
                <button type="button" id="sf2-add-student" class="btn btn-sm btn-primary">Add learner</button>
            </div>
        </div>
        <div class="card-body">
            <div id="sf2-students-container">
                @if(old('students'))
                    @foreach(old('students') as $i => $student)
                        @include('sf2.partials.student-row-static', ['index' => $i, 'student' => $student])
                    @endforeach
                @else
                    @include('sf2.partials.student-row-static', ['index' => 0, 'student' => ['sex' => 'male']])
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save report</button>
        <a href="{{ route('sf2.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

@include('sf2.partials.student-row-template')
@endsection

@push('scripts')
<script src="{{ \App\Support\VersionedAsset::url('js/sf2-calendar.js') }}"></script>
<script src="{{ \App\Support\VersionedAsset::url('js/sf2-form.js') }}"></script>
@endpush
