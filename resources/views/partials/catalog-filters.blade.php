{{--
    Reusable filter panel body for the catalog (brand + category) pages.
    Expected data:
      $primaryGroup = ['title' => string, 'options' => [ ['label','count','url','active'(bool)], ... ]]
      $specFilters  = collection from SiteController::productSpecFilters()
      $clearUrl     = string (route back to the unfiltered page)
      $hasActive    = bool
--}}
@php
    $checkIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
    $caretIcon = '<svg class="cui-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';
@endphp

<div class="cui-panel">
    <div class="cui-panel-head">
        <h2>Filtreler</h2>
        @if($hasActive)
            <a class="cui-clear" href="{{ $clearUrl }}">Filtreleri temizle</a>
        @endif
    </div>

    <details class="cui-fgroup" open>
        <summary>
            <span>{{ $primaryGroup['title'] }}</span>
            {!! $caretIcon !!}
        </summary>
        <div class="cui-fopts">
            @foreach($primaryGroup['options'] as $opt)
                <a class="cui-opt" href="{{ $opt['url'] }}" @if($opt['active']) aria-current="true" @endif>
                    <span class="cui-box">{!! $checkIcon !!}</span>
                    <span class="cui-opt-label">{{ $opt['label'] }}</span>
                </a>
            @endforeach
        </div>
    </details>

    @foreach(($specFilters ?? collect()) as $filter)
        <details class="cui-fgroup" @if($filter['active']) open @endif>
            <summary>
                <span>{{ $filter['label'] }}</span>
                {!! $caretIcon !!}
            </summary>
            <div class="cui-fopts">
                @if($filter['active'])
                    <a class="cui-opt" href="{{ $filter['clear_url'] }}">
                        <span class="cui-box">{!! $checkIcon !!}</span>
                        <span class="cui-opt-label">Tümü</span>
                    </a>
                @endif
                @foreach($filter['options'] as $option)
                    <a class="cui-opt" href="{{ $option['url'] }}" @if($filter['active'] === $option['slug']) aria-current="true" @endif>
                        <span class="cui-box">{!! $checkIcon !!}</span>
                        <span class="cui-opt-label">{{ $option['value'] }}</span>
                    </a>
                @endforeach
            </div>
        </details>
    @endforeach
</div>
