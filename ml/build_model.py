import json
import os
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# 1. Tentukan lokasi folder otomatis berdasarkan posisi file build_model.py
base_dir = os.path.dirname(os.path.abspath(__file__))
cleaned_csv_path = os.path.join(base_dir, 'cleaned_anime.csv')
raw_csv_path = os.path.join(base_dir, 'popular_anime.csv')

csv_path = cleaned_csv_path if os.path.exists(cleaned_csv_path) else raw_csv_path

if not os.path.exists(csv_path):
    raise FileNotFoundError(
        f"File dataset tidak ditemukan di: {csv_path}\n"
        "Jalankan preprocess.py terlebih dahulu atau sediakan popular_anime.csv di folder 'ml'."
    )

print(f"Membaca dataset dari: {os.path.basename(csv_path)}...")
df = pd.read_csv(csv_path)

# 2. Pembersihan & Filtering (ambil Top 5.000 anime berdasarkan jumlah penilai)
df = df.dropna(subset=['score', 'genres', 'name']).copy()
df['scored_by'] = df['scored_by'].fillna(0)
df = df.sort_values(by='scored_by', ascending=False).head(5000).reset_index(drop=True)

# Isi nilai kosong pada kolom pendukung
for col in ['studios', 'type', 'synopsis', 'trailer', 'rating']:
    df[col] = df[col].fillna('')
df['episodes'] = df['episodes'].fillna(0).astype(int)

# 3. Gunakan 'soup' yang sudah di-preprocess atau buat baru jika belum ada
if 'soup' not in df.columns or df['soup'].isnull().any():
    df['soup'] = (
        (df['genres'] + ' ') * 3 +
        (df['studios'] + ' ') * 2 +
        df['type'] + ' ' +
        df['synopsis']
    )

# 4. Hitung TF-IDF & Cosine Similarity
print("Menghitung kemiripan TF-IDF & Cosine Similarity...")
tfidf = TfidfVectorizer(stop_words='english', max_features=10000)
tfidf_matrix = tfidf.fit_transform(df['soup'])
cosine_sim = cosine_similarity(tfidf_matrix, tfidf_matrix)

# 5. Susun struktur data JSON beserta 12 rekomendasi teratas tiap anime
print("Menyusun data rekomendasi...")
anime_list = []
for idx, row in df.iterrows():
    # Ambil 12 anime paling mirip (kecuali dirinya sendiri di indeks 0)
    sim_scores = list(enumerate(cosine_sim[idx]))
    sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)[1:13]
    
    recommendations = [
        {
            'id': int(df.iloc[i]['id']),
            'match': round(float(score) * 100, 1)
        }
        for i, score in sim_scores
    ]
    
    anime_list.append({
        'id': int(row['id']),
        'title': row['name'],
        'name': row['name'],
        'year': int(row['year']) if 'year' in row and pd.notna(row['year']) else None,
        'genres': [g.strip() for g in str(row['genres']).split(',') if g.strip()],
        'type': row['type'],
        'episodes': f"{int(row['episodes'])} Episode" if int(row['episodes']) > 0 else "Movie / Special",
        'score': round(float(row['score']), 2),
        'studio': row['studios'] if row['studios'] != 'Unknown' else 'Studio Jepang',
        'studios': row['studios'],
        'image': row['image'],
        'trailer': row.get('trailer', ''),
        'synopsis': row['synopsis'],
        'samehadaku_url': row.get('samehadaku_url', f"https://v2.samehadaku.how/?s={row['name']}"),
        'anime_planet_url': row.get('anime_planet_url', f"https://www.anime-planet.com/anime/all?name={row['name']}"),
        'slug': row.get('slug', ''),
        'recommendations': recommendations
    })

# 6. Simpan ke folder storage/app Laravel (otomatis buat folder jika belum ada)
output_dir = os.path.abspath(os.path.join(base_dir, '..', 'storage', 'app'))
os.makedirs(output_dir, exist_ok=True)
output_path = os.path.join(output_dir, 'anime_data.json')

with open(output_path, 'w', encoding='utf-8') as f:
    json.dump(anime_list, f, ensure_ascii=False)

print(f"Selesai! Berhasil memproses {len(anime_list)} anime ke:\n{output_path}")