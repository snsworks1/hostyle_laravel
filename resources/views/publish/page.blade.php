@php($tokens = $tokens ?? [])
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Published</title>
<style>@include('publish.styles')</style>
</head>
<body style="font-family: {{ $tokens['fontBase'] ?? 'Noto Sans KR, sans-serif' }}">
  <div style="--content-w: {{ $tokens['contentWidth'] ?? '1080px' }}">
  @foreach(($schema['sections'] ?? []) as $section)
    @include('publish.sections.' . $section['type'], ['props' => $section['props'] ?? []])
  @endforeach
  </div>
</body>
</html>

