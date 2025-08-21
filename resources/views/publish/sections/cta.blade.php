@php($p = $props)
<section style="padding: 56px 0; text-align:center;">
  <p style="font-size:18px;">{{ $p['text'] ?? '' }}</p>
  @php($btn = $p['button'] ?? ['label'=>'','href'=>'#'])
  <a class="btn" href="{{ $btn['href'] }}" style="margin-top:12px;">{{ $btn['label'] }}</a>
</section>

