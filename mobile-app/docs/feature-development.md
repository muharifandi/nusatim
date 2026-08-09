# Panduan Pengembangan Fitur Baru (XML & Fragment)

Dokumen ini adalah panduan *step-by-step* bagi developer untuk membuat fitur baru dengan standar Clean Architecture dan MVI menggunakan XML Layouts dan Fragments.

## 1. Struktur Folder Standar
Setiap fitur di dalam modul `:features:<name>:impl` harus mengikuti struktur berikut:

```text
com.nusatim.partner.features.<name>
├── data
│   ├── network (API Service, DTO)
│   ├── repository (Implementation)
│   └── mapper (DTO/Entity to Domain Model)
├── domain
│   ├── model (Domain Entities)
│   ├── usecase (Business Logic)
│   └── repository (Interface)
├── ui
│   ├── <screen_name>
│   │   ├── state (State, Intent, Effect)
│   │   ├── <Name>Fragment.kt
│   │   └── <Name>ViewModel.kt
├── di (Hilt Modules)
└── navigation (FeatureApi Implementation)
```

---

## 2. Template Implementasi MVI

### A. State, Intent, Effect
```kotlin
data class MyState(val isLoading: Boolean = false, val data: List<String> = emptyList()) : UiState

sealed class MyIntent : UiIntent {
    object LoadData : MyIntent()
}

sealed class MyEffect : UiEffect {
    data class ShowToast(val message: String) : MyEffect()
}
```

### B. ViewModel
```kotlin
@HiltViewModel
class MyViewModel @Inject constructor(
    private val useCase: MyUseCase
) : BaseViewModel<MyState, MyIntent, MyEffect>(MyState()) {
    override fun processIntent(intent: MyIntent) {
        when (intent) {
            is MyIntent.LoadData -> { /* Logic */ }
        }
    }
}
```

### C. Fragment & DataBinding
Pastikan layout XML dibungkus dengan tag `<layout>` dan Fragment mewarisi `BaseFragment`.

```kotlin
@AndroidEntryPoint
class MyFragment : BaseFragment<FragmentMyBinding>(R.layout.fragment_my) {
    private val viewModel: MyViewModel by viewModels()

    override fun onInitViews() {
        binding.btnLoad.setOnClickListener {
            viewModel.processIntent(MyIntent.LoadData)
        }
    }

    override fun onInitObservers() {
        lifecycleScope.launch {
            viewModel.state.collect { state ->
                // Update UI from state
                binding.progressBar.isVisible = state.isLoading
            }
        }
    }
}
```

---

## 3. Registrasi Fitur & Navigasi (Jetpack Navigation)
Proyek ini menggunakan Jetpack Navigation Component dengan Navigation Graph XML.

### Step 1: Daftarkan di Modul API
Buat interface di `:features:<name>:api` untuk mendefinisikan kontrak navigasi jika diperlukan secara dinamis.

### Step 2: Tambahkan rute di Navigation Graph
Edit file navigasi utama (misal `main_nav_graph.xml`) dan tambahkan fragment fitur tersebut.

### Step 3: Implementasi & Binding Hilt
Gunakan `@AndroidEntryPoint` pada Fragment dan Activity agar Hilt dapat melakukan injeksi dependensi.

---

## 4. Cara Memanggil Fitur & Navigasi
Untuk panduan mendalam mengenai perpindahan halaman dan pengiriman parameter, silakan baca:
👉 **[Panduan Navigasi & Parameter](navigation-guide.md)**

---

## 5. Troubleshooting: Kenapa Fitur Saya Error?
| Error | Penyebab Utama | Solusi |
| :--- | :--- | :--- |
| `Unresolved reference Binding` | Layout XML belum menggunakan tag `<layout>`. | Bungkus root element dengan `<layout>`. |
| `Type argument is not within its bounds` | VB di BaseActivity/BaseFragment bukan ViewDataBinding. | Pastikan XML sudah menggunakan DataBinding. |
| `Hilt Missing Binding` | `@AndroidEntryPoint` lupa ditambahkan. | Tambahkan anotasi pada Fragment/Activity. |
| `Module not found` | Modul belum ada di `settings.gradle`. | Cek `settings.gradle.kts` di root. |
