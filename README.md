# Moodle Request Logger Plugin  

Plugin **Request Logger** adalah middleware untuk **Moodle** yang berfungsi mencatat setiap **HTTP request pengguna** dan mengirimkan log ke **Redis** untuk dianalisis lebih lanjut. Plugin ini mendukung pencatatan aktivitas **real-time**, membantu deteksi dini terhadap potensi ancaman, dan mempermudah pemantauan keamanan dalam sistem LMS.  

## 📌 Fitur Utama  
✅ **Logging HTTP request**: Mencatat semua aktivitas pengguna dalam Moodle (akses halaman, submission, interaksi lainnya).  
✅ **Integrasi dengan Redis**: Menyimpan log secara efisien untuk mendukung analisis berbasis machine learning.  
✅ **Client-Side & Server-Side Logging**: Memastikan semua request tercatat secara lengkap.  
✅ **Kompatibel dengan Moodle 4.x** dan mendukung versi terbaru.  

---

## 🛠 Persyaratan Sistem  

Agar plugin ini dapat berjalan dengan optimal, pastikan lingkungan Moodle Anda memenuhi spesifikasi berikut:  

### **Lingkungan Pengembangan**  
- **Moodle 4.5.2**  
- **PHP 8.2.24**  
- **Redis 5.0.14.1** *(Direkomendasikan Redis 6.x+ untuk performa lebih baik)*  
- **Ekstensi PHP Redis 6.1.0**  

### **Spesifikasi Minimum untuk Instalasi**  
- **Moodle 4.1.2+**  
- **PHP 7.4+**  
- **Redis 5.0+** *(Disarankan Redis 6.x+ untuk performa optimal)*  
- **Ekstensi PHP Redis 5.0+**  

---

## 🚀 Instalasi  

Plugin ini dapat diinstal dengan **dua metode**:  

1. **Melalui GUI Moodle** *(Direkomendasikan untuk admin yang tidak terbiasa dengan CLI)*  
2. **Melalui CLI (Command Line)** *(Cocok untuk server atau instalasi otomatis)*  

### **1️⃣ Instalasi Melalui GUI (Site Administrator Moodle)**  

1. Masuk ke **dashboard admin Moodle**.  
2. Pilih menu **Site Administration → Plugins → Install plugins**.  
3. Klik **Upload a plugin** dan pilih **file .zip** dari plugin ini.  
4. Klik **Install plugin from the ZIP file**.  
5. Jika proses berhasil, halaman validasi akan muncul. Klik **Continue**.  
6. Moodle akan menampilkan detail plugin. Jika tidak ada error, klik **Upgrade Moodle database now**.  
7. Tunggu hingga proses selesai, lalu klik **Continue**.  
8. Plugin siap digunakan! 🎉  

---

### **2️⃣ Instalasi Melalui CLI (Command Line)**  

Jika Anda menginstal plugin di server atau menggunakan script otomatis, gunakan langkah berikut:  

1. **Pindahkan plugin ke direktori Moodle**  
   ```sh
   mv local_requestlogger /path/to/moodle/local/

# Jalankan perintah instalasi di terminal
php admin/cli/install.php --plugin=local_requestlogger

# Bersihkan cache Moodle (opsional, tetapi direkomendasikan)
php admin/cli/purge_caches.php

# Plugin sudah terinstall! 🎉

## 🔗 **Download Plugin (ZIP File)**  

Untuk mengunduh plugin dalam format **ZIP**, silakan akses melalui **Google Drive** berikut:  

🔹 **[Download Request Logger Plugin](https://drive.google.com/your-plugin-link)**  



## 📜 **Struktur Folder & File**  

Plugin ini dikembangkan sesuai standar Moodle dengan struktur berikut:  

```sh
local_requestlogger/
│── amd/
│   ├── build/ (Output hasil build JavaScript menggunakan Grunt)
│   │   ├── formlogger.min.js
│   │   ├── formlogger.min.js.map
│   ├── src/ (Kode sumber JavaScript sebelum dibuild)
│   │   ├── formlogger.js
│── classes/
│   ├── logger.php  (Kelas utama untuk logging request)
│   ├── output/
│   │   ├── renderer.php (Menambahkan script logger ke halaman Moodle)
│── lang/
│   ├── en/local_requestlogger.php (String bahasa Inggris)
│── lib.php  (Fungsi utama untuk hooking request)
│── log.php  (Endpoint untuk menangkap dan meneruskan log ke Redis)
│── version.php  (Informasi versi plugin)
```

---

## ⚠ **Catatan Penting**  

- Pastikan Redis sudah berjalan di server Moodle sebelum menggunakan plugin ini.  
- Jika mengalami kendala, periksa **log error di Moodle atau Redis** untuk troubleshooting.  
- Gunakan versi **Moodle 4.x ke atas** untuk kompatibilitas terbaik.  

📧 Jika ada pertanyaan atau ingin berkontribusi, silakan buka **issue di repository ini**!  

---

**© 2025 – Moodle Request Logger Plugin**  
_Dikembangkan oleh Muhamad Masayid Alfarizqi_  
```

---
