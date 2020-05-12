dondev2App.controller('cityMapController',
  function(placesFactory, NgMap, copyService, $scope, $rootScope, $routeParams, $location, $http) {
    //controlador para busqueda escrita
    $rootScope.$watch('currentMarker', function() {
      $scope.currentMarker = $rootScope.currentMarker;
    })

    $rootScope.main = false;
    $rootScope.geo = false;

    $scope.service = copyService.getFor($routeParams.servicio);
    $rootScope.navBar = $scope.service;

    $scope.voteLimit = 5;

    $scope.addComment = function() {
      $scope.voteLimit++;
    }

    $scope.closeCurrent = function() {
      $scope.currentMarker = undefined;
    }

    gtag('event','ver_centro', {
      'event_category': $rootScope.currentMarker.establecimiento,
      'event_label': $scope.service.label
    });

    function correctWebLinks(place){
      var columns = ['web_distrib','web_dc','web_ile','web_infectologia','web_mac','web_testeo','web_ssr','web_vac'];
      var patt = new RegExp("^(http:\/\/|https:\/\/).+$");
      for (var i = 0; i < columns.length; i++) {
        var str = place[columns[i]];
        if(str && !patt.test(str)){
          str = str.toLowerCase();
          place[columns[i]] = "http://" + str;
        }
      }
      return place;
    }

    $rootScope.currentMarker = correctWebLinks($rootScope.currentMarker);

    $scope.zendeskTriggerNotes = "El usuario se encontraba buscando información de " + $scope.service.label + ".\nCiudad: " + $scope.cityId + ".";
  });
