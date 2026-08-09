# Arsitektur MVI (Model-View-Intent)

Dokumen ini menjelaskan implementasi pola MVI di lapisan presentasi menggunakan XML Layouts, DataBinding, dan Kotlin Flow.

## 1. Komponen MVI (File Separation Standard)
Untuk menjaga keterbacaan dan skalabilitas, setiap fitur **wajib** memisahkan komponen MVI ke dalam file masing-masing di dalam paket `ui.state`:

### A. State (Model) - `${Feature}State.kt`
Satu-satunya sumber kebenaran (Single Source of Truth) untuk UI. Bersifat **Immutable**.
```kotlin
@Immutable
data class HomeState(
    val isLoading: Boolean = false,
    val articles: List<Article> = emptyList(),
    val error: String? = null
) : UiState
```

### B. Intent (User Action) - `${Feature}Intent.kt`
Mempresentasikan keinginan pengguna.
```kotlin
sealed interface HomeIntent : UiIntent {
    data object Refresh : HomeIntent
    data class Search(val query: String) : HomeIntent
}
```

### C. Effect (Side Effect) - `${Feature}Effect.kt`
Kejadian satu kali (one-time event) seperti navigasi atau menampilkan Toast.
```kotlin
sealed interface HomeEffect : UiEffect {
    data class NavigateToDetail(val url: String) : HomeEffect
    data class ShowError(val message: String) : HomeEffect
}
```

---

## 2. Alur Data (Unidirectional Data Flow)
1. **User Action**: User melakukan aksi (klik tombol) -> **Intent**.
2. **ViewModel Processing**: **ViewModel** menerima Intent -> Proses ke Repository/UseCase.
3. **State Update**: **ViewModel** memperbarui **State** secara atomik menggunakan `setState`.
4. **Effect Processing**: Jika diperlukan aksi satu kali, ViewModel memanggil `sendEffect`.
5. **UI Rendering**: **Fragment/Activity** mengamati State via `collect` di dalam `lifecycleScope` -> Perbarui UI melalui **DataBinding**.

## 3. Implementasi Teknis (BaseViewModel)
Semua ViewModel dalam proyek ini mewarisi `BaseViewModel` dari `:core:architecture` untuk standarisasi boilerplate.

```kotlin
abstract class BaseViewModel<S : UiState, I : UiIntent, E : UiEffect>(
    initialState: S
) : ViewModel() {
    private val _state = MutableStateFlow(initialState)
    val state = _state.asStateFlow()

    private val _effect = Channel<E>(Channel.BUFFERED)
    val effect = _effect.receiveAsFlow()

    abstract fun processIntent(intent: I)

    protected fun setState(reducer: S.() -> S) {
        _state.update { it.reducer() }
    }

    protected fun sendEffect(builder: () -> E) {
        viewModelScope.launch {
            _effect.send(builder())
        }
    }
}
```

### Contoh Implementasi ViewModel
```kotlin
@HiltViewModel
class HomeViewModel @Inject constructor(
    private val loginUseCase: LoginUseCase
) : BaseViewModel<HomeState, HomeIntent, HomeEffect>(HomeState()) {

    override fun processIntent(intent: HomeIntent) {
        when (intent) {
            is HomeIntent.SubmitLogin -> performLogin()
        }
    }
    // ... loadNews implementation ...
}
```

---

## 4. Keuntungan MVI
- **Predictability:** State aplikasi mudah diprediksi karena hanya berubah di satu tempat.
- **Consistency:** Menghindari masalah UI yang "stuck" karena state yang saling bertabrakan.
- **Debugging:** Mudah melacak urutan Intent yang menyebabkan error.

---

## 5. Kesimpulan
MVI tetap relevan untuk aplikasi berbasis XML/View karena sinkronisasi state yang ketat, memastikan pengalaman pengguna yang mulus dan data yang konsisten.
