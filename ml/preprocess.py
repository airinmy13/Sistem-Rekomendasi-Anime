import os
import re
import html
import urllib.parse
import pandas as pd

def generate_slug(name):
    """
    Mengubah nama anime menjadi slug yang ramah URL.
    Contoh: 'Frieren: Beyond Journey's End' -> 'frieren-beyond-journeys-end'
    """
    if pd.isna(name):
        return ""
    slug = re.sub(r"[^\w\s-]", "", str(name).lower())
    slug = re.sub(r"[-\s]+", "-", slug).strip("-")
    return slug

def generate_anime_planet_url(name):
    """
    Membuat URL resmi pencarian langsung ke Anime-Planet secara otomatis.
    """
    if pd.isna(name):
        return ""
    encoded_name = urllib.parse.quote_plus(str(name).strip())
    return f"https://www.anime-planet.com/anime/all?name={encoded_name}"

def generate_samehadaku_url(name):
    """
    Membuat URL pencarian langsung ke Samehadaku untuk menonton anime dengan Sub Indo.
    Contoh: https://v2.samehadaku.how/?s=Solo+Leveling
    """
    if pd.isna(name):
        return ""
    # Hapus karakter pemisah berlebih seperti tanda kutip atau strip ganjil
    clean_query = re.sub(r'[:!?"]', '', str(name)).strip()
    encoded_name = urllib.parse.quote_plus(clean_query)
    return f"https://v2.samehadaku.how/?s={encoded_name}"

def clean_synopsis(text):
    """
    Membersihkan teks sinopsis dari noise seperti tag sumber MAL, 
    entitas HTML, URL, dan spasi berlebih.
    """
    if pd.isna(text) or not str(text).strip():
        return ""
    
    text = str(text)
    # Decode HTML entities (misal &amp; -> &, &#039; -> ')
    text = html.unescape(text)
    
    # Hapus tag sumber umum dari MyAnimeList (MAL Rewrite, Source, etc)
    text = re.sub(r'\[Written by MAL Rewrite\]', '', text, flags=re.IGNORECASE)
    text = re.sub(r'\(Source:.*?\)', '', text, flags=re.IGNORECASE)
    text = re.sub(r'\[Source:.*?\]', '', text, flags=re.IGNORECASE)
    
    # Hapus URL jika ada
    text = re.sub(r'http\S+|www\.\S+', '', text)
    
    # Hapus tag HTML jika ada
    text = re.sub(r'<.*?>', '', text)
    
    # Normalisasi spasi dan baris baru
    text = re.sub(r'\s+', ' ', text).strip()
    return text

def clean_genres(genre_str):
    """
    Membersihkan dan memformat string genre menjadi seragam.
    """
    if pd.isna(genre_str) or not str(genre_str).strip():
        return ""
    genres = [g.strip() for g in str(genre_str).split(',') if g.strip()]
    return ", ".join(genres)

def build_soup(row):
    """
    Menyusun 'content soup' yang dioptimalkan untuk Content-Based Filtering.
    Genre diberikan bobot lebih tinggi (diulang 3x), studio (2x), type, dan sinopsis.
    """
    genres = (row['genres'] + ' ') * 3 if row['genres'] else ''
    studios = (row['studios'] + ' ') * 2 if row['studios'] and row['studios'] != 'Unknown' else ''
    anime_type = (row['type'] + ' ') if row['type'] else ''
    synopsis = row['synopsis'] if row['synopsis'] else ''
    
    soup = f"{genres}{studios}{anime_type}{synopsis}".strip().lower()
    return soup

def preprocess_anime_data(max_year=2024, only_single_year=False):
    """
    max_year: Batasan tahun rilis (default 2024)
    only_single_year: Jika True, hanya ambil anime tahun 2024 saja.
                      Jika False, ambil anime sampai dengan tahun 2024 (<= 2024).
    """
    base_dir = os.path.dirname(os.path.abspath(__file__))
    input_path = os.path.join(base_dir, 'popular_anime.csv')
    output_path = os.path.join(base_dir, 'cleaned_anime.csv')
    
    if not os.path.exists(input_path):
        raise FileNotFoundError(f"File {input_path} tidak ditemukan!")
    
    print("=" * 60)
    print("MEMULAI PROSES PREPROCESSING DATASET ANIME")
    print("=" * 60)
    
    df = pd.read_csv(input_path)
    initial_count = len(df)
    print(f"1. Total baris data awal: {initial_count:,}")
    
    # 1. Hapus duplikat ID
    df = df.drop_duplicates(subset=['id']).copy()
    print(f"2. Setelah menghapus duplikat ID: {len(df):,} (dibuang: {initial_count - len(df):,})")
    
    # 2. Filter rating dewasa (Hapus Rx - Hentai)
    hentai_before = len(df)
    df = df[df['rating'] != 'Rx - Hentai'].copy()
    print(f"3. Setelah menghapus Rx - Hentai: {len(df):,} (dibuang: {hentai_before - len(df):,})")
    
    # 3. Filter tipe anime yang relevan (buang PV, CM, Music, dan tipe kosong)
    valid_types = ['TV', 'Movie', 'OVA', 'ONA', 'Special', 'TV Special']
    type_before = len(df)
    df = df[df['type'].isin(valid_types)].copy()
    print(f"4. Setelah filter tipe non-konten (PV/CM/Music): {len(df):,} (dibuang: {type_before - len(df):,})")
    
    # 4. Hapus data tanpa nama, genre, atau skor (krusial untuk sistem rekomendasi)
    crucial_before = len(df)
    df = df.dropna(subset=['name', 'genres', 'score']).copy()
    print(f"5. Setelah menghapus data tanpa name/genres/score: {len(df):,} (dibuang: {crucial_before - len(df):,})")
    
    # 5. Ekstraksi Tahun Rilis & Filter Tahun 2024
    df['year'] = pd.to_datetime(df['aired_from'], errors='coerce').dt.year
    year_before = len(df)
    if only_single_year:
        df = df[df['year'] == max_year].copy()
        print(f"6. Filter hanya anime rilis tahun {max_year}: {len(df):,} (dibuang: {year_before - len(df):,})")
    else:
        # Ambil semua anime yang rilis sampai tahun max_year (membuang anime belum rilis 2025/2026 ke atas)
        df = df[df['year'].isna() | (df['year'] <= max_year)].copy()
        print(f"6. Filter anime rilis sampai tahun {max_year} (<= {max_year}): {len(df):,} (dibuang: {year_before - len(df):,})")
    
    # 6. Minimal penilai (scored_by) untuk menjaga kualitas data rekomendasi
    df['scored_by'] = df['scored_by'].fillna(0).astype(int)
    score_before = len(df)
    df = df[df['scored_by'] >= 50].copy()
    print(f"7. Setelah filter minimal 50 scored_by: {len(df):,} (dibuang: {score_before - len(df):,})")
    
    # 7. Pembersihan Teks & Format Kolom
    print("8. Membersihkan teks sinopsis, genre, dan studio...")
    df['synopsis'] = df['synopsis'].fillna('').apply(clean_synopsis)
    df['genres'] = df['genres'].apply(clean_genres)
    df['studios'] = df['studios'].fillna('Unknown').str.strip()
    df['rating'] = df['rating'].fillna('Unknown').str.strip()
    df['episodes'] = df['episodes'].fillna(0).astype(int)
    df['score'] = df['score'].round(2)
    
    # 8. Tambahkan Kolom Tautan Otomatis ke Samehadaku, Anime-Planet & URL Slug
    print("9. Membuat tautan otomatis ke Samehadaku, Anime-Planet & URL slug...")
    df['samehadaku_url'] = df['name'].apply(generate_samehadaku_url)
    df['anime_planet_url'] = df['name'].apply(generate_anime_planet_url)
    df['slug'] = df['name'].apply(generate_slug)
    
    # 9. Pembuatan Content Soup untuk Content-Based Filtering
    print("10. Membuat fitur 'soup' (kombinasi genre berbobot, studio, type, dan sinopsis)...")
    df['soup'] = df.apply(build_soup, axis=1)
    
    # 10. Urutkan berdasarkan popularitas (scored_by) tertinggi
    df = df.sort_values(by='scored_by', ascending=False).reset_index(drop=True)
    
    # 11. Simpan ke CSV bersih
    df.to_csv(output_path, index=False, encoding='utf-8')
    print("=" * 60)
    print(f"PREPROCESSING SELESAI!")
    print(f"Hasil disimpan di: {output_path}")
    print(f"Total data bersih: {len(df):,} baris")
    print("=" * 60)
    
    # Tampilkan preview contoh data
    print("\nContoh 3 data teratas beserta link Anime-Planet:")
    for _, row in df.head(3).iterrows():
        print(f"[{row['year']}] {row['name']} ({row['rating']}) - Skor: {row['score']}")
        print(f" -> Anime-Planet Link: {row['anime_planet_url']}")
        print(f" -> Slug: {row['slug']}")

if __name__ == '__main__':
    preprocess_anime_data(max_year=2024, only_single_year=False)

