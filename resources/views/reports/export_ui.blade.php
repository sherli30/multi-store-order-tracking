{{--
    This view is no longer rendered directly.
    ReportController@export (no type) now redirects to reports.index#ekspor.
    Kept as a fallback only.
--}}
@php
    header('Location: ' . route('reports.index') . '#ekspor');
    exit;
@endphp
