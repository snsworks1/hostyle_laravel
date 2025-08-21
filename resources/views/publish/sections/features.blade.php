@php($p = $props)
@php($cols = $p['cols'] ?? 3)
<section style="padding: 48px 0;">
  <div class="container" style="display:grid; gap:24px; grid-template-columns: repeat({{ $cols }}, minmax(0,1fr));">
    @foreach(($p['items'] ?? []) as $it)
      <div style="border:1px solid #eee; border-radius: var(--radius); padding:20px; background:#fff;">
        <div style="font-weight:600">{{ $it['title'] ?? '' }}</div>
        @if(!empty($it['text']))
        <div style="color:#6b7280;margin-top:6px;">{{ $it['text'] }}</div>
        @endif
      </div>
    @endforeach
  </div>
</section>

