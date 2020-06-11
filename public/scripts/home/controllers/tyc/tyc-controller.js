dondev2App.controller('tycController', function($scope, $rootScope, $http, $translate) {

  // Términos y condiciones
  $scope.setTyC = function() {
    var tyc = $("#tyc");
    if(tyc){
      if($rootScope.selectedLanguage == "es"){
        tyc.load("/scripts/home/controllers/tyc/tyc-spanish.html");  
      }
      else{
        tyc.load("/scripts/home/controllers/tyc/tyc-english.html");
      }
    }
  }

  $rootScope.$watch('selectedLanguage', function(){
    $scope.setTyC();
  });

  $scope.setTyC();

});