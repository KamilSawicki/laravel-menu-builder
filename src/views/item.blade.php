<li>
    <a href="{!! $url !!}" @isset($class)@class($class)@endisset {!! $attr ?? '' !!}>{{ $label }}</a>
    @if($hasChild)
        {!! $childInnerHtml !!}
    @endif
</li>
