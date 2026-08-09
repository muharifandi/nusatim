# Modularisasi & Pengembangan Modul

Dokumen ini menjelaskan bagaimana aplikasi dipecah menjadi modul-modul kecil untuk skalabilitas tim dan performa build yang lebih cepat.

## 1. Jenis Modul

### A. Core Modules (`:core`)
Modul yang berisi kode yang digunakan oleh banyak fitur.
- `:core:ui`: Design system, komponen UI global (XML), tema.
- `:core:network`: Konfigurasi Retrofit, OkHttp, dan base API service.
- `:core:architecture`: Base class untuk MVI, ViewModel, dan Navigasi.
- `:core:common`: Utility, Extension, dan Security logic.
- `:core:model`: Pure Kotlin data models (Domain Entities).

### B. Feature Modules (`:features`)
Setiap fitur besar harus dipisah menggunakan pola **API/Impl Split**.
- **`:api` module:** Berisi kontrak navigasi (Destination) dan data model publik. Ringan dan cepat di-compile.
- **`:impl` module:** Berisi UI (XML/Fragment), ViewModel, UseCase, dan Repository.

---

## 2. Mengapa Memisahkan API & Impl?
1. **Build Speed:** Saat modul `:impl` berubah, modul fitur lain yang hanya tergantung pada `:api` tidak perlu di-compile ulang.
2. **Encapsulation:** Detail implementasi (seperti library internal) tidak bocor ke modul lain.
3. **Decoupling:** Menghindari *Cyclic Dependency*.

---

## 3. Aturan Dependensi (Dependency Direction)
Aplikasi kita menggunakan **Directed Acyclic Graph (DAG)**:
- `:features:<name>:impl` -> `:features:<name>:api`
- `:features:<name>:impl` -> `:core:architecture`
- `:features:<name>:impl` -> `:core:ui`
- `:app` -> Semua `:impl`

**DILARANG:** Modul A butuh B dan Modul B butuh A (*Circular Dependency*).

---

## 4. Kapan Membuat Modul Baru?
- **Business Domain Baru:** Fitur yang memiliki logika bisnis berbeda (misal: Payments, Chat).
- **Independent Ownership:** Modul yang akan dikerjakan oleh tim yang berbeda.
- **Build Optimization:** Jika aplikasi sudah terlalu besar, modularisasi membantu kompilasi paralel.

### Kapan TIDAK Perlu Modul Baru?
- Jika hanya berisi 1-2 kelas helper (masukkan ke `:core:common`).
- Jika fitur tersebut sangat kecil dan tidak akan dibagikan (masukkan ke modul fitur yang sudah ada).

---

## 5. Cara Membuat Modul (Step-by-Step)
1. Buat folder baru di bawah `features/`.
2. Buat file `build.gradle.kts` menggunakan **Convention Plugin** (`myapp.android.feature`).
3. Daftarkan modul di `settings.gradle.kts`.
4. Jalankan **Gradle Sync**.
5. Tambahkan dependensi di modul `:app` jika diperlukan untuk injeksi Hilt.

---

## 6. Menghindari "Module Explosion"
Jangan terlalu agresif membuat modul untuk hal-hal yang sangat kecil. Gabungkan fitur-fitur yang memiliki keterkaitan domain yang sangat erat (*Highly Coupled*) ke dalam satu modul fitur yang sama namun di folder package yang berbeda.

---

## 7. Kesimpulan
Dengan struktur ini, aplikasi kita siap untuk **Dynamic Feature Module** di masa depan jika diperlukan. Disiplin dalam menjaga batas antar modul adalah kunci keberhasilan arsitektur modular.
