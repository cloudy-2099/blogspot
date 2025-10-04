@php
    $title = "Profil " . $user->username;
        $active = $active ?? '';

@endphp
@extends('layout.main')

@section('container')

<div class="container">
    <div class="main-body">
<div class="row gutters-sm">
  <div class="col-md-5 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-column align-items-center text-center">
          <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Admin" class="rounded-circle" width="150">
          <div class="mt-3">
            <p>
              Followers: {{ $user->followers()->count() }} |
              Following: {{ $user->followings()->count() }}
            </p>

            {{-- Tombol follow/unfollow --}}
            @auth
              @if(auth()->user()->id !== $user->id)
                <form method="POST" action="{{ route('follow.toggle', $user->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-primary">
                    {{ auth()->user()->isFollowing($user->id) ? 'Unfollow' : 'Follow' }}
                  </button>
                </form>
              @endif
            @endauth
          </div>
        </div>

        <hr>

        <div class="row">
          <div class="col-sm-3">
            <h6 class="mb-0">Full Name</h6>
          </div>
          <div class="col-sm-9 text-secondary">
            {{ $user->username }}
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-sm-3">
            <h6 class="mb-0">Email</h6>
          </div>
          <div class="col-sm-9 text-secondary">
            {{ $user->email }}
          </div>
        </div>
        <hr>

        <div class="row">
        <div class="col-sm-3">
            <h6 class="mb-0">Email Verified</h6>
        </div>
        <div class="col-sm-9 text-secondary">
            {{ $user->email_verified_at ? 'Sudah terverifikasi' : 'Belum terverifikasi' }}
        </div>
        </div>

        <hr>
        <div class="row">
          <div class="col-sm-12">
            <a class="btn btn-info" target="_blank" href="https://www.bootdey.com/snippets/view/profile-edit-data-and-skills">Edit</a>
          </div>
        </div>
      </div>
    </div>
  </div>


<div class="col-md-6">
             
 {{-- Tabs --}}
    <ul class="nav nav-tabs" id="profileTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="posts-tab" data-bs-toggle="tab" href="#posts" role="tab">Postingan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="comments-tab" data-bs-toggle="tab" href="#comments" role="tab">Komentar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="likes-tab" data-bs-toggle="tab" href="#likes" role="tab">Likes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="likes-tab" data-bs-toggle="tab" href="#followers" role="tab">Followers</a>
        </li>
    </ul>

    <div class="tab-content mt-3" id="profileTabContent">
{{-- Postingan --}}
<div class="tab-pane fade show active" id="posts" role="tabpanel">
    @livewire('posts', ['userId' => $user->id], key('posts-'.$user->id))
</div>

{{-- Komentar --}}
<div class="tab-pane fade" id="comments" role="tabpanel">
    @livewire('comments', ['userId' => $user->id], key('comments-'.$user->id))
</div>

{{-- Likes --}}
<div class="tab-pane fade" id="likes" role="tabpanel">
    @livewire('likes', ['userId' => $user->id], key('likes-'.$user->id))
</div>

{{-- Followers --}}
<div class="tab-pane fade" id="followers" role="tabpanel">
    @livewire('followers', ['userId' => $user->id], key('followers-'.$user->id))
</div>



    </div>
        </div>
    </div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Cek hash di URL, kalau ada tab yang sesuai, aktifkan
    var hash = window.location.hash;
    if (hash) {
        var tab = document.querySelector('a[href="' + hash + '"]');
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }

    // Update hash ketika tab diklik
    var tabs = document.querySelectorAll('a[data-bs-toggle="tab"]');
    tabs.forEach(function (tab) {
        tab.addEventListener("shown.bs.tab", function (e) {
            history.replaceState(null, null, e.target.getAttribute("href"));
        });
    });
});
</script>
<script>
document.addEventListener("livewire:load", () => {
    Livewire.hook('message.processed', (message, component) => {
        let activeTab = document.querySelector('.tab-pane.active.show');
        if (activeTab) {
            let pagination = activeTab.querySelector('.pagination-wrapper');

            if (pagination) {
                let itemCount = parseInt(pagination.dataset.count, 10);
                let perPage   = 5; // samakan dengan paginate(5) di Livewire

                // Ambil elemen ul.pagination untuk cek jumlah halaman
                let totalPages = pagination.querySelectorAll("ul.pagination li.page-item").length;

                // Scroll hanya jika:
                // 1. item penuh (= perPage), atau
                // 2. jumlah halaman > 1
                if (itemCount === perPage || totalPages > 3) { 
                    // ("> 3" karena ul.pagination selalu ada prev & next)
                    pagination.scrollIntoView({ behavior: "smooth", block: "nearest" });
                }
            }
        }
    });
});
</script>





@endsection