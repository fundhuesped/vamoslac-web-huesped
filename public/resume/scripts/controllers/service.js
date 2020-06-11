'use strict';
angular.module('dondeDataVizApp').controller('serviceCtrl',
  function(moment, NgMap, $interval, $routeParams, $scope, $rootScope, $timeout, $document, $http, $translate, $cookies) {

    $rootScope.navigating = true;

    // Change language of this module
    var lang = $rootScope.selectedLanguage;
    if (lang) {
      $translate.use(lang);
    }

    $scope.services = [{
        icon: "condones.svg",
        icon_friendly: "condones_friendly.svg",
        title: "condones_name",
        code: "condones",
        code_friendly: "friendly_condones",
        content: "condones_content"
      },
      {
        icon: "vih.svg",
        icon_friendly: "vih_friendly.svg",
        title: "prueba_name",
        code: "prueba",
        code_friendly: "friendly_prueba",
        content: "prueba_content"
      },
      {
        icon: "salud.svg",
        icon_friendly: "salud_friendly.svg",
        title: "ssr_name",
        code: "ssr",
        code_friendly: "friendly_ssr",
        content: "ssr_content"
      },
      {
        icon: "deteccion.svg",
        icon_friendly: "deteccion_friendly.svg",
        title: "dc_name",
        code: "dc",
        code_friendly: "friendly_dc",
        content: "dc_content"
      },
      {
        icon: "mac.svg",
        icon_friendly: "mac_friendly.svg",
        title: "mac_name",
        code: "mac",
        code_friendly: "friendly_mac",
        content: "mac_content"
      },
      {
        icon: "ile.svg",
        icon_friendly: "ile_friendly.svg",
        title: "ile_name",
        code: "ile",
        code_friendly: "friendly_ile",
        content: "ile_content"
      }
    ];
    
    $scope.nameCountry = $routeParams.pais.split('-')[1];
    $scope.idCountry = $routeParams.pais.split('-')[0];
    $scope.nameProvince = $routeParams.provincia.split('-')[1];
    $scope.idProvince = $routeParams.provincia.split('-')[0];
    $scope.nameParty = $routeParams.partido.split('-')[1];
    $scope.idParty = $routeParams.partido.split('-')[0];
    $scope.nameCity = $routeParams.ciudad.split('-')[1];
    $scope.idCity = $routeParams.ciudad.split('-')[0];

  });
