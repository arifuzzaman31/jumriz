<footer style="background-color:#242424;">
<div class="container">
    <div class="row footer-primary clearfix   mb20 ">
                <div class="col-lg-4 col-sm-6 xs-mb30">
                    <div class="logo">
                        <a href="{{ url('/') }}"><img src="{{ asset('images/logo/'.$shop_info->logo_footer) }}" alt="logo" class="img-fluid"></a>
                    </div>
                    <div class="footer-menu contact-info mt30">
                        <ul>
                            <li class="telephone"> <i class='lni lni-mobile'></i> {{ $shop_info->phone }}
                            <li class="address"> <i class="lni lni-direction"></i> {{ $shop_info->address }}</li>
                        </ul>
                  </div>
                </div>
                <div class="col-lg-2 col-sm-6 xs-mb30">
                    <div class="footer-menu">
                        <h4>Quick Links</h4>
                        <ul>
                            <!-- it's coming from hlpers  in app/helpers/helpers -->
                            @if(count(allPages()) > 0)
                                @foreach(allPages() as $value)
                                    <li><a href="{{ url('page',str_replace(' ','-',$value->title))}}"><i class='lni lni-chevron-right'></i> {{ $value->title }}</a></li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 xs-mb30">
                    <div class="footer-menu">
                        <h4>Individual user</h4>
                        <ul>
                            @auth
                            <li><a href="{{ route('user.profile') }}"><i class='lni lni-chevron-right'></i> Profile</a></li>
                            <li><a href="{{ route('user.order') }}"><i class='lni lni-chevron-right'></i> My Order</a></li>
                            <li><a href="{{ route('user.logout') }}"><i class='lni lni-chevron-right'></i> Logout</a></li>
                            @endauth
                            @guest
                            <li><a href="{{ route('login') }}"><i class='lni lni-chevron-right'></i> Sign in</a></li>
                            <li><a href="{{ route('register') }}"><i class='lni lni-chevron-right'></i> Sign up</a></li>                            
                            @endguest
                            <li><a href="{{ route('order.track') }}"><i class='lni lni-chevron-right'></i> Track Your Order</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 xs-mb30">
                    <div class="footer-menu contact-info subscribes">
                        <h4>Get our notification at email</h4>
                        <div class="subscribes search">
                            <form action="{{ route('user/subscribe') }}" method="POST">
                            {{ csrf_field() }}
                                <div class="subs-box">
                                    <input name="email" class="form-control" placeholder="Your Email Here" type="text">
                                    <button type="submit" class="button src-btn" id="SubscribeBtn"> Subscribe </button>
                                </div>
                            </form>
                            @if(Session::has('message'))
                            <p class="{{ Session::get('color-red', 'text-info') }} mt5">{{ Session::get('message') }}</p>
                            @endif
                            @error('email')
                                <div class="color-red mt5">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

    </div>
</div>
<div class="footer-secondary  p30" style="background-color:#242424;">
    <div class="container">
        <div class="row">
                <div class="col-lg-6 col-sm-12 col-xs-12">
                    <div class="footer-copyright ">
                        <span>{{ $shop_info->footer_text }}</span>
                    </div>
                </div>
            <div class="col-lg-6 col-sm-12 xs-mb30">
                <div class="social-icons text-r xs-text-c">
                    <ul>
                        <li><a href="{{ $shop_info->facebook }}"><i class="lni-facebook-filled"></i></a></li>
                        <li><a href="{{ $shop_info->twitter }}"><i class="lni-twitter-filled"></i></a></li>
                        <li><a href="{{ $shop_info->youtube }}"><i class="lni-youtube"></i></a></li>
                        <!-- <li><a href="#"><i class="lni-instagram-filled"></i></a></li>
                        <li><a href="#"><i class="lni-pinterest"></i></a></li> -->
                    </ul>
                </div>
            </div>

            </div>
        </div>
    </div>
</footer>
