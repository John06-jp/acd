<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Library Landing</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/books/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/navbar.css') }}">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="logo" style="margin-left: 100px;">
            <img src="{{ asset('images/pantasLogo.png') }}" alt="Library Logo">
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mb-0">
            @csrf
            <button type="submit" class="logout-btn" onclick="logout()" style="margin-right: 60px;">Logout</button>
        </form>
    </header>

    <!-- Hero Banner -->
    <section class="hero-text" style="padding:0; margin:0;">
        <div class="acd-banner" role="banner">
            <div class="acd-banner-watermark" aria-hidden="true">
                <img src="{{ asset('images/pantasLogo.png') }}" alt="">
            </div>
            <div class="acd-banner-waves" aria-hidden="true">
                <svg viewBox="0 0 260 180" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMaxYMid meet">
                    <path d="M20 20 Q80 -10 140 20 Q200 50 260 20"  stroke="#1a73e8" stroke-width="1.6" stroke-opacity="0.18" fill="none"/>
                    <path d="M20 45 Q80 15 140 45 Q200 75 260 45"  stroke="#1a73e8" stroke-width="1.6" stroke-opacity="0.18" fill="none"/>
                    <path d="M20 70 Q80 40 140 70 Q200 100 260 70" stroke="#1a73e8" stroke-width="1.6" stroke-opacity="0.18" fill="none"/>
                    <path d="M20 95 Q80 65 140 95 Q200 125 260 95" stroke="#1a73e8" stroke-width="1.6" stroke-opacity="0.18" fill="none"/>
                    <path d="M20 120 Q80 90 140 120 Q200 150 260 120" stroke="#1a73e8" stroke-width="1.6" stroke-opacity="0.18" fill="none"/>
                    <path d="M20 145 Q80 115 140 145 Q200 175 260 145" stroke="#1a73e8" stroke-width="1.6" stroke-opacity="0.12" fill="none"/>
                </svg>
            </div>
            <div class="acd-banner-inner">
                <div class="acd-banner-text">
                    <h1 class="acd-banner-title">
                        <span class="acd-banner-line">ASSUMPTION</span>
                        <span class="acd-banner-line">COLLEGE OF DAVAO</span>
                    </h1>
                    <div class="acd-banner-rule" aria-hidden="true"></div>
                    <p class="acd-banner-powered">POWERED BY PANTAS</p>
                </div>
                <div class="acd-banner-seal" aria-label="ACD Seal">
                    <img src="{{ asset('images/pantasLogo.png') }}" alt="Assumption College of Davao">
                </div>
            </div>
        </div>
    </section>

    <h1 style="text-align: center; margin-bottom: 30px; margin-top: 30px;">New Arrival Books</h1>

    <!-- Carousel -->
    <div class="carousel">
        <div class="carousel-container">
            <div class="arrow left" onclick="slide(-1)">
                <svg viewBox="0 0 20 20">
                    <path d="M12.5 3L5 10l7.5 7" stroke="#5b5e64" stroke-width="2.5" fill="none" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>

            <div class="carousel-track" id="carouselTrack">
                @foreach ($carouselBooks as $book)
                <div class="carosel" onclick="showBookDetails(
              '{{ $book->cover_image ? asset('public/storage/' . $book->cover_image) : asset('images/defaultBook.png') }}',
              '{{ $book->title_statement }}',
              '{{ $book->main_author }}',
              '{{ $book->call_number }}',
               {{ $book->id }},
              '{{ $book->availability }}'
            )">
                    <img src="{{ $book->cover_image ? asset('public/storage/' . $book->cover_image) : asset('images/defaultBook.png') }}"
                        alt="{{ $book->title_statement }}">
                    <p>{{ $book->title_statement }}</p>
                </div>
                @endforeach
            </div>

            <div class="arrow right" onclick="slide(1)">
                <svg viewBox="0 0 20 20">
                    <path d="M7.5 3L15 10l-7.5 7" stroke="#5b5e64" stroke-width="2.5" fill="none" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Layout -->
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3>Courses</h3>
            <a href="{{ route('books.landing', ['course' => 'all']) }}" class="{{ request('course', 'all') === 'all' ? 'active' : '' }}">
                View All
            </a>

            @foreach ($courses as $course)
            <a href="{{ route('books.landing', ['course' => $course]) }}" class="{{ request('course') === $course ? 'active' : '' }}">
                {{ $course }}
            </a>
            @endforeach

            <button id="e-book" onclick="goToEBookPage()">E-Book</button>
        </aside>


        <!-- Main Content -->
        <main class="main-content">
            <!-- Filters -->
            <div class="filters">
                <div class="search">
                    <input type="text" id="searchBar" placeholder="Search book title..." oninput="filterBooks()">
                </div>
                <div class="lahi">
                    <select id="yearFilter" onchange="filterBooks()">
                        <option value="All">All Years</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>

                    <select id="courseInput" onchange="filterBooks()">
                        <option value="All">All Resources</option>
                        <option value="Digital/Scanned">Digital/Scanned</option>
                        <option value="Video/Multimedia Resources">Video/Multimedia Resources</option>
                        <option value="Research Papers">Research Papers</option>
                        <option value="Course Modules">Course Modules</option>
                        <option value="E-Book">E-Book</option>
                    </select>

                    <select id="programInput" onchange="filterBooks()">
                        <option value="All">All Subjects</option>
                        @foreach($programs as $id => $programLabel)
                        <option value="{{ strtolower($programLabel) }}">{{ $programLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Book Grid -->
            <div class="book-grid" id="bookGrid">
                @foreach ($books as $book)
                <div class="book-card" data-year="{{ $book->year ?? '1st Year' }}"
                    data-course="{{ $book->course ?? 'General' }}"
                    data-program="{{ $book->program ?? 'General'}}"
                    data-category="{{ $book->course ?? 'General' }}" onclick="showBookDetails(
              '{{ $book->cover_image ? asset('public/storage/' . $book->cover_image) : asset('images/defaultBook.png') }}',
              '{{ $book->title_statement }}',
              '{{ $book->main_author }}',
              '{{ $book->call_number }}',
              {{ $book->id }},
              '{{ $book->availability }}'
            )">
                    <p class="{{ $book->availability === 'Available' ? 'text-success' : 'text-danger' }}">
                        {{ $book->availability }}
                    </p>
                    <img src="{{ $book->cover_image ? asset('public/storage/' . $book->cover_image) : asset('images/defaultBook.png') }}"
                        alt="{{ $book->title_statement }}">
                    <p>{{ $book->title_statement }}</p>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $books->links('pagination::bootstrap-5') }}
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal" id="bookModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalImg" src="" alt="Book Image" style="width: 100%; height: auto;">
            <h2 id="modalTitle"></h2>
            <h4 id="modalAuthor"></h4>
            <p id="modalDescription"></p>
            <button id="checkoutBtn" class="btn btn-primary mt-3" style="display: none;" onclick="printReceipt()" hidden>Self
                Check-Out</button>
        </div>
    </div>

    <!-- Footer -->


    <script>
        let activeCategory = 'all';
        let selectedBookId = null;

        let selectedBook = {};

        function showBookDetails(img, title, author, description, bookId, availability) {
            document.getElementById('modalImg').src = img;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalAuthor').textContent = author;
            document.getElementById('modalDescription').textContent = description;
            selectedBook = {
                title: title,
                author: author,
                time: new Date().toLocaleString(), // capture current time
                user: currentUser.name
            };

            const checkoutBtn = document.getElementById('checkoutBtn');
            checkoutBtn.style.display = availability === 'Available' ? 'block' : 'none';

            document.getElementById('bookModal').style.display = 'flex';
        }


        function closeModal() {
            document.getElementById('bookModal').style.display = 'none';
        }

        function filterBooks() {
            const year = document.getElementById('yearFilter').value.toLowerCase();
            const course = document.getElementById('courseInput').value.toLowerCase();
            const program = document.getElementById('programInput').value.toLowerCase();
            const search = document.getElementById('searchBar').value.toLowerCase();
            const cards = document.querySelectorAll('.book-card');

            cards.forEach(card => {
                const cardYear = card.getAttribute('data-year').toLowerCase();
                const cardCourse = card.getAttribute('data-course').toLowerCase();
                const cardProgram = card.getAttribute('data-program').toLowerCase();
                const cardTitle = card.textContent.toLowerCase();
                const cardCategory = card.dataset.category.toLowerCase();

                const matchesYear = (year === 'all' || cardYear === year);
                const matchesCourse = (course === 'all' || cardCourse === course);
                const matchesProgram = (program === 'all' || cardProgram === program);
                const matchesSearch = (search === '' || cardTitle.includes(search));
                const matchesCategory = (activeCategory === 'all' || cardCategory === activeCategory);

                card.style.display = (matchesYear && matchesCourse && matchesProgram && matchesSearch && matchesCategory) ? 'block' : 'none';
            });
        }

        function filterByCategory(category, btn) {
            document.getElementById('courseInput').value = 'All';
            document.getElementById('programInput').value = 'All';
            activeCategory = category.toLowerCase();
            document.querySelectorAll(".sidebar button").forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            filterBooks();
        }

        function goToEBookPage() {
            window.location.href = '/ebooks';
        }

        function logout() {
            window.location.href = "{{ route('logout') }}";
        }




        const track = document.getElementById('carouselTrack');
        let scrollAmount = 0;

        function slide(direction) {
            const bookWidth = 130;
            scrollAmount += direction * bookWidth * 2;

            if (scrollAmount < 0) scrollAmount = 0;
            const maxScroll = track.scrollWidth - track.clientWidth;
            if (scrollAmount > maxScroll) scrollAmount = maxScroll;

            track.style.transform = `translateX(-${scrollAmount}px)`;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/qz-tray/qz-tray.js"></script>
    <script>
        qz.security.setCertificatePromise(function (resolve, reject) {
            resolve("-----BEGIN CERTIFICATE-----\nYOUR CERT HERE\n-----END CERTIFICATE-----");
        });
        qz.security.setSignaturePromise(function (toSign) {
            return function (resolve, reject) {
                resolve("SIGNATURE");
            };
        });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/qz-tray/qz-tray.js"></script>

    <script>
        function printReceipt() {
            let receipt = [
                { type: 'raw', format: 'plain', data: 'PANTAS\n' },
                { type: 'raw', format: 'plain', data: '------------------------------\n' },
                { type: 'raw', format: 'plain', data: `Title: ${selectedBook.title}\n` },
                { type: 'raw', format: 'plain', data: `Author: ${selectedBook.author}\n` },
                { type: 'raw', format: 'plain', data: `Checked out by: ${selectedBook.user}\n` },
                { type: 'raw', format: 'plain', data: `Time: ${selectedBook.time}\n` },
                { type: 'raw', format: 'plain', data: '------------------------------\n' },
                { type: 'raw', format: 'plain', data: 'Thank you!\n\n\n' },
                { type: 'raw', format: 'plain', data: '\x1B\x69' } // Cut paper
            ];

            qz.websocket.connect()
                .then(() => qz.printers.find("POS-58"))
                .then(printer => {
                    let config = qz.configs.create(printer);
                    return qz.print(config, receipt);
                })
                .catch(err => console.error("Print error:", err));
        }
    </script>
    <script>
        let currentUser = {
            name: "{{ auth()->user()->lname ?? '' }}, {{ auth()->user()->fname ?? '' }}"
        };
    </script>
</body>

</html>