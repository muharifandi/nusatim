# Panduan Navigasi & Pengiriman Parameter

Dokumen ini menjelaskan cara berpindah antar halaman (Fragment/Activity) dalam arsitektur multi-modul PartnerAndroid, termasuk cara mengirim dan menerima parameter secara aman.

## 1. Strategi Navigasi
Karena proyek ini menggunakan modularisasi tingkat lanjut, navigasi antar fitur dilakukan melalui **Deep Links** untuk menjaga agar antar modul tetap *decoupled* (tidak saling tergantung).

### A. Navigasi Antar Fragment (Internal Feature)
Jika masih dalam satu modul fitur yang sama, Anda dapat menggunakan ID navigasi dari `nav_graph`:
```kotlin
findNavController().navigate(R.id.action_list_to_detail)
```

### B. Navigasi Antar Modul (Inter-Feature)
Gunakan Deep Link URI yang sudah didaftarkan di `main_nav_graph.xml`:
```kotlin
val uri = Uri.parse("partner://feature_name")
findNavController().navigate(uri)
```

---

## 2. Mengirim Parameter

### A. Melalui Deep Link (Rekomendasi)
Daftarkan argumen di `main_nav_graph.xml`:
```xml
<fragment android:id="@+id/detail_fragment" ...>
    <deepLink app:uri="partner://detail/{jobId}" />
    <argument
        android:name="jobId"
        app:argType="string" />
</fragment>
```

Cara navigasi dari Fragment pengirim:
```kotlin
val jobId = "123"
val uri = Uri.parse("partner://detail/$jobId")
findNavController().navigate(uri)
```

### B. Melalui Safe Args (Sesama Modul)
```kotlin
val action = HomeFragmentDirections.actionHomeToDetail(jobId = "123")
findNavController().navigate(action)
```

---

## 3. Menerima Parameter

### A. Di Fragment Tujuan
Gunakan delegasi `navArgs()` jika menggunakan Safe Args, atau ambil langsung dari `arguments`:
```kotlin
class DetailFragment : BaseFragment<FragmentDetailBinding>() {
    
    // Cara 1: Mengambil dari arguments bundle
    private val jobId by lazy {
        arguments?.getString("jobId") ?: ""
    }

    override fun onInitViews() {
        // Gunakan jobId di sini
    }
}
```

---

## 4. Navigasi Activity

### A. Berpindah ke Activity Lain
Gunakan Intent standar. Jika activity berada di modul lain, gunakan *Fully Qualified Name*:
```kotlin
val intent = Intent().setClassName(
    requireContext(), 
    "com.nusatim.partner.features.auth.AuthActivity"
)
intent.putExtra("EXTRA_DATA", "Hello")
startActivity(intent)
```

### B. Menerima Data di Activity
```kotlin
class AuthActivity : BaseActivity<ActivityAuthBinding>() {
    override fun onInitViews() {
        val data = intent.getStringExtra("EXTRA_DATA")
    }
}
```

---

## 5. Tips Best Practice
1. **Single Source of Truth**: Definisikan semua URI Deep Link di satu tempat (misal: objek `NavigationConstants` di modul `:navigation`).
2. **Minimal Data**: Jangan mengirim objek besar (seperti Data Class) antar halaman. Kirimkan saja **ID**-nya, lalu biarkan fragment tujuan mengambil data lengkap dari Repository/Database berdasarkan ID tersebut.
3. **Type Safety**: Gunakan Safe Args untuk navigasi internal modul guna menghindari error typo pada kunci parameter.
