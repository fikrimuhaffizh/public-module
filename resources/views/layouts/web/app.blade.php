<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>{{ config('app.name') }}</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="{{ asset('images/favicon.png') }}" rel="icon">
  <link href="{{ asset('images/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  @vite(['Modules/Public/resources/assets/css/public.css', 'Modules/Public/resources/assets/js/public.js'])
</head>

<body class="index-page">

  @include('public::layouts.web.header')

  <main class="main">
    @yield('content')
  </main>

  @include('public::layouts.web.footer')

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  @stack('scripts')
</body>

</html>
