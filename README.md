# 🎬 AniMatch — Sistem Rekomendasi Anime Berbasis AI Hybrid

<p align="center">
  <strong>Sistem Rekomendasi Anime Modern Berbasis Content-Based Filtering (TF-IDF & Cosine Similarity) dengan Integrasi Hybrid Jikan API, Anime-Planet, dan Streaming Samehadaku.</strong>
</p>

---

## 🌟 Fitur Utama

- **🧠 Mesin Rekomendasi Content-Based Filtering**:
  - Menggunakan algoritma **TF-IDF Vectorizer** dan **Cosine Similarity** untuk menghitung kemiripan anime berdasarkan genre (pembobotan tinggi), studio produksi, tipe tayangan, dan sinopsis cerita.
  - Memuat **13.000+ dataset anime bersih** dan menghasilkan rekomendasi teratas untuk **5.000 anime terpopuler**.
- **🌐 Arsitektur Hybrid**:
  - **Dataset Lokal (Kaggle Bersih)**: Untuk kalkulasi rekomendasi instan berkecepatan tinggi tanpa kuota/limitasi API.
  - **Jikan API v4 (Live Fallback)**: Jika judul anime baru dicari dan belum ada di dataset lokal, sistem secara dinamis mengambil poster visual HD dan sinopsis dari publik MyAnimeList secara real-time.
- **🪐 Integrasi Anime-Planet**:
  - Tautan otomatis ke halaman profil anime resmi di **Anime-Planet** (`anime_planet_url`).
- **🎬 Integrasi Nonton Streaming Samehadaku**:
  - Tautan langsung ke pencarian dan streaming anime ber-subtitle Indonesia di **Samehadaku** (`samehadaku_url`).
- **🎨 Antarmuka Modern & Responsif**:
  - Tema *Dark Mode* elegan menggunakan **Tailwind CSS**.
  - *Split Hero Section* dengan **Angled 4-Column Infinite Marquee Poster Wall** 60fps.
  - *Smart Search Bar* dengan *live autocomplete* instan.
  - *Quick Filter Pills* berdasarkan genre terpopuler (Action, Fantasy, Romance, Sci-Fi, Slice of Life).
  - *Glassmorphism Detail Modal* menampilkan informasi lengkap skor, studio, episode, sinopsis, dan tombol aksi rekomendasi.

---

## 🛠️ Tech Stack

- **Backend Web**: [Laravel](https://laravel.com) & PHP 8.3+
- **Frontend**: Blade Templating, [Tailwind CSS](https://tailwindcss.com), Vanilla JS (ES6+)
- **Machine Learning & Preprocessing**:
  - Python 3.10+
  - Pandas (Pembersihan data, normalisasi teks, penanganan missing values)
  - Scikit-Learn (TfidfVectorizer & Cosine Similarity)
- **Eksternal API & Sumber Luar**:
  - [Jikan API v4](https://jikan.moe/) (Unofficial MyAnimeList API)
  - [Anime-Planet](https://www.anime-planet.com/)
  - [Samehadaku](https://v2.samehadaku.how/)

---

## 📁 Struktur Direktori Penting

```text
├── app/
│   └── Http/Controllers/
│       └── AnimeController.php         # Handler rute web, pencarian & API rekomendasi
├── ml/
│   ├── popular_anime.csv              # Dataset mentah dari Kaggle (28k baris)
│   ├── cleaned_anime.csv              # Dataset bersih hasil filter & sanitasi (13k baris)
│   ├── preprocess.py                  # Skrip pembersih data, filter NSFW & URL generator
│   └── build_model.py                 # Skrip kalkulasi TF-IDF Cosine Similarity
├── resources/
│   └── views/
│       └── welcome.blade.php          # Tampilan UI modern AniMatch
├── storage/app/
│   └── anime_data.json                # Hasil export model rekomendasi siap pakai
└── routes/
    └── web.php                        # Definisi rute aplikasi
```

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Kloning Repositori
```bash
git clone https://github.com/airinmy13/Sistem-Rekomendasi-Anime.git
cd Sistem-Rekomendasi-Anime
```

### 2. Instalasi Dependensi PHP & Environment
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Preprocessing Data & Bangun Model (Opsional, Data Siap Pakai Sudah Tersedia)
Jika ingin menjalankan ulang pembersihan dataset dan pemodelan:
```bash
python ml/preprocess.py
python ml/build_model.py
```

### 4. Jalankan Aplikasi
```bash
php artisan serve
```
Buka browser Anda dan akses: `http://127.0.0.1:8000` atau melalui domain lokal Laragon Anda.

---

## 📄 Lisensi
Proyek ini dibuat untuk tujuan edukasi dan pengembangan sistem rekomendasi berbasis kecerdasan buatan.
