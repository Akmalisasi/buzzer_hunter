### Buzzer Documentation App



<img width="1895" height="591" alt="image" src="https://github.com/user-attachments/assets/30d49ef3-e1c1-421d-924f-08d2e96dacd5" />



Aplikasi web sederhana berbasis PHP native untuk mendokumentasikan aktivitas akun buzzer di media sosial. Setiap pengguna bisa membuat laporan dokumentasi lengkap dengan bukti screenshot, dan semua laporan dari seluruh pengguna bisa dilihat bersama lewat dashboard.

---

## Fitur

### Autentikasi
- Login & Sign Up (role otomatis `user` saat daftar)
- Logout
- Ganti password (dengan verifikasi password lama)

### Dokumentasi Buzzer (CRUD)
- **Create**: buat laporan baru dengan field judul, sosmed (dropdown), nama akun buzzer, tanggal, deskripsi, dan upload screenshot (multiple, maksimal 5 file, format jpg/jpeg/png)
- **Read**: dashboard menampilkan semua laporan dari seluruh pengguna
- **Update**: edit laporan milik sendiri (atau semua laporan kalau login sebagai admin), termasuk menambah/menghapus screenshot satu per satu
- **Delete**: hapus laporan beserta seluruh file screenshot terkait

### Dashboard
- Tabel semua laporan, urut dari yang terbaru
- Search berdasarkan judul / nama akun buzzer
- Filter berdasarkan platform sosmed
- Pagination (default 10 laporan per halaman, bisa diubah di `dashboard.php`)
- Thumbnail screenshot bisa **diklik untuk membuka pop-up full image**

### Profile
- Lihat & update data akun (username, email)
- Lihat daftar laporan yang pernah dibuat sendiri, dengan opsi edit/hapus langsung dari situ

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
| Buat laporan baru | ✅ | ❌ (admin tidak membuat laporan) |
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

- Export laporan ke PDF/Excel
- Kategori/jenis pelanggaran buzzer
- Status laporan (pending, verified, ditolak)
- Notifikasi email saat laporan baru dibuat
- Log aktivitas (siapa mengedit/menghapus apa dan kapan)
