# Panduan Networking & Image Loading

Dokumen ini mendokumentasikan bagaimana aplikasi berkomunikasi dengan server dan mengelola media secara efisien.

## 1. Stack Teknologi Networking

| Library | Purpose | Why Chosen | When To Use | When NOT To Use | Alternative | Performance Impact |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Retrofit** | Type-safe HTTP Client | Integrasi Flow, Mapper mudah. | Semua API Call. | Low-level Socket. | Ktor, Volley | Low overhead |
| **OkHttp** | HTTP Engine | Interceptor, Caching, Logging. | Networking layer. | - | Java URLConnection | High (Optimized) |
| **Coil** | Image Loading | Kotlin-first, ringan. | Menampilkan gambar URL. | Local static drawable. | Glide, Fresco | Low (Memory efficient) |
| **Kotlin Ser.** | JSON Serialization | Native Kotlin, Type-safe. | Parsing JSON. | Manual JSON parsing. | Gson, Moshi | Fast (No reflection) |

---

## 2. Retrofit & OkHttp
- **Konsep:** Retrofit mengubah interface Kotlin menjadi REST API call. OkHttp menangani pengiriman data di level network.
- **Masalah yang diselesaikan:** Boilerplate koneksi HTTP, manual thread handling, dan parsing JSON manual.
- **Cara Kerja:** Menggunakan *Proxy* untuk menangkap pemanggilan fungsi interface dan mengirimnya via OkHttp.
- **Best Practice:**
  - Gunakan `Interceptor` untuk menambahkan Header (Auth Token).
  - Gunakan `HttpLoggingInterceptor` hanya untuk build DEBUG.
  - Selalu kembalikan `Response<T>` atau langsung data model dengan `suspend`.

---

## 3. Image Loading dengan Coil
- **Kenapa dipilih:** Coil jauh lebih ringan dibanding Glide dan terintegrasi dengan baik menggunakan ekstensi Kotlin.
- **APK Size Impact:** Sangat kecil (~2000 method) dibanding library lain.
- **Integration Flow (View Implementation):**
  ```kotlin
  // Di dalam Fragment atau Activity
  binding.imageView.load("https://example.com/image.jpg") {
      crossfade(true)
      placeholder(R.drawable.placeholder)
      transformations(CircleCropTransformation())
  }
  ```
- **Tradeoff:** Fitur video atau GIF mungkin tidak sekomplet Glide tanpa library extension.

---

## 4. Networking Flow (End-to-End)
1. **Request:** `Repository` memanggil `ApiService` (Retrofit).
2. **Interception:** `OkHttp Interceptor` menambahkan API Key.
3. **Transport:** `OkHttp` mengirim byte data ke internet.
4. **Serialization:** JSON diterima dan diubah menjadi `DTO` oleh Kotlin Serialization.
5. **Mapping:** `Repository` mengubah `DTO` (Data Transfer Object) menjadi `Domain Model`.

---

## 5. Common Mistakes
- **Hardcoded URL:** Taruh Base URL di `BuildConfig` atau `local.properties`.
- **Large JSON:** Tidak melakukan filtrasi field JSON (gunakan `@SerialName` hanya pada yang dibutuhkan).
- **No Timeout:** Tidak mengatur timeout di OkHttp, menyebabkan aplikasi "hang" saat koneksi lambat.

---

## 6. Security Impact
- **SSL Pinning:** Dapat dikonfigurasi di OkHttp untuk mencegah *Man-in-the-Middle* attack.
- **Network Security Config:** Digunakan untuk membatasi domain yang boleh diakses (`docs/res/xml/network_security_config.xml`).
