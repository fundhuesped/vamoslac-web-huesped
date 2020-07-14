dondev2App.controller('locateMapController',
  function(placesFactory,NgMap, $scope,$rootScope, $routeParams, $location, $http){

    checkCurrentMarker();

    $rootScope.geo = true;
    $scope.service = $routeParams.servicio;
    $rootScope.navBar = $scope.service;
    $scope.main = true;
    $rootScope.main = false;

    $scope.closeCurrent = function(){
      $scope.currentMarker = undefined;
    }

    $scope.isValidAttr = function(attr){
      return attr !== '' && attr !== ' ' && attr !== undefined && attr !== null;
    }

    function checkCurrentMarker(){
      if(!$rootScope.currentMarker || !$scope.currentMarker)
        window.history.back();
    }

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

    $scope.zendeskTriggerNotes = "El usuario se encontraba buscando información de " + $scope.service + ".\n"+
    "Buscó utilizando su ubicación actual";

  });
