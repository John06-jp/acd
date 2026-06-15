@extends('layouts.sec')

@section('content')
<div class="py-2 d-flex justify-content-center">
<div class="w-100" style="max-width: 660px;">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Card -->
    <div class="bg-white rounded-4 shadow-sm p-3 p-sm-4">

        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-3 d-flex align-items-center justify-content-center"
                 style="width:42px; height:42px; background:#e8f0fe;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="#4a6cf7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <polygon points="10 8 16 11 10 14 10 8" fill="#4a6cf7" stroke="none"/>
                </svg>
            </div>
            <div>
                <h5 class="mb-0 fw-semibold" style="font-size:1rem;">Change attendance video</h5>
                <small class="text-muted">Replace the current video shown during attendance</small>
            </div>
        </div>

        <!-- Video Preview -->
        <div class="rounded-3 overflow-hidden mb-2"
             style="background:#111; position:relative; aspect-ratio:16/9;">
            <video id="currentVideo" muted autoplay loop controls
                   style="width:100%; height:100%; object-fit:contain; display:block;">
                <source src="{{ asset('videos/area51_product_slideshow.mp4') }}" type="video/mp4">
            </video>
            <!-- No-video placeholder (hidden when video loads) -->
            <div id="noVideoPlaceholder"
                 style="display:none; position:absolute; inset:0; flex-direction:column;
                        align-items:center; justify-content:center; color:#555;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.5">
                    <polygon points="5 3 19 12 5 21 5 3"/>
                </svg>
                <span class="mt-2" style="font-size:.85rem;">No video selected</span>
            </div>
        </div>

        <!-- Format hint -->
        <div class="d-flex align-items-center gap-1 text-muted mb-4" style="font-size:.78rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Supports MP4, MOV, AVI — max 500 MB
        </div>

        <!-- Upload Form -->
        <form id="uploadForm" method="POST" action="{{ route('attendance.uploadVideo') }}"
              enctype="multipart/form-data">
            @csrf
            <input type="file" name="video" id="videoUpload"
                   accept="video/mp4,video/quicktime,video/avi,video/*"
                   required style="display:none;">

            <!-- Drag & Drop Zone -->
            <div id="dropZone"
                 class="rounded-3 d-flex flex-column align-items-center justify-content-center gap-2 mb-3"
                 style="border: 2px dashed #cdd5e0; background:#f8fafc;
                        padding: 2rem 1rem; cursor:pointer; transition: border-color .2s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24"
                     fill="none" stroke="#6b7280" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 16 12 12 8 16"/>
                    <line x1="12" y1="12" x2="12" y2="21"/>
                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                </svg>
                <p class="mb-0 fw-semibold" style="font-size:.95rem; color:#1a1a1a;">
                    Drag &amp; drop a video here
                </p>
                <p class="mb-0" style="font-size:.85rem; color:#6b7280;">
                    or <a href="#" id="browseLink" class="text-primary text-decoration-none fw-medium">browse files</a>
                </p>
                <p id="selectedFileName" class="mb-0 text-success fw-medium" style="font-size:.82rem; display:none;"></p>
            </div>

            <!-- Upload Button -->
            <button type="submit" id="uploadBtn"
                    class="btn w-100 d-flex align-items-center justify-content-center gap-2"
                    style="background:#d9e8fd; color:#1a4bbf; font-weight:600;
                           border:none; padding:.65rem; border-radius:.5rem; font-size:.95rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 16 12 12 8 16"/>
                    <line x1="12" y1="12" x2="12" y2="21"/>
                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                </svg>
                Upload video
            </button>
        </form>
    </div>
</div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55);
            z-index:9999; align-items:center; justify-content:center;">
    <div class="text-white text-center">
        <div class="spinner-border mb-3" role="status"></div>
        <div class="fw-bold fs-5">Uploading…</div>
    </div>
</div>

<script>
    const videoInput      = document.getElementById('videoUpload');
    const dropZone        = document.getElementById('dropZone');
    const browseLink      = document.getElementById('browseLink');
    const selectedName    = document.getElementById('selectedFileName');
    const uploadForm      = document.getElementById('uploadForm');
    const loadingOverlay  = document.getElementById('loadingOverlay');
    const currentVideo    = document.getElementById('currentVideo');
    const noPlaceholder   = document.getElementById('noVideoPlaceholder');

    // Show placeholder if no video source loads
    currentVideo.addEventListener('error', function () {
        currentVideo.style.display = 'none';
        noPlaceholder.style.display = 'flex';
    });

    // Browse click
    browseLink.addEventListener('click', function (e) {
        e.preventDefault();
        videoInput.click();
    });
    dropZone.addEventListener('click', function () {
        videoInput.click();
    });

    // Drag over styling
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.style.borderColor = '#4a6cf7';
        dropZone.style.background  = '#eef2ff';
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.style.borderColor = '#cdd5e0';
        dropZone.style.background  = '#f8fafc';
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.borderColor = '#cdd5e0';
        dropZone.style.background  = '#f8fafc';
        if (e.dataTransfer.files.length) {
            setFile(e.dataTransfer.files[0]);
        }
    });

    // File input change
    videoInput.addEventListener('change', function () {
        if (this.files[0]) setFile(this.files[0]);
    });

    function setFile(file) {
        if (file.size > 500 * 1024 * 1024) {
            alert('File is too large! Maximum allowed size is 500 MB.');
            videoInput.value = '';
            selectedName.style.display = 'none';
            return;
        }
        // Transfer dropped file to the input when needed
        if (videoInput.files.length === 0) {
            const dt = new DataTransfer();
            dt.items.add(file);
            videoInput.files = dt.files;
        }
        selectedName.textContent = '✓ ' + file.name;
        selectedName.style.display = 'block';
        // Preview dropped/selected video
        const url = URL.createObjectURL(file);
        currentVideo.style.display = 'block';
        noPlaceholder.style.display = 'none';
        currentVideo.src = url;
        currentVideo.load();
    }

    // Submit
    uploadForm.addEventListener('submit', function () {
        if (videoInput.files.length === 0) return false;
        loadingOverlay.style.display = 'flex';
    });
</script>
@endsection
