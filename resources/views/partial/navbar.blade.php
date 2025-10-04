<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
    <a href="/" style="background-color: transparent">
      <img src="{{ asset('images/logo2.png') }}" alt="Logo" height="40" class="px-3" >
    </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link {{ $active === "home" ? 'active' : '' }}" aria-current="page" href="/">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $active === "about" ? 'active' : '' }}"  href="/about">About</a>
          </li>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $active === "post" ? 'active' : '' }}" href="/posts">Post</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $active === "categories" ? 'active' : '' }}" href="/categories">Categories</a>
          </li>
@auth
<li class="nav-item dropdown">
    <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown">
        🔔
        <span class="badge bg-danger" id="notifCount">
            {{ auth()->user()->unreadNotifications->count() }}
        </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="notifDropdown" id="notifList">
        @forelse(auth()->user()->unreadNotifications as $notif)
            <li>
<a href="{{ $notif->data['post']['url'] ?? '#' }}"
   class="dropdown-item notif-item ellipsis"
   title="{{ $notif->data['user']['username'] }} {{ $notif->data['type'] }}"
   data-id="{{ $notif->id }}">

    @if($notif->data['type'] === 'like')
        👍 {{ $notif->data['user']['username'] }} menyukai postingan 
        <strong>{{ $notif->data['post']['title'] ?? '' }}</strong>

    @elseif($notif->data['type'] === 'comment')
        💬 {{ $notif->data['user']['username'] }} mengomentari postingan 
        <strong>{{ $notif->data['post']['title'] ?? '' }}</strong>

    @elseif($notif->data['type'] === 'reply')
        ↩️ {{ $notif->data['user']['username'] }} membalas komentar di 
        <strong>{{ $notif->data['post']['title'] ?? '' }}</strong>

@elseif($notif->data['type'] === 'follow')
    👤 {{ $notif->data['user']['username'] }} mulai mengikuti kamu
@endif

</a>

            </li>
        @empty
            <li><span class="dropdown-item text-muted">Tidak ada notifikasi</span></li>
        @endforelse
    </ul>
</li>
@endauth



        </ul>
        <ul class="navbar-nav ms-auto">
        @auth         
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Welcome back, {{ auth()->user()->name }}
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="/dashboard"><i class="bi bi-book-half"> </i>My dahsboard</a></li>
            <li><hr class="dropdown-divider"></li>
<a class="dropdown-item" href="{{ route('profile.show', auth()->user()->username) }}">
    <i class="bi bi-person-circle"></i> Profil Saya
</a>

            <li><hr class="dropdown-divider"></li>
            <li>            
                <form action="/logout" method="post">
                  @csrf
                  <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-left"> </i>Logout</a></button>
                </form>
            </li>
          </ul>
        </li>
            @else
              <li class="nav-item mt-1 mx-2">
                <a href="/login" class="nav-link {{ $active === "login" ? 'active' : '' }}"><i class="bi bi-box-arrow-right"> </i>
                    Login</a>
              </li>

        @endauth
              <li>
              <form class="d-flex" action="/posts">
                @if (request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}" id="">
                @endif
                @if (request('author'))
                <input type="hidden" name="author" value="{{ request('author') }}" id="">
                @endif
              <input id="live-search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search" id="search" value="{{ request('search') }}"">
                <button class="btn btn-success" type="submit">Search</button>
              </form>
              <div id="search-results" class="list-group position-absolute mt-1" style="z-index:1000; width:300px;"></div>
            </li>


      <ul>         
         <button id="theme-toggle" class="btn btn-success mt-1">
            <i class="fa fa-moon-o"></i>
          </button>
        </ul>



      </div>
    </div>
  </nav>


  <script>
    document.getElementById('live-search').addEventListener('keyup', function() {
    let query = this.value;
    let resultsDiv = document.getElementById('search-results');

    if (query.length > 1) {
        fetch(`/search?q=${query}`)
            .then(res => res.json())
            .then(data => {
                resultsDiv.innerHTML = "";

                if (data.length > 0) {
                    data.forEach(post => {
                        resultsDiv.innerHTML += `
                            <a href="/posts/${post.slug}" class="list-group-item list-group-item-action">
                                <strong>${post.title}</strong><br>
                                <small>${post.category.name} - by ${post.author.name}</small>
                            </a>
                        `;
                    });
                } else {
                    resultsDiv.innerHTML = `<p class="text-muted p-2">No results found</p>`;
                }
            });
    } else {
        resultsDiv.innerHTML = "";
    }
});
</script>

