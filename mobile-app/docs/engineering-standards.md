# Standar Engineering, Tata Kelola & Workflow

Dokumen ini mendefinisikan standar penulisan kode, tata kelola arsitektur, dan alur kerja tim untuk menjaga kualitas dan keterbacaan codebase.

## 1. Naming Convention
- **Class:** `PascalCase` (Contoh: `HomeViewModel`).
- **Function/Variable:** `camelCase` (Contoh: `onRefreshClick`).
- **XML Resource:** `snake_case` (Contoh: `ic_back_button`).
- **UI Component:** `PascalCase` dan sebaiknya kata benda (Contoh: `PartnerToolbar`).

## 2. Tata Kelola Arsitektur (Hard Rules)
- **Boundary Modul:** Modul `:core` dilarang bergantung pada modul `:features`.
- **API/Impl:** Modul `:api` dilarang bergantung pada `:impl`.
- **Pure Kotlin:** Domain Layer wajib 100% bebas dari framework Android (Context, Intent, dll).
- **Stateless UI:** Komponen di `:core:ui` dilarang memegang ViewModel.

## 3. Workflow Pengembangan Fitur
1. **Planning:** Pahami spesifikasi Produk & Desain.
2. **Module Creation:** Gunakan pola `:api` dan `:impl` jika fitur baru.
3. **Domain First:** Buat Entity, Repository Interface, dan UseCase terlebih dahulu.
4. **Implementation:** Selesaikan Data Layer (Retrofit/Room) dan UI Layer (XML/Fragment/MVI).
5. **Testing:** Wajib membuat Unit Test untuk UseCase dan ViewModel.
6. **Code Review:** Kirim PR dan pastikan lolos pengecekan Detekt.

## 4. UI Architecture & Guidelines (XML & DataBinding)
- **DataBinding Expression:** Gunakan ekspresi DataBinding hanya untuk logika tampilan sederhana. Logika kompleks tetap di ViewModel atau Mapper.
- **Two-way Binding:** Gunakan hanya jika diperlukan (misal: input form), pastikan tidak menyebabkan infinite loop.
- **Optimasi Layout:**
    - Hindari hierarki view yang terlalu dalam (Gunakan `ConstraintLayout`).
    - Gunakan `<merge>` dan `<include>` untuk efisiensi layout.
- **Anti-Pattern:**
    - Dilarang memanggil ViewModel langsung dari expression XML jika bisa menggunakan State.
    - Hindari file XML lebih dari 500 baris. Gunakan `<include>`.
    - Jangan lakukan format data berat di dalam XML (misal: format mata uang). Lakukan di ViewModel/Mapper.

## 5. ViewModel & MVI
- **UDF (Unidirectional Data Flow):** Jangan pernah mengubah state langsung dari UI. Selalu kirim Intent ke ViewModel.
- **Immutable State:** Gunakan `data class` dengan `val` untuk State.
- **MVI File Separation:** Pisahkan `State`, `Intent`, dan `Effect` ke dalam file masing-masing (contoh: `HomeState.kt`, `HomeIntent.kt`, `HomeEffect.kt`) di dalam package `ui.state`.
- **Lifecycle Aware:** Gunakan `lifecycleScope.launch` dengan `repeatOnLifecycle` untuk collect state di Fragment.

## 6. Documentation Convention
- Gunakan **KDoc** (`/** ... */`) untuk mendokumentasikan logika bisnis yang kompleks.
- Tulis penjelasan parameter fungsi dalam Bahasa Indonesia untuk komponen `:core:ui`.

## 7. Kesimpulan
Disiplin dalam mengikuti alur dan aturan ini adalah kunci kesuksesan proyek enterprise untuk mencegah penumpukan Technical Debt.
