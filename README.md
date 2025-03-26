# Moodle Request Logger Plugin  

Plugin **Request Logger** adalah middleware untuk **Moodle** yang mencatat **HTTP request pengguna** dan mengirimkan log ke **Redis** secara **real-time** untuk analisis keamanan.  

## 📌 Fitur  
✅ **Logging otomatis** semua request pengguna (akses halaman, submission, dll.).  
✅ **Integrasi Redis** untuk penyimpanan log efisien.  
✅ **Dukungan Client-Side & Server-Side Logging**.  
✅ **Kompatibel dengan Moodle 4.x**.  

---

## 🛠 Persyaratan  
- **Moodle 4.1.2+**  
- **PHP 7.4+**  
- **Redis 5.0+** *(Direkomendasikan 6.x+ untuk performa lebih baik)*  
- **Ekstensi PHP Redis 5.0+**  

---

## 🚀 Instalasi  

### **1️⃣ Instalasi Melalui GUI**  
1. Masuk ke **Site Administration → Plugins → Install plugins**.  
2. Upload file **ZIP plugin**, klik **Install**.  
3. Setelah validasi, klik **Upgrade Moodle database now**.  

### **2️⃣ Instalasi Melalui CLI**  

# Pindahkan plugin ke direktori Moodle
```sh
mv local_requestlogger /path/to/moodle/local/
```
# Jalankan instalasi
```sh
php admin/cli/install.php --plugin=local_requestlogger
```
# Bersihkan cache (opsional)
```sh
php admin/cli/purge_caches.php
```

---

## 🔗 **Download Plugin**  
🔹 **[Unduh ZIP Plugin](https://drive.google.com/file/d/1RdKV9eHC3gmHR2iNRcME_jdFZsD1TWps/view?usp=sharing)**  

---

## 📜 **Struktur Folder**  
```sh
local_requestlogger/
│── amd/ (JavaScript logger)
│── classes/ (Logger utama)
│── lang/ (Dukungan bahasa)
│── lib.php (Fungsi utama)
│── log.php (Endpoint log ke Redis)
│── version.php (Info plugin)
```

---

## ⚠ **Catatan**  
- **Pastikan Redis berjalan** sebelum menggunakan plugin.  
- **Gunakan Moodle 4.x ke atas** untuk kompatibilitas terbaik.  
- Jika mengalami kendala, periksa **log error di Moodle/Redis**.  

📧 Jika ada pertanyaan, silakan buka **issue di repository ini**!  

---

**© 2025 – Moodle Request Logger Plugin**
