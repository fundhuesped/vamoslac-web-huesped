@extends('layouts.master')
@section('meta')
<title>VAMOS | vamoslac.org</title>

<meta name="author" content="IPPF">
<meta name="google-site-verification" content="7Myd-74iCkDjnz5HEK2-iF7gNuDUL8TMmkg6DiwxsZc" />
<link rel="canonical" href="https://vamoslac.org/"/>
<meta property='og:title' content="@lang('site.page_title')" />
<meta property="og:description" content="@lang('site.seo_meta_description_content')" />
<meta property='og:type' content="@lang('site.page_title')" />
<meta property='og:locale' content='es_LA'/>
<meta property='og:url' content='https://vamoslac.org/'/>
<meta property='og:site_name' content='VAMOS'/>
<meta property='og:image' content='https://vamoslac.org/og.png'/>
<meta property='fb:app_id' content='1964173333831483' />
<meta name="twitter:card" content="summary">
<meta name='twitter:title' content="@lang('site.page_title')" />
<meta name="twitter:description" content="@lang('site.seo_meta_description_content')" />
<meta name='twitter:url' content='https://vamoslac.org/'/>
<meta name='twitter:image' content='https://vamoslac.org/og.png'/>
<meta name='twitter:site' content='@fundhuesped' />
<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
<!-- Start of vamoslac Zendesk Widget script -->
<script>/*<![CDATA[*/window.zE||(function(e,t,s){var n=window.zE=window.zEmbed=function(){n._.push(arguments)}, a=n.s=e.createElement(t),r=e.getElementsByTagName(t)[0];n.set=function(e){ n.set._.push(e)},n._=[],n.set._=[],a.async=true,a.setAttribute("charset","utf-8"), a.src="https://static.zdassets.com/ekr/asset_composer.js?key="+s, n.t=+new Date,a.type="text/javascript",r.parentNode.insertBefore(a,r)})(document,"script","5fb55130-f94f-460e-a0cf-d41081e7d54b");/*]]>*/</script>
<!-- End of vamoslac Zendesk Widget script -->
@stop

@section('content')
<div ng-app="dondev2App">
	@include('navbar')
	<div ng-controller="termsController">
		<div class="row mt-1">
			<div class="col s12 offset-m2 m8">
				<a style="float: left; width: 50%" href='https://www.ippfwhr.org/' target="_blank">
					<img src="images/logo_ippf.png" width="80%" />
				</a>
				<a style="float: right; width: 50%;text-align: right;" href='https://www.huesped.org.ar/' target="_blank">
					<img src="images/logo_huesped.png" width="60%" />
				</a>
			</div>
		</div>
		<div class="row terms mt-1">
			<div class="col s12 offset-m2 m8">
				<div ng-if="$root.selectedLanguage === 'es'">
					@include("t&c.spanish")
				</div>
				<div ng-if="$root.selectedLanguage !== 'es'">
					@include("t&c.english")
				</div>	
			</div>
		</div>
	</div>

	@include('acerca')
</div>
@stop

@section('js')
{!!Html::script('bower_components/materialize/dist/js/materialize.min.js')!!}
{!!Html::script('bower_components/ngmap/build/scripts/ng-map.min.js')!!}
{!!Html::script('bower_components/angularjs-socialshare/dist/angular-socialshare.min.js')!!}
{!!Html::script('bower_components/angular-recaptcha/release/angular-recaptcha.js')!!}
{!!Html::script('bower_components/ng-text-truncate/ng-text-truncate.js')!!}
{!!Html::script('bower_components/angular-translate/angular-translate.js')!!}

{!!Html::script('scripts/translations/es.js')!!}
{!!Html::script('scripts/translations/br.js')!!}
{!!Html::script('scripts/translations/en.js')!!}

{!!Html::script('scripts/home/app.js')!!}
{!!Html::script('scripts/home/controllers/nav/nav-controller.js')!!}
{!!Html::script('scripts/home/controllers/terms/terms-controller.js')!!}
{!!Html::script('scripts/home/services/places.js')!!}

<script>
  $(document).ready(function() {
    $('select').material_select();
  });
</script>
@stop
