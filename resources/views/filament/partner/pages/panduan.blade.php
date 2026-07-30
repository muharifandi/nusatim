<x-filament-panels::page>
    <style>
        .pnd-wrap { max-width: 900px; }
        .pnd-intro { color: rgb(113 113 122); margin-bottom: 28px; max-width: 70ch; }
        .pnd-toc {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 8px;
            margin-bottom: 40px;
            padding: 18px;
            border: 1px solid rgb(228 228 231 / 1);
            border-radius: 12px;
            background: rgb(250 250 250);
        }
        :root[data-theme="dark"] .pnd-toc,
        .dark .pnd-toc { background: rgb(39 39 42 / 0.4); border-color: rgb(63 63 70); }
        .pnd-toc a { font-size: 0.85rem; text-decoration: none; color: inherit; padding: 3px 0; }
        .pnd-toc a:hover { text-decoration: underline; }
        .pnd-card {
            scroll-margin-top: 90px;
            border: 1px solid rgb(228 228 231);
            border-radius: 12px;
            padding: 20px 22px 22px;
            margin-bottom: 16px;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.04), 0 1px 3px 0 rgb(0 0 0 / 0.06);
        }
        :root[data-theme="dark"] .pnd-card,
        .dark .pnd-card { border-color: rgb(63 63 70); }
        .pnd-card h3 { margin: 0 0 4px; font-size: 1.05rem; }
        .pnd-card .pnd-path { font-size: 0.78rem; color: rgb(161 161 170); margin-bottom: 12px; }
        .pnd-card p { margin: 0 0 10px; }
        .pnd-card ol, .pnd-card ul { margin: 0 0 10px; padding-left: 20px; }
        .pnd-card li { margin-bottom: 5px; }
        .pnd-note {
            margin-top: 12px; padding: 10px 13px; border-radius: 8px;
            background: rgb(250 250 250); border-left: 3px solid rgb(161 161 170); font-size: 0.86rem;
        }
        :root[data-theme="dark"] .pnd-note,
        .dark .pnd-note { background: rgb(39 39 42 / 0.5); }
        .pnd-note.tip { border-left-color: #16a34a; }
        .pnd-note.warn { border-left-color: #dc2626; }
        .pnd-note strong { display: block; margin-bottom: 3px; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .pnd-pill { display: inline-block; padding: 2px 9px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; background: rgb(244 244 245); border: 1px solid rgb(228 228 231); white-space: nowrap; }
        :root[data-theme="dark"] .pnd-pill,
        .dark .pnd-pill { background: rgb(39 39 42); border-color: rgb(63 63 70); }
    </style>

    <div class="pnd-wrap">
        <p class="pnd-intro">
            Panduan ini menjelaskan setiap menu di sidebar <strong>Panel Partner</strong> — dari mencatat lead pertama
            sampai menarik komisi. Kalau akun Anda masih berstatus <strong>Pending Review</strong>, sebagian besar
            menu ini belum bisa diakses sampai admin menyetujui pendaftaran Anda.
        </p>

        <div class="pnd-toc">
            <a href="#p-dashboard">Dashboard</a>
            <a href="#p-lead">Lead & Opportunity</a>
            <a href="#p-pipeline">Sales Pipeline</a>
            <a href="#p-customer">Customer</a>
            <a href="#p-project-board">Project Board</a>
            <a href="#p-commission">Commission</a>
            <a href="#p-withdrawal">Withdrawal</a>
            <a href="#p-marketing">Marketing Center</a>
            <a href="#p-support">Support Ticket</a>
            <a href="#p-notification">Notifikasi</a>
            <a href="#p-profile">Profile</a>
        </div>

        <div class="pnd-card" id="p-dashboard">
            <h3>🏠 Dashboard</h3>
            <div class="pnd-path"><code>/partner</code></div>
            <p>Ringkasan aktivitas dan performa Anda sejak login.</p>
            <ul>
                <li><strong>Statistik aktivitas</strong>: Total Lead, Opportunity, Customer, Project, Project Available, Follow Up &amp; Meeting hari ini.</li>
                <li><strong>Statistik finansial</strong>: Total Nilai Project, Total Komisi, Komisi Pending, Komisi siap ditarik, Total Withdrawal, dan progress Target Penjualan bulan ini.</li>
                <li><strong>Grafik</strong>: sebaran status lead (pipeline), tren closing per bulan, tren komisi per bulan (12 bulan terakhir).</li>
            </ul>
        </div>

        <div class="pnd-card" id="p-lead">
            <h3>🔻 Lead & Opportunity</h3>
            <div class="pnd-path"><code>/partner/leads</code></div>
            <p>Mencatat dan mengelola calon customer (lead) milik Anda sendiri.</p>
            <ol>
                <li>Klik <strong>New Lead</strong> — isi nama, telepon, email, produk yang diminati, estimasi nilai.</li>
                <li>Update <strong>Status</strong> seiring proses penjualan: New → Contacted → Qualified → Opportunity → Proposal → Negotiation → <strong>Won</strong> (otomatis jadi Customer) atau <strong>Lost</strong>.</li>
                <li>Buka detail lead untuk menambah <strong>Catatan</strong>, mengatur pengingat <strong>Follow Up</strong>/<strong>Meeting</strong>, dan upload <strong>Dokumen</strong> (mis. proposal).</li>
            </ol>
            <div class="pnd-note tip">Timeline aktivitas lead (dibuat, ganti status, dokumen, catatan) otomatis tercatat dan tetap terlihat setelah lead jadi Customer.</div>
        </div>

        <div class="pnd-card" id="p-pipeline">
            <h3>📌 Sales Pipeline</h3>
            <div class="pnd-path"><code>/partner/pipeline</code></div>
            <p>Tampilan Kanban board — semua lead ditampilkan sebagai kartu, dikelompokkan per kolom status.</p>
            <ol>
                <li><strong>Drag &amp; drop</strong> kartu lead antar kolom untuk mengubah statusnya (menggeser ke kolom "Won" otomatis membuat Customer).</li>
                <li>Gunakan filter <strong>Produk</strong> dan <strong>Rentang Tanggal</strong> di atas board untuk mempersempit tampilan.</li>
            </ol>
        </div>

        <div class="pnd-card" id="p-customer">
            <h3>🤝 Customer</h3>
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

        <div class="pnd-card" id="p-project-board">
            <h3>💼 Project Board</h3>
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

        <div class="pnd-card" id="p-commission">
            <h3>💵 Commission</h3>
            <div class="pnd-path"><code>/partner/commissions</code></div>
            <p>Melihat riwayat komisi dari semua deal — Project, Produk, Skema Komisi yang dipakai, Nilai Project, Persentase, Nominal, dan Status (<span class="pnd-pill">Pending</span> → Waiting Client Payment → Approved → Paid, atau Rejected).</p>
        </div>

        <div class="pnd-card" id="p-withdrawal">
            <h3>📥 Withdrawal</h3>
            <div class="pnd-path"><code>/partner/withdrawals</code></div>
            <p>Mengajukan pencairan komisi yang sudah berstatus Approved.</p>
            <ol>
                <li>Klik <strong>Ajukan Withdrawal</strong> — saldo tersedia ditampilkan otomatis, rekening bank dipakai dari data profil.</li>
                <li>Wajib upload <strong>Foto KTP</strong> tiap pengajuan (terpisah dari KTP registrasi).</li>
                <li>Pantau statusnya: Pending → Approved → Paid (bukti transfer bisa dilihat), atau Rejected.</li>
            </ol>
        </div>

        <div class="pnd-card" id="p-marketing">
            <h3>📣 Marketing Center</h3>
            <div class="pnd-path"><code>/partner/marketing-materials</code></div>
            <p>Mengunduh materi promosi resmi (brosur, company profile, logo, banner, video) atau menyalin teks siap pakai (template WhatsApp, template email, FAQ, selling point) yang disediakan admin — dikelompokkan per kategori.</p>
        </div>

        <div class="pnd-card" id="p-support">
            <h3>🛟 Support Ticket</h3>
            <div class="pnd-path"><code>/partner/support-tickets</code></div>
            <p>Mengajukan pertanyaan/kendala ke tim Nusatim.</p>
            <ol>
                <li>Klik <strong>New</strong>, isi Subjek dan Deskripsi.</li>
                <li>Pantau statusnya: <span class="pnd-pill">Open</span> → In Progress (sudah ditugaskan ke staff) → Resolved (ada catatan penyelesaian) → Closed.</li>
            </ol>
            <div class="pnd-note">Tiket yang sudah dibuat tidak bisa diedit/dihapus sendiri — kalau ada tambahan info, buat tiket baru atau tunggu staff merespons.</div>
        </div>

        <div class="pnd-card" id="p-notification">
            <h3>🔔 Notifikasi</h3>
            <div class="pnd-path">Ikon lonceng di pojok kanan atas</div>
            <p>Notifikasi otomatis muncul saat: status lead berubah, ada project baru tersedia, klaim project disetujui/ditolak, pengingat follow up/meeting jatuh tempo, komisi baru masuk, dan pengumuman dari admin.</p>
        </div>

        <div class="pnd-card" id="p-profile">
            <h3>👤 Profile</h3>
            <div class="pnd-path">Menu akun (avatar) di pojok kanan atas</div>
            <p>Mengubah data akun sendiri: nama, foto profil, data rekening bank, dan preferensi notifikasi email.</p>
        </div>
    </div>
</x-filament-panels::page>
