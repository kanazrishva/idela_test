<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>IDELA Data Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Styles -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <script src="https://kit.fontawesome.com/59f8b0bb9c.js" crossorigin="anonymous"></script>

    {{-- Inertia --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign" defer></script>

    {{-- IDELA Specific --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=String.prototype.startsWith" defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.2.2/d3.min.js"></script>
    <script src="https://d3js.org/d3-geo.v1.min.js"></script>
    <script src="https://d3js.org/topojson.v2.min.js"></script>
    <script src="https://bl.ocks.org/mbostock/raw/7ea1dde508cec6d2d95306f92642bc42/6aac691494f752142a67cc43c51a0fd09896dbd4/versor.js"></script>
    <script src="https://unpkg.com/topojson@3.0.2/dist/topojson.min.js"></script>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    
    <script src="{{ asset('/js/manifest.js') }}" defer></script>
    <script src="{{ asset('/js/vendor.js') }}" defer></script>
    <script src="{{ asset('/js/app.js') }}" defer></script>
    @routes
  </head>
  <body>
    @inertia
  </body>
</html>



