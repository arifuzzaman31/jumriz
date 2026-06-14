     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="description" content="{{ $seo_info->description }}">
     <meta name="keywords" content="{{ $seo_info->keyword }}">
     <meta name="author" content="{{ $seo_info->author }}">
     <meta name="sitemap_link" content="{{ $seo_info->sitemap_link }}">
     <meta property="og:site_name" content="{{ $shop_info->shop_name }}" />
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <meta name="theme-color" content="{{ $shop_info->theme_color }}">
     <!-- laravelPWA -->
     @yield('meta')
     <link rel="shortcut icon" href="{{ asset('images/logo/'.$shop_info->favicon) }}" />
     <title>@yield('title',$shop_info->shop_name)</title>

     <!--bootstrap css-->
     <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">

     <!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"> -->
     <!--line icons-->
     <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/LineIcons.min.css') }}">
     <!--style css-->
     <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
     <!--setting css-->
     <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/setting.css') }}">
     <!--responsive css-->
     <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">
      <!--responsive css-->
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
      <!--google font css-->
      <!-- <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet"> -->

      <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet">

      @vite(['resources/css/app.css', 'resources/js/app.js'])
