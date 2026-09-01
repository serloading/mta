@extends('layouts.site')

@section('content')
    @include('partials.service-detail-body', [
        'kind' => 'technical',
        'title' => $technicalServiceSeo['h1'] ?? $technicalService['title'],
        'leadText' => $technicalServiceSeo['hero_text'] ?? trim(($technicalService['summary'] ?? '') . ' ' . ($technicalService['answer'] ?? '')),
        'breadcrumb' => [
            ['label' => 'Ana Sayfa', 'url' => route('home')],
            ['label' => 'Teknik Servis', 'url' => route('technical-services.index')],
            ['label' => $technicalService['title']],
        ],
        'scopeGroups' => [],
        'standards' => [],
        'servicedDevices' => $technicalService['devices'] ?? $technicalServiceSeo['device_list'] ?? [],
        'relatedProducts' => $products,
        'faqs' => $technicalServiceSeo['faq'] ?? [],
        'cta' => $technicalServiceSeo['cta'] ?? ['title' => $technicalService['title'] . ' İçin Servis Talebi Oluşturun'],
        'quoteCta' => $quoteCta,
        'formAction' => route('leads.store'),
        'sourceType' => 'technical_service',
        'sourceSlug' => $technicalService['slug'],
    ])
@endsection
