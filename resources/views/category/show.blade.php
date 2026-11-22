@extends ('web')

@section ('content')
    <x-category.entries :category="$page" />
@endsection
