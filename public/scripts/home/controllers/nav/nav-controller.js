dondev2App.controller('navController', function($scope, $rootScope, $http, $translate) {

	initLang();

	function initLang(){
		var lang = localStorage.getItem("lang");
		if(lang === undefined || lang === null || lang == "")
			lang = "es";
		$rootScope.selectedLanguage = $scope.selectedLanguage = lang;
	}

	$scope.changeLanguage = function() {
		var lang = $scope.selectedLanguage;
		$rootScope.selectedLanguage = lang;

		localStorage.setItem("lang", lang);
		localStorage.setItem("selectedByUser", true);
		$translate.use(lang);
		$http.get('changelang/' + lang)
		.success(function(response) {
			if (response.statusText.toLowerCase() != 'ok'){
				Materialize.toast('Intenta nuevamente mas tarde.', 5000);
			}
		})
		.error(function(response) {
			Materialize.toast('Intenta nuevamente mas tarde.', 5000);
		});
		return;
	}
});
