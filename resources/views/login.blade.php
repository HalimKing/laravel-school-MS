<!DOCTYPE html>
<!--
Template Name: NobleUI - HTML Bootstrap 5 Admin Dashboard Template
Author: NobleUI
Website: https://nobleui.com
Contact: nobleui.team@gmail.com
Purchase: https://1.envato.market/nobleui_html
License: You must have a valid license to legally use this template for your project.
-->
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="Responsive HTML Admin Dashboard Template based on Bootstrap 5">
  <meta name="author" content="NobleUI">
  <meta name="keywords" content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

  <title>NobleUI - HTML Bootstrap 5 Admin Dashboard Template</title>

  <!-- color-modes:js -->
  <script src="./assets/js/color-modes.js"></script>
  <!-- endinject -->

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
  <!-- End fonts -->

  <!-- core:css -->
  <link rel="stylesheet" href="./assets/vendors/core/core.css">
  <!-- endinject -->

  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->

  <!-- inject:css -->
  <!-- endinject -->

  <!-- Layout styles -->
  <link rel="stylesheet" href="./assets/css/demo1/style.css">
  <!-- End layout styles -->

  <link rel="shortcut icon" href="./assets/images/favicon.png" />

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-NXVN8PTKWG"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-NXVN8PTKWG');
  </script>

</head>

<body>
  <div class="main-wrapper">
    <div class="page-wrapper full-page">
      <div class="page-content container-xxl d-flex align-items-center justify-content-center">

        <div class="row w-100 mx-0 auth-page">
          <div class="col-md-10 col-lg-8 col-xl-6 mx-auto">
            <div class="card">
              <div class="row">
                <div class="col-md-4 pe-md-0">
                  <div class="auth-side-wrapper" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; min-height: 100%;">
                    <div class="text-center text-white px-3">
                      <h2 class="mb-3 fw-bold">School MS</h2>
                      <p class="lead mb-2">Manage your school</p>
                      <p class="text-white-50">with ease and efficiency</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-8 ps-md-0">
                  <div class="auth-form-wrapper px-4 py-5">
                    <a href="{{ route('dashboard') }}" class="nobleui-logo d-block mb-2">School<span>MS</span></a>
                    <h5 class="text-secondary fw-normal mb-4">Welcome back! Log in to your account.</h5>

                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Login Failed!</strong>
                      @foreach ($errors->all() as $error)
                      <div>{{ $error }}</div>
                      @endforeach
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      {{ session('success') }}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('auth.login.post') }}">
                      @csrf
                      <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                          id="email" name="email" placeholder="Enter email address"
                          value="{{ old('email', 'admin@schoolms.com') }}" required autofocus>
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                          id="password" name="password" placeholder="Enter password"
                          autocomplete="current-password" required>
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                          Remember me
                        </label>
                      </div>
                      <div>
                        <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0 text-white">Login</button>
                      </div>
                      <p class="mt-3 text-secondary">
                        Demo credentials: admin@schoolms.com / password
                      </p>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- core:js -->
  <script src="./assets/vendors/core/core.js"></script>
  <!-- endinject -->

  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->

  <!-- inject:js -->
  <script src="./assets/js/app.js"></script>
  <!-- endinject -->

  <!-- Custom js for this page -->
  <!-- End custom js for this page -->

</body>

</html>