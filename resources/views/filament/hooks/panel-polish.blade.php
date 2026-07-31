{{--
    Shared visual polish for both the admin and partner panels. Filament's
    default look is very flat (thin 1px rings, no elevation, no motion) -
    these rules add depth (soft shadows, hover lift, a colored accent) on
    top of Filament's own shipped CSS, without touching structure/layout.

    Loaded as a plain inline <style> block (same pattern as
    pagination-layout.blade.php) rather than through a Filament
    ->viteTheme() build - this project's installed tailwindcss (^4.3) no
    longer exports the `tailwindcss/base` etc. subpaths that Filament
    3.3's bundled theme.css imports, so rebuilding Filament's own theme via
    Vite fails at build time. Hand-written CSS avoids that entirely and
    needs no build step.
--}}
<style>
    /* Cards/sections: soft elevation instead of a flat 1px ring */
    .fi-section {
        box-shadow:
            0 1px 2px 0 rgb(0 0 0 / 0.04),
            0 1px 3px 0 rgb(0 0 0 / 0.06);
    }

    /* Table container: same soft elevation as sections */
    .fi-ta-ctn {
        box-shadow:
            0 1px 2px 0 rgb(0 0 0 / 0.04),
            0 1px 3px 0 rgb(0 0 0 / 0.06);
    }

    /* Stat widgets: gentle hover lift so the dashboard reads as a set of
       distinct cards rather than a flat grid of numbers */
    .fi-wi-stats-overview-stat {
        position: relative;
        overflow: hidden;
        box-shadow:
            0 1px 2px 0 rgb(0 0 0 / 0.04),
            0 1px 3px 0 rgb(0 0 0 / 0.06);
        transition: transform 150ms ease, box-shadow 150ms ease;
    }

    .fi-wi-stats-overview-stat:hover {
        transform: translateY(-2px);
        box-shadow:
            0 8px 10px -4px rgb(0 0 0 / 0.08),
            0 4px 6px -4px rgb(0 0 0 / 0.06);
    }

    /* Sidebar: subtle separation from the main content */
    .fi-sidebar {
        box-shadow: 1px 0 0 0 rgb(0 0 0 / 0.04);
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-button {
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }

    /* Topbar: gentle separation from the page content beneath it */
    .fi-topbar nav {
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.04);
    }

    /* Buttons: small lift on hover so primary actions feel more tactile */
    .fi-btn {
        transition: transform 150ms ease, box-shadow 150ms ease, background-color 150ms ease;
    }

    .fi-btn:not(:disabled):hover {
        transform: translateY(-1px);
    }

    /* Login/registration pages render through .fi-simple-layout - give the
       card the same elevation treatment */
    .fi-simple-layout .fi-simple-main {
        box-shadow:
            0 4px 6px -2px rgb(0 0 0 / 0.05),
            0 10px 15px -3px rgb(0 0 0 / 0.06);
    }

    @media (prefers-color-scheme: dark) {
        .fi-section,
        .fi-ta-ctn,
        .fi-wi-stats-overview-stat {
            box-shadow:
                0 1px 2px 0 rgb(0 0 0 / 0.2),
                0 1px 3px 0 rgb(0 0 0 / 0.3);
        }

        .fi-simple-layout .fi-simple-main {
            box-shadow:
                0 4px 6px -2px rgb(0 0 0 / 0.25),
                0 10px 15px -3px rgb(0 0 0 / 0.3);
        }
    }
</style>
