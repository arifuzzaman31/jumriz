@extends('front.master.master')

@section('title')
{{ $seo_info->title }}
@endsection

@section('meta')
<!-- Twitter Card  -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $seo_info->title }}">
<meta name="twitter:site" content="@arifuzzaman31">
<meta name="twitter:creator" content="@arifuzzaman31">
<meta name="twitter:description" content="{{ $seo_info->description }}">
<meta name="twitter:image" content="{{ url('images/setting/seo/'.$seo_info->meta_image) }}">

<!-- Open Graph  -->
<meta property="og:title" content="{{ $seo_info->title }}" />
<meta property="og:type" content="Ecommerce Site" />
<meta property="og:url" content="{{ url('/') }}" />
<meta property="og:image" content="{{ url('images/setting/seo/'.$seo_info->meta_image) }}" />
<meta property="og:description" content="{{ $seo_info->description }}" />

@endsection
@section('content')
@php
$currency = getCurrentCurrency();
@endphp
<home-page 
  :shop_info="{{ $shop_info }}"
  :currency="{{ $currency }}"
></home-page>

<div class="mapouter">
  <div class="gmap_canvas">
    <iframe width="100%" height="400px" src="{{ $seo_info->google_map }}" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    <a href="https://123movies-to.org"></a><br>
  </div>
</div>
@endsection
