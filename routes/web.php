<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/hizmetler', [SiteController::class, 'services'])->name('services.index');
Route::get('/hizmetler/{slug}', [SiteController::class, 'serviceDetail'])->name('services.show');
Route::get('/teknik-servis', [SiteController::class, 'technicalServices'])->name('technical-services.index');
Route::get('/teknik-servis/{slug}', [SiteController::class, 'technicalServiceDetail'])->name('technical-services.show');
Route::get('/kapsam', [SiteController::class, 'scope'])->name('scope');
Route::get('/urunler', [SiteController::class, 'products'])->name('products.index');
Route::get('/urun/{slug}', [SiteController::class, 'productDetail'])->name('products.show');
Route::get('/markalar', [SiteController::class, 'brands'])->name('brands.index');
Route::get('/urunler/marka/{brand}', [SiteController::class, 'productBrand'])->name('products.brand');
Route::get('/urunler/{category}', [SiteController::class, 'productCategory'])->name('products.category');
Route::get('/blog', [SiteController::class, 'blog'])->name('blog.index');
Route::get('/bilgi-merkezi', [SiteController::class, 'knowledge'])->name('knowledge.index');
Route::get('/bilgi-merkezi/kategori/{category}', [SiteController::class, 'knowledgeCategory'])->name('knowledge.category');
Route::get('/bilgi-merkezi/{slug}', [SiteController::class, 'articleDetail'])->name('knowledge.show');
Route::get('/hakkimizda', [SiteController::class, 'about'])->name('about');
Route::get('/sertifikalar', [SiteController::class, 'certificates'])->name('certificates');
Route::get('/referanslar', [SiteController::class, 'references'])->name('references');
Route::get('/teklif-al', [SiteController::class, 'quote'])->name('quote');
Route::post('/teklif-al', [SiteController::class, 'submitLead'])->middleware('throttle:6,1')->name('quotes.store');
Route::get('/iletisim', [SiteController::class, 'contact'])->name('contact');
Route::post('/iletisim', [SiteController::class, 'submitLead'])->middleware('throttle:6,1')->name('leads.store');
Route::get('/ara', [SiteController::class, 'search'])->name('search');
Route::get('/gizlilik-politikasi', [SiteController::class, 'legal'])->defaults('slug', 'gizlilik-politikasi')->name('legal.privacy');
Route::get('/cerez-politikasi', [SiteController::class, 'legal'])->defaults('slug', 'cerez-politikasi')->name('legal.cookies');
Route::get('/kvkk', [SiteController::class, 'legal'])->defaults('slug', 'kvkk')->name('legal.kvkk');
Route::get('/robots.txt', [SiteController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
Route::get('/{any}', [SiteController::class, 'redirectFallback'])->where('any', '.*')->name('redirects.fallback');
