@extends('dashboard.layouts.main')

@section('container')
<div class="container ">
    <div class="row my-5">
        <div class="col-lg-8">
            <h2 class="mb-3"> {{ $post->title }}</h2>

            <a href="/dashboard/posts" class="btn btn-success"><span data-feather="arrow-left"></span> Back To My Post</a>
            <a href="/dashboard/posts/{{ $post->slug }}/edit" class="btn btn-warning"><span data-feather="edit"></span> Edit</a>
            <form action="/dashboard/posts/{{ $post->slug }}" method="post" class="d-inline">
                @method('delete')
                @csrf
                <button class="btn btn-danger" onclick="return confirm('are you sure?')"><span data-feather="x-circle"></span> Delete</button>
            </form>
            @if($post->image)
            <div style="max-height: 350px; overflow:hidden;">
            <img src="{{ asset('storage/'. $post->image) }}" class="img-fluid mt-3" alt="">
            </div>
            @else
            <img src="https://picsum.photos/1200/400?{{ $post->category->name }}" class="img-fluid mt-3" alt="">
            @endif
                <article class="mt-3">
                    {!! $post->body !!}
                </article>
    
        </div>
    </div>
</div>
@endsection