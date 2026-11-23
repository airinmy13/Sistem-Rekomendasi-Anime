<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AniMatch — Sistem Rekomendasi Anime Modern</title>
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          colors: {
            slateDark: '#0F172A',
            violetAccent: '#8B5CF6',
            cyanAccent: '#06B6D4',
          },
        },
      },
    };
  </script>
  <style>
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #0F172A;
    }
    ::-webkit-scrollbar-thumb {
      background: #1E293B;
      border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #8B5CF6;
    }

    /* Angled Marquee Animations (Pure CSS for 60fps performance) */
    @keyframes scrollUp {
      0% {
        transform: translateY(0%);
      }
      100% {
        transform: translateY(-50%);
      }
    }

    @keyframes scrollDown {
      0% {
        transform: translateY(-50%);
      }
      100% {
        transform: translateY(0%);
      }
    }

    .marquee-up {
      animation: scrollUp 34s linear infinite;
    }

    .marquee-down {
      animation: scrollDown 38s linear infinite;
    }

    .marquee-up-slow {
      animation: scrollUp 42s linear infinite;
    }

    .marquee-down-slow {
      animation: scrollDown 36s linear infinite;
    }

    /* Pause animation smoothly when user hovers over the poster wall */
    .poster-wall:hover .marquee-up,
    .poster-wall:hover .marquee-down,
    .poster-wall:hover .marquee-up-slow,
    .poster-wall:hover .marquee-down-slow {
      animation-play-state: paused;
    }

    /* Angled Container */
    .angled-grid-wrapper {
      transform: rotate(-13deg) scale(1.18);
      transform-origin: center center;
    }

    /* Seamless Edge Fades for Right Poster Wall */
    .mask-fade-left {
      background: linear-gradient(90deg, #0F172A 0%, rgba(15, 23, 42, 0.85) 22%, rgba(15, 23, 42, 0.15) 65%, rgba(15, 23, 42, 0) 100%);
    }
    .mask-fade-vertical {
      background: linear-gradient(180deg, #0F172A 0%, rgba(15, 23, 42, 0) 18%, rgba(15, 23, 42, 0) 80%, #0F172A 100%);
    }

    /* Card Glow & Smooth Scale */
    .anime-card {
      transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.28s ease, border-color 0.28s ease;
    }
    .anime-card:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 20px 35px -10px rgba(139, 92, 246, 0.28), 0 0 20px -5px rgba(6, 182, 212, 0.2);
      border-color: rgba(139, 92, 246, 0.55);
    }
  </style>
</head>
<body class="bg-[#0F172A] text-slate-100 font-sans antialiased overflow-x-hidden selection:bg-[#8B5CF6] selection:text-white">

  <!-- ==================== SPLIT HERO SECTION ==================== -->
  <section class="relative min-h-[92vh] w-full flex flex-col lg:flex-row overflow-hidden border-b border-slate-800/80">
    
    <!-- LEFT PANEL (60% Desktop Width): Solid Dark Background + Navbar + Search -->
    <div class="relative z-20 w-full lg:w-[60%] bg-[#0F172A] flex flex-col justify-between px-6 sm:px-10 lg:px-16 py-6 lg:py-10">
      
      <!-- Navbar -->
      <header class="flex items-center justify-between">
        <a href="#" onclick="resetToDefault(); return false;" class="flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#8B5CF6] to-[#06B6D4] flex items-center justify-center shadow-lg shadow-violet-500/25 group-hover:scale-105 transition-transform">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z" />
            </svg>
          </div>
          <div>
            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-cyan-300 bg-clip-text text-transparent">AniMatch</span>
            <span class="hidden sm:inline-block ml-2 text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-violet-500/15 text-violet-300 border border-violet-500/30">AI Hybrid</span>
          </div>
        </a>

        <nav class="flex items-center gap-3 sm:gap-5 text-sm text-slate-400">
          <a href="#recommendations-section" class="hover:text-white transition-colors">Eksplorasi</a>
          <button onclick="pickRandomAnime()" class="px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700/80 hover:border-cyan-500/50 transition-all flex items-center gap-2 text-xs sm:text-sm font-medium">
            <span>🎲</span>
            <span>Acak Anime</span>
          </button>
        </nav>
      </header>

      <!-- Main Hero Content -->
      <div class="my-auto py-12 lg:py-8 max-w-2xl">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900/90 border border-slate-800 text-xs font-medium text-cyan-300 mb-6 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-[#06B6D4] animate-pulse"></span>
          <span>Content-Based Filtering + Jikan API + Anime-Planet</span>
        </div>

        <h1 class="text-3xl sm:text-5xl lg:text-[3.25rem] font-extrabold tracking-tight leading-[1.15] text-white mb-5">
          Temukan Anime Berikutnya yang <span class="bg-gradient-to-r from-[#8B5CF6] via-fuchsia-400 to-[#06B6D4] bg-clip-text text-transparent">Paling Cocok</span> Untukmu.
        </h1>

        <p class="text-slate-400 text-base sm:text-lg leading-relaxed mb-8">
          Ketik satu judul anime favoritmu. Mesin rekomendasi kami menganalisis kemiripan genre, atmosfer cerita, dan studio untuk menyusun daftar tontonan terbaik secara instan.
        </p>

        <!-- Smart Search Bar with Autocomplete -->
        <div class="relative w-full max-w-xl z-30" id="search-container">
          <div class="relative flex items-center">
            <div class="absolute left-4 text-slate-400 pointer-events-none">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16.65 10.825a5.825 5.825 0 11-11.65 0 5.825 5.825 0 0111.65 0z" />
              </svg>
            </div>
            <input
              id="anime-search-input"
              type="text"
              autocomplete="off"
              placeholder="Cari judul anime... (misal: Solo Leveling, Frieren, Steins;Gate)"
              class="w-full pl-12 pr-28 py-4 rounded-2xl bg-slate-900/95 border border-slate-700/90 focus:border-[#8B5CF6] focus:ring-4 focus:ring-violet-500/20 text-white placeholder-slate-500 text-sm sm:text-base outline-none transition-all shadow-2xl shadow-black/40"
            />
            <button
              id="search-submit-btn"
              onclick="handleSearchSubmit()"
              class="absolute right-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#8B5CF6] to-[#06B6D4] hover:opacity-95 active:scale-95 text-white font-semibold text-xs sm:text-sm shadow-lg shadow-violet-600/25 transition-all"
            >
              Cari Mirip
            </button>
          </div>

          <!-- Autocomplete Dropdown -->
          <div
            id="autocomplete-dropdown"
            class="hidden absolute left-0 right-0 mt-2 rounded-2xl bg-slate-900/95 backdrop-blur-xl border border-slate-700/90 shadow-2xl overflow-hidden max-h-80 overflow-y-auto divide-y divide-slate-800/80"
          >
            <!-- Populated dynamically via JS -->
          </div>
        </div>

        <!-- Quick Genre Filter Pills -->
        <div class="mt-6">
          <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">Atau eksplorasi cepat berdasarkan genre:</p>
          <div class="flex flex-wrap gap-2" id="genre-pills">
            <button onclick="filterByGenre('All')" data-genre="All" class="genre-pill active px-4 py-2 rounded-xl text-xs sm:text-sm font-medium bg-[#8B5CF6] text-white shadow-md shadow-violet-500/25 transition-all">
              Semua Populer
            </button>
            <button onclick="filterByGenre('Action')" data-genre="Action" class="genre-pill px-4 py-2 rounded-xl text-xs sm:text-sm font-medium bg-slate-800/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-700/70 transition-all">
              ⚔️ Action
            </button>
            <button onclick="filterByGenre('Fantasy')" data-genre="Fantasy" class="genre-pill px-4 py-2 rounded-xl text-xs sm:text-sm font-medium bg-slate-800/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-700/70 transition-all">
              ✨ Fantasy
            </button>
            <button onclick="filterByGenre('Slice of Life')" data-genre="Slice of Life" class="genre-pill px-4 py-2 rounded-xl text-xs sm:text-sm font-medium bg-slate-800/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-700/70 transition-all">
              ☕ Slice of Life
            </button>
            <button onclick="filterByGenre('Sci-Fi')" data-genre="Sci-Fi" class="genre-pill px-4 py-2 rounded-xl text-xs sm:text-sm font-medium bg-slate-800/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-700/70 transition-all">
              🚀 Sci-Fi
            </button>
            <button onclick="filterByGenre('Romance')" data-genre="Romance" class="genre-pill px-4 py-2 rounded-xl text-xs sm:text-sm font-medium bg-slate-800/80 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-700/70 transition-all">
              💖 Romance
            </button>
          </div>
        </div>
      </div>

      <!-- Subtle Stats Footer in Hero -->
      <div class="flex flex-wrap items-center gap-6 pt-4 border-t border-slate-800/70 text-xs text-slate-400">
        <div class="flex items-center gap-2">
          <span class="text-cyan-400 font-bold text-sm">{{ number_format($totalCount ?? 5000) }}+</span>
          <span>Dataset Bersih (Kaggle)</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-violet-400 font-bold text-sm">Cosine Similarity</span>
          <span>TF-IDF Recommendation</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-amber-400 font-bold text-sm">Anime-Planet</span>
          <span>Direct Linking</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-emerald-400 font-bold text-sm">Jikan v4 API</span>
          <span>Live Fallback Search</span>
        </div>
      </div>
    </div>

    <!-- RIGHT PANEL (40-45% Desktop Width): Angled 4-Column Alternating Marquee Poster Wall -->
    <div class="relative w-full lg:w-[42%] lg:-ml-[2%] h-[360px] sm:h-[420px] lg:h-auto overflow-hidden bg-[#0F172A] poster-wall select-none">
      
      <!-- Angled 4-Column Grid Container -->
      <div class="angled-grid-wrapper absolute inset-[-18%] grid grid-cols-4 gap-3 sm:gap-4 p-2">
        <!-- Column 1: Scrolls UP -->
        <div class="flex flex-col gap-3 sm:gap-4 marquee-up" id="marquee-col-1"></div>
        <!-- Column 2: Scrolls DOWN -->
        <div class="flex flex-col gap-3 sm:gap-4 marquee-down" id="marquee-col-2"></div>
        <!-- Column 3: Scrolls UP (Slightly different speed for organic feel) -->
        <div class="flex flex-col gap-3 sm:gap-4 marquee-up-slow" id="marquee-col-3"></div>
        <!-- Column 4: Scrolls DOWN -->
        <div class="flex flex-col gap-3 sm:gap-4 marquee-down-slow" id="marquee-col-4"></div>
      </div>

      <!-- Seamless Left Gradient Blend (Merges into #0F172A Left Panel) -->
      <div class="hidden lg:block pointer-events-none absolute inset-y-0 left-0 w-44 mask-fade-left z-10"></div>

      <!-- Seamless Top & Bottom Gradient Blend -->
      <div class="pointer-events-none absolute inset-0 mask-fade-vertical z-10"></div>

      <!-- Subtle Violet/Cyan Ambient Glow -->
      <div class="pointer-events-none absolute -bottom-16 -right-16 w-72 h-72 rounded-full bg-violet-600/20 blur-3xl z-10"></div>
      <div class="pointer-events-none absolute top-10 right-10 w-56 h-56 rounded-full bg-cyan-500/15 blur-3xl z-10"></div>
    </div>
  </section>

  <!-- ==================== MAIN RECOMMENDATION GRID SECTION ==================== -->
  <main id="recommendations-section" class="max-w-7xl mx-auto px-6 sm:px-10 py-14 sm:py-20">
    
    <!-- Section Header & Active Context Banner -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
      <div>
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-cyan-400 mb-2">
          <span id="section-kicker">Rekomendasi Terkurasi</span>
        </div>
        <h2 id="section-title" class="text-2xl sm:text-3xl font-bold text-white">
          Anime Populer & Rating Tertinggi
        </h2>
        <p id="section-subtitle" class="text-slate-400 text-sm sm:text-base mt-1">
          Klik salah satu kartu anime untuk melihat detail lengkap atau mencari anime yang mirip dengannya.
        </p>
      </div>

      <!-- Active Reference Pill (Shown when user selects a specific anime) -->
      <div id="active-reference-box" class="hidden items-center gap-3 px-4 py-2.5 rounded-2xl bg-slate-900 border border-violet-500/40 shadow-lg shadow-violet-500/10">
        <div class="text-xs">
          <span class="text-slate-400 block">Berdasarkan kemiripan dengan:</span>
          <span id="active-reference-name" class="text-white font-bold text-sm">Solo Leveling</span>
        </div>
        <button onclick="resetToDefault()" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Reset Filter">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading-banner" class="hidden mb-8 p-4 rounded-2xl bg-slate-900/90 border border-cyan-500/30 flex items-center gap-3 text-sm text-cyan-300">
      <svg class="w-5 h-5 animate-spin text-cyan-400" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
      <span id="loading-text">Mencari anime di dataset lokal & Jikan API...</span>
    </div>

    <!-- 4-Column Responsive Anime Cards Grid -->
    <div id="anime-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Cards injected dynamically via JS -->
    </div>
  </main>

  <!-- ==================== FOOTER ==================== -->
  <footer class="border-t border-slate-800/80 py-8 px-6 text-center text-xs text-slate-500">
    <p>Sistem Rekomendasi Anime <span class="text-slate-300 font-semibold">AniMatch</span> — Hybrid Dataset Bersih + TF-IDF Cosine Similarity + Integrasi Anime-Planet & Jikan API.</p>
  </footer>

  <!-- ==================== GLASSMORPHISM DETAIL MODAL ==================== -->
  <div id="detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6">
    <!-- Backdrop -->
    <div onclick="closeModal()" class="absolute inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"></div>

    <!-- Modal Card -->
    <div class="relative z-10 w-full max-w-3xl rounded-3xl bg-slate-900/90 backdrop-blur-2xl border border-slate-700/80 shadow-2xl overflow-hidden animate-[fadeIn_0.2s_ease-out]">
      <!-- Close Button -->
      <button onclick="closeModal()" class="absolute top-4 right-4 z-20 w-9 h-9 rounded-full bg-slate-950/70 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/70 flex items-center justify-center transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div class="flex flex-col md:flex-row">
        <!-- Left Cover Image -->
        <div class="w-full md:w-2/5 relative bg-slate-950 aspect-[2/3] md:aspect-auto">
          <img id="modal-poster" src="" alt="Cover Anime" class="w-full h-full object-cover" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent md:hidden"></div>
        </div>

        <!-- Right Details -->
        <div class="w-full md:w-3/5 p-6 sm:p-8 flex flex-col justify-between">
          <div>
            <!-- Badges Row -->
            <div class="flex flex-wrap items-center gap-2 mb-3">
              <span id="modal-match-badge" class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                98% Match
              </span>
              <span id="modal-score-badge" class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                ★ 8.75
              </span>
              <span id="modal-year-badge" class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-300">
                2024
              </span>
            </div>

            <h3 id="modal-title" class="text-2xl sm:text-3xl font-extrabold text-white leading-tight mb-3">
              Judul Anime
            </h3>

            <!-- Metadata Grid -->
            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 mb-4 text-xs">
              <div>
                <span class="text-slate-500 block">Studio</span>
                <span id="modal-studio" class="text-slate-200 font-semibold">Studio</span>
              </div>
              <div>
                <span class="text-slate-500 block">Jumlah Episode</span>
                <span id="modal-episodes" class="text-slate-200 font-semibold">12 Episode</span>
              </div>
            </div>

            <!-- Genres -->
            <div id="modal-genres" class="flex flex-wrap gap-1.5 mb-4"></div>

            <!-- Synopsis -->
            <div class="max-h-36 overflow-y-auto pr-1 text-sm text-slate-300 leading-relaxed mb-6">
              <p id="modal-synopsis">Sinopsis anime akan tampil di sini.</p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-800">
            <button id="modal-find-similar-btn" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-[#8B5CF6] to-[#06B6D4] hover:opacity-95 active:scale-[0.99] text-white font-bold text-xs sm:text-sm shadow-lg shadow-violet-600/25 transition-all flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <span>Cari yang Mirip Ini</span>
            </button>
            <a id="modal-samehadaku-btn" href="#" target="_blank" rel="noopener noreferrer" class="py-3 px-4 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:opacity-95 text-white font-bold text-xs sm:text-sm shadow-lg shadow-red-600/30 transition-all flex items-center justify-center gap-2">
              <span>🎬 Nonton di Samehadaku</span>
            </a>
            <a id="modal-anime-planet-btn" href="#" target="_blank" rel="noopener noreferrer" class="py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 hover:text-white border border-cyan-500/30 font-semibold text-xs sm:text-sm transition-colors flex items-center justify-center gap-2">
              <span>🪐 Info Planet</span>
            </a>
            <button onclick="closeModal()" class="py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium text-xs sm:text-sm transition-colors">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ==================== JAVASCRIPT APPLICATION LOGIC ==================== -->
  <script>
    // Injected from Laravel Controller (Top anime from clean Kaggle dataset with recommendations)
    const INITIAL_ANIME = @json($topAnime ?? []);

    // Fallback static dataset in case backend is empty
    const FALLBACK_DATASET = [
      {
        id: 52991,
        title: "Frieren: Beyond Journey's End",
        name: "Frieren: Beyond Journey's End",
        year: 2023,
        score: 9.33,
        studio: "Madhouse",
        episodes: "28 Episode",
        genres: ["Adventure", "Drama", "Fantasy"],
        image: "https://cdn.myanimelist.net/images/anime/1015/138006l.jpg",
        anime_planet_url: "https://www.anime-planet.com/anime/all?name=Frieren%3A+Beyond+Journey%27s+End",
        synopsis: "Setelah mengalahkan Raja Iblis bersama kelompok pahlawannya, penyihir elf Frieren yang berumur panjang memulai perjalanan baru untuk memahami arti kehidupan manusia dan kenangan bersama rekan-rekannya."
      }
    ];

    const ANIME_DATASET = INITIAL_ANIME && INITIAL_ANIME.length > 0 ? INITIAL_ANIME : FALLBACK_DATASET;

    // Helper function to generate stylish SVG fallback cover if image fails
    function createFallbackCover(title, color1 = '#8B5CF6', color2 = '#06B6D4') {
      const safeTitle = (title || 'Anime').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
      const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="400" height="600" viewBox="0 0 400 600">
          <defs>
            <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="${color1}" stop-opacity="0.85"/>
              <stop offset="100%" stop-color="${color2}" stop-opacity="0.9"/>
            </linearGradient>
          </defs>
          <rect width="400" height="600" fill="#0F172A"/>
          <rect width="400" height="600" fill="url(#g)" opacity="0.35"/>
          <circle cx="320" cy="100" r="120" fill="${color1}" opacity="0.25"/>
          <circle cx="80" cy="500" r="140" fill="${color2}" opacity="0.2"/>
          <rect x="28" y="28" width="344" height="544" rx="20" fill="none" stroke="rgba(255,255,255,0.18)" stroke-width="2"/>
          <text x="50%" y="48%" dominant-baseline="middle" text-anchor="middle" fill="#F8FAFC" font-family="Inter, sans-serif" font-weight="800" font-size="24">${safeTitle.slice(0, 20)}</text>
          <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="#94A3B8" font-family="Inter, sans-serif" font-weight="600" font-size="14">ANIMATCH COVER</text>
        </svg>
      `;
      return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
    }

    // State Management
    let currentDisplayedList = [];
    let currentReferenceAnime = null;

    // ==================== 1. POPULATE ANGLED 4-COLUMN POSTER WALL ====================
    function initPosterWall() {
      const columns = [
        document.getElementById('marquee-col-1'),
        document.getElementById('marquee-col-2'),
        document.getElementById('marquee-col-3'),
        document.getElementById('marquee-col-4')
      ];

      const pool = ANIME_DATASET.length >= 16 ? ANIME_DATASET : [...ANIME_DATASET, ...ANIME_DATASET, ...ANIME_DATASET];

      columns.forEach((colEl, colIdx) => {
        const slice = pool.slice(colIdx * 4, colIdx * 4 + 4);
        const loopItems = [...slice, ...slice];

        colEl.innerHTML = loopItems.map(anime => {
          const title = anime.title || anime.name;
          const fallback = createFallbackCover(title);
          return `
            <div onclick="openModalById(${anime.id})" class="cursor-pointer group relative rounded-2xl overflow-hidden aspect-[2/3] bg-slate-900 border border-slate-800/90 shadow-xl">
              <img
                src="${anime.image}"
                alt="${title}"
                loading="lazy"
                onerror="this.onerror=null; this.src='${fallback}';"
                class="w-full h-full object-cover opacity-85 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-transparent opacity-80"></div>
              <div class="absolute bottom-2 left-2 right-2">
                <p class="text-[11px] font-bold text-white truncate">${title}</p>
                <span class="text-[10px] text-cyan-300 font-semibold">★ ${anime.score}</span>
              </div>
            </div>
          `;
        }).join('');
      });
    }

    // ==================== 2. RECOMMENDATION ENGINE (ML PRECOMPUTED + CLIENT-SIDE SIMILARITY) ====================
    async function recommendSimilarTo(animeObj) {
      currentReferenceAnime = animeObj;

      document.querySelectorAll('.genre-pill').forEach(btn => {
        btn.classList.remove('bg-[#8B5CF6]', 'text-white', 'shadow-md', 'shadow-violet-500/25');
        btn.classList.add('bg-slate-800/80', 'text-slate-300');
      });

      const animeTitle = animeObj.title || animeObj.name;

      // Update UI Headers
      document.getElementById('section-kicker').textContent = 'Hasil Analisis Content-Based Filtering (Cosine Similarity)';
      document.getElementById('section-title').textContent = `Rekomendasi Mirip "${animeTitle}"`;
      document.getElementById('section-subtitle').textContent = `Dihitung berdasarkan kemiripan genre, tema cerita sinopsis, dan studio.`;

      const refBox = document.getElementById('active-reference-box');
      refBox.classList.remove('hidden');
      refBox.classList.add('flex');
      document.getElementById('active-reference-name').textContent = animeTitle;

      // 1. Coba ambil rekomendasi Cosine Similarity precomputed dari API backend Laravel
      try {
        const res = await fetch(`/api/anime/${animeObj.id}/recommendations`);
        if (res.ok) {
          const data = await res.json();
          if (data.recommendations && data.recommendations.length > 0) {
            renderAnimeGrid(data.recommendations);
            document.getElementById('recommendations-section').scrollIntoView({ behavior: 'smooth' });
            return;
          }
        }
      } catch (err) {
        console.warn('Backend recommendation error, falling back to client-side:', err);
      }

      // 2. Client-side fallback similarity
      const recommendations = ANIME_DATASET
        .filter(item => item.id !== animeObj.id)
        .map(item => {
          const sharedGenres = (item.genres || []).filter(g => (animeObj.genres || []).includes(g)).length;
          const unionGenres = new Set([...(animeObj.genres || []), ...(item.genres || [])]).size;
          const genreScore = unionGenres > 0 ? sharedGenres / unionGenres : 0;
          const studioBonus = (item.studio && animeObj.studio && item.studio === animeObj.studio) ? 0.15 : 0;
          const matchScore = Math.min(98, Math.max(70, Math.round(68 + (genreScore * 0.7 + studioBonus) * 30)));
          return { ...item, matchScore };
        })
        .sort((a, b) => b.matchScore - a.matchScore || b.score - a.score)
        .slice(0, 12);

      renderAnimeGrid(recommendations);
      document.getElementById('recommendations-section').scrollIntoView({ behavior: 'smooth' });
    }

    // ==================== 3. RENDER RECOMMENDATION CARDS GRID ====================
    function renderAnimeGrid(list) {
      currentDisplayedList = list;
      const grid = document.getElementById('anime-grid');

      if (!list || list.length === 0) {
        grid.innerHTML = `
          <div class="col-span-full text-center py-16 bg-slate-900/50 rounded-3xl border border-slate-800">
            <p class="text-slate-400 text-base">Tidak ada anime yang cocok ditemukan. Coba kata kunci lain!</p>
          </div>
        `;
        return;
      }

      grid.innerHTML = list.map((anime, index) => {
        const title = anime.title || anime.name;
        const fallback = createFallbackCover(title);
        const matchPct = anime.matchScore || Math.min(97, Math.round(anime.score * 10.6));
        const genreTags = (anime.genres || []).slice(0, 3).map(g =>
          `<span class="px-2.5 py-0.5 rounded-lg text-[11px] font-medium bg-slate-800/90 text-slate-300 border border-slate-700/70">${g}</span>`
        ).join('');
        const cleanQuery = (title || '').replace(/[:!?"]/g, '').trim();
        const samehadakuUrl = anime.samehadaku_url || `https://v2.samehadaku.how/?s=${encodeURIComponent(cleanQuery)}`;
        const apUrl = anime.anime_planet_url || `https://www.anime-planet.com/anime/all?name=${encodeURIComponent(title)}`;

        return `
          <article
            onclick="openModalByIndex(${index})"
            class="anime-card cursor-pointer group rounded-2xl bg-slate-900/90 border border-slate-800/90 overflow-hidden flex flex-col justify-between"
          >
            <!-- Cover Poster 2:3 -->
            <div class="relative aspect-[2/3] w-full overflow-hidden bg-slate-950">
              <img
                src="${anime.image}"
                alt="${title}"
                loading="lazy"
                onerror="this.onerror=null; this.src='${fallback}';"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent opacity-90"></div>

              <!-- Top-Left Similarity Match Badge -->
              <div class="absolute top-3 left-3 px-2.5 py-1 rounded-xl bg-slate-950/85 backdrop-blur-md border border-cyan-500/40 text-cyan-300 text-xs font-bold shadow-lg">
                ${matchPct}% Match
              </div>

              <!-- Top-Right MAL Score Badge -->
              <div class="absolute top-3 right-3 px-2.5 py-1 rounded-xl bg-slate-950/85 backdrop-blur-md border border-amber-500/40 text-amber-300 text-xs font-bold shadow-lg">
                ★ ${anime.score ? Number(anime.score).toFixed(2) : 'N/A'}
              </div>

              <!-- Bottom Overlay Info -->
              <div class="absolute bottom-3 left-3.5 right-3.5">
                <span class="text-[11px] font-medium text-violet-300 block mb-0.5">${anime.studio || anime.studios || 'Anime Series'} • ${anime.year || ''}</span>
                <h3 class="text-base sm:text-lg font-bold text-white leading-snug line-clamp-2 group-hover:text-cyan-300 transition-colors">
                  ${title}
                </h3>
              </div>
            </div>

            <!-- Card Footer: Genre Pills + Watch & Info Links -->
            <div class="p-3 bg-slate-900/95 flex items-center justify-between gap-2 border-t border-slate-800/70">
              <div class="flex flex-wrap gap-1">
                ${genreTags}
              </div>
              <div class="flex items-center gap-1.5 shrink-0">
                <a
                  href="${samehadakuUrl}"
                  target="_blank"
                  rel="noopener noreferrer"
                  onclick="event.stopPropagation();"
                  title="Nonton Streaming Sub Indo di Samehadaku"
                  class="text-[11px] font-bold text-rose-300 hover:text-white bg-rose-950/80 hover:bg-rose-900 px-2 py-1 rounded-lg border border-rose-800/60 transition-colors"
                >
                  🎬 Nonton
                </a>
                <a
                  href="${apUrl}"
                  target="_blank"
                  rel="noopener noreferrer"
                  onclick="event.stopPropagation();"
                  title="Buka Info di Anime-Planet"
                  class="text-[11px] font-medium text-cyan-400 hover:text-cyan-300 bg-cyan-950/60 px-1.5 py-1 rounded-lg border border-cyan-800/40 transition-colors"
                >
                  🪐 Info
                </a>
              </div>
            </div>
          </article>
        `;
      }).join('');
    }

    // ==================== 4. SEARCH & AUTOCOMPLETE (LOCAL DATASET + JIKAN API FALLBACK) ====================
    const searchInput = document.getElementById('anime-search-input');
    const dropdown = document.getElementById('autocomplete-dropdown');
    let searchTimeout = null;

    searchInput.addEventListener('input', (e) => {
      const query = e.target.value.trim();
      if (!query) {
        dropdown.classList.add('hidden');
        return;
      }

      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(async () => {
        // Cari di server Laravel (5000 preprocessed dataset)
        try {
          const res = await fetch(`/api/anime/search?q=${encodeURIComponent(query)}`);
          if (res.ok) {
            const matches = await res.json();
            if (matches.length > 0) {
              dropdown.innerHTML = matches.map(anime => {
                const title = anime.title || anime.name;
                const fallback = createFallbackCover(title);
                const genresText = (anime.genres || []).join(', ');
                return `
                  <div onclick="selectAnimeObject(${JSON.stringify(anime).replace(/"/g, '&quot;')})" class="flex items-center gap-3.5 p-3 hover:bg-slate-800/90 cursor-pointer transition-colors">
                    <img
                      src="${anime.image}"
                      alt="${title}"
                      onerror="this.onerror=null; this.src='${fallback}';"
                      class="w-10 h-14 object-cover rounded-lg shrink-0 bg-slate-950"
                    />
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-bold text-white truncate">${title}</p>
                      <p class="text-xs text-slate-400 truncate">${anime.year || ''} • ${genresText}</p>
                    </div>
                    <span class="text-xs font-bold text-amber-300 shrink-0">★ ${anime.score}</span>
                  </div>
                `;
              }).join('');
              dropdown.classList.remove('hidden');
              return;
            }
          }
        } catch (err) {
          console.warn('Local search error:', err);
        }

        // Jika tidak ditemukan di dataset lokal, tampilkan opsi cari ke Jikan API MyAnimeList
        dropdown.innerHTML = `
          <div onclick="handleSearchSubmit()" class="p-3.5 hover:bg-slate-800/90 cursor-pointer text-xs text-cyan-300 flex items-center justify-between">
            <span>Cari "<strong>${e.target.value}</strong>" langsung via Jikan API (MyAnimeList)</span>
            <span>↵ Enter</span>
          </div>
        `;
        dropdown.classList.remove('hidden');
      }, 250);
    });

    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        dropdown.classList.add('hidden');
        handleSearchSubmit();
      }
    });

    document.addEventListener('click', (e) => {
      if (!document.getElementById('search-container').contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });

    function selectAnimeObject(anime) {
      const title = anime.title || anime.name;
      searchInput.value = title;
      dropdown.classList.add('hidden');
      recommendSimilarTo(anime);
    }

    async function handleSearchSubmit() {
      const query = searchInput.value.trim();
      dropdown.classList.add('hidden');
      if (!query) return;

      const loadingBanner = document.getElementById('loading-banner');
      const loadingText = document.getElementById('loading-text');
      loadingBanner.classList.remove('hidden');
      loadingText.textContent = `Mencari "${query}" di dataset & Jikan API...`;
      document.getElementById('recommendations-section').scrollIntoView({ behavior: 'smooth' });

      // 1. Cek dataset lokal Laravel dulu
      try {
        const localRes = await fetch(`/api/anime/search?q=${encodeURIComponent(query)}`);
        if (localRes.ok) {
          const localData = await localRes.json();
          if (localData.length > 0) {
            loadingBanner.classList.add('hidden');
            recommendSimilarTo(localData[0]);
            return;
          }
        }
      } catch (err) {
        console.warn('Search local fallback:', err);
      }

      // 2. Fallback ke Jikan API v4 jika belum ada di dataset lokal
      try {
        loadingText.textContent = `Mengambil data visual & anime terbaru dari Jikan API (MyAnimeList)...`;
        const res = await fetch(`https://api.jikan.moe/v4/anime?q=${encodeURIComponent(query)}&limit=8&sfw=true`);
        const json = await res.json();
        const apiData = (json.data || []).map((item, idx) => {
          const title = item.title_english || item.title;
          return {
            id: item.mal_id,
            title: title,
            name: title,
            year: item.year || (item.aired?.prop?.from?.year) || 2024,
            score: item.score || 8.0,
            studio: item.studios?.[0]?.name || 'Studio Jepang',
            episodes: item.episodes ? `${item.episodes} Episode` : 'Ongoing / Movie',
            genres: (item.genres || []).map(g => g.name).slice(0, 3),
            image: item.images?.jpg?.large_image_url || item.images?.jpg?.image_url || '',
            synopsis: item.synopsis || 'Sinopsis tidak tersedia untuk judul ini.',
            anime_planet_url: `https://www.anime-planet.com/anime/all?name=${encodeURIComponent(title)}`,
            samehadaku_url: `https://v2.samehadaku.how/?s=${encodeURIComponent(title.replace(/[:!?"]/g, '').trim())}`,
            matchScore: Math.max(75, 96 - idx * 3)
          };
        });

        document.getElementById('section-kicker').textContent = 'Live Fetch • Jikan API v4';
        document.getElementById('section-title').textContent = `Hasil Pencarian untuk "${query}"`;
        document.getElementById('section-subtitle').textContent = 'Data ditarik secara dinamis dari MyAnimeList melalui Jikan API.';
        document.getElementById('active-reference-box').classList.add('hidden');

        renderAnimeGrid(apiData);
      } catch (err) {
        console.error('Jikan API Error:', err);
      } finally {
        loadingBanner.classList.add('hidden');
      }
    }

    // ==================== 5. GENRE PILLS & RESET ====================
    function filterByGenre(genre) {
      currentReferenceAnime = null;
      document.getElementById('active-reference-box').classList.add('hidden');

      document.querySelectorAll('.genre-pill').forEach(btn => {
        if (btn.dataset.genre === genre) {
          btn.classList.remove('bg-slate-800/80', 'text-slate-300');
          btn.classList.add('bg-[#8B5CF6]', 'text-white', 'shadow-md', 'shadow-violet-500/25');
        } else {
          btn.classList.remove('bg-[#8B5CF6]', 'text-white', 'shadow-md', 'shadow-violet-500/25');
          btn.classList.add('bg-slate-800/80', 'text-slate-300');
        }
      });

      if (genre === 'All') {
        document.getElementById('section-kicker').textContent = 'Rekomendasi Terkurasi';
        document.getElementById('section-title').textContent = 'Anime Populer & Rating Tertinggi';
        document.getElementById('section-subtitle').textContent = 'Klik salah satu kartu anime untuk melihat detail lengkap atau mencari anime yang mirip dengannya.';
        const sorted = [...ANIME_DATASET].sort((a, b) => b.score - a.score).slice(0, 16);
        renderAnimeGrid(sorted);
      } else {
        document.getElementById('section-kicker').textContent = `Filter Genre • ${genre}`;
        document.getElementById('section-title').textContent = `Anime Terbaik Genre ${genre}`;
        document.getElementById('section-subtitle').textContent = `Menampilkan pilihan anime dengan elemen ${genre} paling menonjol.`;
        const filtered = ANIME_DATASET
          .filter(a => (a.genres || []).some(g => g.toLowerCase() === genre.toLowerCase()))
          .sort((a, b) => b.score - a.score);
        renderAnimeGrid(filtered);
      }

      document.getElementById('recommendations-section').scrollIntoView({ behavior: 'smooth' });
    }

    function resetToDefault() {
      searchInput.value = '';
      filterByGenre('All');
    }

    function pickRandomAnime() {
      const random = ANIME_DATASET[Math.floor(Math.random() * ANIME_DATASET.length)];
      searchInput.value = random.title || random.name;
      recommendSimilarTo(random);
    }

    // ==================== 6. GLASSMORPHISM DETAIL MODAL ====================
    function openModalByIndex(idx) {
      const anime = currentDisplayedList[idx];
      if (anime) showModal(anime);
    }

    function openModalById(id) {
      const anime = ANIME_DATASET.find(a => Number(a.id) === Number(id));
      if (anime) showModal(anime);
    }

    function showModal(anime) {
      const modal = document.getElementById('detail-modal');
      const title = anime.title || anime.name;
      const fallback = createFallbackCover(title);
      const matchPct = anime.matchScore || Math.min(97, Math.round(anime.score * 10.6));

      const posterEl = document.getElementById('modal-poster');
      posterEl.onerror = function() { this.onerror = null; this.src = fallback; };
      posterEl.src = anime.image;

      document.getElementById('modal-match-badge').textContent = `${matchPct}% Match`;
      document.getElementById('modal-score-badge').textContent = `★ ${anime.score ? Number(anime.score).toFixed(2) : 'N/A'}`;
      document.getElementById('modal-year-badge').textContent = anime.year || '2024';
      document.getElementById('modal-title').textContent = title;
      document.getElementById('modal-studio').textContent = anime.studio || anime.studios || 'Studio Jepang';
      document.getElementById('modal-episodes').textContent = anime.episodes || '12 Episode';
      document.getElementById('modal-synopsis').textContent = anime.synopsis || 'Sinopsis belum tersedia.';

      document.getElementById('modal-genres').innerHTML = (anime.genres || []).map(g =>
        `<span class="px-3 py-1 rounded-xl text-xs font-semibold bg-violet-500/15 text-violet-300 border border-violet-500/30">${g}</span>`
      ).join('');

      // Update Tautan Streaming Samehadaku & Info Anime-Planet
      const cleanTitleQuery = (title || '').replace(/[:!?"]/g, '').trim();
      const samehadakuBtn = document.getElementById('modal-samehadaku-btn');
      if (samehadakuBtn) {
        samehadakuBtn.href = anime.samehadaku_url || `https://v2.samehadaku.how/?s=${encodeURIComponent(cleanTitleQuery)}`;
      }

      const apBtn = document.getElementById('modal-anime-planet-btn');
      if (apBtn) {
        apBtn.href = anime.anime_planet_url || `https://www.anime-planet.com/anime/all?name=${encodeURIComponent(title)}`;
      }

      // Action Button: Cari yang mirip ini
      const findBtn = document.getElementById('modal-find-similar-btn');
      findBtn.onclick = () => {
        closeModal();
        searchInput.value = title;
        recommendSimilarTo(anime);
      };

      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      const modal = document.getElementById('detail-modal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      document.body.style.overflow = '';
    }

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeModal();
    });

    // Initialize App on Load
    window.addEventListener('DOMContentLoaded', () => {
      initPosterWall();
      const initialPopular = [...ANIME_DATASET].sort((a, b) => b.score - a.score).slice(0, 16);
      renderAnimeGrid(initialPopular);
    });
  </script>
</body>
</html>
