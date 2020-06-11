'use strict';
angular.module('dondeDataVizApp').controller('HomeCtrl',
  function(moment, NgMap, $interval, $scope, $rootScope, $timeout, $document, $http, $translate, $cookies) {

    $scope.serviceCode = 'resume';
    $rootScope.serviceCode = 'resume';
    $rootScope.navigating = true;
    var approvedUrl = 'api/v2/places/getApproved';

    // Change language of this module
    var lang = $rootScope.selectedLanguage;
    if (lang) {
      $translate.use(lang);
    }

    $scope.closeDetail = function() {
      $rootScope.currentMarker = $scope.currentMarker = undefined;
      var anchor = document.querySelector('#top');
      smoothScroll.animateScroll(anchor);
    }

    $scope.goToMap = function() {
      var anchor = document.querySelector('#mainMap');
      smoothScroll.animateScroll(anchor);
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

    $rootScope.$watch('currentMarker', function() {
      $scope.currentMarker = $rootScope.currentMarker;
    });

    $rootScope.showDetail = function(p) {
      $rootScope.currentMarker = p;
      $rootScope.centerMarkers.push(p);
      // This showDetail is called in rootScope (from ANOTHER controller), so we need to apply to the scope of this controller.
      $scope.$apply();

      p = correctWebLinks(p);
      p.serviceCode = 'resume';

      var anchor = document.querySelector('#top');
      smoothScroll.animateScroll(anchor);
    }

    $http.get('api/v2/countries/ranking').then(function(d) {
      $scope.ranking = d.data;
    });

    $scope.places = [];
    var preData = [];
    var getStats = function() {
      var onPageFinished = function() {
        for (var i = 0; i < preData.length; i++) {
          if (preData[i].aprobado){
            $scope.places.push(preData[i]);
          }
        }
        $rootScope.places = $scope.places;
      };
      var getNextPage = function(url) {
        $http.get(url)
          .then(function(d) {
            preData = preData.concat(d.data.data);
            if (d.data.next_page_url) {
              getNextPage(d.data.next_page_url);
            } else {
              onPageFinished();
            }
          });
      };

      getNextPage(approvedUrl);

    };

    getStats();

  });
