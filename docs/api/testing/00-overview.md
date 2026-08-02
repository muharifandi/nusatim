# Skenario Test API — Panduan untuk QA & Android Developer

Folder ini berisi daftar skenario test per grup endpoint, satu file per modul (penomoran sama persis dengan [../](../) — `01-auth.md` di sini membahas skenario untuk `../01-auth.md`, dst).

**Sumber kebenaran skenario ini bukan ditulis ulang dari ingatan** — setiap skenario di sini diambil langsung dari test otomatis (PHPUnit) yang sudah berjalan hijau di backend, ada di `tests/Feature/Api/*.php`. Artinya: setiap baris di sini adalah perilaku yang **sudah diverifikasi nyata berjalan**, bukan asumsi/rencana. Kalau mau lihat kode test aslinya, nama file & nama method disebutkan di tiap skenario supaya gampang ditelusuri.

## Untuk apa dokumen ini dipakai

- **QA manual**: checklist untuk uji coba aplikasi Android terhadap API asli, memastikan aplikasi menangani semua kondisi ini dengan benar (bukan cuma "jalur bahagia").
- **Android developer**: daftar *edge case* yang wajib ditangani di kode aplikasi — status error apa yang bisa muncul, kapan, dan apa maknanya (lihat kolom "Hasil yang Diharapkan" di tiap skenario).
- **Referensi saat menulis unit/instrumented test di sisi Android**: skenario di sini bisa langsung dijadikan nama test case di Android (mis. skenario "Login gagal dengan password salah" → `LoginViewModelTest.loginFailsWithWrongPassword()`).

## Cara baca tabel skenario

| Kolom | Artinya |
|---|---|
| Skenario | Nama singkat kasus yang diuji |
| Kondisi Awal | Data/state yang harus ada sebelum aksi dilakukan |
| Aksi | Request yang dikirim (endpoint + data kunci) |
| Hasil yang Diharapkan | Status HTTP + hal penting yang harus benar di response |

## Pola yang berulang di hampir semua modul

Supaya tidak diulang-ulang di 12 file, ini pola umum yang berlaku across-the-board kecuali disebutkan lain:

1. **Kepemilikan data selalu dicek** — mengakses record milik partner lain (lead, customer, project, komisi, withdrawal, tiket) selalu menghasilkan **404**, bukan 403 atau data orang lain yang ke-expose. Ini dites di hampir tiap modul dengan pola "buat partner A dan partner B, coba akses data B pakai token A, pastikan 404".
2. **Modul bisnis butuh status `approved`** — partner `pending_review`/`rejected`/`suspended` yang mencoba akses modul bisnis (Dashboard, Leads, Customers, Projects, Commissions, Withdrawals, Marketing Materials, Support Tickets, Notifications, Pipeline) selalu dapat **403**. Profile & Auth adalah pengecualian (lihat file masing-masing).
3. **Validasi field wajib** menghasilkan **422** dengan `errors` per field — dites minimal sekali per modul yang punya endpoint create/update.
4. **Token tidak dikirim / tidak valid** menghasilkan **401** — ini digeneralisasi lewat satu test di `AuthApiTest` (lihat [01-auth.md](01-auth.md)), berlaku ke SEMUA endpoint berbayar-token di seluruh API, tidak diulang per modul.

Kalau mengetes fitur baru di luar 65 skenario yang terdokumentasi di sini, ini 4 pola di atas adalah baseline minimal yang wajib tetap benar.
