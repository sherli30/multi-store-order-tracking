{{--
    This view is no longer rendered directly.
    ReportController@consolidated now redirects to reports.index#konsolidasi.
    Kept as a fallback only.
--}}
@php
    header('Location: ' . route('reports.index') . '#konsolidasi');
    exit;
@endphp
