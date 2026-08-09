# PartnerAndroid - Scalable Enterprise Android Foundation (Starter Kit)

Selamat datang di repository **PartnerAndroid Starter Kit**. Proyek ini merupakan **fondasi arsitektur Android tingkat enterprise** yang dibangun dengan standar industri tinggi, dirancang khusus sebagai titik awal (template) yang bersih untuk membangun aplikasi baru.

## 📝 Ringkasan Fondasi
Fondasi ini dirancang untuk menyelesaikan tantangan umum dalam pengembangan aplikasi skala besar, seperti manajemen state yang kompleks, modularisasi yang sulit dipelihara, dan performa UI.

### Key Capabilities:
- **Partner Design System:** Sistem UI terpusat dengan komponen yang Figma-compliant.
- **Modularization Engine:** Struktur modul `:api` (kontrak) dan `:impl` (detail) yang optimal.
- **Enterprise Security:** Implementasi `SecurityGuard` terintegrasi.
- **Predictable Robustness:** Arsitektur MVI untuk konsistensi state UI.

---

## 📊 Alur Arsitektur MVI (Technical Flow)
Aplikasi ini menggunakan pola **Unidirectional Data Flow (UDF)** yang memastikan aliran data satu arah dan state yang dapat diprediksi.

```mermaid
sequenceDiagram
    participant UI as UI (XML/View)
    participant VM as ViewModel
    participant UC as UseCase / Data
    
    Note over UI, UC: Siklus MVI (Start to End)
    UI->>VM: 1. Kirim Intent (contoh: LoginClick)
    VM->>VM: 2. Update State (isLoading = true)
    VM->>UC: 3. Panggil Logika Bisnis/Repository
    UC-->>VM: 4. Kembalikan Data/Hasil
    VM->>VM: 5. Reducer: Update State Akhir & Fire Effect
    VM-->>UI: 6. Reactive Binding: UI Render (DataBinding)
    VM-->>UI: 7. Side Effect (Navigasi/Toast)
```

**Penjelasan Detail Alur:**
1.  **Start (Intent):** Pengguna berinteraksi dengan UI (klik tombol, input teks) yang memicu sebuah `Intent`.
2.  **State Management:** `ViewModel` menerima intent dan segera memperbarui `State` awal (misal: menampilkan loading).
3.  **Data Processing:** `ViewModel` memanggil `UseCase` atau `Repository` untuk memproses data secara asinkron.
4.  **Result:** Setelah data didapat, `ViewModel` memetakan hasil tersebut ke dalam objek `State` yang bersifat *immutable*.
5.  **Reactive Render:** UI secara otomatis memperbarui tampilan menggunakan **DataBinding** karena mengamati perubahan pada `State`.
6.  **End (Effect):** Jika ada aksi sekali jalan (seperti pindah layar atau muncul snackbar), `ViewModel` mengirimkan `Effect`.

---

## 📦 Struktur Modul & Dependensi
PartnerAndroid menggunakan struktur **Multi-module** tingkat lanjut.

```mermaid
graph TD
    APP[":app"]
    NAV[":navigation"]
    API[":features:X:api"]
    IMPL[":features:X:impl"]
    CORE[":core:architecture/ui/etc"]

    APP --> IMPL
    APP --> NAV
    IMPL --> API
    IMPL --> CORE
    NAV --> API
```

---

## 🚀 Quick Start
1. **Clone:** `git clone -b starter-project-xml https://github.com/muharifandi/compose-mvi.git`
2. **Setup:** Gunakan Android Studio Ladybug (2024.2.1) atau lebih baru.
3. **Build:** Jalankan `./gradlew assembleDebug`.
4. **Docs:** Baca [Architecture Guide](docs/architecture.md).

---

## 🏗 Arsitektur Utama
Proyek ini mengadopsi standar **Modern Android Development (MAD)**:
- **Modularization:** API/Impl split strategy.
- **Clean Architecture:** Separation of Concerns.
- **MVI Pattern:** Reactive & Predictable UI.

---

## 📖 Dokumentasi Lengkap (Starter Kit)
| Dokumen | Deskripsi |
| :--- | :--- |
| [Architecture](docs/architecture.md) | Detail Clean Architecture & Data Flow. |
| [Design System](docs/ui-component-guide.md) | Katalog komponen UI Partner. |
| [Modularization](docs/modularization.md) | Struktur modul & aturan dependency. |
| [MVI Guide](docs/mvi-architecture.md) | Panduan State, Intent, & Effect. |
| [Standards](docs/engineering-standards.md) | Aturan coding & workflow. |
| [Feature Guide](docs/feature-development.md) | Panduan membuat fitur baru. |
| [App Lifecycle](docs/app-lifecycle.md) | Alur aplikasi (Splash -> Auth). |
| [Testing Guide](docs/testing-guide.md) | Strategi pengujian. |

---

## 🛠 Tech Stack
- **UI:** XML Layout, DataBinding, Material Components
- **Network:** Retrofit, OkHttp, Kotlin Serialization
- **DI:** Hilt (Dagger)
- **Quality:** JUnit 5, MockK, Turbine

---
**Created by:** Muh. Arifandi  
**Email:** arif76440@gmail.com
