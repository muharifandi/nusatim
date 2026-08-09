# Changelog (Starter Kit)

Semua perubahan signifikan pada proyek **PartnerAndroid Starter Kit** didokumentasikan di sini.

## [2.0.0-starter] - 2026-05-25

### Added
- **Project Baseline**: Fondasi bersih untuk pengembangan aplikasi baru tingkat enterprise.
- **Core Modules**: Implementasi lengkap modul `:core` (Architecture, UI, Network, Model, Common).
- **Essential Features**: Modul `:features:splash`, `:features:login`, dan `:features:register` sebagai baseline.
- **Documentation**: Panduan arsitektur yang disederhanakan khusus untuk starter kit.

### Removed (Clean up for Starter Kit)
- **Features**: Menghapus seluruh fitur spesifik project sebelumnya (`Transfer`, `Master`, `Message`, `News`, `About`, `Onboarding`, `ForgotPassword`).
- **Docs**: Menghapus panduan spesifik fitur dan dokumen manajerial yang tidak esensial.

### Changed
- **Navigation**: Restrukturisasi `AppNavHost` untuk memulai alur langsung dari Splash ke Auth.
- **Dependencies**: Membersihkan `settings.gradle.kts` dan `build.gradle.kts` dari dependensi modul yang dihapus.
