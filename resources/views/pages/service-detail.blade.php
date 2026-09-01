@extends('layouts.site')

@section('content')
    @include('partials.service-detail-body', [
        'kind' => 'calibration',
        'title' => $serviceSeo['h1'] ?? $service['title'],
        'leadText' => $serviceSeo['hero_text'] ?? trim(($service['summary'] ?? '') . ' ' . ($service['answer'] ?? '')),
        'breadcrumb' => [
            ['label' => 'Ana Sayfa', 'url' => route('home')],
            ['label' => 'Kalibrasyon', 'url' => route('services.index')],
            ['label' => $service['title']],
        ],
        'scopeGroups' => $service['scope_groups'] ?? [],
        'standards' => $service['standards'] ?? [],
        'servicedDevices' => $service['devices'] ?? [],
        'relatedProducts' => $products,
        'faqs' => $serviceSeo['faq'] ?? ($faqs ?? []),
        'cta' => $serviceSeo['cta'] ?? ['title' => $service['title'] . ' İçin Teklif Alın'],
        'quoteCta' => $quoteCta,
        'formAction' => route('leads.store'),
        'sourceType' => 'service',
        'sourceSlug' => $service['slug'],
    ])
@endsection
