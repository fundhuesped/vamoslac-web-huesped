'use strict';
angular.module('dondeDataVizApp').controller('countryCtrl',
  function(moment, NgMap, $interval, $routeParams, $scope, $rootScope, $timeout, $document, $http, $translate, $location) {

    $rootScope.navigating = true;

    // Change language of this module
    var lang = $rootScope.selectedLanguage;
    if (lang) {
      $translate.use(lang);
    }

    $scope.nameCountry = $routeParams.pais.split('-')[1];
    $scope.idCountry = $routeParams.pais.split('-')[0];

    gtag('event','pais', {
      'nombre_pais':   $scope.nameCountry
    });

    $scope.provinces = [];
    var url = 'pais/' + $scope.idCountry + '/province';
    $http.get(url)
    .success(function(provinces) {
      $scope.provinces = provinces;
    });

    $scope.loadProvince = function(id,name){
      $location.path(
        '/pais/'+$scope.idCountry+'-'+$scope.nameCountry+
        '/provincia/'+id+'-'+name+'/partido');
    };

  });
