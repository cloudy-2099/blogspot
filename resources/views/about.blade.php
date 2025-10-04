@extends('layout.main')

@section('container')
    <h1>about
    </h1>
    <h1>{{ $name }}</h1>
    <h1>{{ $email }}</h1>
    <img src="img/{{ $image }}" alt="{{ $email }}" width="200">
@endsection