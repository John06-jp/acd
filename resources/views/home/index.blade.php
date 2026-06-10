@extends('layouts.main')

@push('page-styles')
<style>
    .faq-header h2,
    .faq-subtitle,
    .faq-item {
        font-family: var(--brand-font-family, 'Inter', sans-serif);
    }
    .faq-header h2 { letter-spacing: -0.02em; }
    .home-actions {
        display: flex;
        justify-content: center;
        margin: 0 auto 28px;
        width: 100%;
    }
    .home-scanner-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 44px;
        padding: 0.7rem 1.2rem;
        border-radius: 8px;
        background: var(--brand-button-bg, #29abe2);
        color: var(--brand-button-text, #fff);
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(18, 90, 130, 0.16);
    }
    .home-scanner-link:hover {
        background: var(--brand-button-hover-bg, #1a8fc4);
        color: var(--brand-button-hover-text, #fff);
    }
</style>
@endpush

@section('content')
    <section class="faq-section">
        <div class="faq-container">

            <div class="faq-header">
                <h2>Frequently Asked Questions</h2>
            </div>

            <h3 class="faq-subtitle">Getting Started</h3>

            <div class="faq-list">
                <div class="faq-item">
                    <p>
                        <a class="faq-toggle" data-bs-toggle="collapse" href="#registerCollapse" role="button"
                            aria-expanded="false" aria-controls="registerCollapse">
                            <strong>How can I register?</strong>
                        </a>
                    </p>
                    <div class="collapse" id="registerCollapse">
                        <div class="faq-video mt-2">
                            <video width="100%" controls playsinline>
                                <source src="{{ asset('videos/how_to_register.mp4') }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <p><strong>Who can register?</strong></p>
                    <p class="mb-0 mt-2 text-muted">
                        Students and employees can register online. After submitting the form, staff will review and approve your account.
                    </p>
                </div>

                <div class="faq-item">
                    <p><strong>Where do I sign in for attendance?</strong></p>
                    <p class="mb-0 mt-2">
                        <a href="{{ route('attendance.scan') }}" class="faq-toggle">Open the attendance scanner</a>
                    </p>
                </div>

                @guest
                <div class="faq-item">
                    <p><strong>Ready to register?</strong></p>
                    <p class="mb-0 mt-2">
                        <a href="{{ route('patron.register') }}" class="faq-toggle">Student or employee registration</a>
                    </p>
                </div>
                @endguest
            </div>
        </div>
    </section>
@endsection


