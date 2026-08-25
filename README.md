### Buzzer Documentation App

<img width="1895" height="591" alt="image" src="https://github.com/user-attachments/assets/30d49ef3-e1c1-421d-924f-08d2e96dacd5" />

Aplikasi web sederhana berbasis PHP native untuk mendokumentasikan aktivitas akun buzzer di media sosial. Setiap pengguna bisa membuat laporan dokumentasi lengkap dengan bukti screenshot, dan semua laporan dari seluruh pengguna bisa dilihat bersama lewat dashboard.

---

## Fitur

<img width="1892" height="411" alt="image" src="https://github.com/user-attachments/assets/a97e7dca-2d0d-4913-88c8-e2851a92d499" />

### Autentikasi
- Login & Sign Up (role otomatis `user` saat daftar)
- Logout
- Ganti password (dengan verifikasi password lama)

### Dokumentasi Buzzer (CRUD)
- **Create**: buat laporan baru dengan field judul, sosmed (dropdown), nama akun buzzer, tanggal, deskripsi, dan upload screenshot (multiple, maksimal 5 file, format jpg/jpeg/png)
- **Read**: dashboard menampilkan semua laporan dari seluruh pengguna
- **Update**: edit laporan milik sendiri (atau semua laporan kalau login sebagai admin), termasuk menambah/menghapus screenshot satu per satu
- **Delete**: hapus laporan beserta seluruh file screenshot terkait

<img width="1893" height="793" alt="image" src="https://github.com/user-attachments/assets/f0782750-2b99-4f30-af7f-3da10dc1b755" />

### Dashboard
- Tampilan **card** untuk setiap laporan (bukan tabel) — menampilkan avatar & tag platform sosmed, judul, nama akun buzzer, dan tanggal
- Aksen warna berbeda otomatis per platform (Twitter/X, Facebook, Instagram, TikTok, YouTube, lainnya)
- Search berdasarkan judul / nama akun buzzer
- Filter berdasarkan platform sosmed
- Diurutkan dari laporan terbaru
- Pagination (default **5 laporan per halaman**, bisa diubah lewat variabel `$limit` di `dashboard.php`)
- Klik card untuk membuka detail laporan lengkap beserta screenshot

<img width="1876" height="796" alt="image" src="https://github.com/user-attachments/assets/c673d6b1-76ee-423b-8f00-623087e11eee" />

### Profile
- Lihat & update data akun (username, email)
- Lihat daftar laporan yang pernah dibuat sendiri, dengan opsi edit/hapus langsung dari situ

<img width="1899" height="438" alt="image" src="https://github.com/user-attachments/assets/8f9e15bc-a9e6-4666-9d2c-cc8ac3891afb" />

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
