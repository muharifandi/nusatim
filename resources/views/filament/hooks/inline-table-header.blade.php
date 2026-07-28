{{--
    Filament's table widget markup renders the heading and the search/filter
    toolbar as two separate stacked rows (each its own <div>, divided by a
    top border). Registered via a render hook scoped to specific widget
    classes (see AdminPanelProvider), so this only ever gets output on pages
    where those widgets actually render - safe to use a plain selector here
    without touching the Posts resource list or any other table.
--}}
<style>
	.fi-ta-header-ctn {
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
		align-items: center;
		justify-content: space-between;
	}

	.fi-ta-header-ctn > * {
		border-top-width: 0 !important;
	}
</style>
