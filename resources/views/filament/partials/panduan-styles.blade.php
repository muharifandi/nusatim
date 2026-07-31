{{-- Shared styling for both Panduan pages (admin + partner) - kept as one
     partial so the two guides never visually drift apart. --}}
<style>
    .pnd-layout {
        display: grid;
        grid-template-columns: 260px minmax(0, 1fr);
        gap: 28px;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .pnd-layout {
            grid-template-columns: 1fr;
        }
    }

    .pnd-intro {
        color: rgb(113 113 122);
        margin-bottom: 20px;
        max-width: 70ch;
    }

    /* Left nav: sticky so it stays visible while scrolling through a long
       guide, with its own scroll for tall lists on short viewports */
    .pnd-nav {
        position: sticky;
        top: 84px;
        max-height: calc(100vh - 104px);
        overflow-y: auto;
        padding-right: 4px;
    }

    @media (max-width: 1024px) {
        .pnd-nav {
            position: static;
            max-height: none;
            margin-bottom: 24px;
        }
    }

    .pnd-search {
        display: block;
        width: 100%;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid rgb(228 228 231);
        background: rgb(250 250 250);
        font-size: 0.85rem;
        margin-bottom: 14px;
    }

    :root[data-theme="dark"] .pnd-search,
    .dark .pnd-search {
        background: rgb(39 39 42 / 0.5);
        border-color: rgb(63 63 70);
        color: rgb(244 244 245);
    }

    .pnd-nav-group {
        margin-bottom: 4px;
    }

    .pnd-nav-group summary {
        list-style: none;
        cursor: pointer;
        padding: 6px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
        color: rgb(113 113 122);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .pnd-nav-group summary::-webkit-details-marker {
        display: none;
    }

    .pnd-nav-group summary::after {
        content: '\203A';
        transform: rotate(90deg);
        transition: transform 150ms ease;
        display: inline-block;
    }

    .pnd-nav-group[open] summary::after {
        transform: rotate(270deg);
    }

    .pnd-nav-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 8px;
        margin: 1px 0;
        border-radius: 6px;
        font-size: 0.83rem;
        text-decoration: none;
        color: inherit;
    }

    .pnd-nav-link:hover {
        background: rgb(244 244 245);
    }

    :root[data-theme="dark"] .pnd-nav-link:hover,
    .dark .pnd-nav-link:hover {
        background: rgb(39 39 42);
    }

    .pnd-nav-link svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        color: rgb(161 161 170);
    }

    .pnd-group-heading {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        font-weight: 700;
        color: rgb(161 161 170);
        margin: 36px 0 14px;
        padding-top: 4px;
    }

    .pnd-group-heading:first-child {
        margin-top: 0;
    }

    .pnd-card {
        scroll-margin-top: 90px;
        border: 1px solid rgb(228 228 231);
        border-radius: 12px;
        padding: 20px 22px 22px;
        margin-bottom: 14px;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.04), 0 1px 3px 0 rgb(0 0 0 / 0.06);
    }

    :root[data-theme="dark"] .pnd-card,
    .dark .pnd-card {
        border-color: rgb(63 63 70);
    }

    .pnd-card h3 {
        margin: 0 0 4px;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pnd-card h3 svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        color: var(--primary-600);
    }

    .pnd-card .pnd-path {
        font-size: 0.78rem;
        color: rgb(161 161 170);
        margin-bottom: 12px;
        margin-left: 30px;
    }

    .pnd-card p {
        margin: 0 0 10px;
        line-height: 1.65;
    }

    .pnd-card ol,
    .pnd-card ul {
        margin: 0 0 10px;
        padding-left: 20px;
    }

    .pnd-card li {
        margin-bottom: 7px;
        line-height: 1.6;
    }

    .pnd-note {
        margin-top: 12px;
        padding: 10px 13px;
        border-radius: 8px;
        background: rgb(250 250 250);
        border-left: 3px solid rgb(161 161 170);
        font-size: 0.86rem;
        line-height: 1.55;
    }

    :root[data-theme="dark"] .pnd-note,
    .dark .pnd-note {
        background: rgb(39 39 42 / 0.5);
    }

    .pnd-note.tip {
        border-left-color: #16a34a;
    }

    .pnd-note.warn {
        border-left-color: #dc2626;
    }

    .pnd-note strong {
        display: block;
        margin-bottom: 3px;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .pnd-table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0 12px;
        font-size: 0.86rem;
    }

    .pnd-table th,
    .pnd-table td {
        text-align: left;
        padding: 6px 10px;
        border-bottom: 1px solid rgb(228 228 231);
        vertical-align: top;
    }

    :root[data-theme="dark"] .pnd-table th,
    :root[data-theme="dark"] .pnd-table td,
    .dark .pnd-table th,
    .dark .pnd-table td {
        border-color: rgb(63 63 70);
    }

    .pnd-table th {
        color: rgb(161 161 170);
        font-weight: 600;
        font-size: 0.72rem;
        text-transform: uppercase;
    }

    .pnd-pill {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgb(244 244 245);
        border: 1px solid rgb(228 228 231);
        white-space: nowrap;
    }

    :root[data-theme="dark"] .pnd-pill,
    .dark .pnd-pill {
        background: rgb(39 39 42);
        border-color: rgb(63 63 70);
    }

    .pnd-empty {
        padding: 40px 20px;
        text-align: center;
        color: rgb(161 161 170);
        font-size: 0.9rem;
    }
</style>
