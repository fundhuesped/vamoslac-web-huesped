'use strict';

/**
 * @ngdoc function
 * @name houstonDiversityMap:controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the houstonDiversityMap
 */
angular.module('dondeDataVizApp').controller('placeCtrl',
  function(moment, NgMap, $interval, $routeParams, $scope, $timeout, $document, $http, $translate, $cookies) {

    // // Change language of this module
    // $scope.lang = $cookies.get('lang');
    // if ($scope.lang) {
    //   $translate.use($scope.lang);
    // }else {
    //      $translate.use('en');
    // }
    $scope.lang = $translate.preferredLanguage();
    
    $scope.nameCountry = $routeParams.pais;
    $scope.nameProvince = $routeParams.provincia;
    $scope.nameParty = $routeParams.partido;
    $scope.nameCity = $routeParams.ciudad;
    $scope.serviceCode = $routeParams.code;
    $scope.translateKeyService = $routeParams.code + "_name";
    $scope.translateKeyService = checkFriendlyCode($scope.translateKeyService);
    $translate($routeParams.code + "_name").then(function(t){
      $scope.serviceName =   t;
    })

    $scope.places = [];
    $http.get('pais/' + $scope.nameCountry + '/provincia/' + $scope.nameProvince + '/partido/' + $scope.nameParty + '/ciudad/' + $scope.nameCity + '/servicio/' + $scope.serviceCode)
      .success(function(places) {
        $scope.places = places.lugares;
        console.log($scope.cPlaces = places.cantidad);
      })
      .error(function(response){
        console.log(response);
      });
      

    $http.get('api/v1/single/service/'+ $scope.serviceCode)
    .success(function(service) {
      $scope.service = service;
      console.log($scope.service);
    });

    function checkFriendlyCode(code){
      var str = "friendly_";
      var i = code.indexOf(str);
      if (i != -1){
        code = code.substr(i + str.length);
      }
      return code;
    }

  });
