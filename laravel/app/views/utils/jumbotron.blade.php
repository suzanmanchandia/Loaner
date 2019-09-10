<div class="jumbotron {{ $class }}">
    <div class="container">
        <h1>{{ $title }}</h1>
        @if (isset($subtitle))
        <p>{{ $subtitle }}</p>
        @endif
    </div>
</div>