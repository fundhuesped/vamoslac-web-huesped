dondev2App.controller('navController', function($scope, $rootScope, $http, $translate) {

	initLang();
	window.onload = function() {
		$scope.changeLanguage();
	};

	function initLang(){
		var lang = localStorage.getItem("lang");
		if(lang === undefined || lang === null || lang == ""){
			lang = "es";
			localStorage.setItem("selectedByUser", false);
			localStorage.setItem("lang", lang);
		}
		$rootScope.selectedLanguage = $scope.selectedLanguage = lang;
	}

	$rootScope.$watch('navigating', function(){
		$scope.navigating = $rootScope.navigating;
	});

	$scope.changeLanguage = function() {
		var lang = $scope.selectedLanguage;
		$rootScope.selectedLanguage = lang;

		localStorage.setItem("lang", lang);
		localStorage.setItem("selectedByUser", true);
		$translate.use(lang);
		$http.get('/changelang/' + lang)
		.success(function(response) {
			if (response.status.toLowerCase() != 'ok'){
				Materialize.toast('Intenta nuevamente mas tarde.', 5000);
			}
		})
		.error(function(response) {
			Materialize.toast('Intenta nuevamente mas tarde.', 5000);
		});
		return;
	}
});
