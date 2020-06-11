'use strict';
angular.module('dondeDataVizApp').controller('partyCtrl',
  function(moment, NgMap, $interval, $routeParams, $scope, $rootScope, $timeout, $document, $http, $translate, $location) {

    $rootScope.navigating = true;

    // Change language of this module
    var lang = $rootScope.selectedLanguage;
    if (lang) {
      $translate.use(lang);
    }

    $scope.nameCountry = $routeParams.pais.split('-')[1];
    $scope.idCountry = $routeParams.pais.split('-')[0];
    $scope.nameProvince = $routeParams.provincia.split('-')[1];
    $scope.idProvince = $routeParams.provincia.split('-')[0];
    $scope.nameParty = $routeParams.partido.split('-')[1];
    $scope.idParty = $routeParams.partido.split('-')[0];

    $scope.ciudades = [];
    $http.get('partido/' + $scope.idParty + '/ciudad')
    .success(function(ciudades) {
      $scope.ciudades = ciudades;
    });

    $scope.loadCity = function(id,name){
      $location.path(
        '/pais/'+$scope.idCountry+'-'+$scope.nameCountry+
        '/provincia/'+$scope.idProvince+'-'+$scope.nameProvince+
        '/partido/'+$scope.idParty+'-'+$scope.nameParty+
        '/ciudad/'+id+'-'+name+'/servicio');
    };

  });
