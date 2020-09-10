@if (count($breadcrumbs))
    @foreach ($breadcrumbs as $breadcrumb)
        <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ ucwords(strtolower($breadcrumb->title)) }}</a></li>
    @endforeach
@endif