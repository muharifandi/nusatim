{{--
    Global admin-panel CSS fix (loaded panel-wide, not widget-scoped) for
    Filament's pagination bar. The blade-view override in
    resources/views/vendor/filament/components/pagination/index.blade.php
    changes grid-cols-[1fr_auto_1fr] to grid-cols-[auto_auto_1fr] so the
    "showing X of Y" text and the per-page selector sit tight on the left
    and only the page links get the right side - but that arbitrary Tailwind
    value was never compiled into Filament's shipped CSS (this project has
    no build step scanning vendor blade files for new utility strings), so
    the class has no effect. A plain CSS rule here is what actually applies.
--}}
<style>
	.fi-pagination {
		grid-template-columns: auto auto 1fr !important;
	}
</style>
