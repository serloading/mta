@php
    $leadContext = $leadContext ?? null;
    $formAction = $formAction ?? route('leads.store');
    $formTitle = $formTitle ?? 'Teklif / Bilgi Talebi';
    $formNote = $formNote ?? 'Formu iletin, teknik ekibimiz 24 saat içinde dönüş yapsın.';
    $inputCls = 'mt-1 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20';
    $labelCls = 'block text-sm font-semibold text-slate-700';
    $errCls = 'mt-1 block text-xs font-medium text-rose-600';
@endphp

<form class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6" method="POST" action="{{ $formAction }}">
    @csrf
    <div class="mb-4">
        <h2 class="text-base font-bold text-slate-900">{{ $formTitle }}</h2>
        <p class="mt-1 text-xs text-slate-500">{{ $formNote }}</p>
    </div>

    @if(session('lead_success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('lead_success') }}
        </div>
    @endif

    <input type="hidden" name="source_url" value="{{ url()->full() }}">
    @if($leadContext)
        <input type="hidden" name="source_type" value="{{ $leadContext['type'] }}">
        <input type="hidden" name="source_name" value="{{ $leadContext['name'] }}">
        @if($leadContext['slug'])
            <input type="hidden" name="{{ $leadContext['type'] }}" value="{{ $leadContext['slug'] }}">
        @endif
    @endif
    @foreach(request()->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
        <label for="website">Website</label>
        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="{{ $labelCls }}">
            Ad Soyad <span class="text-rose-500">*</span>
            <input type="text" name="name" value="{{ old('name') }}" required class="{{ $inputCls }}">
            @error('name')<span class="{{ $errCls }}">{{ $message }}</span>@enderror
        </label>
        <label class="{{ $labelCls }}">
            Firma
            <input type="text" name="company" value="{{ old('company') }}" class="{{ $inputCls }}">
            @error('company')<span class="{{ $errCls }}">{{ $message }}</span>@enderror
        </label>
        <label class="{{ $labelCls }}">
            Telefon <span class="text-rose-500">*</span>
            <input type="tel" name="phone" value="{{ old('phone') }}" required class="{{ $inputCls }}">
            @error('phone')<span class="{{ $errCls }}">{{ $message }}</span>@enderror
        </label>
        <label class="{{ $labelCls }}">
            E-posta
            <input type="email" name="email" value="{{ old('email') }}" class="{{ $inputCls }}">
            @error('email')<span class="{{ $errCls }}">{{ $message }}</span>@enderror
        </label>
    </div>

    <label class="{{ $labelCls }} mt-4">
        Mesaj <span class="text-rose-500">*</span>
        <textarea name="message" rows="5" required placeholder="İhtiyacınızı, cihaz/model ve adet bilgisini yazın." class="{{ $inputCls }}">{{ old('message') }}</textarea>
        @error('message')<span class="{{ $errCls }}">{{ $message }}</span>@enderror
    </label>

    <button type="submit"
            class="mt-5 inline-flex h-12 w-full items-center justify-center rounded-lg bg-amber-600 px-6 text-sm font-bold text-white shadow-lg transition hover:bg-amber-500">
        Talebi Gönder
    </button>
    <p class="mt-3 text-center text-xs text-slate-400">Bilgileriniz yalnızca talebinizin değerlendirilmesi için kullanılır.</p>
</form>
