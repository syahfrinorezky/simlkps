# SIM LKPS (Sistem Informasi Manajemen LKPS)

Sistem Informasi Manajemen untuk Laporan Kinerja Program Studi yang dibangun dengan CodeIgniter 4, TailwindCSS, dan Alpine.js.

## 📋 Daftar Isi

- [Persyaratan Sistem](#persyaratan-sistem)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Database](#database)
- [Struktur Proyek](#struktur-proyek)
- [Troubleshooting](#troubleshooting)

## 🔧 Persyaratan Sistem

Pastikan sistem Anda memiliki software berikut terinstal:

### Wajib:
- **PHP >= 8.2** dengan extension:
  - intl
  - mbstring
  - json
  - mysqlnd (untuk MySQL)
  - libcurl
  - gd (untuk image processing)
  - xml
  - zip
- **Composer** (latest version)
- **Node.js >= 16.x** dan **npm**
- **MySQL >= 5.7** atau **MariaDB >= 10.3**
- **Web Server** (Apache/Nginx) atau PHP Development Server

### Opsional:
- **Git** (untuk version control)

## 🛠 Teknologi yang Digunakan

### Backend:
- **CodeIgniter 4.7** - PHP Framework
- **Intervention Image** - Image processing
- **mPDF** - PDF generation
- **PhpSpreadsheet** - Excel processing
- **Ramsey UUID** - UUID generation

### Frontend:
- **TailwindCSS 4.3** - CSS Framework
- **Alpine.js 3.15** - JavaScript Framework
- **Chart.js** - Data visualization
- **SweetAlert2** - Beautiful alerts
- **Flatpickr** - Date picker
- **Tom Select** - Select enhancement
- **FilePond** - File upload
- **Lucide Icons** - Icon library

## 📦 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/syahfrinorezky/simlkps.git
cd simlkps
```

Atau jika Anda sudah memiliki folder proyek, lewati langkah ini.

### 2. Install Dependencies PHP

```bash
composer install
```

Perintah ini akan mengunduh dan menginstal semua dependencies PHP yang diperlukan termasuk CodeIgniter 4 framework.

### 3. Install Dependencies JavaScript

```bash
npm install
```

Perintah ini akan mengunduh dan menginstal semua dependencies frontend seperti TailwindCSS, Alpine.js, Chart.js, dll.

## ⚙️ Konfigurasi

### 1. Setup File Environment

File `.env` sudah ada di root proyek. Jika belum ada, copy dari `env`:

```bash
copy env .env
```

### 2. Konfigurasi Database

Edit file `.env` dan sesuaikan pengaturan database:

```ini
#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = localhost
database.default.database = simlkps
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

**Catatan:** Pastikan database `simlkps` sudah dibuat di MySQL/MariaDB Anda:

```sql
CREATE DATABASE simlkps CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### 3. Konfigurasi Environment

Pastikan environment mode sudah diset:

```ini
CI_ENVIRONMENT = development
```

Untuk production, ubah ke:

```ini
CI_ENVIRONMENT = production
```

### 4. Konfigurasi Email (Opsional)

Jika Anda ingin menggunakan fitur email, sesuaikan konfigurasi SMTP di `.env`:

```ini
#--------------------------------------------------------------------
# EMAIL SMTP
#--------------------------------------------------------------------
email.fromEmail = 'admin@simlkps.com'
email.fromName = 'SIM LKPS Admin'
email.protocol = 'smtp'
email.SMTPHost = 'smtp.gmail.com'
email.SMTPUser = 'your-email@gmail.com'
email.SMTPPass = 'your-app-password'
email.SMTPPort = 465
email.SMTPCrypto = 'ssl'
email.mailType = 'html'
```

**Penting:** Jika menggunakan Gmail, gunakan [App Password](https://support.google.com/accounts/answer/185833), bukan password akun biasa.

### 5. Generate Encryption Key

```bash
php spark key:generate
```

Perintah ini akan membuat encryption key dan otomatis menyimpannya di file `.env`.

## 🗄️ Database

### 1. Jalankan Migration

Migration akan membuat semua tabel yang diperlukan:

```bash
php spark migrate
```

Perintah ini akan membuat tabel-tabel berikut:
- reporting_periods
- roles
- study_programs
- partners
- lecturers
- users
- password_resets
- student_admissions
- foreign_students
- cooperations
- lecturer_workloads
- thesis_supervisions
- lecturer_recognitions
- researches
- research_members
- community_services
- community_service_members
- fund_usages
- courses
- learning_integrations
- graduate_gpas
- dan tabel lainnya

**Jika terjadi error saat migration:**

1. **Error: "Unknown column 'jumlah_dana'"** - Sudah diperbaiki di migration terbaru
2. **Error: "Table already exists"** - Rollback dulu:
   ```bash
   php spark migrate:rollback
   php spark migrate
   ```
3. **Error: "Database connection failed"** - Periksa kembali konfigurasi database di `.env`

### 2. Seed Database (Opsional)

Jika ada seeder untuk data awal:

```bash
php spark db:seed NamaSeeder
```

### 3. Membuat Tabel Session

Untuk session database handler:

```bash
php spark session:migration
php spark migrate
```

## 🚀 Menjalankan Aplikasi

Ada beberapa cara untuk menjalankan aplikasi:

### Opsi 1: Menggunakan npm start (Direkomendasikan untuk Development)

```bash
npm start
```

Perintah ini akan menjalankan secara bersamaan:
- PHP Development Server (http://localhost:8080)
- TailwindCSS watcher (auto-compile CSS saat ada perubahan)
- esbuild watcher (auto-compile JavaScript saat ada perubahan)

### Opsi 2: Menjalankan Secara Manual

**Terminal 1 - PHP Server:**
```bash
php spark serve
```

**Terminal 2 - TailwindCSS Watcher:**
```bash
npm run dev:css
```

**Terminal 3 - JavaScript Bundler:**
```bash
npm run dev:js
```

### Opsi 3: Build untuk Production

Jika ingin membuild untuk production:

```bash
npm run build:css
npm run build:js
```

Kemudian deploy ke web server (Apache/Nginx) dengan document root mengarah ke folder `public/`.

### 4. Akses Aplikasi

Buka browser dan akses:

```
http://localhost:8080
```

Atau sesuai dengan konfigurasi web server Anda.

## 📁 Struktur Proyek

```
simlkps/
├── app/
│   ├── Config/           # File konfigurasi aplikasi
│   ├── Controllers/      # Controller untuk handle request
│   ├── Database/         # Migration dan seeder
│   ├── Models/           # Model untuk database
│   ├── Views/            # Template view
│   └── ...
├── public/               # Document root (file publik)
│   ├── css/             # File CSS
│   ├── js/              # File JavaScript
│   ├── images/          # Gambar
│   └── index.php        # Entry point aplikasi
├── writable/            # Folder untuk cache, logs, uploads
│   ├── cache/
│   ├── logs/
│   └── uploads/
├── vendor/              # Dependencies PHP (dari Composer)
├── node_modules/        # Dependencies JavaScript (dari npm)
├── .env                 # File konfigurasi environment
├── composer.json        # Dependencies PHP
└── package.json         # Dependencies JavaScript
```

## 🐛 Troubleshooting

### 1. Error "Whoops! We seem to have hit a snag"

**Penyebab:** Error umum CodeIgniter, bisa berbagai hal.

**Solusi:**
- Periksa log error di `writable/logs/log-YYYY-MM-DD.log`
- Pastikan folder `writable` dan subfolder-nya writable (permission 755 atau 777)
- Pastikan `.env` sudah dikonfigurasi dengan benar

### 2. Database Connection Failed

**Solusi:**
- Pastikan MySQL/MariaDB sudah running
- Cek kredensial database di `.env`
- Pastikan database `simlkps` sudah dibuat
- Test koneksi database:
  ```bash
  php spark db:table users
  ```

### 3. CSS atau JavaScript Tidak Muncul

**Solusi:**
- Pastikan sudah menjalankan `npm install`
- Jalankan build:
  ```bash
  npm run build:css
  npm run build:js
  ```
- Periksa apakah file `public/css/app.css` dan `public/js/bundle.js` sudah ter-generate
- Clear browser cache

### 4. Error "Class not found" atau "Namespace not found"

**Solusi:**
- Regenerate autoload:
  ```bash
  composer dump-autoload
  ```

### 5. Error Migration

**Solusi:**
- Rollback dan jalankan ulang:
  ```bash
  php spark migrate:rollback
  php spark migrate
  ```
- Atau refresh database (HATI-HATI: akan menghapus semua data):
  ```bash
  php spark migrate:refresh
  ```

### 6. Permission Denied di Folder writable/

**Windows:**
```bash
icacls writable /grant Everyone:(OI)(CI)F /T
```

**Linux/Mac:**
```bash
chmod -R 777 writable/
```

### 7. Port 8080 Sudah Digunakan

Gunakan port lain:

```bash
php spark serve --port=8081
```

Atau edit `package.json` di bagian script `serve`.

## 📚 Dokumentasi Tambahan

- [CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Chart.js Documentation](https://www.chartjs.org/docs/)

## 🤝 Kontribusi

Jika Anda ingin berkontribusi:

1. Fork repository ini
2. Buat branch feature (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Lisensi

Project ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail.

## 👨‍💻 Developer

Developed by Syahfrin Orezky

- GitHub: [@syahfrinorezky](https://github.com/syahfrinorezky)
- Repository: [simlkps](https://github.com/syahfrinorezky/simlkps)

## 📞 Support

Jika mengalami masalah atau ada pertanyaan:

1. Buka [GitHub Issues](https://github.com/syahfrinorezky/simlkps/issues)
2. Atau hubungi developer melalui email di `.env` configuration

---

**Selamat Menggunakan SIM LKPS! 🎉**
