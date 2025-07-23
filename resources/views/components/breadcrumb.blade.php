<div class="col-md-8">
    <div class="page-header-title">
        <h5 class="m-b-10">{{ $pageTitle ?? 'Dashboard' }}</h5>
    </div>
    <ul class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Home</a>
        </li>
        @foreach($breadcrumbs ?? [] as $breadcrumb)
            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                @if (!$loop->last && !empty($breadcrumb['url']))
                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                @else
                    {{ $breadcrumb['name'] }}
                @endif
            </li>
        @endforeach
    </ul>
</div> 