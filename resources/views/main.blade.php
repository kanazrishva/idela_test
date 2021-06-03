<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>IDELA Data Explorer - {{ $meta['title'] }}</title>
    <link rel="canonical" href="" />

    <meta name="robots" content="index, follow" />
    <meta charset="utf-8">
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="">
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ URL::to('/') }}/{{ $slug }}" />
    <meta property="og:title" content="IDELA Data Explorer - {{ $meta['ogtitle'] }}" />
    <meta property="og:description" content="{{ $meta['ogdescription'] }}" />
    <meta property="og:site_name" content="{{ URL::to('/') }}/" />
    <meta name="twitter:site" content="{{ URL::to('/') }}/">
    <meta name="twitter:title" content="IDELA Data Explorer - {{ $meta['twittertitle'] }}">
    <meta name="twitter:description" content="{{ $meta['twitterdescription'] }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:widgets:new-embed-design" content="on">

    <meta property="og:image" content="{{ 'https://staging-idela-data.imgix.net/'.$meta['ogimage'].'?w=1200&h=630&fit=crop' }}" />
    <meta name="twitter:image:src" content="{{ 'https://staging-idela-data.imgix.net/'.$meta['twitterimage'].'?w=1200&h=628&fit=crop' }}">

    <meta name="apple-mobile-web-app-title" content="IDELA Data Explorer">
    <meta name="theme-color" content="#FFFFFF" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="asset-url" content="{{ config('app.asset_url') }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('/images/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('/images/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('/css/main.css') }}">
    <script src="https://kit.fontawesome.com/59f8b0bb9c.js" crossorigin="anonymous"></script>

    {{-- Inertia --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign" defer></script>

    {{-- IDELA Specific --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=String.prototype.startsWith" defer></script>


    <script src="{{ asset('/js/manifest.js') }}" defer></script>
    <script src="{{ asset('/js/vendor.js') }}" defer></script>
    <script src="{{ asset('/js/app.js') }}" defer></script>
    
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <script src="https://bl.ocks.org/mbostock/raw/7ea1dde508cec6d2d95306f92642bc42/6aac691494f752142a67cc43c51a0fd09896dbd4/versor.js"></script>

    @routes
  </head>
  <body>

    @inertia
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','dataLayer','GTM-5C987JP');</script>
  </body>
</html>



