'use strict';
angular.module('dondeDataVizApp').controller('provinceCtrl',
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

    $scope.partidos = [];
    $http.get('provincia/' + $scope.idProvince + '/partido')
    .success(function(partidos) {
      $scope.partidos = partidos;
    });

    $scope.loadPartido = function(id,name){
      $location.path(
        '/pais/'+$scope.idCountry+'-'+$scope.nameCountry+
        '/provincia/'+$scope.idProvince+'-'+$scope.nameProvince+
        '/partido/'+id+'-'+name+'/ciudad');
    };

  });
