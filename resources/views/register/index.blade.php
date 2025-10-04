@extends('layout.main')

@section('container')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <main class="form-registration">
            <h1 class="h3 mb-3 fw-normal text-center">Registration</h1>
            <form action="/register" method="post">
              @csrf 
              <div class="form-floating">
                <input type="text" name="name" class="form-control rounded-top @error('name') is-invalid @enderror" id="name" placeholder="Name" required value="{{ old('name') }}">
                <label for="name">Name</label>
                @error('name')
                <div id="validationServer04Feedback" class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>
              <div class="form-floating">
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" placeholder="Username" required value="{{ old('username') }}">
                <label for="username">Username</label>
                @error('username')
                <div id="validationServer04Feedback" class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>
              <div class="form-floating">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email Address" required value="{{ old('email') }}">
                <label for="email">Email Address</label>
                @error('email')
                <div id="validationServer04Feedback" class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>
              <div class="form-floating">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password rounded-bottom" name="password" placeholder="Password" required>
                <label for="password">Password</label>
                @error('password')
                <div id="validationServer04Feedback" class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>
        
              <button class="w-100 btn btn-lg btn-primary mt-3" type="submit">Register Now</button>
            </form>
            <small class="d-block text-center mt-3">Already Have An Account?<a href="/login">Login Now</a></small>
          </main>
          <p class="mt-5 mb-3 text-muted">Laravel Rizky / Cloudy</p>

    </div>
</div>

@endsection