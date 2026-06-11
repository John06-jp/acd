<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ config('app.name') }} — Attendance</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ \App\Support\Branding::stylesheetUrl() }}">
  <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/attendance/scan.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="scan-kiosk-page">

<header>
  <div class="header">
    <div class="logo-title">
      <img src="{{ asset('images/pantasLogo.png') }}" alt="Logo">
      <div class="system-title">Powered by Pantas</div>
    </div>
  </div>
</header>

<div class="main">
  <div class="sidebar" id="scanSidebar">
    <div class="date" id="currentDate">Date</div>
    <div class="time" id="currentTime">--:--:--</div>
    <div class="profile-pic">
      <img src="{{ asset('images/2x2_undifined_gender.jpg') }}" alt="Default Profile">
    </div>
    <div id="earlyOutAlarm" class="early-out-alarm" hidden aria-live="assertive" role="alert">
      <div class="early-out-alarm__icon">⚠</div>
      <div class="early-out-alarm__title">Early checkout not allowed</div>
      <p class="early-out-alarm__message" id="earlyOutAlarmMessage"></p>
      <p class="early-out-alarm__hint">Allowed after <strong id="earlyOutAlarmTime">{{ $earlyDepartureCutoffLabel ?? '4:00 PM' }}</strong></p>
    </div>
  </div>

  <div class="right-content">
    <form id="scanForm">
      @csrf
      <textarea name="qrcode" id="qrcode" style="opacity:0; position:absolute;" autofocus autocomplete="off"></textarea>
    </form>
    <video muted autoplay loop controls class="ads-vid">
      <source src="{{ asset('videos/area51_product_slideshow.mp4') }}" type="video/mp4">
    </video>
  </div>
</div>

<footer>
  <div class="footer1">
    <div class="footer-logo">
      <div class="marquee-container">
        <div class="marquee">Welcome to {{ config('app.name') }}</div>
      </div>
    </div>
  </div>
</footer>

<div id="sectionModal" class="section-modal" aria-hidden="true">
  <div class="modal-content section-picker-modal">
    <h2>Select library section</h2>
    <div class="section-buttons" id="sectionButtons" data-count="{{ count($attendanceSections ?? []) }}">
      @forelse($attendanceSections ?? [] as $section)
        <button type="button" data-section="{{ $section }}">{{ $section }}</button>
      @empty
        <p class="section-empty-msg">No sections configured. Add sections under Attendance → Section Picker.</p>
      @endforelse
    </div>
  </div>
</div>

<div id="feedbackModal" class="section-modal" aria-hidden="true">
  <div class="modal-content feedback-card">
    <h2>How was your library experience?</h2>
    <div class="feedback-options">
      <button type="button" data-rating="excellent">😊<span>Excellent</span></button>
      <button type="button" data-rating="good">🙂<span>Good</span></button>
      <button type="button" data-rating="medium">😐<span>Medium</span></button>
      <button type="button" data-rating="poor">🙁<span>Poor</span></button>
      <button type="button" data-rating="very_bad">😠<span>Very Bad</span></button>
    </div>
    <button type="button" id="declineFeedback" class="decline-btn">Skip</button>
  </div>
</div>

<script>
  const LOGOUT_FEEDBACK_ENABLED = @json($logoutFeedbackEnabled ?? true);
  const SECTION_PICKER_ENABLED = @json($sectionPickerEnabled ?? true);
  const HAS_ATTENDANCE_SECTIONS = @json(count($attendanceSections ?? []) > 0);
  const EARLY_DEPARTURE_ENABLED = @json($earlyDepartureEnabled ?? true);
  const feedbackModal = document.getElementById('feedbackModal');
  const earlyOutAlarm = document.getElementById('earlyOutAlarm');
  const earlyOutAlarmMessage = document.getElementById('earlyOutAlarmMessage');
  const earlyOutAlarmTime = document.getElementById('earlyOutAlarmTime');
  const scanSidebar = document.getElementById('scanSidebar');
  const sectionModal = document.getElementById('sectionModal');
  let selectedStudent = null;
  let currentStudentId = null;
  let clearDisplayTimer = null;

  document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('qrcode');
    const profileImg = document.querySelector('.profile-pic img');
    const sidebar = document.querySelector('.sidebar');
    let isCooldown = false;

    setInterval(() => input.focus(), 100);
    input.focus();

    function clearDisplay() {
      if (feedbackModal && feedbackModal.style.display === 'flex') return;
      profileImg.src = "{{ asset('images/2x2_undifined_gender.jpg') }}";
      document.querySelectorAll('.name-box').forEach(box => box.remove());
      hideEarlyOutAlarm();
      selectedStudent = null;
      currentStudentId = null;
    }

    function playAlarmTone() {
      try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const playBeep = (start, freq) => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.type = 'square';
          osc.frequency.value = freq;
          gain.gain.value = 0.15;
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start(start);
          osc.stop(start + 0.2);
        };
        playBeep(ctx.currentTime, 880);
        playBeep(ctx.currentTime + 0.25, 660);
        playBeep(ctx.currentTime + 0.5, 880);
      } catch (e) {
        /* audio optional */
      }
    }

    function showEarlyOutAlarm(data) {
      if (!earlyOutAlarm) return;
      const student = data.student || {};
      const name = [student.firstname, student.lastname].filter(Boolean).join(' ');
      const year = student.year ? ` (${student.year})` : '';

      if (earlyOutAlarmMessage) {
        earlyOutAlarmMessage.textContent = data.message || 'Cannot check out before the allowed time.';
      }
      if (earlyOutAlarmTime && data.allowed_after) {
        earlyOutAlarmTime.textContent = data.allowed_after;
      }

      profileImg.src = profileUrl(student.profile_picture);

      const div = document.createElement('div');
      div.classList.add('name-box', 'name-box--blocked');
      div.innerHTML = `
        <div class="student-name">${name}${year}</div>
        <div class="label">Still checked in</div>
        <div class="status-button status-blocked">NOT ALLOWED</div>
      `;
      sidebar.appendChild(div);

      earlyOutAlarm.hidden = false;
      scanSidebar?.classList.add('sidebar--alarm');
      playAlarmTone();
      scheduleClear(8000);
    }

    function hideEarlyOutAlarm() {
      earlyOutAlarm?.setAttribute('hidden', '');
      scanSidebar?.classList.remove('sidebar--alarm');
    }

    function scheduleClear(delayMs) {
      if (clearDisplayTimer) clearTimeout(clearDisplayTimer);
      clearDisplayTimer = setTimeout(clearDisplay, delayMs);
    }

    function showLogoutFeedback() {
      const enabled = LOGOUT_FEEDBACK_ENABLED;
      if (!enabled || !feedbackModal || !currentStudentId) {
        scheduleClear(2000);
        return;
      }
      if (clearDisplayTimer) {
        clearTimeout(clearDisplayTimer);
        clearDisplayTimer = null;
      }
      setTimeout(() => {
        feedbackModal.style.display = 'flex';
        feedbackModal.setAttribute('aria-hidden', 'false');
      }, 500);
    }

    function profileUrl(path) {
      return path ? "{{ asset('') }}" + path.replace(/^\//, '') : "{{ asset('images/2x2_undifined_gender.jpg') }}";
    }

    input.addEventListener('keypress', function (e) {
      if (e.key !== 'Enter') return;
      e.preventDefault();
      if (isCooldown) return;
      isCooldown = true;
      setTimeout(() => { isCooldown = false; }, 300);

      const formData = new FormData();
      formData.append('qrcode', input.value.trim().replace(/\r/g, ''));
      formData.append('_token', '{{ csrf_token() }}');

      fetch("{{ route('attendance.process') }}", { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
          if (feedbackModal && feedbackModal.style.display === 'flex') {
            closeFeedbackModal();
          }
          clearDisplay();

          if (data.type === 'early_out_blocked') {
            showEarlyOutAlarm(data);
            input.value = '';
            return;
          }

          if (data.type === 'student') {
            selectedStudent = data.student;
            currentStudentId = data.student_id;
            profileImg.src = profileUrl(data.student.profile_picture);

            if (data.next_status === 'OUT') {
              fetch("{{ route('attendance.section') }}", {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Accept': 'application/json',
                },
                body: JSON.stringify({ student_id: currentStudentId, section: null })
              })
              .then(async res => {
                const response = await res.json();
                if (res.status === 403) {
                  showEarlyOutAlarm({
                    message: response.message,
                    allowed_after: response.allowed_after,
                    student: selectedStudent,
                  });
                  return;
                }
                const div = document.createElement('div');
                div.classList.add('name-box');
                div.innerHTML = `
                  <div class="student-name">${selectedStudent.firstname} ${selectedStudent.lastname}</div>
                  <div class="label">Name</div>
                  <div class="status-button status-out">OUT</div>
                  <div class="timestamp">${response.scanned_at}</div>
                `;
                sidebar.appendChild(div);

                const feedbackOn = response.logout_feedback_enabled ?? data.logout_feedback_enabled ?? LOGOUT_FEEDBACK_ENABLED;
                if (feedbackOn) {
                  showLogoutFeedback();
                } else {
                  scheduleClear(2000);
                }
              });
            } else {
              const sectionPickerOn = (data.section_picker_enabled ?? SECTION_PICKER_ENABLED) && HAS_ATTENDANCE_SECTIONS;
              if (sectionPickerOn) {
                sectionModal.style.display = 'flex';
                sectionModal.setAttribute('aria-hidden', 'false');
              } else {
                fetch("{{ route('attendance.section') }}", {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                  },
                  body: JSON.stringify({ student_id: currentStudentId, section: null })
                })
                .then(res => res.json())
                .then(response => {
                  const div = document.createElement('div');
                  div.classList.add('name-box');
                  div.innerHTML = `
                    <div class="student-name">${selectedStudent.firstname} ${selectedStudent.lastname}</div>
                    <div class="label">Name</div>
                    <div class="status-button">${response.status}</div>
                    <div class="timestamp">${response.scanned_at}</div>
                  `;
                  sidebar.appendChild(div);
                  scheduleClear(3000);
                });
              }
            }
          } else if (data.type === 'error') {
            const div = document.createElement('div');
            div.classList.add('name-box');
            div.innerHTML = `
              <div class="student-name">${data.message}</div>
              <div class="label">Error</div>
            `;
            sidebar.appendChild(div);
            scheduleClear(2000);
          }

          input.value = '';
        })
        .catch(err => console.error(err));
    });

    document.querySelectorAll('.section-buttons button').forEach(btn => {
      btn.addEventListener('click', function () {
        if (!currentStudentId) return;

        fetch("{{ route('attendance.section') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            student_id: currentStudentId,
            section: this.dataset.section
          })
        })
        .then(res => res.json())
        .then(response => {
          sectionModal.style.display = 'none';
          sectionModal.setAttribute('aria-hidden', 'true');

          const div = document.createElement('div');
          div.classList.add('name-box');
          div.innerHTML = `
            <div class="student-name">${selectedStudent.firstname} ${selectedStudent.lastname}</div>
            <div class="label">${this.dataset.section}</div>
            <div class="status-button">${response.status}</div>
            <div class="timestamp">${response.scanned_at}</div>
          `;
          sidebar.appendChild(div);
          scheduleClear(3000);
        });
      });
    });

    function closeFeedbackModal() {
      if (!feedbackModal) return;
      feedbackModal.style.display = 'none';
      feedbackModal.setAttribute('aria-hidden', 'true');
    }

    function sendFeedback(rating = null, declined = 0) {
      if (!currentStudentId) {
        closeFeedbackModal();
        clearDisplay();
        input.focus();
        return;
      }

      fetch("{{ route('attendance.feedback.store') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          student_id: currentStudentId,
          rating: rating,
          declined: declined ? 1 : 0,
        }),
      }).catch(err => console.error(err)).finally(() => {
        closeFeedbackModal();
        clearDisplay();
        input.focus();
      });
    }

    document.querySelectorAll('.feedback-options button').forEach(btn => {
      btn.addEventListener('click', function () {
        sendFeedback(this.dataset.rating, 0);
      });
    });

    document.getElementById('declineFeedback')?.addEventListener('click', function () {
      sendFeedback(null, 1);
    });

    function updateDateTime() {
      const now = new Date();
      const dateEl = document.getElementById('currentDate');
      const timeEl = document.getElementById('currentTime');
      if (dateEl && timeEl) {
        dateEl.textContent = now.toLocaleDateString('en-GB', {
          weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        timeEl.textContent = now.toLocaleTimeString('en-US');
      }
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
  });
</script>
</body>
</html>
