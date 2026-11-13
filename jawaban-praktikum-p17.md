# Jawaban Praktikum Pertemuan 17 - Validasi Input & Autentikasi Tokokita

## Daftar Isi

1. [Apa yang Dilakukan Laravel Saat `$request->validate()` Gagal](#validasi-gagal)
2. [Mengapa Validasi HTML5 Saja Tidak Cukup Aman](#html5-tidak-cukup)
3. [Perbedaan `old('nama')` vs `old('nama', $barang->nama)`](#old-perbedaan)
4. [Fungsi `->ignore($barang->id)` pada `Rule::unique`](#ignore-unique)
5. [Fungsi Argumen Ketiga (Alias Atribut) pada `$request->validate()`](#alias-atribut)
6. [Perbedaan `$request->all()` vs `$request->validated()`](#all-vs-validated)
7. [File & Folder Utama Hasil Instalasi Laravel Breeze](#breeze-file-folder)
8. [Panduan Instalasi Breeze & Backup `layouts/app.blade.php`](#panduan-breeze)
9. [Lapisan `middleware('auth')` dalam Siklus Request-Response](#auth-middleware-lapisan)
10. [Refleksi Tertulis Tugas 9](#refleksi-tugas-9)

---

## <a name="validasi-gagal"></a>1. Apa yang Dilakukan Laravel Saat `$request->validate()` Gagal

Ketika `$request->validate()` gagal (data tidak lolos rule validasi), Laravel secara otomatis:

1. **Menghentikan eksekusi lebih lanjut** — request tidak dilanjutkan ke kode selanjutnya di controller.
2. **Menyimpan pesan error ke session** — semua pesan validasi disimpan dalam flash session melalui instance `$errors` (tersedia di semua view Blade).
3. **Redirect kembali ke halaman sebelumnya** — Laravel melakukan redirect `back()` ke URL asal request (misalnya `/barang/create` atau `/barang/{id}/edit`).
4. **Mem-flash input lama** — semua input yang dikirim user disimpan sebagai `old()` input agar tidak hilang saat form ditampilkan ulang.

Hasilnya, user dialihkan kembali ke form dengan pesan error ditampilkan dan input yang telah diisi tetap terisi.

**Mengapa validasi HTML5 (`required`, `min="0"`) di browser saja tidak cukup aman:**

Validasi HTML5 hanya terjadi di sisi klien (browser). Kelemahannya:

- **Dapat dibypass dengan mudah** — user dapat menonaktifkan JavaScript, menggunakan alat pengembang browser (DevTools) untuk menghapus atribut `required` atau `min="0"`, atau mengirim request langsung melalui curl/Postman tanpa melalui form HTML.
- **Tidak terjamin di semua browser** — beberapa browser lama atau non-standard mungkin tidak sepenuhnya menghormati atribut validasi HTML5.
- **Tidak ada pemeriksaan logika bisnis** — HTML5 hanya bisa memeriksa format sederhana (required, email, min/max), tidak bisa memeriksa apakah SKU sudah ada di database (`unique`) atau apakah kategori_id valid (`exists`).

Oleh karena itu, **validasi server-side wajib selalu dilakukan** sebagai lini pertahanan utama. Validasi HTML5 hanya sebagai penambah pengalaman user (UX) agar mendapat feedback segera tanpa harus reload halaman.

---

## <a name="old-perbedaan"></a>2. Perbedaan `old('nama')` vs `old('nama', $barang->nama)`

| Mode | Kode | Arti | Digunakan pada |
|------|------|------|----------------|
| Create | `old('nama')` | Ambil nilai input lama dari session. Jika tidak ada, kembalikan `null` (kosong). | `create.blade.php` — saat validasi gagal, nilai yang baru saja diisi user tetap terisi. |
| Edit | `old('nama', $barang->nama)` | Ambil nilai input lama dari session. Jika tidak ada, gunakan nilai asli `$barang->nama`. | `edit.blade.php` — saat pertama kali membuka form edit, tampilkan data asli barang; setelah validasi gagal, tampilkan input yang baru diubah user. |

**Konsep:** Helper `old()` menerima dua argumen: kunci dan nilai default. Jika ada data lama di session, gunakan itu; jika tidak, gunakan nilai default. Pada mode edit, nilai default adalah data asli dari database sehingga form tidak kosong saat pertama kali dimuat.

---

## <a name="ignore-unique"></a>3. Fungsi `->ignore($barang->id)` pada `Rule::unique`

**Fungsi:** `Rule::unique('barangs')->ignore($barang->id)` berarti "nilai SKU harus unik di tabel `barangs`, kecuali baris dengan id milik barang yang sedang diedit."

**Dampak jika tidak menggunakan `->ignore()` saat update:**

Misalnya ada barang dengan id 5 dan SKU "BRG-001". Saat user membuka form edit barang id 5 tanpa mengubah SKU (masih "BRG-001") lalu menyimpan, validasi `unique:barangs` akan melihat bahwa SKU "BRG-001" sudah ada di baris id 5 itu sendiri. Karena tidak ada `->ignore(5)`, Laravel akan menolak dan menghasilkan error `"SKU ini sudah dipakai produk lain."` meskipun user tidak mengubah apapun.

Dengan `->ignore($barang->id)`, baris id 5 dikecak dari pemeriksaan uniqueness, sehingga update berhasil meskipun SKU sama dengan yang sudah ada pada baris itu sendiri.

---

## <a name="alias-atribut"></a>4. Fungsi Argumen Ketiga (Alias Atribut) pada `$request->validate()`

Tanda tangan lengkap:
```php
$request->validate(array $rules, array $messages = [], array $attributes = [])
```

Argumen ketiga `$attributes` digunakan untuk memberikan **nama alias (label yang ramah user)** kepada setiap field. Alias ini dipakai dalam pesan validasi otomatis ketika Anda tidak menulis pesan kustom untuk setiap rule.

Contoh dalam kode kami:
```php
], [], [
    'nama'        => 'Nama Produk',
    'sku'         => 'SKU',
    'kategori_id' => 'Kategori',
]);
```

Jika pesan kustom tidak dituliskan untuk rule `required`, Laravel akan menghasilkan pesan seperti:
> "The Nama Produk field is required."

Daripada secara default:
> "The nama field is required."

Argumen ketiga membuat pesan error lebih mudah dimengerti oleh pengguna akhir karena menggunakan nama field yang natural, bukan nama kolom database.

---

## <a name="all-vs-validated"></a>5. Perbedaan `$request->all()` vs `$request->validated()`

| | `$request->all()` | `$request->validated()` |
|---|---|---|
| **Isi** | Semua input yang dikirim client (termasuk field yang tidak divalidasi) | Hanya field yang telah lolos validasi dan terdaftar di rules |
| **Keamanan mass assignment** | **Risiko tinggi** — jika ada field berbahaya (misalnya `is_admin`, `role`, `kategori_id` dimanipulasi) akan ikut tersimpan ke database melalui `create()` / `update()` | **Aman** — hanya field yang divalidasi saja yang lolos, field lain diabaikan |
| **Pemakaian** | Digunakan sebelum ada validasi, atau saat ingin seluruh input tanpa filter | Digunakan **setelah** validasi lolos, untuk mengambil data yang bersih |

**Mengapa `$request->validated()` lebih aman untuk mass assignment:**

Laravel Eloquent memiliki fitur mass assignment protection melalui `$fillable`. Namun, jika Anda melewatkan `$request->all()` ke `Barang::create($request->all())`, field apa pun yang dikirim client (termasuk field yang tidak ada di form atau yang dimanipulasi melalui DevTools) akan mencoba diisi ke model. Jika field tersebut ada di `$fillable`, akan tersimpan tanpa validasi.

Dengan `$request->validated()`, hanya field yang telah melalui validasi server-side yang dikembalikan, sehingga mass assignment hanya menyimpan data yang sudah terjamin aman dan valid.

---

## <a name="breeze-file-folder"></a>6. File & Folder Utama Hasil Instalasi Laravel Breeze

Setelah menjalankan `php artisan breeze:install blade`, Laravel Breeze membuat:

**Routing & Auth:**
- `routes/web.php` — ditambahkan rute login, register, logout, password reset, email verification, dashboard.
- `app/Http/Controllers/Auth/` — controller autentikasi (LoginController, RegisterController, ForgetPasswordController, dll.).
- `app/Http/Middleware/Authorize.php` — middleware otorisasi (opsional).

**View (Blade):**
- `resources/views/auth/` — login.blade.php, register.blade.php, verify-email.blade.php, password/ (email, reset, confirm).
- `resources/views/layouts/` — app.blade.php (layout utama Breeze), guest.blade.php (layout untuk halaman auth).
- `resources/views/dashboard.blade.php` — halaman dashboard setelah login.

**CSS/JS:**
- `resources/css/app.css` — Tailwind CSS input.
- `resources/js/app.js` — JavaScript untuk interaktivitas (dropdown, modal, dll.).
- `tailwind.config.js` — konfigurasi Tailwind.
- `postcss.config.js` — konfigurasi PostCSS.

**Konfigurasi:**
- `config/fortify.php` — konfigurasi Laravel Fortify (digunakan Breeze di belakang layar).
- `config/sanctum.php` — (opsional) untuk API token.

---

## <a name="panduan-breeze"></a>7. Panduan Instalasi Breeze & Backup `layouts/app.blade.php`

### Urutan perintah terminal

Jalankan secara berurutan di root project (`D:\UTI\S-5\Pweb_2\tokokita`):

```bash
# 0. Backup layout Tokokita sebelum Breeze menimpa
copy resources/views/layouts/app.blade.php resources/views/layouts/app.blade.php.tokokita-backup

# 1. Install paket Laravel Breeze sebagai dev dependency
composer require laravel/breeze --dev

# 2. Install scaffold Breeze dengan stack Blade
php artisan breeze:install blade

# 3. Install dan build asset frontend (Tailwind CSS)
npm install
npm run build

# 4. Jalankan migrasi untuk tabel users, password_reset_tokens, sessions, dll.
php artisan migrate
```

### Mengatasi jika `breeze:install` menimpa `resources/views/layouts/app.blade.php`

`php artisan breeze:install blade` akan menimpa file `resources/views/layouts/app.blade.php` dengan layout bawaan Breeze. Karena project Tokokita telah menyesuaikan `app.blade.php` (navbar TOKOKITA, Bootstrap CDN, `@yield('content')`, footer), langkah-langkah berikut disarankan:

1. **Sebelum menjalankan `breeze:install`, salin cadangan** (lihat langkah 0 di atas).
2. **Setelah `breeze:install` selesai, pulihkan atau gabungkan:**
   - Pindahkan kembali cadangan:
     ```bash
     copy resources/views/layouts/app.blade.php.tokokita-backup resources/views/layouts/app.blade.php
     ```
   - Atau buka file baru hasil Breeze dan tambahkan elemen Tokokita (navbar, footer, `@yield('title')`, Bootstrap CDN) secara manual.
3. **Pastikan komponen Blade Tokokita tetap tersedia:** `<x-alert-status>` dan `<x-card-barang>` harus tetap bisa digunakan. Jika Breeze mengganti layout, pastikan `@yield('content')` masih ada di posisi yang benar.

**Catatan:** Jika `npm` tidak tersedia di environment, Anda bisa melewati `npm install && npm run build` dan menggunakan CDN CSS seperti yang sudah ada di `app.blade.php` milik Tokokita (Bootstrap 5.3.0).

### Integrasi Navigasi Breeze dengan Katalog & Form CRUD Tokokita

Setelah Breeze terinstal, navigasi Breeze (nama user login, tombol Logout, link Dashboard/Katalog) dapat diintegrasi dengan halaman katalog dan form CRUD Tokokita dengan menambahkan link navigasi di `resources/views/layouts/app.blade.php`:

```blade
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">TOKOKITA</a>
        <div class="d-flex">
            @auth
                <span class="navbar-text me-3">{{ auth()->user()->name }}</span>
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light">Dashboard</a>
                <a href="{{ route('katalog') }}" class="btn btn-sm btn-outline-light">Katalog</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">Login</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-outline-light">Register</a>
            @endauth
        </div>
    </div>
</nav>
```

Dengan struktur ini, user yang sudah login akan melihat nama sendiri, link Dashboard, Katalog, dan tombol Logout. User yang belum login (guest) akan melihat link Login dan Register. Rute `/katalog` tetap publik tanpa login sesuai spesifikasi.

---

## <a name="auth-middleware-lapisan"></a>8. Lapisan `middleware('auth')` dalam Siklus Request-Response

`middleware('auth')` bekerja di **lapisan HTTP (Routing Layer)** dalam siklus request-response Laravel, lebih tepatnya di **middleware group `web`** yang diproses sebelum request mencapai controller.

**Alur siklus request-response dengan auth middleware:**

1. Request masuk melalui `public/index.php` → `Illuminate\Foundation\Http\Kernel`.
2. Kernel menjalankan middleware global, lalu middleware group `web` (termasuk `StartSession`, `AddQueuedCookiesToResponse`, `EncryptCookies`, dan `Authenticate`).
3. Middleware `Authenticate` (`auth`) memeriksa apakah user sudah login melalui guard (biasanya `web` session).
   - Jika **belum login** → redirect ke halaman login (`/login`).
   - Jika **sudah login** → lanjutkan ke controller.
4. Controller method dipanggil, view dirender, response dikembalikan ke user.

Jadi, `auth` middleware adalah **tangga pengaman di pintu masuk route** sebelum logika bisnis controller dijalankan.

---

## <a name="refleksi-tugas-9"></a>9. Refleksi Tertulis Tugas 9

### 9.1 Perbedaan Validasi Server-side dan Validasi HTML5

| Aspek | Validasi HTML5 (Client-side) | Validasi Server-side (Laravel) |
|-------|------------------------------|-------------------------------|
| Lokasi | Browser pengguna | Server Laravel |
| Kecepatan | Langsung, tanpa reload | Perlu request ke server |
| Keamanan | **Tidak aman**, mudah dibypass | **Aman**, tidak bisa dibypass user |
| Cakupan | Format dasar (required, email, number) | Rule kompleks (unique, exists, custom) |
| Tujuan | UX / nyaman pengguna | Integritas data & keamanan |

**Alasan keduanya diperlukan:** Validasi HTML5 memberikan feedback segera kepada pengguna tanpa menunggu server, sehingga UX lebih baik. Namun karena bisa dibypass, validasi server-side tetap wajib sebagai linie pertahanan utama untuk memastikan data yang masuk ke database selalu valid dan aman.

### 9.2 Perbedaan Autentikasi dan Otorisasi

- **Autentikasi (Authentication):** Proses memverifikasi identitas user (siapa Anda?). Contoh: login dengan email dan password. Di Tokokita, dilakukan oleh Laravel Breeze (`auth` middleware memeriksa session user).
- **Otorisasi (Authorization):** Proses memverifikasi apakah user berhak melakukan suatu aksi (apa yang boleh Anda lakukan?). Contoh: hanya admin yang boleh menghapus produk, atau user hanya boleh edit produk miliknya sendiri.

**Contoh penerapan di Tokokita:**
- Autentikasi: User harus login (`auth` middleware) sebelum bisa mengakses create/store/edit/update/destroy barang.
- Otorisasi (belum diterapkan penuh): Saat ini semua user yang login dapat mengedit/hapus semua barang. Untuk otorisasi per data, perlu menambahkan pengecekan seperti `$barang->user_id === auth()->id()` atau menggunakan Laravel Policies.

### 9.3 Menggunakan Laravel Breeze Lebih Aman Dari Membangun Login Dari Nol

Laravel Breeze lebih aman karena:

- **Sudah diuji komunitas** — kode Breeze dan Fortify yang di belakangnya telah digunakan ribuan developer dan diuji terhadap ancaman umum.
- **Perlindungan bawaan** — terintegrasi dengan CSRF protection, rate limiting pada login, password hashing (bcrypt), dan secure session handling secara otomatis.
- **Password reset & verifikasi email** — sudah tersedia siap pakai dengan alur yang aman.
- **Tidak perlu mengimplementasikan ulang** — risiko kesalahan implementasi (misalnya lupa hash password, session fixation, timing attack) diminimalkan karena logika inti sudah teruji di kernel Laravel.

Membangun login dari nol rentan terhadap kesalahan implementasi yang bisa membuka celah keamanan serius.

### 9.4 Bagian Tokokita yang Masih Rentan Setelah Modul Ini

Setelah modul ini (validasi + autentikasi), bagian yang masih rentan terkait otorisasi per data / hak akses antar user:

- **Tidak ada pemilikan data (ownership):** Setiap barang tidak memiliki field `user_id`, sehingga tidak diketahui siapa yang membuat/mengedit barang tersebut. Semua user yang login dapat mengedit atau menghapus barang milik user lain.
- **Tidak ada role-based access control (RBAC):** Tidak ada perbedaan antara role "admin" dan "kasir" atau "pembeli". Semua user login memiliki hak akses yang sama terhadap CRUD barang.
- **Tidak ada Policy/Gate:** Tidak menggunakan Laravel Policies untuk membatasi aksi berdasarkan pemilik data atau role.

**Solusi lanjutan:** Tambahkan kolom `user_id` pada tabel `barangs`, buat relasi `belongsTo(User::class)`, dan gunakan Laravel Policy atau pengecekan manual (`if ($barang->user_id !== auth()->id()) abort(403)`) di controller edit/update/destroy.

---

## Ringkasan File

| File | Status | Keterangan |
|------|--------|------------|
| `app/Http/Requests/StoreBarangRequest.php` | Lengkap | authorize, rules, messages (Bahasa Indonesia), attributes |
| `app/Http/Controllers/BarangController.php` | Lengkap | store pakai StoreBarangRequest + validated(); update pakai Rule::unique->ignore() |
| `resources/views/barang/create.blade.php` | Lengkap | Blok error validasi + old() pada semua input |
| `resources/views/barang/edit.blade.php` | Lengkap | Blok error validasi + old('field', $barang->field) + selected logika |
| `routes/web.php` | Terkonfigurasi | `barang` resource dengan `->middleware('auth')`; `/katalog` publik |
| `.env` | Diubah | SQLite untuk development tanpa MySQL server |
