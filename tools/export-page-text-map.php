<?php

declare(strict_types=1);

date_default_timezone_set('Europe/Istanbul');

$baseUrl = 'http://127.0.0.1:8000';
$outputPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'SAYFA_METIN_HARITASI.md';

function fetch_url(string $url): string
{
    $context = stream_context_create([
        'http' => [
            'timeout' => 20,
            'ignore_errors' => true,
            'header' => "User-Agent: MTA-Text-Map-Exporter\r\n",
        ],
    ]);

    $html = @file_get_contents($url, false, $context);

    if ($html === false) {
        throw new RuntimeException("URL okunamadı: {$url}");
    }

    return $html;
}

function normalize_text(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
    return trim($text);
}

function load_dom(string $html): DOMXPath
{
    $dom = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
    libxml_clear_errors();

    return new DOMXPath($dom);
}

function node_text(DOMNode $node): string
{
    return normalize_text($node->textContent ?? '');
}

function element_class_contains(DOMElement $element, string $needle): bool
{
    $class = ' ' . $element->getAttribute('class') . ' ';
    return str_contains($class, ' ' . $needle . ' ');
}

function should_skip_element(DOMElement $element): bool
{
    $tag = strtolower($element->tagName);
    if (in_array($tag, ['script', 'style', 'svg', 'noscript'], true)) {
        return true;
    }

    if ($element->hasAttribute('aria-hidden') && $element->getAttribute('aria-hidden') === 'true') {
        return true;
    }

    return element_class_contains($element, 'sr-only')
        || element_class_contains($element, 'skip-link');
}

function table_markdown(DOMXPath $xpath, DOMElement $table): array
{
    $rows = [];
    foreach ($xpath->query('.//tr', $table) as $tr) {
        $cells = [];
        foreach ($xpath->query('./th|./td', $tr) as $cell) {
            $cells[] = node_text($cell);
        }
        if ($cells !== []) {
            $rows[] = '| ' . implode(' | ', $cells) . ' |';
        }
    }

    if (count($rows) > 1) {
        $columnCount = substr_count($rows[0], '|') - 1;
        array_splice($rows, 1, 0, ['| ' . implode(' | ', array_fill(0, max(1, $columnCount), '---')) . ' |']);
    }

    return $rows;
}

function extract_meta(DOMXPath $xpath): array
{
    $titleNode = $xpath->query('//title')->item(0);
    $descriptionNode = $xpath->query('//meta[@name="description"]')->item(0);

    return [
        'title' => $titleNode ? node_text($titleNode) : '',
        'description' => $descriptionNode instanceof DOMElement ? normalize_text($descriptionNode->getAttribute('content')) : '',
    ];
}

function extract_common_content(DOMXPath $xpath, string $selector): array
{
    $root = $xpath->query($selector)->item(0);
    if (! $root instanceof DOMElement) {
        return [];
    }

    $lines = [];
    foreach ($xpath->query('.//h1|.//h2|.//h3|.//a|.//strong|.//small|.//p', $root) as $node) {
        if (! $node instanceof DOMElement || should_skip_element($node)) {
            continue;
        }

        $text = node_text($node);
        if ($text === '') {
            continue;
        }

        $tag = strtoupper($node->tagName);
        if ($node->tagName === 'a') {
            $lines[] = "- Link: {$text}";
        } elseif (in_array($node->tagName, ['h1', 'h2', 'h3'], true)) {
            $lines[] = "- {$tag}: {$text}";
        } else {
            $lines[] = "- Metin: {$text}";
        }
    }

    return array_values(array_unique($lines));
}

function extract_main_content(DOMXPath $xpath): array
{
    $main = $xpath->query('//main')->item(0);
    if (! $main instanceof DOMElement) {
        return ['Ana içerik bulunamadı.'];
    }

    $lines = [];
    $lastHeadingLevel = 0;
    $seenNear = [];

    $walk = function (DOMNode $node) use (&$walk, &$lines, &$lastHeadingLevel, &$seenNear, $xpath): void {
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMText) {
                continue;
            }

            if (! $child instanceof DOMElement || should_skip_element($child)) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (preg_match('/^h([1-6])$/', $tag, $matches)) {
                $level = (int) $matches[1];
                $text = node_text($child);
                if ($text !== '') {
                    $lines[] = str_repeat('  ', max(0, $level - 1)) . "- H{$level}: {$text}";
                    $lastHeadingLevel = $level;
                    $seenNear = [];
                }
                continue;
            }

            if ($tag === 'table') {
                foreach (table_markdown($xpath, $child) as $row) {
                    $lines[] = str_repeat('  ', max(0, $lastHeadingLevel)) . $row;
                }
                continue;
            }

            if (in_array($tag, ['p', 'li', 'label', 'button', 'summary', 'figcaption'], true)) {
                $text = node_text($child);
                if ($text !== '') {
                    $prefix = match ($tag) {
                        'li' => 'Liste',
                        'label' => 'Form alanı',
                        'button' => 'Buton',
                        'summary' => 'Açılır başlık',
                        default => 'Metin',
                    };
                    $line = str_repeat('  ', max(0, $lastHeadingLevel)) . "- {$prefix}: {$text}";
                    if (! isset($seenNear[$line])) {
                        $lines[] = $line;
                        $seenNear[$line] = true;
                    }
                }
                continue;
            }

            if ($tag === 'a') {
                $text = node_text($child);
                if ($text !== '' && mb_strlen($text) <= 90) {
                    $line = str_repeat('  ', max(0, $lastHeadingLevel)) . "- Link/CTA: {$text}";
                    if (! isset($seenNear[$line])) {
                        $lines[] = $line;
                        $seenNear[$line] = true;
                    }
                }
                continue;
            }

            if ($tag === 'img') {
                $alt = normalize_text($child->getAttribute('alt'));
                if ($alt !== '') {
                    $line = str_repeat('  ', max(0, $lastHeadingLevel)) . "- Görsel alt: {$alt}";
                    if (! isset($seenNear[$line])) {
                        $lines[] = $line;
                        $seenNear[$line] = true;
                    }
                }
                continue;
            }

            $walk($child);
        }
    };

    $walk($main);

    return $lines === [] ? ['Ana içerikte metin yakalanamadı.'] : $lines;
}

function classify_url(string $path): string
{
    if ($path === '/') {
        return '01 Ana sayfa';
    }
    if ($path === '/hizmetler' || str_starts_with($path, '/hizmetler/')) {
        return '02 Kalibrasyon hizmetleri';
    }
    if ($path === '/teknik-servis' || str_starts_with($path, '/teknik-servis/')) {
        return '03 Teknik servis';
    }
    if ($path === '/urunler') {
        return '04 Ürün katalog ana sayfası';
    }
    if ($path === '/markalar' || str_starts_with($path, '/urunler/marka/')) {
        return '05 Marka sayfaları';
    }
    if (preg_match('#^/urunler/[^/]+/[^/]+$#', $path)) {
        return '07 Ürün detay sayfaları';
    }
    if (str_starts_with($path, '/urunler/')) {
        return '06 Ürün kategori sayfaları';
    }
    if ($path === '/blog') {
        return '08 Blog';
    }
    if ($path === '/bilgi-merkezi' || str_starts_with($path, '/bilgi-merkezi/kategori/')) {
        return '09 Bilgi merkezi liste/kategori';
    }
    if (str_starts_with($path, '/bilgi-merkezi/')) {
        return '10 Bilgi merkezi yazı detayları';
    }
    return '11 Kurumsal ve iletişim';
}

$sitemap = fetch_url($baseUrl . '/sitemap.xml');
preg_match_all('#<loc>(.*?)</loc>#', $sitemap, $matches);
$urls = array_values(array_unique(array_map('trim', $matches[1] ?? [])));

usort($urls, static function (string $a, string $b) use ($baseUrl): int {
    $pathA = parse_url($a, PHP_URL_PATH) ?: '/';
    $pathB = parse_url($b, PHP_URL_PATH) ?: '/';
    return [classify_url($pathA), $pathA] <=> [classify_url($pathB), $pathB];
});

$homeHtml = fetch_url($baseUrl . '/');
$homeXpath = load_dom($homeHtml);

$markdown = [];
$markdown[] = '# MTA Endüstri Sayfa Metin Haritası';
$markdown[] = '';
$markdown[] = 'Bu dosya canlı yerel sitedeki sayfaların ana içerik metinlerini sayfa sayfa çıkarır. Başlık hiyerarşisi H1, H2, H3 ve devam eden görünür metin akışına göre yazılmıştır.';
$markdown[] = '';
$markdown[] = '- Oluşturulma tarihi: ' . date('Y-m-d H:i');
$markdown[] = '- Kaynak: ' . $baseUrl;
$markdown[] = '- Kapsam: sitemap.xml içinde yer alan ' . count($urls) . ' URL';
$markdown[] = '- Not: Header ve footer ortak metinleri aşağıda ayrı verildi; her sayfada tekrar edilmedi.';
$markdown[] = '';
$markdown[] = '## Ortak Header Metinleri';
$markdown[] = '';
$markdown = array_merge($markdown, extract_common_content($homeXpath, '//header'));
$markdown[] = '';
$markdown[] = '## Ortak Footer Metinleri';
$markdown[] = '';
$markdown = array_merge($markdown, extract_common_content($homeXpath, '//footer'));
$markdown[] = '';

$currentGroup = '';
foreach ($urls as $url) {
    $path = parse_url($url, PHP_URL_PATH) ?: '/';
    $group = classify_url($path);
    if ($group !== $currentGroup) {
        $markdown[] = '## ' . preg_replace('/^\d+\s/u', '', $group);
        $markdown[] = '';
        $currentGroup = $group;
    }

    $html = fetch_url($url);
    $xpath = load_dom($html);
    $meta = extract_meta($xpath);

    $markdown[] = '### ' . $path;
    $markdown[] = '';
    $markdown[] = '- URL: ' . $url;
    $markdown[] = '- Meta Title: ' . ($meta['title'] !== '' ? $meta['title'] : '(boş)');
    $markdown[] = '- Meta Description: ' . ($meta['description'] !== '' ? $meta['description'] : '(boş)');
    $markdown[] = '';
    $markdown[] = 'İçerik akışı:';
    $markdown[] = '';
    $markdown = array_merge($markdown, extract_main_content($xpath));
    $markdown[] = '';
}

if (! is_dir(dirname($outputPath))) {
    mkdir(dirname($outputPath), 0777, true);
}

file_put_contents($outputPath, implode(PHP_EOL, $markdown) . PHP_EOL);

echo $outputPath . PHP_EOL;
