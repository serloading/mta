@if(($specFilters ?? collect())->isNotEmpty())
    @php($isInlineFilter = ($filterVariant ?? 'stacked') === 'inline')
    <div @class(['catalog-filter-group', 'catalog-filter-group--inline' => $isInlineFilter])>
        <div class="catalog-filter-title">
            <strong>Teknik özellikler</strong>
            <span>{{ $specFilters->count() }} alan</span>
        </div>
        <div class="catalog-spec-filters">
            @foreach($specFilters as $filter)
                <details class="catalog-spec-filter" open>
                    <summary>
                        <span>{{ $filter['label'] }}</span>
                        @if($filter['active'])
                            <a href="{{ $filter['clear_url'] }}">Temizle</a>
                        @endif
                    </summary>
                    <nav @class(['catalog-filter-list', 'compact' => ! $isInlineFilter, 'catalog-filter-list--chips' => $isInlineFilter]) aria-label="{{ $filter['label'] }} filtresi">
                        @foreach($filter['options'] as $option)
                            <a @class(['active' => $filter['active'] === $option['slug']]) href="{{ $option['url'] }}">
                                <span>{{ $option['value'] }}</span>
                                <small>{{ $option['count'] }}</small>
                            </a>
                        @endforeach
                    </nav>
                </details>
            @endforeach
        </div>
    </div>
@endif
