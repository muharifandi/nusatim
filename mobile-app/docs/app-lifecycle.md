# Lifecycle & Application Flow 🔄

Dokumen ini menjelaskan alur hidup aplikasi PartnerAndroid dari saat diluncurkan hingga ditutup, serta bagaimana navigasi antar fitur dikelola secara terpusat.

## 1. Alur Visual Navigasi (Visual Flow)

Berikut adalah diagram transisi layar utama dalam aplikasi:

```mermaid
graph TD
    A[App Launch] --> B[Splash Screen]
    B --> C{Is Logged In?}
    
    C -- No --> D[Login Screen]
    C -- Yes --> E[Feature: Dashboard]
    
    D --> E
    
    subgraph "Navigation Rules"
    B -.->|Clear Backstack| D
    D -.->|Clear Backstack| E
    end
```

---

## 2. Fase Lifecycle Aplikasi

### Fase 1: Inisialisasi (Startup)
1.  **Application Class**: Hilt melakukan inisialisasi Dependency Injection.
2.  **MainActivity.onCreate()**: 
    - `installSplashScreen()` dipanggil untuk transisi smooth dari sistem.
    - `enableEdgeToEdge()` diaktifkan untuk tampilan full screen.
    - `SecurityGuard` mengecek integritas aplikasi (Root check, Debug check).

### Fase 2: Splash & Routing
1.  **SplashScreen**: Mengecek logic bisnis (misal: apakah user sudah login?).
2.  **Navigation Decision**: Berdasarkan state di atas, aplikasi akan mengarahkan user ke rute yang sesuai (Login atau Dashboard).

### Fase 3: Main Flow (Features)
- Setelah login, user masuk ke alur utama aplikasi.
- Navigasi antar fitur dilakukan secara *distributed* melalui `FeatureApi`.
- Insets (Padding System Bar) dikelola secara manual melalui `fitsSystemWindows="true"` di XML root atau via `ViewCompat.setOnApplyWindowInsetsListener`.

---

## 3. Mekanisme Navigasi Teknis

Aplikasi ini menggunakan **Navigation Component** dengan Navigation Graph XML:

1.  **Navigation Graph**: Mendefinisikan rute dan aksi navigasi di dalam file XML (`res/navigation/main_nav_graph.xml`).
2.  **FragmentContainerView**: Bertindak sebagai host navigasi di `MainActivity`.
3.  **NavController**: Mengelola perpindahan antar fragment secara type-safe melalui Safe Args.

### Keuntungan:
- **Modular**: Modul fitur tidak perlu tahu keberadaan modul fitur lain.
- **Type-Safe**: Mengirim data menggunakan class, bukan string URL manual.
- **Centralized Management**: Side effect navigasi (seperti dari ViewModel) dikelola oleh `NavigationManager`.

---

## 4. Penutupan (Termination)
Aplikasi akan ditutup jika:
- User menekan tombol Back pada layar utama (Home).
- `SecurityGuard` mendeteksi ancaman keamanan dan memanggil `finish()`.
- Sistem melakukan *process death* untuk menghemat memori.
