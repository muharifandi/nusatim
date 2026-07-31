{{--
    Override of vendor/filament/widgets/resources/views/stats-overview-widget/stat.blade.php
    (same technique already established in this project for
    resources/views/vendor/filament/components/pagination/index.blade.php).

    In this Filament version, Stat::make(...)->color() is only wired into
    the description text/icon and the sparkline chart - the MAIN icon is
    hardcoded to gray regardless of ->color(). That means every stat across
    the app (Total Customer, Komisi Pending, dst) renders with an
    identical flat gray icon no matter what ->color() was set to, which
    reads as generic/unfinished. This override applies the exact same
    color-wiring pattern Filament already uses for its description icon
    (fi-color-{$color} class + get_color_css_variables()) to the main icon
    too, and gives it a soft tinted chip background instead of a bare
    glyph - so each stat's icon actually reflects its semantic color
    (success/warning/info/etc) genuinely, not just a class that has no
    effect.
--}}
@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Facades\FilamentView;

    $color = $getColor() ?? 'gray';
    $chartColor = $getChartColor() ?? 'gray';
    $descriptionColor = $getDescriptionColor() ?? 'gray';
    $descriptionIcon = $getDescriptionIcon();
    $descriptionIconPosition = $getDescriptionIconPosition();
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
    $dataChecksum = $generateDataChecksum();

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-wi-stats-overview-stat-icon h-5 w-5 rounded-lg p-1.5 shrink-0',
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-white/5',
            default => 'fi-color-custom text-custom-600 dark:text-custom-400 bg-custom-50 dark:bg-custom-400/10',
        },
        is_string($color) ? "fi-color-{$color}" : null,
    ]);

    $iconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [50, 400, 600],
            alias: 'widgets::stats-overview-widget.stat.icon',
        ) => $color !== 'gray',
    ]);

    $descriptionIconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-wi-stats-overview-stat-description-icon h-5 w-5',
        match ($descriptionColor) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);

    $descriptionIconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $descriptionColor,
            shades: [500],
            alias: 'widgets::stats-overview-widget.stat.description.icon',
        ) => $descriptionColor !== 'gray',
    ]);
@endphp

<{!! $tag !!}
    @if ($url)
        {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab()) }}
    @endif
    {{
        $getExtraAttributeBag()
            ->class([
                'fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
            ])
    }}
>
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-2">
            @if ($icon = $getIcon())
                <x-filament::icon
                    :icon="$icon"
                    :class="$iconClasses"
                    :style="$iconStyles"
                />
            @endif

            <span
                class="fi-wi-stats-overview-stat-label text-sm font-medium text-gray-500 dark:text-gray-400"
            >
                {{ $getLabel() }}
            </span>
        </div>

        <div
            class="fi-wi-stats-overview-stat-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white"
        >
            {{ $getValue() }}
        </div>

        @if ($description = $getDescription())
            <div class="flex items-center gap-x-1">
                @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::Before, 'before']))
                    <x-filament::icon
                        :icon="$descriptionIcon"
                        :class="$descriptionIconClasses"
                        :style="$descriptionIconStyles"
                    />
                @endif

                <span
                    @class([
                        'fi-wi-stats-overview-stat-description text-sm',
                        match ($descriptionColor) {
                            'gray' => 'text-gray-500 dark:text-gray-400',
                            default => 'fi-color-custom text-custom-600 dark:text-custom-400',
                        },
                        is_string($descriptionColor) ? "fi-color-{$descriptionColor}" : null,
                    ])
                    @style([
                        \Filament\Support\get_color_css_variables(
                            $descriptionColor,
                            shades: [400, 600],
                            alias: 'widgets::stats-overview-widget.stat.description',
                        ) => $descriptionColor !== 'gray',
                    ])
                >
                    {{ $description }}
                </span>

                @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::After, 'after']))
                    <x-filament::icon
                        :icon="$descriptionIcon"
                        :class="$descriptionIconClasses"
                        :style="$descriptionIconStyles"
                    />
                @endif
            </div>
        @endif
    </div>

    @if ($chart = $getChart())
        {{-- An empty function to initialize the Alpine component with until it's loaded with `x-load`. This removes the need for `x-ignore`, allowing the chart to be updated via Livewire polling. --}}
        <div x-data="{ statsOverviewStatChart: function () {} }">
            <div
                @if (FilamentView::hasSpaMode())
                    x-load="visible"
                @else
                    x-load
                @endif
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('stats-overview/stat/chart', 'filament/widgets') }}"
                x-data="statsOverviewStatChart({
                            dataChecksum: @js($dataChecksum),
                            labels: @js(array_keys($chart)),
                            values: @js(array_values($chart)),
                        })"
                @class([
                    'fi-wi-stats-overview-stat-chart absolute inset-x-0 bottom-0 overflow-hidden rounded-b-xl',
                    match ($chartColor) {
                        'gray' => null,
                        default => 'fi-color-custom',
                    },
                    is_string($chartColor) ? "fi-color-{$chartColor}" : null,
                ])
                @style([
                    \Filament\Support\get_color_css_variables(
                        $chartColor,
                        shades: [50, 400, 500],
                        alias: 'widgets::stats-overview-widget.stat.chart',
                    ) => $chartColor !== 'gray',
                ])
            >
                <canvas x-ref="canvas" class="h-6"></canvas>

                <span
                    x-ref="backgroundColorElement"
                    @class([
                        match ($chartColor) {
                            'gray' => 'text-gray-100 dark:text-gray-800',
                            default => 'text-custom-50 dark:text-custom-400/10',
                        },
                    ])
                ></span>

                <span
                    x-ref="borderColorElement"
                    @class([
                        match ($chartColor) {
                            'gray' => 'text-gray-400',
                            default => 'text-custom-500 dark:text-custom-400',
                        },
                    ])
                ></span>
            </div>
        </div>
    @endif
</{!! $tag !!}>
