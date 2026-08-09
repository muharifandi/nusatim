# Strategi & Panduan Testing (XML & Fragment)

Dokumen ini menjelaskan arsitektur dan standar pengujian yang digunakan untuk menjaga stabilitas aplikasi dari unit terkecil hingga alur pengguna utuh.

## 1. Piramida Testing
Kami mengikuti strategi **Test Pyramid** untuk menyeimbangkan kecepatan dan akurasi:
- **Unit Tests (70%)**: Menguji logika bisnis di UseCase, ViewModel, dan Mapper. Cepat dan dijalankan di JVM.
- **Integration Tests (20%)**: Menguji interaksi antar komponen, seperti Repository dengan Database.
- **UI & E2E Tests (10%)**: Menguji Fragment (XML) dan alur pengguna ujung-ke-ujung (End-to-End).

---

## 2. Strategi Unit Testing

### A. ViewModel Testing (MVI)
Fokus pada pengujian apakah **Intent** menghasilkan **State** atau **Effect** yang benar.
```kotlin
@Test
fun `loadArticles should emit Success state when repository is successful`() = runTest {
    // Given
    val articles = listOf(Article(title = "Test"))
    coEvery { useCase() } returns flowOf(ResultState.Success(articles))

    // When
    viewModel.processIntent(HomeIntent.Refresh)

    // Then
    viewModel.state.test {
        val state = awaitItem()
        assert(state.articles == articles)
        assert(!state.isLoading)
    }
}
```

### B. Coroutine & Flow Testing
Gunakan `runTest` dari library `kotlinx-coroutines-test` dan `test()` dari library **Turbine** untuk menguji `StateFlow`.

---

## 3. UI & E2E Testing (Espresso)
E2E testing menguji aplikasi sebagai satu kesatuan, menggunakan **Espresso** untuk interaksi UI XML.

### Robot Pattern (Best Practice)
Kami menggunakan **Robot Pattern** untuk memisahkan logika pengujian dari detail implementasi UI agar test lebih mudah dibaca dan dipelihara.

- **Robot**: Berisi fungsi interaksi teknis menggunakan Espresso API.
- **Test**: Berisi skenario bisnis tingkat tinggi seperti `testLoginSuccess()`.

```kotlin
class LoginRobot {
    fun enterEmail(email: String) {
        onView(withId(R.id.edt_email)).perform(typeText(email))
    }
    
    fun clickLogin() {
        onView(withId(R.id.btn_login)).perform(click())
    }
}
```

---

## 4. Fake Data Strategy
Gunakan **Fake Repository** daripada **Mock** untuk integration test agar lebih mendekati perilaku nyata.
```kotlin
class FakeAuthRepository : AuthRepository {
    private var isSuccess = true
    override suspend fun login(...) = flow { emit(ResultState.Success(Unit)) }
}
```

---

## 5. Automation Workflow
1. **Trigger:** Setiap kali ada merge ke branch utama.
2. **Execution:** Jalankan `./gradlew connectedDebugAndroidTest` via CI/CD.
3. **Reporting:** Report diunggah ke Firebase Test Lab atau platform QA lainnya.

---

## 6. Testing Checklist
- [ ] Unit Test untuk UseCase (Bisnis Logika).
- [ ] Unit Test untuk ViewModel (State & Effect).
- [ ] UI Test (Espresso) untuk komponen kritis di `:core:ui`.
- [ ] Edge case (Error network, list kosong) sudah dites.
- [ ] Coroutine menggunakan `TestDispatcher`.
- [ ] Menggunakan data dummy dari `:core:testing`.
- [ ] Robot sudah didefinisikan untuk setiap layar utama.
