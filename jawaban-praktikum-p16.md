# Jawaban Praktikum Pertemuan 16 - CRUD Lengkap Tokokita

## Daftar Isi

1. [Tabel 7 Rute Hasil `php artisan route:list`](#tabel-7-rute)
2. [Rute yang Tidak Terpakai Tanpa Halaman Detail](#rute-tidak-terpakai)
3. [Eksperimen Error 419 Tanpa `@csrf`](#eksperimen-419)
4. [Jika `sku` Dihapus dari `$fillable`](#sku-dihapus-fillable)
5. [Mengapa Query `Kategori::all()` Dilakukan di Controller](#query-di-controller)
6. [Jika `@method('PUT')` Dihapus Saat Edit](#method-put-dihapus)
7. [Bahaya Menghapus Data dengan Tautan `GET`](#bahaya-hapus-get)
8. [Refleksi Tertulis Tugas 8](#refleksi-tugas-8)

---

## <a name="tabel-7-rute"></a>1. Tabel 7 Rute Hasil `php artisan route:list`

Berikut 7 nama rute yang dihasilkan oleh `Route::resource('barang', BarangController::class)`:

| # | Nama Rute | HTTP Method | URI | Controller Method | Keterangan |
|---|-----------|-------------|-----|-------------------|------------|
| 1 | `barang.index` | GET|HEAD | `/barang` | `index` | Menampilkan daftar semua barang |
| 2 | `barang.create` | GET|HEAD | `/barang/create` | `create` | Menampilkan form tambah barang |
| 3 | `barang.store` | POST | `/barang` | `store` | Menyimpan data barang baru |
| 4 | `barang.show` | GET|HEAD | `/barang/{barang}` | `show` | Menampilkan detail satu barang |
| 5 | `barang.edit` | GET|HEAD | `/barang/{barang}/edit` | `edit` | Menampilkan form edit barang |
| 6 | `barang.update` | PUT|PATCH | `/barang/{barang}` | `update` | Memperbarui data barang |
| 7 | `barang.destroy` | DELETE | `/barang/{barang}` | `destroy` | Menghapus data barang |

Rute tambahan dalam project Tokokita:

| # | Nama Rute | HTTP Method | URI | Controller Method |
|---|-----------|-------------|-----|-------------------|
| 8 | `katalog` | GET|HEAD | `/katalog` | `BarangController@index` |

---

## <a name="rute-tidak-terpakai"></a>2. Rute yang Tidak Terpakai Tanpa Halaman Detail

Rute yang tidak perlu dipakai jika tidak membuat halaman detail adalah:

**`barang.show`** — `GET /barang/{barang}` → `BarangController@show`

**Alasan:** Rute `show` digunakan untuk menampilkan detail lengkap satu barang pada halaman terpisah. Jika fitur detail tidak dibuat, rute ini tidak akan diakses user. Dalam CRUD Tokokita, kita hanya menggunakan index, create, store, edit, update, dan destroy. Rute `show` dapat diabaikan atau di-exclude saat mendaftarkan resource, contohnya:
```php
Route::resource('barang', BarangController::class)->except(['show']);
```

---

## <a name="eksperimen-419"></a>3. Eksperimen Error 419 Tanpa `@csrf`

Jika `@csrf` dihapus dari form POST (create atau store), saat form disubmit akan muncul error **419 Page Expired**.

**Penyebab:** Laravel secara default mengaktifkan perlindungan CSRF (Cross-Site Request Forgery) untuk route POST, PUT, PATCH, dan DELETE. Setiap form harus menyertakan token CSRF yang valid. `@csrf` secara otomatis menambahkan input tersembunyi:

```html
<input type="hidden" name="_token" value="...">
```

Ketika `@csrf` dihapus, request tidak menyertakan token CSRF. Middleware `VerifyCsrfToken` membandingkan token yang dikirim dengan token di session. Karena tidak ada token yang cocok, middleware menolak request dengan response **419 Page Expired**.

**Kesimpulan:** `@csrf` wajib ada pada setiap form yang mengubah data untuk melindungi dari serangan CSRF.

---

## <a name="sku-dihapus-fillable"></a>4. Jika `sku` Dihapus dari `$fillable`

Jika `'sku'` tidak ada di properti `$fillable` model `Barang`, maka saat `Barang::create($request->all())` dijalankan di `store()`, kolom `sku` **tidak akan tersimpan** ke database.

**Penyebab:** Laravel menggunakan **Mass Assignment Protection**. Hanya kolom yang terdaftar di `$fillable` yang boleh diisi secara massal melalui `create()` atau `fill()`. Kolom yang tidak ada di `$fillable` akan diabaikan (silent ignore).

Hasilnya, data barang tersimpan tanpa `sku` (atau `sku` bernilai `null`), meskipun user telah mengisinya di form.

**Solusi:** Tambahkan `'sku'` ke `$fillable`:
```php
protected $fillable = ['nama', 'sku', 'harga', 'stok', 'kategori_id'];
```

---

## <a name="query-di-controller"></a>5. Mengapa Query `Kategori::all()` Dilakukan di Controller

Query `Kategori::all()` dilakukan di Controller, bukan langsung di Blade, karena:

1. **Pemisahan Tanggung Jawab (MVC):** Controller mengambil dan memproses data; view hanya menampilkan data. Jika query ditulis di Blade, view bertanggung jawab atas logika bisnis, melanggar prinsip MVC.
2. **Kemudahan Pengujian:** Data di Controller lebih mudah diuji via unit/feature test. Query di Blade sulit diuji apakah data sudah benar.
3. **Debugging yang Lebih Mudah:** Kesalahan query (tabel belum ada, nama kolom salah) tertangkap di Controller sebelum view dirender.
4. **Kontrol atas Data:** Controller dapat memfilter, mengurutkan, atau memproses data sebelum dikirim ke view (misalnya hanya kategori yang aktif, atau diurutkan nama).
5. **Konsistensi:** Semua data yang dibutuhkan view disiapkan terpusat di Controller.

---

## <a name="method-put-dihapus"></a>6. Jika `@method('PUT')` Dihapus Saat Edit

Jika `@method('PUT')` dihapus dari form edit, request akan dikirim dengan metode **POST** bawaan HTML, bukan PUT.

**Alur error:**
1. Form edit mengirim request `POST` ke `route('barang.update', $barang->id)`, yaitu `POST /barang/{id}`.
2. Route `barang.update` terdaftar dengan metode **PUT|PATCH**, bukan POST.
3. Karena tidak ada route yang cocok untuk `POST /barang/{id}`, Laravel mengembalikan **405 Method Not Allowed** (URI cocok, metode tidak didukung) atau **404 Not Found**.

**Penjelasan:** HTML form standar hanya mendukung `GET` dan `POST`. Untuk PUT/PATCH/DELETE, Laravel menyediakan **method spoofing** melalui `@method('PUT')`, yang menghasilkan input tersembunyi:
```html
<input type="hidden" name="_method" value="PUT">
```
Middleware Laravel membaca `_method` dan memperlakukan request seolah-olah menggunakan metode PUT.

---

## <a name="bahaya-hapus-get"></a>7. Bahaya Menghapus Data dengan Tautan `GET` Biasa

Menghapus data menggunakan link `GET` biasa (`<a href="/barang/1/hapus">Hapus</a>`) berbahaya karena:

1. **Dapat diklik secara tidak sengaja** — atau diikuti oleh bot/crawler search engine, causing data terhapus tanpa sengaja.
2. **Rentan CSRF** — situs jahat dapat membuat halaman dengan link/iframe mengarah ke URL hapus aplikasi kita. Jika user sudah login mengunjungi situs tersebut, data dapat terhapus tanpa sepengetahuan user.
3. **Prefetch browser** — beberapa browser secara otomatis memuat link untuk mempercepat navigasi. Jika link hapus menggunakan GET, browser dapat secara otomatis memanggil URL tersebut.
4. **Riwayat browser** — URL hapus dapat tersimpan dalam riwayat, cache, atau server log, berisiko dieksekusi ulang.

**Solusi yang benar:** Gunakan form dengan metode `POST` + `@method('DELETE')`, lengkap dengan `@csrf` dan konfirmasi JavaScript (`onsubmit="return confirm(...)"`). Ini memastikan aksi hapus hanya dilakukan sengaja oleh user melalui form yang dilindungi token CSRF.

---

## <a name="refleksi-tugas-8"></a>8. Refleksi Tertulis Tugas 8

### 8.1 Alur Lengkap CRUD Simpan Produk

1. User mengklik tombol "Tambah Produk" di halaman katalog → mengarah ke `GET /barang/create` (rute `barang.create`).
2. Method `create()` di `BarangController` mengambil semua kategori via `Kategori::all()`, lalu mengirim ke view `barang.create`.
3. View menampilkan form dengan input nama, SKU, harga, stok, dan dropdown kategori. User mengisi semua field dan klik "Simpan".
4. Form mengirim request `POST /barang` (rute `barang.store`) bersama `@csrf` token dan semua input.
5. Method `store(Request $request)` menerima request, menjalankan `Barang::create($request->all())` untuk menyimpan ke database via mass assignment.
6. Controller redirect ke halaman daftar (`redirect()->route('barang.index')`) dengan flash session `sukses` berisi "Produk berhasil ditambahkan!".
7. Di halaman index, komponen `<x-alert-status type="success" :message="session('sukses')" />` menampilkan pesan sukses kepada user.

### 8.2 Fungsi `@csrf`, `@method('PUT')`, dan `@method('DELETE')`

- **`@csrf`**: Menghasilkan input tersembunyi `_token` dengan nilai token CSRF. Melindungi dari serangan Cross-Site Request Forgery (CSRF). Wajib pada setiap form POST/PUT/PATCH/DELETE. Laravel memeriksa apakah token cocok dengan session; jika tidak, request ditolak dengan error 419.
- **`@method('PUT')`**: Method spoofing untuk form update. HTML form hanya mendukung GET dan POST. `@method('PUT')` menghasilkan `<input type="hidden" name="_method" value="PUT">`, sehingga Laravel memperlakukan request POST sebagai PUT.
- **`@method('DELETE')`**: Method spoofing untuk form hapus. Sama seperti PUT tetapi dengan nilai `DELETE`, memungkinkan form HTML mengirim request DELETE untuk route destroy.

### 8.3 Minimal 2 Titik Penggunaan Relasi Eloquent P15

1. **`index()` di `BarangController`:**
   ```php
   $barangs = Barang::with('kategori')->get();
   ```
   Eager loading dengan `with('kategori')` memuat data kategori terkait setiap barang dalam satu query, menghindari masalah N+1 query saat menampilkan nama kategori di view.

2. **`barang/index.blade.php`:**
   ```blade
   {{ $b->kategori->nama ?? '-' }}
   ```
   Menampilkan nama kategori melalui relasi `belongsTo` yang didefinisikan di model `Barang`:
   ```php
   public function kategori()
   {
       return $this->belongsTo(Kategori::class);
   }
   ```

3. **`create()` dan `edit()` di `BarangController`:**
   ```php
   $kategoris = Kategori::all();
   ```
   Mengambil semua kategori untuk opsi dropdown di form create dan edit.

4. **Model `Barang` dan `Kategori`:**
   - `Barang` memiliki relasi `belongsTo(Kategori::class)` via foreign key `kategori_id`.
   - `Kategori` memiliki relasi `hasMany(Barang::class)` sebagai kebalikan.

### 8.4 Bagian Tersulit dan Cara Mengatasi

**Bagian tersulit:** Integrasi form edit dengan method spoofing (`@method('PUT')`) dan Route Model Binding.

**Mengapa sulit:** Form edit perlu metode `PUT` karena route `barang.update` terdaftar PUT/PATCH, tapi HTML form hanya mendukung POST. Route Model Binding (`Barang $barang`) otomatis mengambil model berdasarkan ID dari URL, sehingga controller tidak perlu mencari data secara manual. Kombinasi `@csrf`, `@method('PUT')`, dan Route Model Binding harus bekerja bersama dengan benar agar update berhasil.

**Solusi:**
- Menggunakan `@method('PUT')` di dalam form untuk method spoofing, sehingga Laravel memperlakukan request POST sebagai PUT.
- Menggunakan type-hint `Barang $barang` di method `edit()` dan `update()` untuk mengaktifkan Route Model Binding, sehingga Laravel otomatis menyuntikkan instance model `Barang` yang sesuai dengan ID dari URL.
- Memastikan route `barang.update` menerima parameter ID yang benar: `route('barang.update', $barang->id)`.
- Menambahkan konfirmasi JavaScript (`onsubmit="return confirm(...)"`) pada form hapus untuk mencegah penghapusan tidak sengaja.

---

## Ringkasan File

| File | Status | Keterangan |
|------|--------|------------|
| `routes/web.php` | Terkonfigurasi | `Route::resource('barang', ...)->middleware('auth')` + `/katalog` publik |
| `app/Http/Controllers/BarangController.php` | Lengkap | 7 method resource (index, create, store, edit, update, destroy + show) |
| `resources/views/barang/index.blade.php` | Lengkap | Alert sukses, tombol tambah/edit/hapus, card produk |
| `resources/views/barang/create.blade.php` | Lengkap | Form POST + `@csrf` + dropdown kategori + `old()` |
| `resources/views/barang/edit.blade.php` | Lengkap | Form POST + `@csrf` + `@method('PUT')` + `old('field', $barang->field)` |
