
@extends('layout.main')

@section('container')
<h1 class="mb-3 text-center">{{ $title }}</h1>

<div class="row justify-content-center mb-3">
  <div class="col-md-6">
    <form action="/posts">
      @if (request('category'))
      <input type="hidden" name="category" value="{{ request('category') }}" id="">
      @endif
      @if (request('author'))
      <input type="hidden" name="author" value="{{ request('author') }}" id="">
      @endif
      <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="search.." name="search" id="search" value="{{ request('search') }}">
        <button class="btn btn-primary" type="submit">Search</button>
      </div>
    </form>
  </div>
</div>

@if($posts->count())
<div class="card mb-3">
            @if($posts[0]->image)
              <div style="max-height: 400px; overflow:hidden;">
                 <img src="{{ asset('storage/'. $posts[0]->image) }}" class="img-fluid" alt="">
              </div>
            @else
                  <img src="https://picsum.photos/1200/400?{{ $posts[0]->category->name }}" class="card-img-top" alt="{{ $posts[0]->category->name }}">
            @endif
    <div class="card-body text-center">
      <h3 class="card-title">  <a href="/post/{{ $posts[0]->slug }}" class="text-decoration-none text-dark">{{ $posts[0]->title }}</a></h3>
      <p> <small class="text-muted">
        By: <a href="/posts?author={{ $posts[0]->author->username }}" class="text-decoration-none">{{ $posts[0]->author->name }}</a> in <a href="/posts?category={{ $posts[0]->category->slug }}" class="text-decoration-none">{{ $posts[0]->category->name }}</a> {{ $posts[0]->created_at->diffForHumans() }}</small>
      </p>

      <p class="card-text">{{ $posts[0]->excerpt }}</p>

      <a href="/post/{{ $posts[0]->slug }}" class="text-decoration-none btn btn-primary">Read More</a>

    </div>
  </div>

<style>
    .bg-primary-transparent {
        background-color: rgba(13, 99, 237, 0.938); /* sama dengan #3b71ca99 */
    }
</style>
<div class="container">
    <div class="row">
        @foreach ($posts->skip(1) as $post)
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="position-absolute bg-primary-transparent px-2 py-2 "><a href="/posts?category={{ $post->category->slug }}" class="text-white text-decoration-none">{{ $post->category->name }}</a></div>
            @if($post->image)
                 <img src="{{ asset('storage/'. $post->image) }}" class="img-fluid" alt="">
            @else
                <img src="https://picsum.photos/500/400?{{ $post->category->name }}" class="card-img-top" alt="{{ $post->category->name }}">
            @endif

                <div class="card-body">
                  <h5 class="card-title">  <a href="/posts/{{ $post->slug }}" class="text-decoration-none">{{ $post->title }}</a></h5>
                  <p> <small class="text-muted">
                    By: <a href="/posts?author={{ $post->author->username }}" class="text-decoration-none">{{ $post->author->name }}</a> {{ $post->created_at->diffForHumans() }}</small>
                  </p>
                  <p class="card-text">{{ $post->excerpt }}</p>
                  <a href="/post/{{ $post->slug }}" class="btn btn-primary">Go somewhere</a>
                </div>
              </div>
        </div>
        @endforeach
    </div>
</div>

@else

<p class="text-center fs-4">Not found</p>
@endif

<div class="d-flex justify-content-center">
  {{ $posts->links() }}
</div>

@endsection

