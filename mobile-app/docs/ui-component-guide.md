# Partner Design System - UI Component Guide (XML Version)

Dokumen ini adalah katalog resmi dan panduan penggunaan komponen UI yang tersedia di modul `:core:ui`. Komponen ini dirancang untuk memastikan konsistensi visual di seluruh aplikasi **PartnerAndroid** yang berbasis XML.

---

## 1. Fondasi & Tema
Seluruh komponen menggunakan identitas visual **Partner**.
- **Warna**: Didefinisikan di `res/values/colors.xml`.
- **Tipografi**: Menggunakan font **Poppins** yang dikonfigurasi melalui `styles.xml` dan `themes.xml`.

---

## 2. Komponen Custom View

### `PartnerToolbar`
Custom Toolbar yang membungkus `MaterialToolbar` untuk standarisasi di seluruh aplikasi.

**Contoh Penggunaan di XML:**
```xml
<com.nusatim.partner.core.ui.customview.PartnerToolbar
    android:id="@+id/custom_toolbar"
    android:layout_width="match_parent"
    android:layout_height="?attr/actionBarSize" />
```

**Penggunaan di Kode (Kotlin):**
```kotlin
binding.customToolbar.setTitle("Judul Layar")
```

---

## 3. Layout Standar (Included Layouts)
Modul `:core:ui` menyediakan layout standar yang bisa di-include ke dalam layout fitur menggunakan DataBinding.

### `layout_partner_toolbar.xml`
Layout dasar untuk toolbar.
```xml
<include
    android:id="@+id/custom_toolbar"
    layout="@layout/layout_partner_toolbar" />
```

### `layout_partner_bottom_nav.xml`
Layout standar untuk Bottom Navigation.
```xml
<include
    android:id="@+id/custom_bottom_nav"
    layout="@layout/layout_partner_bottom_nav" />
```

### `layout_partner_nav_drawer.xml`
Layout standar untuk Navigation Drawer.

---

## 4. Feedback States (Loading, Error, Empty)
Komponen standar untuk menangani berbagai kondisi state layar.
*(Catatan: Implementasi dalam proses migrasi ke XML Custom View)*

---

## Aturan Engineering UI
1. **Stateless**: Custom View sebaiknya tidak memegang logika bisnis.
2. **DataBinding**: Selalu gunakan DataBinding untuk menghubungkan state dari ViewModel ke UI.
3. **Reusability**: Gunakan `<include>` untuk komponen yang berulang untuk menjaga konsistensi.
