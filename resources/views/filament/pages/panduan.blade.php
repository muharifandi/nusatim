<x-filament-panels::page>
    <style>
        .pnd-wrap { max-width: 900px; }
        .pnd-intro { color: rgb(113 113 122); margin-bottom: 28px; max-width: 70ch; }
        .pnd-toc {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 8px;
            margin-bottom: 40px;
            padding: 18px;
            border: 1px solid rgb(228 228 231 / 1);
            border-radius: 12px;
            background: rgb(250 250 250);
        }
        :root[data-theme="dark"] .pnd-toc,
        .dark .pnd-toc { background: rgb(39 39 42 / 0.4); border-color: rgb(63 63 70); }
        .pnd-toc-group { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 700; color: rgb(161 161 170); grid-column: 1 / -1; margin-top: 10px; }
        .pnd-toc-group:first-child { margin-top: 0; }
        .pnd-toc a { font-size: 0.85rem; text-decoration: none; color: inherit; padding: 3px 0; }
        .pnd-toc a:hover { text-decoration: underline; }
        .pnd-group-heading {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.07em; font-weight: 700;
            color: rgb(161 161 170); margin: 40px 0 14px;
        }
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
        .pnd-table { width: 100%; border-collapse: collapse; margin: 8px 0 12px; font-size: 0.86rem; }
        .pnd-table th, .pnd-table td { text-align: left; padding: 6px 10px; border-bottom: 1px solid rgb(228 228 231); vertical-align: top; }
        :root[data-theme="dark"] .pnd-table th,
        :root[data-theme="dark"] .pnd-table td,
        .dark .pnd-table th, .dark .pnd-table td { border-color: rgb(63 63 70); }
        .pnd-table th { color: rgb(161 161 170); font-weight: 600; font-size: 0.72rem; text-transform: uppercase; }
        .pnd-pill { display: inline-block; padding: 2px 9px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; background: rgb(244 244 245); border: 1px solid rgb(228 228 231); white-space: nowrap; }
        :root[data-theme="dark"] .pnd-pill,
        .dark .pnd-pill { background: rgb(39 39 42); border-color: rgb(63 63 70); }
    </style>

    <div class="pnd-wrap">
        <p class="pnd-intro">
            Panduan ini menjelaskan setiap menu di sidebar <strong>Panel Admin</strong>. Perlu diingat: menu yang tampil
            di sidebar Anda bisa berbeda tergantung <strong>Role</strong> yang diberikan (lihat menu "Role &amp; Permission").
            Kalau salah satu menu di panduan ini tidak muncul, akun Anda kemungkinan belum diberi izin untuk modul
            tersebut — hubungi staff dengan role <strong>Super Admin</strong>.
        </p>

        <div class="pnd-toc">
            <div class="pnd-toc-group">Partner Management</div>
            <a href="#a-partner">Partner</a>
            <a href="#a-lead">Lead Monitoring</a>
            <a href="#a-project-board">Project Board</a>
            <a href="#a-sales-target">Sales Target</a>

            <div class="pnd-toc-group">Commission &amp; Withdrawal</div>
            <a href="#a-commission-scheme">Commission Scheme</a>
            <a href="#a-commission">Commission</a>
            <a href="#a-withdrawal">Withdrawal</a>

            <div class="pnd-toc-group">Marketing &amp; Support</div>
            <a href="#a-marketing-material">Marketing Material</a>
            <a href="#a-support-ticket">Support Ticket</a>
            <a href="#a-pengumuman">Pengumuman</a>

            <div class="pnd-toc-group">Reports &amp; Settings</div>
            <a href="#a-reports">Reports</a>
            <a href="#a-partner-settings">Partner Settings</a>

            <div class="pnd-toc-group">Website</div>
            <a href="#a-website">Konten Situs (13 menu)</a>
            <a href="#a-site-settings">Site Settings</a>

            <div class="pnd-toc-group">RBAC &amp; Sistem</div>
            <a href="#a-role">Role &amp; Permission</a>
            <a href="#a-user">Staff User</a>
            <a href="#a-workflow-assignment">Workflow Assignment</a>
            <a href="#a-audit-log">Audit Log</a>
        </div>

        <div class="pnd-group-heading">Partner Management</div>

        <div class="pnd-card" id="a-partner">
            <h3>👤 Partner</h3>
            <div class="pnd-path"><code>/admin/partners</code></div>
            <p>Daftar semua akun sales partner yang mendaftar, beserta status dan dokumen KYC (KTP/NPWP/foto profil) yang mereka upload.</p>
            <ol>
                <li>Klik ikon mata (<strong>View</strong>) untuk melihat detail lengkap dan link download dokumen.</li>
                <li>Klik <strong>Approve</strong> untuk menyetujui pendaftaran — partner menerima email dan bisa langsung login penuh.</li>
                <li>Klik <strong>Reject</strong> untuk menolak — wajib isi alasan, partner menerima email berisi alasan tersebut.</li>
                <li>Klik <strong>Suspend</strong> pada partner yang sudah Approved untuk menonaktifkan sementara. Gunakan <strong>Aktifkan Kembali</strong> untuk mengembalikan aksesnya.</li>
                <li>Klik <strong>Reset Password</strong> untuk mengirim link reset password ke email partner.</li>
                <li>Klik <strong>Ubah Level</strong> untuk menetapkan tier partner (Bronze/Silver/Gold/Platinum) — atribut informasi saja, tidak memengaruhi perhitungan komisi.</li>
            </ol>
        </div>

        <div class="pnd-card" id="a-lead">
            <h3>🔻 Lead Monitoring</h3>
            <div class="pnd-path"><code>/admin/leads</code></div>
            <p>Melihat seluruh lead dari semua partner (read-only lintas partner), memvalidasi lead, dan memindah kepemilikan lead antar partner.</p>
            <ol>
                <li>Klik <strong>View</strong> untuk membuka detail — di sini tampil daftar "Kemungkinan Duplikat" (deteksi otomatis lead lain yang telepon/email/namanya mirip, termasuk typo dan format nomor yang beda).</li>
                <li>Klik <strong>Tandai Valid</strong> setelah memverifikasi data lead benar.</li>
                <li>Klik <strong>Transfer Ownership</strong> untuk memindahkan lead ke partner lain.</li>
            </ol>
        </div>

        <div class="pnd-card" id="a-project-board">
            <h3>💼 Project Board</h3>
            <div class="pnd-path"><code>/admin/partner-projects</code></div>
            <p>Mengelola daftar project yang dibuka untuk diklaim partner, dari draft sampai selesai.</p>
            <table class="pnd-table">
                <tr><th>Status</th><th>Arti</th></tr>
                <tr><td><span class="pnd-pill">Draft</span></td><td>Baru dibuat admin, belum tampil ke partner</td></tr>
                <tr><td><span class="pnd-pill">Available</span></td><td>Sudah dipublish, bisa diklaim partner mana saja</td></tr>
                <tr><td><span class="pnd-pill">Pending Approval</span></td><td>Sudah diklaim, menunggu persetujuan admin</td></tr>
                <tr><td><span class="pnd-pill">Assigned</span></td><td>Klaim disetujui, resmi milik partner tsb</td></tr>
                <tr><td><span class="pnd-pill">In Progress / Closed / Cancelled</span></td><td>Progres pengerjaan lanjutan</td></tr>
            </table>
            <ol>
                <li>Klik <strong>New Project</strong> untuk membuat project baru (status awal Draft).</li>
                <li>Klik <strong>Publish</strong> saat siap ditampilkan ke partner — semua partner approved menerima notifikasi.</li>
                <li>Klik <strong>Assign Partner</strong> untuk menugaskan langsung tanpa lewat mekanisme klaim.</li>
                <li>Gunakan <strong>Approve Claim</strong> / <strong>Reject Claim</strong> saat ada partner mengklaim.</li>
                <li>Isi field <strong>Progress (%)</strong> di form edit untuk update progres pengerjaan.</li>
                <li>Klik <strong>Close</strong> setelah project selesai.</li>
            </ol>
        </div>

        <div class="pnd-card" id="a-sales-target">
            <h3>🚩 Sales Target</h3>
            <div class="pnd-path"><code>/admin/partner-sales-targets</code></div>
            <p>Menetapkan target penjualan bulanan per partner. Target ini tampil sebagai progress bar di dashboard partner.</p>
            <ol>
                <li>Klik <strong>New Sales Target</strong>, pilih partner, pilih bulan, isi nominal target.</li>
                <li>Satu partner hanya boleh punya satu target per bulan.</li>
            </ol>
        </div>

        <div class="pnd-group-heading">Commission &amp; Withdrawal</div>

        <div class="pnd-card" id="a-commission-scheme">
            <h3>🧮 Commission Scheme</h3>
            <div class="pnd-path"><code>/admin/commission-schemes</code></div>
            <p>Mengatur aturan/skema perhitungan komisi. Satu skema hanya boleh diisi salah satu cakupan (Produk / Partner / Project) — kosongkan semua untuk jadi skema global (fallback).</p>
            <table class="pnd-table">
                <tr><th>Jenis Skema</th><th>Perhitungan</th></tr>
                <tr><td>Percentage</td><td>Persentase × Nilai Project (dihitung sekali saat closing)</td></tr>
                <tr><td>Recurring Percentage</td><td>Sama seperti Percentage untuk saat ini (lihat catatan)</td></tr>
                <tr><td>Flat Commission</td><td>Nominal tetap, tidak tergantung nilai project</td></tr>
            </table>
            <p>Urutan prioritas: <strong>Project → Partner → Produk → Default (Partner Settings) → skema global</strong>.</p>
            <div class="pnd-note warn"><strong>Future Enhancement</strong>Recurring Percentage butuh modul Invoice/Payment Tracking yang belum dibangun — saat ini dihitung sekali di awal, sama seperti Percentage biasa.</div>
        </div>

        <div class="pnd-card" id="a-commission">
            <h3>💵 Commission</h3>
            <div class="pnd-path"><code>/admin/commissions</code></div>
            <p>Memproses komisi yang dihasilkan dari deal yang closing.</p>
            <ol>
                <li>Klik <strong>Generate Komisi</strong>, pilih Customer yang belum punya komisi.</li>
                <li>Klik <strong>Bonus Komisi</strong> untuk memberi komisi tambahan manual (di luar skema).</li>
                <li>Alur status: Pending → Waiting Client Payment → Approve → Mark Paid (atau Reject dengan alasan kapan saja sebelum paid).</li>
            </ol>
        </div>

        <div class="pnd-card" id="a-withdrawal">
            <h3>📥 Withdrawal</h3>
            <div class="pnd-path"><code>/admin/withdrawals</code></div>
            <p>Memproses pengajuan pencairan komisi (withdrawal) dari partner.</p>
            <ol>
                <li>Klik <strong>View</strong> untuk melihat data rekening dan foto KTP pemohon.</li>
                <li>Klik <strong>Approve</strong> untuk menyetujui pengajuan.</li>
                <li>Klik <strong>Mark Paid</strong> setelah transfer dilakukan — wajib upload bukti transfer.</li>
                <li>Klik <strong>Reject</strong> kapan saja sebelum paid, dengan alasan.</li>
            </ol>
        </div>

        <div class="pnd-group-heading">Marketing &amp; Support</div>

        <div class="pnd-card" id="a-marketing-material">
            <h3>📣 Marketing Material</h3>
            <div class="pnd-path"><code>/admin/marketing-materials</code></div>
            <p>Upload materi promosi yang bisa diunduh/dicontek partner dari Marketing Center mereka.</p>
            <p><strong>Kategori file</strong>: Brosur, Company Profile, Price List, Proposal, Logo, Banner, Video.<br><strong>Kategori teks</strong>: Template WhatsApp, Template Email, FAQ, Selling Point.</p>
            <ol>
                <li>Klik <strong>New</strong>, pilih kategori — form otomatis menyesuaikan (upload file atau kolom teks).</li>
                <li>Aktifkan toggle <strong>Aktif</strong> supaya materi tampil ke partner.</li>
            </ol>
        </div>

        <div class="pnd-card" id="a-support-ticket">
            <h3>🛟 Support Ticket</h3>
            <div class="pnd-path"><code>/admin/support-tickets</code></div>
            <p>Menangani tiket bantuan yang dibuat partner.</p>
            <ol>
                <li>Klik <strong>Assign</strong> untuk menugaskan tiket ke staff tertentu.</li>
                <li>Klik <strong>Resolve</strong> untuk menutup tiket dengan catatan penyelesaian.</li>
                <li>Klik <strong>Close</strong> untuk menutup tiket tanpa catatan tambahan.</li>
            </ol>
        </div>

        <div class="pnd-card" id="a-pengumuman">
            <h3>📢 Pengumuman</h3>
            <div class="pnd-path"><code>/admin/send-announcement</code></div>
            <p>Mengirim pengumuman/notifikasi massal ke partner. Bisa dikirim ke semua partner atau partner tertentu, muncul di lonceng notifikasi Panel Partner.</p>
        </div>

        <div class="pnd-group-heading">Reports &amp; Settings</div>

        <div class="pnd-card" id="a-reports">
            <h3>📊 Reports</h3>
            <div class="pnd-path"><code>/admin/reports</code></div>
            <p>Melihat &amp; mengekspor 8 jenis laporan: Partner Report, Lead Report, Project Report, Closing Report, Commission Report, Withdrawal Report, Partner Performance Report, dan Total Sales Report.</p>
            <ol>
                <li>Pilih rentang tanggal untuk memfilter data laporan.</li>
                <li>Klik tombol <strong>Export</strong> untuk mengunduh dalam format CSV.</li>
            </ol>
        </div>

        <div class="pnd-card" id="a-partner-settings">
            <h3>⚙️ Partner Settings</h3>
            <div class="pnd-path"><code>/admin/manage-partner-settings</code></div>
            <p>Pengaturan global yang berlaku untuk semua partner:</p>
            <ul>
                <li><strong>Minimum Withdrawal</strong> — nominal minimal pengajuan pencairan.</li>
                <li><strong>Commission Scheme Default</strong> — skema fallback kalau tidak ada yang lebih spesifik cocok.</li>
                <li><strong>Project Claim Rule</strong> — maksimal project diklaim bersamaan, dan batas waktu proses klaim.</li>
                <li><strong>Partner Agreement</strong> — teks perjanjian kemitraan saat registrasi.</li>
                <li><strong>Notifikasi</strong> — kanal email default untuk partner baru.</li>
            </ul>
            <div class="pnd-note">Pengaturan "siapa boleh approve apa" ada di menu Workflow Assignment, bukan di sini.</div>
        </div>

        <div class="pnd-group-heading">Website</div>

        <div class="pnd-card" id="a-website">
            <h3>🌐 Konten Situs Profile</h3>
            <div class="pnd-path">Pages · Posts · Services · Projects · Team Members · Testimonials · Clients · Pricing Plans · Promotions · FAQs · Menus · Contact Messages · Newsletter</div>
            <p>13 menu untuk mengelola isi situs profile publik Nusatim — halaman statis, artikel blog, katalog layanan, portofolio project, tim, testimoni, logo klien, paket harga, promo, FAQ, susunan menu navigasi situs, pesan masuk dari form kontak, dan daftar subscriber newsletter. Masing-masing CRUD standar: <strong>New</strong> untuk menambah, ikon pensil untuk edit, ikon tempat sampah untuk hapus.</p>
        </div>

        <div class="pnd-card" id="a-site-settings">
            <h3>🔧 Site Settings</h3>
            <div class="pnd-path"><code>/admin/manage-site-settings</code></div>
            <p>Pengaturan global situs profile: nama perusahaan, logo, kontak, Google Analytics Measurement ID, dan tampilan lainnya.</p>
        </div>

        <div class="pnd-group-heading">RBAC &amp; Sistem</div>

        <div class="pnd-card" id="a-role">
            <h3>🛡️ Role &amp; Permission</h3>
            <div class="pnd-path"><code>/admin/roles</code></div>
            <p>Mengatur peran (Role) staff dan permission apa saja yang dimiliki tiap Role. Setiap modul punya 8 jenis izin: <strong>View, Create, Update, Delete, Approve, Reject, Assign, Export</strong>.</p>
            <ol>
                <li>Klik <strong>New Role</strong>, beri nama (mis. "Finance", "Sales Manager").</li>
                <li>Centang permission yang diizinkan dari daftar checkbox (bisa dicari).</li>
                <li>Assign staff ke role ini lewat menu Staff User.</li>
            </ol>
            <div class="pnd-note warn"><strong>Penting</strong>Role "Super Admin" sudah otomatis punya semua izin dan tidak bisa dihapus.</div>
        </div>

        <div class="pnd-card" id="a-user">
            <h3>👥 Staff User</h3>
            <div class="pnd-path"><code>/admin/users</code></div>
            <p>Mengelola akun staff internal yang bisa login ke Panel Admin, dan menentukan Role masing-masing.</p>
            <ol>
                <li>Klik <strong>New</strong>, isi nama, email, password.</li>
                <li>Pilih satu atau lebih <strong>Role</strong> — akses staff mengikuti gabungan permission dari semua role yang dipilih.</li>
            </ol>
        </div>

        <div class="pnd-card" id="a-workflow-assignment">
            <h3>✅ Workflow Assignment</h3>
            <div class="pnd-path"><code>/admin/workflow-assignments</code></div>
            <p>Menentukan <strong>siapa</strong> (Role mana) yang berwenang menyetujui tiap alur persetujuan — 6 baris tetap, cuma diedit:</p>
            <table class="pnd-table">
                <tr><th>Workflow</th><th>Terhubung ke</th></tr>
                <tr><td>Registrasi Partner</td><td>Approve/Reject di menu Partner</td></tr>
                <tr><td>Project Claim</td><td>Approve/Reject Claim di menu Project Board</td></tr>
                <tr><td>Project Approval</td><td>Publish project di menu Project Board</td></tr>
                <tr><td>Commission Approval</td><td>Approve/Reject di menu Commission</td></tr>
                <tr><td>Withdrawal Approval</td><td>Approve/Reject di menu Withdrawal</td></tr>
                <tr><td>Support Ticket</td><td>Resolve/Close di menu Support Ticket</td></tr>
            </table>
            <p>Kosongkan ("Siapa saja") kalau semua staff yang punya permission approve di modul itu boleh memutuskan. Pilih Role tertentu untuk membatasinya.</p>
        </div>

        <div class="pnd-card" id="a-audit-log">
            <h3>📋 Audit Log</h3>
            <div class="pnd-path"><code>/admin/audit-logs</code></div>
            <p>Riwayat siapa mengubah apa dan kapan, lintas semua modul penting — tercatat otomatis, tidak bisa diedit/dihapus.</p>
            <p>Beda dengan histori status komisi/withdrawal (hanya mencatat perubahan status), Audit Log mencatat perubahan data apa pun beserta nilai sebelum/sesudahnya. Gunakan filter Model, Aksi, dan tanggal untuk mencari kejadian tertentu.</p>
        </div>
    </div>
</x-filament-panels::page>
