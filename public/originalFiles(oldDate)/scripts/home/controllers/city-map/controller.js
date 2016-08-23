dondev2App.controller('cityMapController', 
	function(placesFactory,NgMap, copyService, $scope,$rootScope, $routeParams, $location, $http){
	$rootScope.main = false;
	$rootScope.geo = false;
	$scope.province = $routeParams.provincia.split('-')[1];
		$scope.provinceId = $routeParams.provincia.split('-')[0];
		$scope.city = $routeParams.ciudad.split('-')[1];
		$scope.cityId = $routeParams.ciudad.split('-')[0];
		$scope.country = $routeParams.pais.split('-')[1];
		$scope.countryId = $routeParams.pais.split('-')[0];
		
		$scope.service = copyService.getFor($routeParams.servicio);
		$rootScope.navBar =$scope.service ;
		var search = {
			provincia:	$scope.provinceId,
			partido:	$scope.cityId,
			pais: $scope.countryId
			
		};$
		search[$routeParams.servicio.toLowerCase()] = true;
		
	$scope.showCurrent = function(i,p){
      $rootScope.navBar = p.establecimiento;
      $scope.currentMarker = p;
    }
    $scope.closeCurrent = function(){
      $scope.currentMarker = undefined;
    }

    if ($rootScope.places.length > 0 && $rootScope.currentMarker){
    	console.log($rootScope.currentMarker);
    	$rootScope.moveMapTo = {
			latitude:$rootScope.currentMarker.latitude,
			longitude:$rootScope.currentMarker.longitude,
			zoom:14,
			center: true,
		};
    }else {
		placesFactory.getAllFor(search, function(data){
			$rootScope.places = $scope.places = data;
			$rootScope.currentMarker = $scope.currentMarker = $scope.places[0];
			console.log($rootScope.currentMarker);
			$rootScope.moveMapTo = {
				latitude:$rootScope.currentMarker.latitude,
				longitude:$rootScope.currentMarker.longitude,
				zoom:14,
				center: true,
			};
			
		})
	}
	$rootScope.$watch('currentMarker',function(){
		 $scope.currentMarker = $rootScope.currentMarker;
	})



});