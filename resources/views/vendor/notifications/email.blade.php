@php
    $actionColor = '#7b4fff';
@endphp
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #f5f6fa; padding: 40px 0;">
  <tr>
    <td align="center">
      <table width="480" cellpadding="0" cellspacing="0" border="0" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px #eee; padding: 32px;">
        <tr>
          <td align="center" style="padding-bottom: 24px;">
            <img src="https://hostyle.online/images/logo.png" width="120" style="height:auto; display:block; margin:0 auto; border-radius:12px;">
            <h1 style="font-size: 24px; color: #5a3ec8; margin: 16px 0 0 0; font-family: Arial, sans-serif;">HOSTYLE</h1>
            <p style="color: #888; font-size: 14px; margin: 0; font-family: Arial, sans-serif;">쉽고 빠른 홈페이지 생성 플랫폼</p>
          </td>
        </tr>
        <tr>
          <td style="color: #222; font-size: 16px; padding-bottom: 16px; font-family: Arial, sans-serif;">
            안녕하세요!<br>
            @if(isset($introLines))
                @foreach ($introLines as $line)
                    {{ $line }}<br>
                @endforeach
            @endif
          </td>
        </tr>
        @if(isset($actionText))
        <tr>
          <td align="center" style="padding-bottom: 24px;">
            <a href="{{ $actionUrl }}" style="background: {{ $actionColor }}; color: #fff; text-decoration: none; padding: 12px 32px; border-radius: 8px; font-weight: bold; display: inline-block; font-family: Arial, sans-serif;">{{ $actionText }}</a>
          </td>
        </tr>
        @endif
        <tr>
          <td style="color: #888; font-size: 13px; font-family: Arial, sans-serif;">
            @if(isset($outroLines))
                @foreach ($outroLines as $line)
                    {{ $line }}<br>
                @endforeach
            @endif
            <br>
            감사합니다.<br>
            <b>HOSTYLE 팀</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
