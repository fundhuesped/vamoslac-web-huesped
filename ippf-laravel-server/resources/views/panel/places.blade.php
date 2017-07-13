@extends('layouts.panel-master')

@section('content')
  <div class="home" ng-controller="panelplaceController"
  ng-init="placeId={{$placeId}}">
  <div ng-cloak ng-show="loading">
     <div class="progress">
              <div class="indeterminate"></div>
         </div>
  </div>
  <div  class="ng-cloak section navigate row wow fadeIn" ng-cloak ng-hide="loading">
    [[establecimiento]]
    <div class="section search search-form row ">
        <div class="row">
            <div class="col s12">
                <div class="row">
                  <div class="col s12 m12">

                    <div class="row col s12">
    <div class="col s12">
        <ul class="tabs" tabs>
            <li class="tab col s2"><a class="active" href="#general">General</a></li>
            <li class="tab col s2"><a href="#Prueba">Prueba</a></li>
            <li class="tab col s2"><a href="#Condones">Condones</a></li>
            <li class="tab col s2"><a href="#Mac">Mac</a></li>
            <li class="tab col s2"><a href="#Ile">Ile</a></li>
            <li class="tab col s2"><a href="#Ssr">SSR</a></li>
            <li class="tab col s2"><a href="#Dc">DC</a></li>

            <li class="tab col s3 Aligner">
                <a href="#Evaluacion" class="panel-evaluation-tab">Evaluaciones
                  <span class="newBadge">[[badge]]</span>
                </a>
                {{-- <a href="#Evaluacion" style="display: flex;">Evaluaciones 6<span class="badge">6</span></a> --}}
                {{-- <span class="badge">6</span> --}}
                 {{-- <i class="material-icons">mode_edit</i> --}}
            </li>

        </ul>
    </div>

      <div id="general" class="col s12">
            @include('panel/edit/general')
      </div>

      <div id="Prueba" class="col s12">
          @include('panel/edit/prueba')
      </div>

      <div id="Condones" class="col s12">
          @include('panel/edit/distrib')
      </div>
<!--
      <div id="Infecciosas" class="col s12">
          @include('panel/edit/infectologia')
      </div>

      <div id="Vacunacion" class="col s12">
          @include('panel/edit/vac')
      </div>
-->
      <div id="Mac" class="col s12">
          @include('panel/edit/mac')
      </div>

      <div id="Ile" class="col s12">
          @include('panel/edit/ile')
      </div>

      <div id="Dc" class="col s12">
          @include('panel/edit/dc')
      </div>

      <div id="Ssr" class="col s12">
          @include('panel/edit/ssr')
      </div>

      <div id="Evaluacion" class="col s12">
          @include('panel/edit/evaluacion')
      </div>

    </div>



            </div>
        </div>
    </div>
  </div>
</div>
@stop

@section('js')

  {!!Html::script('libs/trabex-genosha-geolibs.js')!!}
  {!!Html::script('bower_components/ngmap/build/scripts/ng-map.min.js')!!}

  {!!Html::script('scripts/panel/app.js')!!}
  {!!Html::script('scripts/panel/controllers/places/controller.js')!!}
  {!!Html::script('scripts/home/services/places.js')!!}
  {!!Html::script('scripts/home/services/copy.js')!!}

@stop
