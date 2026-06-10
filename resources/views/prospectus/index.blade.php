@extends('layouts.app')

@section('title', 'Prospectus Manager')

@push('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/prospectus/index.css') }}">
@endpush

@section('content')
<div class="prsp-page">

    {{-- Page header --}}
    <div class="prsp-page-header">
        <div>
            <h1 class="prsp-page-title">Prospectus Manager</h1>
            <p class="prsp-page-sub">Manage programs, year levels, and subjects</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="prsp-alert prsp-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="prsp-alert prsp-alert--error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Add Program form --}}
    <div class="prsp-add-form">
        <p class="prsp-add-form-title">Add Program / Strand</p>
        <form method="POST" action="{{ route('prospectus.storeProgram') }}" class="prsp-form-row">
            @csrf
            <input type="text" name="program_code" placeholder="Code (e.g. BSIT)"
                   class="prsp-input prsp-input--code" required>
            <input type="text" name="program_name" placeholder="Full program name"
                   class="prsp-input prsp-input--name" required>
            <input type="number" name="total_years" placeholder="Yrs" min="1" max="6"
                   class="prsp-input prsp-input--years" required>
            <button type="submit" class="prsp-btn prsp-btn--add">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Add Program
            </button>
        </form>
    </div>

    {{-- Programs list --}}
    @foreach($programs as $program)
    <div class="prsp-program-card bg-white rounded shadow mb-6">

        {{-- Program header --}}
        <div class="prsp-program-header">
            <div class="prsp-program-badge" id="program-badge-{{ $program->id }}">
                {{ $program->program_code }}
            </div>
            <div class="prsp-program-name" id="program-name-{{ $program->id }}">
                {{ $program->program_name }}
            </div>
            <div class="prsp-program-actions">
                <button type="button"
                    onclick="openProgramEditModal({{ $program->id }}, @js($program->program_code), @js($program->program_name))"
                    class="prsp-btn prsp-btn--edit">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </button>
                <button type="button"
                    onclick="openProgramDeleteModal({{ $program->id }}, @js($program->program_code))"
                    class="prsp-btn prsp-btn--delete">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <path d="M10 11v6"/><path d="M14 11v6"/>
                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                    </svg>
                    Delete
                </button>
                <button type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#program-{{ $program->id }}"
                    class="prsp-btn prsp-btn--toggle">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </button>
            </div>
        </div>

        {{-- Collapsible body: year cards --}}
        <div id="program-{{ $program->id }}" class="prsp-program-body hidden">
            <div class="prsp-years-grid">
                @foreach($program->years as $year)
                <div class="prsp-year-card">

                    {{-- Year header --}}
                    <div class="prsp-year-header">
                        <div class="prsp-year-badge">Y{{ $year->year_level }}</div>
                        <span class="prsp-year-title">Year {{ $year->year_level }}</span>
                        <button type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#year-{{ $year->id }}"
                            class="prsp-year-toggle">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                    </div>

                    {{-- Collapsible year body --}}
                    <div id="year-{{ $year->id }}" class="prsp-year-body hidden">
                        <ul class="prsp-course-list">
                            @forelse($year->courses as $course)
                            <li id="course-{{ $course->id }}" class="prsp-course-item">
                                <span class="prsp-course-code">{{ $course->course_code }}</span>
                                <span class="prsp-course-name">{{ $course->course_name }}</span>
                                <div class="prsp-course-actions">
                                    <button type="button" class="prsp-course-btn prsp-course-btn--edit"
                                        onclick="openEditModal({{ $course->id }}, @js($course->course_code), @js($course->course_name))">
                                        Edit
                                    </button>
                                    <button type="button" class="prsp-course-btn prsp-course-btn--delete"
                                        onclick="openDeleteModal({{ $course->id }}, @js($course->course_code))">
                                        Delete
                                    </button>
                                </div>
                            </li>
                            @empty
                            <li class="prsp-empty">No subjects yet.</li>
                            @endforelse
                        </ul>

                        <form method="POST" action="{{ route('prospectus.storeCourse', $year->id) }}"
                              class="prsp-add-course-form add-course-form"
                              data-year="{{ $year->id }}">
                            @csrf
                            <input type="text" name="course_code" placeholder="Code"
                                   class="prsp-input prsp-input--course-code" required>
                            <input type="text" name="course_name" placeholder="Subject name"
                                   class="prsp-input prsp-input--course-name" required>
                            <button type="submit" class="prsp-btn--add-small">
                                <span class="btn-text">Add</span>
                                <span class="spinner hidden"></span>
                            </button>
                        </form>
                    </div>

                </div>
                @endforeach
            </div>
        </div>

    </div>
    @endforeach

</div>

{{-- ── Modals ─────────────────────────────────────────────────────────────── --}}

{{-- Delete course --}}
<div id="deleteModal" class="prsp-modal-backdrop hidden">
    <div class="prsp-modal">
        <h2 class="prsp-modal-title">Delete Subject</h2>
        <p id="deleteMessage" class="prsp-modal-desc"></p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="prsp-modal-actions">
                <button type="button" onclick="closeDeleteModal()" class="prsp-btn prsp-btn--cancel">Cancel</button>
                <button type="submit" id="deleteBtn" class="prsp-btn prsp-btn--delete">
                    <span class="btn-text">Delete</span>
                    <span class="spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit course --}}
<div id="editModal" class="prsp-modal-backdrop hidden">
    <div class="prsp-modal">
        <h2 class="prsp-modal-title">Edit Subject</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="prsp-modal-field">
                <label for="editCourseCode">Subject Code</label>
                <input type="text" id="editCourseCode" name="course_code" class="prsp-modal-input" required>
            </div>
            <div class="prsp-modal-field">
                <label for="editCourseName">Subject Name</label>
                <input type="text" id="editCourseName" name="course_name" class="prsp-modal-input" required>
            </div>
            <div class="prsp-modal-actions">
                <button type="button" onclick="closeEditModal()" class="prsp-btn prsp-btn--cancel">Cancel</button>
                <button type="submit" id="editBtn" class="prsp-btn prsp-btn--save">
                    <span class="btn-text">Save Changes</span>
                    <span class="spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit program --}}
<div id="editProgramModal" class="prsp-modal-backdrop hidden">
    <div class="prsp-modal">
        <h2 class="prsp-modal-title">Edit Program</h2>
        <form id="editProgramForm" method="POST">
            @csrf
            @method('PUT')
            <div class="prsp-modal-field">
                <label for="editProgramCode">Program Code</label>
                <input type="text" id="editProgramCode" name="program_code" class="prsp-modal-input" required>
            </div>
            <div class="prsp-modal-field">
                <label for="editProgramName">Program Name</label>
                <input type="text" id="editProgramName" name="program_name" class="prsp-modal-input" required>
            </div>
            <div class="prsp-modal-actions">
                <button type="button" onclick="closeProgramEditModal()" class="prsp-btn prsp-btn--cancel">Cancel</button>
                <button type="submit" id="editProgramBtn" class="prsp-btn prsp-btn--save">
                    <span class="btn-text">Save</span>
                    <span class="spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete program --}}
<div id="deleteProgramModal" class="prsp-modal-backdrop hidden">
    <div class="prsp-modal">
        <h2 class="prsp-modal-title">Delete Program</h2>
        <p class="prsp-modal-desc">
            Are you sure you want to delete <strong id="deleteProgramCode"></strong>?
            This will permanently remove all year levels and subjects. This action cannot be undone.
        </p>
        <form id="deleteProgramForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="prsp-modal-actions">
                <button type="button" onclick="closeProgramDeleteModal()" class="prsp-btn prsp-btn--cancel">Cancel</button>
                <button type="submit" id="deleteProgramBtn" class="prsp-btn prsp-btn--delete">
                    <span class="btn-text">Delete</span>
                    <span class="spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="toastContainer" style="position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;"></div>

@endsection

@push('scripts')
    <script src="{{ asset('js/prospectus.js') }}"></script>
@endpush
