<!DOCTYPE html>
<html lang="en-us">

<head>
  @include('front.include.header_asset')

  @stack('style')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.17/sweetalert2.css">
  <style>
    /* this style are dynamic with theme  */
    .mapouter {
      margin-top: 20px;
      position: relative;
      text-align: right;
      bottom: 0;
      width: 100%;
    }

    .gmap_canvas {
      overflow: hidden;
      background: none !important;
      bottom: 0;
      width: 100%;
    }

    .theme-background {
      background: {{ $shop_info->theme_color }} !important;
    }

    .theme-color {
      color : {{ $shop_info->theme_color }} !important;
    }

    .theme-hover-bg : hover {

      background: {{ $shop_info->theme_color }} !important;
    }

    .theme-hover-color : hover {
      color : {{ $shop_info->theme_color }} !important;
    }

    .initially_selected .parent_a {
      color : {{ $shop_info->theme_color }};
    }

    .active_color>a {
      color : {{ $shop_info->theme_color }};
    }

    .my-radio {
      display: none;
    }

    .swal2-actions {
      justify-content: space-around !important;
    }

    .whatsapp-ico {
      fill: white;
      width: 50px;
      height: 50px;
      padding: 3px;
      background-color: #4dc247;
      border-radius: 50%;
      /*box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4);*/
      /*box-shadow: 2px 2px 11px rgba(0,0,0,0.7); */
      position: fixed;
      bottom: 50px;
      right: 20px;
      z-index: 10;
    }

    @media only screen and (max-width: 600px) {
      .bIBMON {
        margin-bottom: 36px !important;
      }
    }

    .whatsapp-ico:hover {
      box-shadow: 2px 2px 11px rgba(0, 0, 0, 0.7);
    }
  </style>
  @if($shop_info->sidemenu_status == 0)
  <style>
    .sidebar {
      margin-left: -250px !important;
    }

    .sidebar.active {
      margin-left: 0px !important;
      transition: all ease 1s;
    }
  </style>
  @endif
  <!-- google analytics  -->
  <!-- coming from app/Helpers/helper -->
  @php
  $google_analytics = googleAnalytics();
  @endphp

  @if($google_analytics && $google_analytics->status == 1)
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ $google_analytics->app_id }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', '{{ $google_analytics->app_id }}');
  </script>

  @endif
  <!-- google analytics  -->
  @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/front.js'])
</head>

<body id="app_body">
  <div id="front-wrapper">
    <header class="header theme-background">
      <div class="container-fluid">
        <div class="row">
          <div class="col col-lg-1 col-sm-1">
            <div class="left-menu-toggle">
              <a id="sidebarCollapse" href="#" class="toggle-nav btn-nav nav-toggle-btn">
                <i class="lni-menu"></i>
              </a>
            </div>
          </div>
          <div class="col col-lg-2 col-sm-2 ">
            <div class="logo">
              <a href="{{ url('/') }}"><img src="{{ url('images/logo/'.$shop_info->logo_header) }}" alt="logo" class="img-fluid"></a>
            </div>
          </div>
          <!-- for mobile  -->
          <div class="d-sm-none d-md-none d-lg-none col col-lg-3 col-sm-3 text-center">
            <div class="user-menu" style="height: 100%;">
              <a href="#">
                <i class="lni lni-user" style="font-size: 20px"></i>
              </a>
              <ul class="dropdown-menu1">
                @auth
                <li><a href="{{ route('user.profile') }}"><span>Profile</span></a></li>
                <li><a href="{{ route('user.order') }}"><span>My Orders</span></a></li>
                <li><a href="{{ route('user.logout') }}"><span>Logout</span></a></li>
                @endauth
                @guest
                <li><a href="{{ route('login') }}"><span>Sign in</span></a></li>
                <li><a href="{{ route('register') }}"><span>Sign up</span></a></li>
                @endguest
                <li><a href="{{ route('order.track') }}">Track Order</a></li>
              </ul>
            </div>
          </div>
          <!-- end mobile profile  -->
          <div class="col-12 col-lg-6 col-sm-6">
            <search-box></search-box>
          </div>

          <div class="d-none d-sm-block col-3 col-lg-3 col-sm-3 text-center">
            <div class="user-menu" style="height:100%">
              <a href="#">
                <i class="lni lni-user" style="font-size: 20px"></i>
              </a>
              <ul class="dropdown-menu1">
                @auth
                <li><a href="{{ route('user.profile') }}"><span>Profile</span></a></li>
                <li><a href="{{ route('user.logout') }}"><span>Logout</span></a></li>
                @endauth
                @guest
                <li><a href="{{ route('login') }}"><span>Sign in</span></a></li>
                <li><a href="{{ route('register') }}"><span>Sign up</span></a></li>
                @endguest
                <li><a href="{{ route('order.track') }}">Track Order</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      @include('front.include.cart')

    </header>
    <!--    end header-->
    <div class="wrapper">
      <!-- start Sidebar  -->

      @include('front.include.left_sidebar')

      <!-- end Sidebar  -->

      <!-- Page Content  -->
      <div id="content">
        <!-- search result apear  -->

        <!-- search result apear  -->
        <div class="message" style="width: 100%;/*background: #032c44;*/ display: none;">
          @foreach (['warning', 'success', 'error'] as $msg)
          @if(Session::has($msg))
          <p class="text-center" style="padding-top: 20px; padding-bottom:20px; font-size: 22px; color: #fff;">{{ Session::get($msg) }}</p>
          @endif
          @endforeach
        </div>
        @yield('content')

        <!--start email subscribe-->
        <!-- <section class="subscribe clearfix mt50 text-center">
    <user-subscribe></user-subscribe>
</section> -->
        <!--end email subscribe-->

        <!-- add to cart with color and size   -->
        <add-to-cart-modal></add-to-cart-modal>
        <!-- add to cart with color and size   -->

        @include('front.include.footer')
        @include('front.include.whatsapp-icon')
      </div>
    </div>

  </div>
  <!-- End Back To Top Button -->
  <script>
    var base_url = "{{ url('/') }}" + '/';
  </script>
  <!--jquery js-->
  <script src="{{ asset('assets/js/jquery-3.4.1.js') }}"></script>
  <!--boostrap js-->
  <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
  <!--popper js-->
  <script src="{{ asset('assets/js/popper.js') }}"></script>
  <!--swiper js-->
  <script src="{{ asset('assets/js/jquery.elevatezoom.js') }}"></script>
  <!--magnific js-->
  <script src="{{ asset('assets/js/magnific.js') }}"></script>
  <!--main js-->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  @stack('script')

  <script type="text/javascript">
    $(document).ready(function($) {

      @foreach(['warning', 'success', 'error'] as $msg)
      @if(Session::has($msg))
      @switch($msg)
      @case('warning')
      $('.message').addClass('bg-warning');
      @break;
      @case('error')
      $('.message').addClass('bg-danger');
      @break;
      @default
      $('.message').addClass('bg-success');
      @endswitch
      $('.message').slideDown("slow").delay(4500).slideUp("slow");
      @endif
      @endforeach

      // $('.sidebar').addClass('active');

      // if (sessionStorage.getItem('advertOnce') !== 'true') {
      //   setTimeout(() => myGreeting(), 3000);
      // }

      // function myGreeting() {
      //   const swalWithBootstrapButtons = Swal.mixin({
      //     customClass: {
      //       confirmButton: 'btn btn-success',
      //       cancelButton: 'btn btn-danger'
      //     },
      //     buttonsStyling: false
      //   })

      //   swalWithBootstrapButtons.fire({
      //     title: 'Are you 21 or older?',
      //     text: "Please Verify Your Age!",
      //     icon: 'question',
      //     showCancelButton: true,
      //     confirmButtonText: 'Yes, Im!',
      //     cancelButtonText: 'No, Im Not!',
      //     reverseButtons: true
      //   }).then((result) => {
      //     if (result.isConfirmed) {
      //       sessionStorage.setItem('advertOnce', 'true');
      //     } else {
      //       sessionStorage.setItem('advertOnce', '');
      //       window.close();
      //       window.open("", "_black");
      //     }
      //   })
      // }

    })
  </script>
  <!-- coming from app/helpers/helper  -->
  @php
  $facebook_chat = facebookChat();
  @endphp

  @if($facebook_chat && $facebook_chat->status == 1)
  <div class="fb-customerchat" page_id="{{ $facebook_chat->app_id }}" minimized="true" theme_color="{{ $shop_info->theme_color }}">
  </div>
  <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId: '173637076534990',
        autoLogAppEvents: true,
        xfbml: true,
        version: 'v2.11'
      });
    };
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {
        return;
      }
      js = d.createElement(s);
      js.id = id;
      js.src = "https://connect.facebook.net/he_EN/sdk/xfbml.customerchat.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>
  @endif

  <script type="text/javascript">
    // Initialize the service worker
    // if ('serviceWorker' in navigator) {
    //   navigator.serviceWorker.register('/serviceworker.js', {
    //     scope: '.'
    //   }).then(function(registration) {
        // Registration was successful
        // console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
      // }, function(err) {
        // registration failed :(
        // console.log('Laravel PWA: ServiceWorker registration failed: ', err);
    //   });
    // }
  </script>

  <!-- <script src="https://apps.elfsight.com/p/platform.js" defer></script>
  <div class="elfsight-app-e503b6b9-e339-4702-8497-cd7c96493fae"></div> -->


  <script>
    document.getElementById("SubscribeBtn").addEventListener("click", function(event) {
      event.preventDefault();
    });
  </script>
</body>

</html>
