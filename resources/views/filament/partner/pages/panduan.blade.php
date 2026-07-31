<x-filament-panels::page>
    @include('filament.partials.panduan-styles')

    <p class="pnd-intro">
        Panduan ini menjelaskan setiap menu di sidebar <strong>Panel Partner</strong> — dari mencatat lead pertama
        sampai menarik komisi. Kalau akun Anda masih berstatus <strong>Pending Review</strong>, sebagian besar
        menu ini belum bisa diakses sampai admin menyetujui pendaftaran Anda.
    </p>

    <div class="pnd-layout" x-data="{ search: '' }">
        <nav class="pnd-nav">
            <input
                type="search"
                x-model="search"
                class="pnd-search"
                placeholder="Cari menu... (mis. withdrawal)"
            >

            <details class="pnd-nav-group" open>
                <summary>Menu Utama</summary>
                <a href="#p-dashboard" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-home" /> Dashboard</a>
                <a href="#p-lead" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-funnel" /> Lead & Opportunity</a>
                <a href="#p-pipeline" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-view-columns" /> Sales Pipeline</a>
                <a href="#p-customer" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-user-circle" /> Customer</a>
                <a href="#p-project-board" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-briefcase" /> Project Board</a>
                <a href="#p-commission" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-banknotes" /> Commission</a>
                <a href="#p-withdrawal" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-arrow-down-tray" /> Withdrawal</a>
                <a href="#p-marketing" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-megaphone" /> Marketing Center</a>
                <a href="#p-support" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-lifebuoy" /> Support Ticket</a>
                <a href="#p-notification" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-bell" /> Notifikasi</a>
                <a href="#p-profile" class="pnd-nav-link"><x-filament::icon icon="heroicon-o-user" /> Profile</a>
            </details>
        </nav>

        <div>
            <div class="pnd-card" id="p-dashboard" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-home" /> Dashboard</h3>
                <div class="pnd-path"><code>/partner</code></div>
                <p>Ringkasan aktivitas dan performa Anda sejak login.</p>
                <ul>
                    <li><strong>Statistik aktivitas</strong>: Total Lead, Opportunity, Customer, Project, Project Available, Follow Up &amp; Meeting hari ini.</li>
                    <li><strong>Statistik finansial</strong>: Total Nilai Project, Total Komisi, Komisi Pending, Komisi siap ditarik, Total Withdrawal, dan progress Target Penjualan bulan ini.</li>
                    <li><strong>Grafik</strong>: sebaran status lead (pipeline), tren closing per bulan, tren komisi per bulan (12 bulan terakhir).</li>
                </ul>
            </div>

            <div class="pnd-card" id="p-lead" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-funnel" /> Lead &amp; Opportunity</h3>
                <div class="pnd-path"><code>/partner/leads</code></div>
                <p>Mencatat dan mengelola calon customer (lead) milik Anda sendiri.</p>
                <ol>
                    <li>Klik <strong>New Lead</strong> — isi nama, telepon, email, produk yang diminati, estimasi nilai.</li>
                    <li>Update <strong>Status</strong> seiring proses penjualan: New → Contacted → Qualified → Opportunity → Proposal → Negotiation → <strong>Won</strong> (otomatis jadi Customer) atau <strong>Lost</strong>.</li>
                    <li>Buka detail lead untuk menambah <strong>Catatan</strong>, mengatur pengingat <strong>Follow Up</strong>/<strong>Meeting</strong>, dan upload <strong>Dokumen</strong> (mis. proposal).</li>
                </ol>
                <div class="pnd-note tip">Timeline aktivitas lead (dibuat, ganti status, dokumen, catatan) otomatis tercatat dan tetap terlihat setelah lead jadi Customer.</div>
            </div>

            <div class="pnd-card" id="p-pipeline" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-view-columns" /> Sales Pipeline</h3>
                <div class="pnd-path"><code>/partner/pipeline</code></div>
                <p>Tampilan Kanban board — semua lead ditampilkan sebagai kartu, dikelompokkan per kolom status.</p>
                <ol>
                    <li><strong>Drag &amp; drop</strong> kartu lead antar kolom untuk mengubah statusnya (menggeser ke kolom "Won" otomatis membuat Customer).</li>
                    <li>Gunakan filter <strong>Produk</strong> dan <strong>Rentang Tanggal</strong> di atas board untuk mempersempit tampilan.</li>
                </ol>
            </div>

            <div class="pnd-card" id="p-customer" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-user-circle" /> Customer</h3>
                <div class="pnd-path"><code>/partner/customers</code></div>
                <p>Daftar deal yang sudah closing (dari Lead yang Won, atau dari Project Board yang di-assign) — satu halaman gabungan semua informasi per customer.</p>
                <p>Buka detail salah satu customer untuk melihat semua panel sekaligus:</p>
                <ul>
                    <li><strong>Informasi Customer</strong>, <strong>Nilai Project</strong>, <strong>Status Pembayaran</strong></li>
                    <li><strong>Status Project</strong> (kalau berasal dari Project Board) — termasuk tombol <strong>Update Progress</strong></li>
                    <li><strong>Timeline</strong> (gabungan semua riwayat), <strong>Aktivitas</strong> (event otomatis), <strong>Catatan</strong> (manual)</li>
                    <li><strong>Follow Up</strong>, <strong>Meeting</strong>, <strong>Proposal</strong> (dokumen dari lead asal)</li>
                    <li><strong>Status Komisi</strong> yang dihasilkan dari deal ini</li>
                </ul>
            </div>

            <div class="pnd-card" id="p-project-board" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-briefcase" /> Project Board</h3>
                <div class="pnd-path"><code>/partner/partner-projects</code></div>
                <p>Marketplace project yang dibuka admin untuk diklaim siapa saja.</p>
                <ol>
                    <li>Lihat daftar project <span class="pnd-pill">Available</span> beserta Budget, Lokasi, Deadline, Tingkat Kesulitan, dan estimasi Nilai Komisi.</li>
                    <li>Klik <strong>Claim</strong> pada project yang diminati — status jadi Pending Approval menunggu admin.</li>
                    <li>Klik <strong>Batalkan Klaim</strong> kalau berubah pikiran (hanya selama masih Pending Approval).</li>
                    <li>Setelah disetujui admin, project ini otomatis muncul sebagai Customer.</li>
                </ol>
                <div class="pnd-note warn">Kalau sudah mencapai batas maksimal project yang boleh diklaim bersamaan (diatur admin), tombol Claim akan gagal dengan pesan error.</div>
            </div>

            <div class="pnd-card" id="p-commission" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-banknotes" /> Commission</h3>
                <div class="pnd-path"><code>/partner/commissions</code></div>
                <p>Melihat riwayat komisi dari semua deal — Project, Produk, Skema Komisi yang dipakai, Nilai Project, Persentase, Nominal, dan Status (<span class="pnd-pill">Pending</span> → Waiting Client Payment → Approved → Paid, atau Rejected).</p>
            </div>

            <div class="pnd-card" id="p-withdrawal" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-arrow-down-tray" /> Withdrawal</h3>
                <div class="pnd-path"><code>/partner/withdrawals</code></div>
                <p>Mengajukan pencairan komisi yang sudah berstatus Approved.</p>
                <ol>
                    <li>Klik <strong>Ajukan Withdrawal</strong> — saldo tersedia ditampilkan otomatis, rekening bank dipakai dari data profil.</li>
                    <li>Wajib upload <strong>Foto KTP</strong> tiap pengajuan (terpisah dari KTP registrasi).</li>
                    <li>Pantau statusnya: Pending → Approved → Paid (bukti transfer bisa dilihat), atau Rejected.</li>
                </ol>
            </div>

            <div class="pnd-card" id="p-marketing" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-megaphone" /> Marketing Center</h3>
                <div class="pnd-path"><code>/partner/marketing-materials</code></div>
                <p>Mengunduh materi promosi resmi (brosur, company profile, logo, banner, video) atau menyalin teks siap pakai (template WhatsApp, template email, FAQ, selling point) yang disediakan admin — dikelompokkan per kategori.</p>
            </div>

            <div class="pnd-card" id="p-support" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-lifebuoy" /> Support Ticket</h3>
                <div class="pnd-path"><code>/partner/support-tickets</code></div>
                <p>Mengajukan pertanyaan/kendala ke tim Nusatim.</p>
                <ol>
                    <li>Klik <strong>New</strong>, isi Subjek dan Deskripsi.</li>
                    <li>Pantau statusnya: <span class="pnd-pill">Open</span> → In Progress (sudah ditugaskan ke staff) → Resolved (ada catatan penyelesaian) → Closed.</li>
                </ol>
                <div class="pnd-note">Tiket yang sudah dibuat tidak bisa diedit/dihapus sendiri — kalau ada tambahan info, buat tiket baru atau tunggu staff merespons.</div>
            </div>

            <div class="pnd-card" id="p-notification" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-bell" /> Notifikasi</h3>
                <div class="pnd-path">Ikon lonceng di pojok kanan atas</div>
                <p>Notifikasi otomatis muncul saat: status lead berubah, ada project baru tersedia, klaim project disetujui/ditolak, pengingat follow up/meeting jatuh tempo, komisi baru masuk, dan pengumuman dari admin.</p>
            </div>

            <div class="pnd-card" id="p-profile" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                <h3><x-filament::icon icon="heroicon-o-user" /> Profile</h3>
                <div class="pnd-path">Menu akun (avatar) di pojok kanan atas</div>
                <p>Mengubah data akun sendiri: nama, foto profil, data rekening bank, dan preferensi notifikasi email.</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
