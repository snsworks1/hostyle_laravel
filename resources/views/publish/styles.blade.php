:root{
  --brand: {{ $tokens['brandColor'] ?? '#111111' }};
  --radius: {{ $tokens['radius'] ?? '12px' }};
}
.btn{display:inline-block;border:1px solid var(--brand);padding:.6rem 1rem;border-radius:var(--radius)}
.container{max-width:var(--content-w);margin:0 auto}

