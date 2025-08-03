# Projek Dapur Kreatif

Projek Dapur Kreatif adalah aplikasi web berbasis PHP yang memungkinkan pengguna untuk berbagi, mencari, dan memoderasi resep masakan secara online. Aplikasi ini mendukung peran user dan admin, serta dilengkapi fitur interaktif seperti rating, favorit, dan komentar.

---

## Daftar Isi
- [Fitur Utama](#fitur-utama)
- [Struktur Folder](#struktur-folder)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Instalasi & Konfigurasi](#instalasi--konfigurasi)
- [Penggunaan](#penggunaan)
- [Hak Akses](#hak-akses)
- [CRUD Admin](#crud-admin)
- [Lisensi](#lisensi)

---

## Fitur Utama
1. **Autentikasi**: Registrasi, login, dan logout untuk user dan admin.
2. **Manajemen Resep**: User dapat menambah, mengedit, dan menghapus resep. Setiap perubahan harus melalui persetujuan admin.
3. **Moderasi Resep**: Admin dapat menyetujui, menolak, mengedit, dan menghapus resep.
4. **Dashboard**: User melihat resep terbaru, favorit, dan status resep. Admin melihat daftar resep yang perlu dimoderasi.
5. **Pencarian & Filter**: Cari resep berdasarkan judul, kategori, tingkat kesulitan, dan rating.
6. **Detail Resep**: Lihat detail resep, bahan, langkah, gambar, rating, favorit, dan komentar.
7. **Interaksi**: User dapat memberi rating, menandai favorit, dan menulis komentar.
8. **Keamanan**: Session untuk autentikasi dan otorisasi, serta validasi hak akses.

---

## Struktur Folder

```
├── config/                # Konfigurasi database
│   └── db.php
├── controllers/           # Logic aplikasi (MVC Controller)
│   ├── AuthController.php
│   ├── CommentController.php
│   ├── FavoriteController.php
│   ├── RatingController.php
│   └── RecipeController.php
├── models/                # Model database (MVC Model)
│   ├── Comment.php
│   ├── Favorite.php
│   ├── Rating.php
│   ├── Recipe.php
│   └── User.php
├── uploads/               # File gambar resep
├── views/                 # Tampilan (MVC View)
│   ├── add_recipe.php
│   ├── dashboard.php
│   ├── dashboard_admin.php
│   ├── edit_recipe.php
│   ├── footer.php
│   ├── header.php
│   ├── home.php
│   ├── login.php
│   ├── recipe_detail.php
│   ├── register.php
│   ├── search.php
│   └── css/
├── dapur_kreatif.sql      # Struktur dan data awal database
├── index.php              # Routing utama aplikasi
├── README.md              # Dokumentasi proyek
└── struktur.txt           # Struktur aplikasi (opsional)
```

---

## Teknologi yang Digunakan
- PHP Native (tanpa framework)
- MySQL
- HTML, CSS (custom di `views/css/`)
- JavaScript (opsional untuk interaksi tambahan)

---

## Instalasi & Konfigurasi
1. **Clone repository** ke folder web server (misal: `c:/laragon/www/Projek_Dapur`).
2. **Import database**: Import file `dapur_kreatif.sql` ke MySQL melalui phpMyAdmin atau command line.
3. **Konfigurasi database**: Edit `config/db.php` sesuai dengan pengaturan database lokal Anda.
4. **Jalankan aplikasi** melalui browser dengan mengakses `http://localhost/Projek_Dapur`.

---

## Penggunaan
- **Visitor** dapat melihat daftar resep terbaru tanpa login.
- **User** dapat registrasi, login, menambah, mengedit, menghapus resep, memberi rating, menandai favorit, dan menulis komentar.
- **Admin** dapat memoderasi resep (approve/reject/edit/delete), serta memantau aktivitas user.

---

## Hak Akses
- **Visitor**: Melihat daftar resep dan detail resep.
- **User**: Semua fitur visitor + CRUD resep milik sendiri, rating, favorit, komentar.
- **Admin**: Semua fitur user + moderasi dan CRUD semua resep.

---

## CRUD Admin
Admin dapat melakukan:
- **Create**: Menambah resep (jika diperlukan, misal untuk konten awal).
- **Read**: Melihat semua resep, baik yang sudah disetujui maupun pending.
- **Update**: Mengedit resep, baik milik sendiri maupun user lain.
- **Delete**: Menghapus resep yang tidak layak tampil.
Semua proses ini dilakukan melalui dashboard admin (`views/dashboard_admin.php`) dan diproses oleh `controllers/RecipeController.php`.

---

## Lisensi
Projek ini dibuat untuk keperluan pembelajaran dan pengembangan aplikasi berbasis web.