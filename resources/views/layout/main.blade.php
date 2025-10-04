<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/app.css">
    <title>{{ $title }}</title>
  </head>
  <body>
    @include('partial.navbar')

    <div class="container mt-4">
      @yield('container')
          
      @livewireScripts
    </div>

    @auth
    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 2000">
      <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <i class="bi bi-chat-left-text me-2"></i>
          <strong class="me-auto">notifications</strong>
          <small class="text-muted">baru saja</small>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          <a href="#" id="toastLink" class="text-decoration-none text-dark"></a>
        </div>
      </div>
    </div>

    <!-- Tailwind.css -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Pusher -->
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <!-- Laravel Echo -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>

    <script>
    let userId = {{ auth()->id() }};
    let notifList = document.getElementById("notifList"); // 👈 ini yang hilang

      window.Pusher = Pusher;
      window.Echo = new Echo({
        broadcaster: 'pusher',
        key: "{{ config('broadcasting.connections.pusher.key') }}",
        cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
        forceTLS: true
      });

      let notifAudio = null;
      document.addEventListener("click", () => {
        if (!notifAudio) {
          notifAudio = new Audio("{{ asset('/sound/mambo.mp3') }}");
          console.log("🔊 Audio siap digunakan");
        }
      });

Echo.private(`user.{{ auth()->id() }}`)
    .listen('.CommentReplied', (e) => {
        console.log("Balasan baru:", e);

        if (!e.reply) return;
        // 🚫 skip kalau reply berasal dari user yang sedang login
        if (e.reply.user.id === userId) return;
        // 🔔 mainkan audio + update dropdown notifikasi
        if (notifAudio) {
            notifAudio.currentTime = 0;
            notifAudio.play().catch(err => console.log("Audio blocked:", err));
        }

        let badge = document.getElementById("notifCount");
        let count = parseInt(badge.innerText) || 0;
        badge.innerText = count + 1;

        let notifList = document.getElementById("notifList");
        let noNotif = notifList.querySelector('.dropdown-item.text-muted');
        if (noNotif) {
            noNotif.parentElement.remove();
        }

        let li = document.createElement("li");
        li.innerHTML = `
            <a href="${e.reply.post.url}" class="dropdown-item notif-item">
                ↩️ ${e.reply.user.username} membalas komentar di 
                <strong>${e.reply.post.title}</strong>
            </a>
        `;
        notifList.prepend(li);

        // 🔔 tampilkan toast
        document.getElementById("toastLink").innerText =
            `${e.reply.user.username} membalas komentar di post "${e.reply.post.title}"`;
        document.getElementById("toastLink").setAttribute("href", e.reply.post.url);
        let toastEl = document.getElementById('liveToast');
        let toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    })
     .listen(".PostLiked", (e) => {
        console.log("👍 Post liked event diterima:", e);

        if (!e.like) return;
        if (e.like.user.id === userId) return; // abaikan kalau like sendiri

        if (notifAudio) {
            notifAudio.currentTime = 0;
            notifAudio.play().catch(err => console.log("Audio blocked:", err));
        }

        let badge = document.getElementById("notifCount");
        let count = parseInt(badge.innerText) || 0;
        badge.innerText = count + 1;

        let notifList = document.getElementById("notifList");
        let noNotif = notifList.querySelector('.dropdown-item.text-muted');
        if (noNotif) {
            noNotif.parentElement.remove();
        }

        let li = document.createElement("li");
        li.innerHTML = `
            <a href="${e.like.post.url}" class="dropdown-item notif-item">
                ${e.like.user.username} menyukai postingan 
                <strong>${e.like.post.title}</strong>
            </a>
        `;
        notifList.prepend(li);

        document.getElementById("toastLink").innerText =
            `${e.like.user.username} Menyukai post "${e.like.post.title}"`;
        document.getElementById("toastLink").setAttribute("href", e.like.post.url);

        let toastEl = document.getElementById('liveToast');
        let toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    })
  .listen('.CommentAdded', (e) => {
        console.log("📝 Komentar baru diterima:", e);
        // 🚫 skip kalau reply berasal dari user yang sedang login
        if (!e.comment) return;
        if (e.comment.user.id === userId) return;
        if (notifAudio) {
            notifAudio.currentTime = 0;
            notifAudio.play().catch(err => console.log("Audio blocked:", err));
        }

        let badge = document.getElementById("notifCount");
        let count = parseInt(badge.innerText) || 0;
        badge.innerText = count + 1;

        let notifList = document.getElementById("notifList");
        let noNotif = notifList.querySelector('.dropdown-item.text-muted');
        if (noNotif) {
            noNotif.parentElement.remove();
        }

        let li = document.createElement("li");
        li.innerHTML = `
            <a href="${e.comment.post.url}" class="dropdown-item notif-item">
                💬 ${e.comment.user.username} berkomentar di 
                <strong>${e.comment.post.title}</strong>
            </a>
        `;
        notifList.prepend(li);

        // Toast
        document.getElementById("toastLink").innerText =
            `${e.comment.user.username} berkomentar di post "${e.comment.post.title}"`;
        document.getElementById("toastLink").setAttribute("href", e.comment.post.url);

        let toastEl = document.getElementById('liveToast');
        new bootstrap.Toast(toastEl, { delay: 5000 }).show();
    })
    .listen('.UserFollowed', (e) => {
        console.log("👥 Follow baru:", e);

        if (!e.follower) return;

        if (e.follower.id === userId) return;

        if (notifAudio) {
            notifAudio.currentTime = 0;
            notifAudio.play().catch(err => console.log("Audio blocked:", err));
        }

        // update badge
        let badge = document.getElementById("notifCount");
        let count = parseInt(badge.innerText) || 0;
        badge.innerText = count + 1;

        // buat elemen notifikasi baru
        let li = document.createElement("li");
        li.innerHTML = `
            <a href="/profile/${e.follower.username}" class="dropdown-item notif-item">
                👤 ${e.follower.username} mulai mengikuti kamu
            </a>
        `;
        notifList.prepend(li);

        // isi teks toast
        document.getElementById("toastLink").innerText =
            `${e.follower.username} mulai mengikuti kamu`;
        document.getElementById("toastLink").setAttribute("href", `/profile/${e.follower.username}`);

        // tampilkan toast
        let toastEl = document.getElementById('liveToast');
        new bootstrap.Toast(toastEl, { delay: 5000 }).show();
    });


    // mark as read saat notif diklik
      // mark all as read ketika dropdown dibuka
      document.getElementById('notifDropdown').addEventListener('click', function () {
          fetch("/notifications/read", {
              method: "POST",
              headers: {
                  "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                  "Accept": "application/json"
              }
          }).then(res => {
              if (res.ok) {
                  document.getElementById("notifCount").innerText = "0";
              }
          });
      });
    </script>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dark Mode -->
    <script>
      const toggle = document.getElementById("theme-toggle");
      const body = document.body;

      if (localStorage.getItem("theme") === "dark") {
        body.classList.add("dark-mode");
        toggle.innerHTML = '<i class="fa fa-sun-o"></i>';
      }

      toggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        if (body.classList.contains("dark-mode")) {
          localStorage.setItem("theme", "dark");
          toggle.innerHTML = '<i class="fa fa-sun-o"></i>';
        } else {
          localStorage.setItem("theme", "light");
          toggle.innerHTML = '<i class="fa fa-moon-o"></i>';
        }
      });
    </script>
  </body>
</html>
