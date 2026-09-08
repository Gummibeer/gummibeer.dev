@extends ('web')

@push ('head')
    <x-og.website />
@endpush

@section ('content')
    <x-stream.entries />
@endsection
