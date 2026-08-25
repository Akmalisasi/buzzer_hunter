### Buzzer Documentation App

Aplikasi web sederhana berbasis PHP native untuk mendokumentasikan aktivitas akun buzzer di media sosial. Setiap pengguna bisa membuat laporan dokumentasi lengkap dengan bukti screenshot, dan semua laporan dari seluruh pengguna bisa dilihat bersama lewat dashboard.

---

## Fitur

<img width="1847" height="517" alt="image" src="https://github.com/user-attachments/assets/cb752908-31d9-4cc8-b33d-1564bba49134" />

### Autentikasi
- Login & Sign Up (role otomatis `user` saat daftar)
- Logout
- Ganti password (dengan verifikasi password lama)

### Dokumentasi Buzzer (CRUD)
- **Create**: buat laporan baru dengan field judul, sosmed (dropdown), nama akun buzzer, tanggal, deskripsi, dan upload screenshot (multiple, maksimal 5 file, format jpg/jpeg/png)
- **Read**: dashboard menampilkan semua laporan dari seluruh pengguna
- **Update**: edit laporan milik sendiri (atau semua laporan kalau login sebagai admin), termasuk menambah/menghapus screenshot satu per satu
- **Delete**: hapus laporan beserta seluruh file screenshot terkait

<img width="1884" height="854" alt="image" src="https://github.com/user-attachments/assets/9e02c370-1634-4bdd-a29f-c4cf6adfd2fd" />

### Dashboard
- Tampilan **card** untuk setiap laporan (bukan tabel) — menampilkan avatar & tag platform sosmed, judul, nama akun buzzer, dan tanggal
- Aksen warna berbeda otomatis per platform (Twitter/X, Facebook, Instagram, TikTok, YouTube, lainnya)
- Search berdasarkan judul / nama akun buzzer
- Filter berdasarkan platform sosmed
- Diurutkan dari laporan terbaru
- Pagination (default **5 laporan per halaman**, bisa diubah lewat variabel `$limit` di `dashboard.php`)
- Klik card untuk membuka detail laporan lengkap beserta screenshot

<img width="1856" height="850" alt="image" src="https://github.com/user-attachments/assets/65f2c965-9898-42f6-8ba1-eb7f484780ad" />

### Profile
- Lihat & update data akun (username, email)
- Lihat daftar laporan yang pernah dibuat sendiri, dengan opsi edit/hapus langsung dari situ

<img width="1887" height="635" alt="image" src="https://github.com/user-attachments/assets/9375d3ba-95a8-456c-b66b-c67a9fa62d9d" />


### Admin Panel
- CRUD data user (tambah, edit, hapus)
- Bisa mengatur role user (`user` / `admin`)
- Bisa mengedit dan menghapus laporan milik siapa pun (moderasi)
- Tidak bisa menghapus akunnya sendiri (untuk mencegah admin terkunci dari sistem)

---

## Role & Hak Akses

| Aksi | User | Admin |
|---|---|---|
| Login / Sign Up | ✅ | ✅ |
| Lihat dashboard (semua laporan) | ✅ | ✅ |
| Buat laporan baru | ✅ | ✅ |
| Edit/Hapus laporan milik sendiri | ✅ | ✅ |
| Edit/Hapus laporan milik user lain | ❌ | ✅ |
| Update profil & ganti password sendiri | ✅ | ✅ |
| CRUD data user lain | ❌ | ✅ |
| Ubah role user | ❌ | ✅ |

---

## Tech Stack

| Bagian | Teknologi |
|---|---|
| Backend | PHP native (tanpa framework) |
| Database | MySQL (`mysqli` extension) |
| Frontend | HTML + CSS murni (`assets/style.css`) |
| Interaktivitas | Vanilla JavaScript (live preview upload, modal gambar) |
| Server lokal | Kompatibel dengan XAMPP / Laragon / Apache + PHP + MySQL manapun |

---

## Rencana Pengembangan
- Export laporan ke PDF/Excel
- Kategori/jenis pelanggaran buzzer
- Status laporan (pending, verified, ditolak)
- Notifikasi email saat laporan baru dibuat
- Log aktivitas (siapa mengedit/menghapus apa dan kapan)
