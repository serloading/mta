<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Son düzenlenen içerikler</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-gray-500">
                        <th class="py-2 pr-4 font-medium">Modül</th>
                        <th class="py-2 pr-4 font-medium">İçerik</th>
                        <th class="py-2 pr-4 font-medium">Durum</th>
                        <th class="py-2 font-medium">Son düzenleme</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->latestItems() as $item)
                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 text-gray-600">{{ $item['type'] }}</td>
                            <td class="py-3 pr-4 font-medium text-gray-950">{{ $item['title'] }}</td>
                            <td class="py-3 pr-4 text-gray-600">{{ $item['status'] }}</td>
                            <td class="py-3 text-gray-600">{{ $item['updated_at']?->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 text-gray-500" colspan="4">Henüz düzenlenen içerik yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
