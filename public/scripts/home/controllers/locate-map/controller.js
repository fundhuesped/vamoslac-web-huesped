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

    function checkCurrentMarker(){
      if(!$rootScope.currentMarker || !$scope.currentMarker)
        window.history.back();
    }

    $scope.zendeskTriggerNotes = "El usuario se encontraba buscando información de " + $scope.service + ".\n"+
    "Buscó utilizando su ubicación actual";

  });
