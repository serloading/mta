@php
    $leadContext = $leadContext ?? null;
    $formAction = $formAction ?? route('leads.store');
@endphp

<form class="lead-form" method="POST" action="{{ $formAction }}">
    @csrf
    @if(session('lead_success'))
        <div class="success-message">{{ session('lead_success') }}</div>
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
    <div class="honeypot">
        <label for="website">Website</label>
        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
    </div>
    <label>
        Ad Soyad
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name')<span>{{ $message }}</span>@enderror
    </label>
    <label>
        Firma
        <input type="text" name="company" value="{{ old('company') }}">
        @error('company')<span>{{ $message }}</span>@enderror
    </label>
    <label>
        Telefon
        <input type="tel" name="phone" value="{{ old('phone') }}" required>
        @error('phone')<span>{{ $message }}</span>@enderror
    </label>
    <label>
        E-posta
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email')<span>{{ $message }}</span>@enderror
    </label>
    <label>
        Mesaj
        <textarea name="message" rows="7" required placeholder="Mesajınızı yazın.">{{ old('message') }}</textarea>
        @error('message')<span>{{ $message }}</span>@enderror
    </label>
    <button class="button button-primary" type="submit">Talebi Gönder</button>
</form>
