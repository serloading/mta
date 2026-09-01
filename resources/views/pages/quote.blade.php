@extends('layouts.site')

@section('content')
<section class="section quote-form-section">
    <div class="container narrow">
        <h1>{{ $pageSeo['h1'] }}</h1>
        @include('partials.lead-form', ['leadContext' => $leadContext, 'formAction' => $formAction])
    </div>
</section>
@endsection
