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
 .faq-accordion .faq-item {
        padding: 0;
        overflow: hidden;
    }
    .faq-accordion-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        width: 100%;
        padding: 15px 20px;
        border: 0;
        background: transparent;
        color: #000;
        font: inherit;
        font-weight: 700;
        text-align: left;
        text-decoration: none;
        cursor: pointer;
    }
    .faq-accordion-toggle:hover,
    .faq-accordion-toggle:focus {
        color: #0d6efd;
        outline: none;
        text-decoration: none;
    }
    .faq-accordion-toggle::after {
        content: "";
        flex: 0 0 auto;
        width: 0.6rem;
        height: 0.6rem;
        border-bottom: 2px solid currentColor;
        border-right: 2px solid currentColor;
        transform: rotate(45deg);
        transition: transform 0.2s ease;
    }
    .faq-accordion-toggle[aria-expanded="true"]::after {
        transform: rotate(225deg);
    }
    .faq-answer {
        padding: 0 20px 18px;
    }
    @media (max-width: 575.98px) {
        .faq-accordion-toggle {
            padding: 12px 14px;
        }
        .faq-answer {
            padding: 0 14px 14px;
        }
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
 @auth
            <div class="faq-list faq-accordion" id="homeFaqAccordion">
                <div class="faq-item">
                    <button class="faq-accordion-toggle collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#registerCollapse" aria-expanded="false" aria-controls="registerCollapse">
                        How can I register?
                    </button>
                    <div class="collapse" id="registerCollapse" data-bs-parent="#homeFaqAccordion">
                        <div class="faq-answer">
                            <video width="100%" controls playsinline>
                                <source src="{{ asset('videos/how_to_register.mp4') }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-accordion-toggle collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#eligibleCollapse" aria-expanded="false" aria-controls="eligibleCollapse">
                        Who can register?
                    </button>
                    <div class="collapse" id="eligibleCollapse" data-bs-parent="#homeFaqAccordion">
                        <div class="faq-answer text-muted">
                            Students and employees can register online. After submitting the form, staff will review and approve your account.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-accordion-toggle collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#attendanceCollapse" aria-expanded="false" aria-controls="attendanceCollapse">
                        Where do I sign in for attendance?
                    </button>
                    <div class="collapse" id="attendanceCollapse" data-bs-parent="#homeFaqAccordion">
                        <div class="faq-answer">
                            <a href="{{ route('attendance.scan') }}">Open the attendance scanner</a>
                        </div>
                    </div>
                </div>
            </div>
            @else
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


