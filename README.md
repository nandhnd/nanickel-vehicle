# Nanickel Vehicle Management System

Sistem informasi manajemen pemesanan dan penggunaan kendaraan operasional perusahaan.

Aplikasi ini digunakan untuk mengelola data kendaraan, driver, pemesanan kendaraan, proses approval bertingkat, penggunaan kendaraan, fuel log, service schedule, activity log, dashboard pemakaian kendaraan, serta laporan pemesanan kendaraan.

Untuk dokumentasi ada di folder docs.

---

## 1. Teknologi yang Digunakan

| Komponen         | Teknologi / Versi   |
| ---------------- | ------------------- |
| Framework        | Laravel 12.66.0     |
| PHP              | 8.2.26              |
| Admin Panel      | Filament 3.3.0      |
| Database         | MySQL               |
| Frontend         | Filament + Livewire |
| ORM              | Eloquent            |
| Web Server       | Laragon             |
| Operating System | Windows             |

---

## 2. Database

Aplikasi menggunakan database **MySQL** dengan nama database:

```
nanickel_vehicle
```

Konfigurasi database pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nanickel_vehicle
DB_USERNAME=root
DB_PASSWORD=
```

> Sesuaikan konfigurasi database dengan environment yang digunakan.

---

## 3. Akun Login

### Admin

```
Email    : adminkp1@nanickel.com
Password : password
Role     : admin
```

Admin memiliki akses untuk mengelola master data, membuat booking, memilih approver, mengelola kendaraan, driver, fuel log, service schedule, serta menyelesaikan booking.

### Approver Level 1

```
Email    : ekoprasetyo@nanickel.com
Password : password
Role     : approver
Level    : 1
```

Approver Level 1 bertugas melakukan approval tahap pertama terhadap booking kendaraan.

### Approver Level 2

```
Email    : hendra.wijaya@nanickel.com
Password : password
Role     : approver
Level    : 2
```

Approver Level 2 bertugas melakukan approval tahap kedua setelah booking disetujui oleh Approver Level 1.

> Username/email dan password di atas merupakan contoh. Sesuaikan dengan akun yang tersedia pada database aplikasi.

---

## 4. Instalasi

### 4.1 Persiapan

Pastikan sudah terinstall:

- PHP 8.2 atau lebih baru
- Composer
- MySQL
- Laragon
- Git

### 4.2 Clone / Salin Project

Tempatkan project pada folder Laragon:

```
C:\laragon\www\nanickel-vehicle
```

Masuk ke folder project:

```bash
cd C:\laragon\www\nanickel-vehicle
```

### 4.3 Install Dependency

```bash
composer install
```

### 4.4 Konfigurasi Environment

Salin file `.env.example` menjadi `.env`.

Windows:

```bash
copy .env.example .env
```

Kemudian generate application key:

```bash
php artisan key:generate
```

### 4.5 Konfigurasi Database

Buat database MySQL:

```
nanickel_vehicle
```

Kemudian konfigurasi file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nanickel_vehicle
DB_USERNAME=root
DB_PASSWORD=
```

### 4.6 Migration

Jalankan migration:

```bash
php artisan migrate
```

Jalankan seeder:

```bash
php artisan db:seed
```

## 5. Menjalankan Aplikasi

Jalankan Laravel:

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```
http://127.0.0.1:8000
```

Login menggunakan akun yang tersedia pada database.

---

## 6. Fitur Aplikasi

### 6.1 Dashboard

Dashboard digunakan untuk menampilkan informasi ringkas mengenai kondisi kendaraan dan aktivitas pemesanan.

Informasi yang tersedia antara lain:

- Total kendaraan
- Kendaraan tersedia
- Kendaraan sedang digunakan
- Kendaraan maintenance
- Booking pending
- Booking approved
- Booking ongoing
- Grafik pemakaian kendaraan

Grafik pemakaian kendaraan digunakan untuk melihat jumlah pemakaian kendaraan berdasarkan periode.

---

## 7. Master Data

### 7.1 Region

Digunakan untuk mengelola wilayah perusahaan.

Data: Nama region, Kode region

### 7.2 Office

Digunakan untuk mengelola kantor atau lokasi operasional.

Data: Region, Nama office, Tipe office, Alamat, PIC

Tipe office: `pusat`, `cabang`, `tambang`

### 7.3 User

Digunakan untuk mengelola pengguna aplikasi.

Data: Nama, Email, Password, Role, Jabatan, Approval level, Office, Status aktif

Role: `admin`, `approver`

### 7.4 Vehicle Type

Digunakan untuk mengelompokkan jenis kendaraan.

Kategori: `orang`, `barang`

### 7.5 Rental Company

Digunakan untuk mengelola perusahaan penyedia kendaraan sewa.

Data: Nama perusahaan, Contact person, Nomor telepon

### 7.6 Vehicle

Digunakan untuk mengelola data kendaraan.

Data: Nomor polisi, Merk, Model, Tahun, Jenis kendaraan, Kepemilikan, Perusahaan rental, Office, Kapasitas, Last odometer, Status

Kepemilikan: `milik_sendiri`, `sewa`

Status kendaraan: `tersedia`, `dipakai`, `maintenance`, `tidak_aktif`

### 7.7 Driver

Digunakan untuk mengelola driver kendaraan.

Data: Nama, Nomor SIM, Jenis SIM, Office, Status

Jenis SIM: `A`, `B1`, `B2`

Status driver: `tersedia`, `bertugas`, `cuti`

---

## 8. Booking Kendaraan

Booking digunakan untuk melakukan pemesanan kendaraan operasional.

Data booking meliputi: Kode booking, Nama pemohon, Office pemohon, Kendaraan, Driver, Keperluan, Tujuan, Waktu mulai, Waktu selesai, Start odometer, End odometer, Status, Current approval level, Pembuat booking, Approver, Catatan

> Pemohon booking dapat berbeda dengan user yang membuat booking. Booking dibuat oleh Admin, sedangkan nama pemohon diinput secara terpisah.

---

## 9. Alur Approval Booking

Setelah Admin membuat booking:

```
Booking dibuat
      ↓
Status Pending
      ↓
Approval Level 1
```

Approver Level 1 dapat: **Approve** atau **Reject**

Jika ditolak: `Booking → Rejected`

Jika disetujui:

```
Approval Level 1
      ↓
Approval Level 2 dibuat
```

Approver Level 2 kemudian dapat: **Approve** atau **Reject**

Jika ditolak: `Booking → Rejected`

Jika disetujui: `Booking → Approved`

> Approval Level 2 baru dibuat setelah Approval Level 1 disetujui.

---

## 10. Pemilihan Approver

Approver booking ditentukan oleh Admin ketika membuat booking.

Admin dapat memilih:

- Approver Level 1
- Approver Level 2

Dengan demikian Admin dapat menentukan siapa yang bertanggung jawab melakukan approval terhadap booking tersebut.

---

## 11. Memulai Booking

Booking yang sudah mendapatkan seluruh approval memiliki status `Approved`.

Admin kemudian dapat memulai penggunaan kendaraan. Saat booking dimulai, Admin harus mengisi **Start Odometer**.

Setelah proses dimulai:

```
Booking : Approved → Ongoing
Vehicle : Tersedia → Dipakai
```

> Start odometer hanya diisi ketika booking sudah disetujui dan akan dimulai.

---

## 12. Menyelesaikan Booking

Setelah perjalanan selesai, Admin dapat menyelesaikan booking dengan mengisi **End Odometer**.

Kemudian status berubah:

```
Booking : Ongoing → Completed
Vehicle : Dipakai → Tersedia
```

Nilai End Odometer digunakan untuk memperbarui `Vehicle.last_odometer`.

---

## 13. Membatalkan Booking

Pembatalan booking hanya dapat dilakukan oleh **Admin**.

Approver tidak memiliki hak untuk membatalkan booking. Approver hanya dapat **Approve** atau **Reject**.

---

## 14. Booking Approval

Menu Booking Approval digunakan oleh Approver untuk melihat booking yang membutuhkan persetujuan. Approver hanya dapat melihat approval yang ditujukan kepada dirinya.

Informasi yang ditampilkan: Kode booking, Nama pemohon, Office pemohon, Kendaraan, Driver, Tujuan, Waktu mulai, Waktu selesai, Level approval, Status approval

Status approval: `menunggu`, `disetujui`, `ditolak`

---

## 15. Fuel Log

Fuel Log digunakan untuk mencatat penggunaan bahan bakar kendaraan.

Data: Kendaraan, Booking, Tanggal pengisian, Jumlah liter, Biaya, User pencatat

> Fuel Log dapat diinput oleh Admin dan tidak harus menunggu booking berstatus Completed.

---

## 16. Service Schedule

Service Schedule digunakan untuk mengelola jadwal perawatan kendaraan.

Data: Kendaraan, Jenis service, Tanggal jadwal, Tanggal selesai, Status, User pembuat

Status: `terjadwal`, `selesai`, `terlambat`

Service Schedule dibuat dan dikelola oleh Admin.

---

## 17. Activity Log

Activity Log digunakan untuk mencatat aktivitas penting pengguna di dalam aplikasi.

Data yang dicatat: User, Module, Action, Description, Waktu aktivitas

Contoh aktivitas:

- Admin membuat booking BK-001
- Approver menyetujui booking BK-001
- Approver menolak booking BK-002
- Admin memulai booking BK-001
- Admin menyelesaikan booking BK-001

Activity Log digunakan untuk membantu monitoring dan audit aktivitas sistem.

---

## 18. Laporan Pemesanan Kendaraan

Menu:

```
Laporan
└── Laporan Booking
```

Digunakan untuk menampilkan data pemesanan kendaraan berdasarkan periode tertentu.

Laporan dapat difilter berdasarkan: Tanggal mulai, Tanggal selesai

Data laporan meliputi: Kode booking, Pemohon, Office, Kendaraan, Driver, Tujuan, Waktu mulai, Waktu selesai, Status

---

## 19. Export Excel

Data laporan booking dapat diekspor dalam format `.xlsx`.

Langkah:

1. Buka menu Laporan Booking.
2. Pilih periode laporan.
3. Klik **Export Excel**.
4. File Excel akan otomatis diunduh.

---

## 20. Status Booking

| Status    | Keterangan                         |
| --------- | ---------------------------------- |
| pending   | Booking menunggu approval          |
| approved  | Seluruh approval telah disetujui   |
| rejected  | Booking ditolak oleh approver      |
| ongoing   | Kendaraan sedang digunakan         |
| completed | Penggunaan kendaraan telah selesai |
| cancelled | Booking dibatalkan oleh Admin      |

---

## 21. Status Kendaraan

| Status      | Keterangan                       |
| ----------- | -------------------------------- |
| tersedia    | Kendaraan dapat digunakan        |
| dipakai     | Kendaraan sedang digunakan       |
| maintenance | Kendaraan sedang dalam perawatan |
| tidak_aktif | Kendaraan tidak dapat digunakan  |

---

## 22. Status Driver

| Status   | Keterangan             |
| -------- | ---------------------- |
| tersedia | Driver tersedia        |
| bertugas | Driver sedang bertugas |
| cuti     | Driver sedang cuti     |

---

## 23. Struktur Proses Booking

```
Admin
  │
  ├── Membuat Booking
  │
  ▼
Pending
  │
  ▼
Approver Level 1
  │
  ├── Reject ──────────► Rejected
  │
  └── Approve
          │
          ▼
    Approver Level 2
          │
          ├── Reject ──► Rejected
          │
          └── Approve
                  │
                  ▼
               Approved
                  │
                  ▼
             Start Odometer
                  │
                  ▼
               Ongoing
                  │
                  ▼
             End Odometer
                  │
                  ▼
              Completed
```

---

## 24. Perintah Artisan yang Sering Digunakan

```bash
# Membersihkan cache
php artisan optimize:clear

# Menjalankan server
php artisan serve

# Melihat route
php artisan route:list

# Melihat status migration
php artisan migrate:status

# Menjalankan migration
php artisan migrate

# Menjalankan seeder
php artisan db:seed

```

---

## 26. Informasi Versi

```
Laravel   : 12.66.0
PHP       : 8.2.26
Filament  : 3.3.0
Database  : MySQL
```

---

## 27. Ringkasan Hak Akses

| Fitur            | Admin | Approver |
| ---------------- | :---: | :------: |
| Dashboard        |   ✓   |    ✓     |
| Region           |   ✓   |    -     |
| Office           |   ✓   |    -     |
| User             |   ✓   |    -     |
| Vehicle Type     |   ✓   |    -     |
| Rental Company   |   ✓   |    -     |
| Vehicle          |   ✓   |    -     |
| Driver           |   ✓   |    -     |
| Membuat Booking  |   ✓   |    -     |
| Memilih Approver |   ✓   |    -     |
| Approve Booking  |   -   |    ✓     |
| Reject Booking   |   -   |    ✓     |
| Cancel Booking   |   ✓   |    -     |
| Start Booking    |   ✓   |    -     |
| Complete Booking |   ✓   |    -     |
| Fuel Log         |   ✓   |    -     |
| Service Schedule |   ✓   |    -     |
| Activity Log     |   ✓   |    -     |
| Laporan Booking  |   ✓   |    -     |
| Export Excel     |   ✓   |    -     |
