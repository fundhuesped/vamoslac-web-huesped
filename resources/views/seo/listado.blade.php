@extends('layouts.master')
@section('meta')

<title>VAMOS | vamoslac.org</title>
<meta name="google-site-verification" content="7Myd-74iCkDjnz5HEK2-iF7gNuDUL8TMmkg6DiwxsZc" />
<meta name="description" content="@lang('site.seo_meta_description_content')">
<meta name="author" content="@lang('site.seo_meta_author_content')">
<link rel="canonical" href="@lang('site.seo_meta_canonicallink')">
<meta property='og:locale' content="@lang('site.seo_meta_property_local')">
<meta property='og:title' content="@lang('site.seo_meta_property_title')">
<meta property="og:description" content="@lang('site.seo_meta_property_description')" >

@stop

@section('content')
<link rel="stylesheet" href="resume/styles/resume.css">
<div ng-app="dondeDataVizApp">

  @include('navbar')

  <div class="container home new-home">
    <div class="container" ng-view></div>
  </div>

  @include('acerca')

</div>
@stop

@section('js')

{!!Html::script('https://rawgit.com/allenhwkim/angularjs-google-maps/master/build/scripts/ng-map.js')!!}
{!!Html::script('bower_components/moment/moment.js')!!}
{!!Html::script('bower_components/angular-moment/angular-moment.js')!!}
{!!Html::script('bower_components/odometer/odometer.js')!!}
{!!Html::script('bower_components/angular-odometer-js/dist/angular-odometer.js')!!}
{!!Html::script('bower_components/smooth-scroll/dist/js/smooth-scroll.min.js')!!}

{!!Html::script('bower_components/materialize/dist/js/materialize.min.js')!!}
{!!Html::script('bower_components/ngmap/build/scripts/ng-map.min.js')!!}
{!!Html::script('bower_components/angularjs-socialshare/dist/angular-socialshare.min.js')!!}
{!!Html::script('bower_components/angular-recaptcha/release/angular-recaptcha.js')!!}
{!!Html::script('bower_components/ng-text-truncate/ng-text-truncate.js')!!}
{!!Html::script('bower_components/angular-translate/angular-translate.js')!!}

{!!Html::script('scripts/translations/es.js')!!}
{!!Html::script('scripts/translations/br.js')!!}
{!!Html::script('scripts/translations/en.js')!!}

{!!Html::script('resume/scripts/app.js')!!}
{!!Html::script('resume/scripts/controllers/nav-controller.js')!!}
{!!Html::script('resume/scripts/controllers/map-controller.js')!!}

{!!Html::script('resume/scripts/controllers/home.js')!!}
{!!Html::script('resume/scripts/controllers/country.js')!!}
{!!Html::script('resume/scripts/controllers/province.js')!!}
{!!Html::script('resume/scripts/controllers/party.js')!!}
{!!Html::script('resume/scripts/controllers/service.js')!!}
{!!Html::script('resume/scripts/controllers/place.js')!!}
{!!Html::script('resume/scripts/controllers/country-list.js')!!}

<script>
  $(document).ready(function() {
    $('select').material_select();
  });
</script>
@stop
