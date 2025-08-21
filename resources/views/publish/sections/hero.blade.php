@php($p = $props)
<section style="background: {{ $p['bg'] ?? '#fff' }}; padding: {{ ($p['py'] ?? 60) }}px 0; text-align: {{ $p['align'] ?? 'center' }};">
  <div class="container">
    <h1 style="font-size: 32px; font-weight: 700">{{ $p['title'] ?? '' }}</h1>
    @if(!empty($p['subtitle']))
    <p style="color:#6b7280;margin-top:8px;">{{ $p['subtitle'] }}</p>
    @endif
  </div>
</section>

