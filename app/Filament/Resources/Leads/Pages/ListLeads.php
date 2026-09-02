<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('CSV indir')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(fn () => $this->exportCsv()),
        ];
    }

    protected function exportCsv(): StreamedResponse
    {
        $filename = 'lead-talepleri-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            // Excel'in UTF-8 tanıması için BOM
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Tarih', 'Ad Soyad', 'Firma', 'Telefon', 'E-posta', 'Kaynak', 'Kaynak Adı', 'Durum', 'Mesaj', 'Kaynak URL']);

            Lead::query()->orderByDesc('created_at')->chunk(200, function ($leads) use ($out) {
                foreach ($leads as $lead) {
                    fputcsv($out, [
                        optional($lead->created_at)->format('d.m.Y H:i'),
                        $lead->name,
                        $lead->company,
                        $lead->phone,
                        $lead->email,
                        $lead->source_type,
                        $lead->source_name ?? ($lead->payload['source_name'] ?? ''),
                        $lead->status,
                        preg_replace('/\s+/', ' ', (string) $lead->message),
                        $lead->source_url,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
