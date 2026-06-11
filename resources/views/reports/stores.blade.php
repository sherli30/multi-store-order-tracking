{{--
    This view is no longer rendered directly.
    ReportController@stores now redirects to reports.index#per-toko.
    Kept as a fallback only.
--}}
@php
    header('Location: ' . route('reports.index') . '#per-toko');
    exit;
@endphp
