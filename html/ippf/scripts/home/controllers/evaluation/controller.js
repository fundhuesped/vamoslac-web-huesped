dondev2App.controller('evaluationController',
	function(NgMap,vcRecaptchaService,placesFactory, $scope, $rootScope, $http, $interpolate, $location, $routeParams, $window, $compile, $translate) {
    console.log('evaluationController');
    $scope.submiteable = false;
    $scope.voto = "";
    $scope.response = null;
    $scope.widgetId = null;
		$scope.placeId = $routeParams.id;
		console.log("placeId : " + $scope.placeId);
		//$scope.services = [{"name":"Prueba VIH","shortname":"prueba"},{"name":"Condones","shortname":"condones"},{"name":"Vacunatorios","shortname":"vacunatorios"},{"name":"Centros de Infectología","shortname":"cdi"},{"name":"Servicios de Salud Sexual y Repoductiva","shortname":"ssr"},{"name":"Interrupción Legal del Embarazo","shortname":"ile"}];
    // $scope.grecaptcha = 99;
		$scope.selectedService = "";
		$scope.services = [];
		$scope.selectedServiceQuestions = [];
		$scope.questionsAndAnswers = [];
		$scope.evaluation = [];
		$scope.evaluation.responses = [];
		$scope.validForm = false;
		$scope.respuestas = {};
		$scope.voto = "";
		$scope.validCheckBoxes = [];
		$scope.exactAgeInput = "";
		$scope.exactAgeRequired = false;
		$scope.cerrado = false;

/*
> "Piqué".latinize();
"Pique"
> "Piqué".isLatin();
false
> "Pique".isLatin();
true
> "Piqué".latinise().isLatin();
true
*/
		var Latinise={};Latinise.latin_map={"Á":"A","Ă":"A","Ắ":"A","Ặ":"A","Ằ":"A","Ẳ":"A","Ẵ":"A","Ǎ":"A","Â":"A","Ấ":"A","Ậ":"A","Ầ":"A","Ẩ":"A","Ẫ":"A","Ä":"A","Ǟ":"A","Ȧ":"A","Ǡ":"A","Ạ":"A","Ȁ":"A","À":"A","Ả":"A","Ȃ":"A","Ā":"A","Ą":"A","Å":"A","Ǻ":"A","Ḁ":"A","Ⱥ":"A","Ã":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ḃ":"B","Ḅ":"B","Ɓ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ć":"C","Č":"C","Ç":"C","Ḉ":"C","Ĉ":"C","Ċ":"C","Ƈ":"C","Ȼ":"C","Ď":"D","Ḑ":"D","Ḓ":"D","Ḋ":"D","Ḍ":"D","Ɗ":"D","Ḏ":"D","ǲ":"D","ǅ":"D","Đ":"D","Ƌ":"D","Ǳ":"DZ","Ǆ":"DZ","É":"E","Ĕ":"E","Ě":"E","Ȩ":"E","Ḝ":"E","Ê":"E","Ế":"E","Ệ":"E","Ề":"E","Ể":"E","Ễ":"E","Ḙ":"E","Ë":"E","Ė":"E","Ẹ":"E","Ȅ":"E","È":"E","Ẻ":"E","Ȇ":"E","Ē":"E","Ḗ":"E","Ḕ":"E","Ę":"E","Ɇ":"E","Ẽ":"E","Ḛ":"E","Ꝫ":"ET","Ḟ":"F","Ƒ":"F","Ǵ":"G","Ğ":"G","Ǧ":"G","Ģ":"G","Ĝ":"G","Ġ":"G","Ɠ":"G","Ḡ":"G","Ǥ":"G","Ḫ":"H","Ȟ":"H","Ḩ":"H","Ĥ":"H","Ⱨ":"H","Ḧ":"H","Ḣ":"H","Ḥ":"H","Ħ":"H","Í":"I","Ĭ":"I","Ǐ":"I","Î":"I","Ï":"I","Ḯ":"I","İ":"I","Ị":"I","Ȉ":"I","Ì":"I","Ỉ":"I","Ȋ":"I","Ī":"I","Į":"I","Ɨ":"I","Ĩ":"I","Ḭ":"I","Ꝺ":"D","Ꝼ":"F","Ᵹ":"G","Ꞃ":"R","Ꞅ":"S","Ꞇ":"T","Ꝭ":"IS","Ĵ":"J","Ɉ":"J","Ḱ":"K","Ǩ":"K","Ķ":"K","Ⱪ":"K","Ꝃ":"K","Ḳ":"K","Ƙ":"K","Ḵ":"K","Ꝁ":"K","Ꝅ":"K","Ĺ":"L","Ƚ":"L","Ľ":"L","Ļ":"L","Ḽ":"L","Ḷ":"L","Ḹ":"L","Ⱡ":"L","Ꝉ":"L","Ḻ":"L","Ŀ":"L","Ɫ":"L","ǈ":"L","Ł":"L","Ǉ":"LJ","Ḿ":"M","Ṁ":"M","Ṃ":"M","Ɱ":"M","Ń":"N","Ň":"N","Ņ":"N","Ṋ":"N","Ṅ":"N","Ṇ":"N","Ǹ":"N","Ɲ":"N","Ṉ":"N","Ƞ":"N","ǋ":"N","Ñ":"N","Ǌ":"NJ","Ó":"O","Ŏ":"O","Ǒ":"O","Ô":"O","Ố":"O","Ộ":"O","Ồ":"O","Ổ":"O","Ỗ":"O","Ö":"O","Ȫ":"O","Ȯ":"O","Ȱ":"O","Ọ":"O","Ő":"O","Ȍ":"O","Ò":"O","Ỏ":"O","Ơ":"O","Ớ":"O","Ợ":"O","Ờ":"O","Ở":"O","Ỡ":"O","Ȏ":"O","Ꝋ":"O","Ꝍ":"O","Ō":"O","Ṓ":"O","Ṑ":"O","Ɵ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Õ":"O","Ṍ":"O","Ṏ":"O","Ȭ":"O","Ƣ":"OI","Ꝏ":"OO","Ɛ":"E","Ɔ":"O","Ȣ":"OU","Ṕ":"P","Ṗ":"P","Ꝓ":"P","Ƥ":"P","Ꝕ":"P","Ᵽ":"P","Ꝑ":"P","Ꝙ":"Q","Ꝗ":"Q","Ŕ":"R","Ř":"R","Ŗ":"R","Ṙ":"R","Ṛ":"R","Ṝ":"R","Ȑ":"R","Ȓ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꜿ":"C","Ǝ":"E","Ś":"S","Ṥ":"S","Š":"S","Ṧ":"S","Ş":"S","Ŝ":"S","Ș":"S","Ṡ":"S","Ṣ":"S","Ṩ":"S","Ť":"T","Ţ":"T","Ṱ":"T","Ț":"T","Ⱦ":"T","Ṫ":"T","Ṭ":"T","Ƭ":"T","Ṯ":"T","Ʈ":"T","Ŧ":"T","Ɐ":"A","Ꞁ":"L","Ɯ":"M","Ʌ":"V","Ꜩ":"TZ","Ú":"U","Ŭ":"U","Ǔ":"U","Û":"U","Ṷ":"U","Ü":"U","Ǘ":"U","Ǚ":"U","Ǜ":"U","Ǖ":"U","Ṳ":"U","Ụ":"U","Ű":"U","Ȕ":"U","Ù":"U","Ủ":"U","Ư":"U","Ứ":"U","Ự":"U","Ừ":"U","Ử":"U","Ữ":"U","Ȗ":"U","Ū":"U","Ṻ":"U","Ų":"U","Ů":"U","Ũ":"U","Ṹ":"U","Ṵ":"U","Ꝟ":"V","Ṿ":"V","Ʋ":"V","Ṽ":"V","Ꝡ":"VY","Ẃ":"W","Ŵ":"W","Ẅ":"W","Ẇ":"W","Ẉ":"W","Ẁ":"W","Ⱳ":"W","Ẍ":"X","Ẋ":"X","Ý":"Y","Ŷ":"Y","Ÿ":"Y","Ẏ":"Y","Ỵ":"Y","Ỳ":"Y","Ƴ":"Y","Ỷ":"Y","Ỿ":"Y","Ȳ":"Y","Ɏ":"Y","Ỹ":"Y","Ź":"Z","Ž":"Z","Ẑ":"Z","Ⱬ":"Z","Ż":"Z","Ẓ":"Z","Ȥ":"Z","Ẕ":"Z","Ƶ":"Z","Ĳ":"IJ","Œ":"OE","ᴀ":"A","ᴁ":"AE","ʙ":"B","ᴃ":"B","ᴄ":"C","ᴅ":"D","ᴇ":"E","ꜰ":"F","ɢ":"G","ʛ":"G","ʜ":"H","ɪ":"I","ʁ":"R","ᴊ":"J","ᴋ":"K","ʟ":"L","ᴌ":"L","ᴍ":"M","ɴ":"N","ᴏ":"O","ɶ":"OE","ᴐ":"O","ᴕ":"OU","ᴘ":"P","ʀ":"R","ᴎ":"N","ᴙ":"R","ꜱ":"S","ᴛ":"T","ⱻ":"E","ᴚ":"R","ᴜ":"U","ᴠ":"V","ᴡ":"W","ʏ":"Y","ᴢ":"Z","á":"a","ă":"a","ắ":"a","ặ":"a","ằ":"a","ẳ":"a","ẵ":"a","ǎ":"a","â":"a","ấ":"a","ậ":"a","ầ":"a","ẩ":"a","ẫ":"a","ä":"a","ǟ":"a","ȧ":"a","ǡ":"a","ạ":"a","ȁ":"a","à":"a","ả":"a","ȃ":"a","ā":"a","ą":"a","ᶏ":"a","ẚ":"a","å":"a","ǻ":"a","ḁ":"a","ⱥ":"a","ã":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ḃ":"b","ḅ":"b","ɓ":"b","ḇ":"b","ᵬ":"b","ᶀ":"b","ƀ":"b","ƃ":"b","ɵ":"o","ć":"c","č":"c","ç":"c","ḉ":"c","ĉ":"c","ɕ":"c","ċ":"c","ƈ":"c","ȼ":"c","ď":"d","ḑ":"d","ḓ":"d","ȡ":"d","ḋ":"d","ḍ":"d","ɗ":"d","ᶑ":"d","ḏ":"d","ᵭ":"d","ᶁ":"d","đ":"d","ɖ":"d","ƌ":"d","ı":"i","ȷ":"j","ɟ":"j","ʄ":"j","ǳ":"dz","ǆ":"dz","é":"e","ĕ":"e","ě":"e","ȩ":"e","ḝ":"e","ê":"e","ế":"e","ệ":"e","ề":"e","ể":"e","ễ":"e","ḙ":"e","ë":"e","ė":"e","ẹ":"e","ȅ":"e","è":"e","ẻ":"e","ȇ":"e","ē":"e","ḗ":"e","ḕ":"e","ⱸ":"e","ę":"e","ᶒ":"e","ɇ":"e","ẽ":"e","ḛ":"e","ꝫ":"et","ḟ":"f","ƒ":"f","ᵮ":"f","ᶂ":"f","ǵ":"g","ğ":"g","ǧ":"g","ģ":"g","ĝ":"g","ġ":"g","ɠ":"g","ḡ":"g","ᶃ":"g","ǥ":"g","ḫ":"h","ȟ":"h","ḩ":"h","ĥ":"h","ⱨ":"h","ḧ":"h","ḣ":"h","ḥ":"h","ɦ":"h","ẖ":"h","ħ":"h","ƕ":"hv","í":"i","ĭ":"i","ǐ":"i","î":"i","ï":"i","ḯ":"i","ị":"i","ȉ":"i","ì":"i","ỉ":"i","ȋ":"i","ī":"i","į":"i","ᶖ":"i","ɨ":"i","ĩ":"i","ḭ":"i","ꝺ":"d","ꝼ":"f","ᵹ":"g","ꞃ":"r","ꞅ":"s","ꞇ":"t","ꝭ":"is","ǰ":"j","ĵ":"j","ʝ":"j","ɉ":"j","ḱ":"k","ǩ":"k","ķ":"k","ⱪ":"k","ꝃ":"k","ḳ":"k","ƙ":"k","ḵ":"k","ᶄ":"k","ꝁ":"k","ꝅ":"k","ĺ":"l","ƚ":"l","ɬ":"l","ľ":"l","ļ":"l","ḽ":"l","ȴ":"l","ḷ":"l","ḹ":"l","ⱡ":"l","ꝉ":"l","ḻ":"l","ŀ":"l","ɫ":"l","ᶅ":"l","ɭ":"l","ł":"l","ǉ":"lj","ſ":"s","ẜ":"s","ẛ":"s","ẝ":"s","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ᵯ":"m","ᶆ":"m","ń":"n","ň":"n","ņ":"n","ṋ":"n","ȵ":"n","ṅ":"n","ṇ":"n","ǹ":"n","ɲ":"n","ṉ":"n","ƞ":"n","ᵰ":"n","ᶇ":"n","ɳ":"n","ñ":"n","ǌ":"nj","ó":"o","ŏ":"o","ǒ":"o","ô":"o","ố":"o","ộ":"o","ồ":"o","ổ":"o","ỗ":"o","ö":"o","ȫ":"o","ȯ":"o","ȱ":"o","ọ":"o","ő":"o","ȍ":"o","ò":"o","ỏ":"o","ơ":"o","ớ":"o","ợ":"o","ờ":"o","ở":"o","ỡ":"o","ȏ":"o","ꝋ":"o","ꝍ":"o","ⱺ":"o","ō":"o","ṓ":"o","ṑ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","õ":"o","ṍ":"o","ṏ":"o","ȭ":"o","ƣ":"oi","ꝏ":"oo","ɛ":"e","ᶓ":"e","ɔ":"o","ᶗ":"o","ȣ":"ou","ṕ":"p","ṗ":"p","ꝓ":"p","ƥ":"p","ᵱ":"p","ᶈ":"p","ꝕ":"p","ᵽ":"p","ꝑ":"p","ꝙ":"q","ʠ":"q","ɋ":"q","ꝗ":"q","ŕ":"r","ř":"r","ŗ":"r","ṙ":"r","ṛ":"r","ṝ":"r","ȑ":"r","ɾ":"r","ᵳ":"r","ȓ":"r","ṟ":"r","ɼ":"r","ᵲ":"r","ᶉ":"r","ɍ":"r","ɽ":"r","ↄ":"c","ꜿ":"c","ɘ":"e","ɿ":"r","ś":"s","ṥ":"s","š":"s","ṧ":"s","ş":"s","ŝ":"s","ș":"s","ṡ":"s","ṣ":"s","ṩ":"s","ʂ":"s","ᵴ":"s","ᶊ":"s","ȿ":"s","ɡ":"g","ᴑ":"o","ᴓ":"o","ᴝ":"u","ť":"t","ţ":"t","ṱ":"t","ț":"t","ȶ":"t","ẗ":"t","ⱦ":"t","ṫ":"t","ṭ":"t","ƭ":"t","ṯ":"t","ᵵ":"t","ƫ":"t","ʈ":"t","ŧ":"t","ᵺ":"th","ɐ":"a","ᴂ":"ae","ǝ":"e","ᵷ":"g","ɥ":"h","ʮ":"h","ʯ":"h","ᴉ":"i","ʞ":"k","ꞁ":"l","ɯ":"m","ɰ":"m","ᴔ":"oe","ɹ":"r","ɻ":"r","ɺ":"r","ⱹ":"r","ʇ":"t","ʌ":"v","ʍ":"w","ʎ":"y","ꜩ":"tz","ú":"u","ŭ":"u","ǔ":"u","û":"u","ṷ":"u","ü":"u","ǘ":"u","ǚ":"u","ǜ":"u","ǖ":"u","ṳ":"u","ụ":"u","ű":"u","ȕ":"u","ù":"u","ủ":"u","ư":"u","ứ":"u","ự":"u","ừ":"u","ử":"u","ữ":"u","ȗ":"u","ū":"u","ṻ":"u","ų":"u","ᶙ":"u","ů":"u","ũ":"u","ṹ":"u","ṵ":"u","ᵫ":"ue","ꝸ":"um","ⱴ":"v","ꝟ":"v","ṿ":"v","ʋ":"v","ᶌ":"v","ⱱ":"v","ṽ":"v","ꝡ":"vy","ẃ":"w","ŵ":"w","ẅ":"w","ẇ":"w","ẉ":"w","ẁ":"w","ⱳ":"w","ẘ":"w","ẍ":"x","ẋ":"x","ᶍ":"x","ý":"y","ŷ":"y","ÿ":"y","ẏ":"y","ỵ":"y","ỳ":"y","ƴ":"y","ỷ":"y","ỿ":"y","ȳ":"y","ẙ":"y","ɏ":"y","ỹ":"y","ź":"z","ž":"z","ẑ":"z","ʑ":"z","ⱬ":"z","ż":"z","ẓ":"z","ȥ":"z","ẕ":"z","ᵶ":"z","ᶎ":"z","ʐ":"z","ƶ":"z","ɀ":"z","ﬀ":"ff","ﬃ":"ffi","ﬄ":"ffl","ﬁ":"fi","ﬂ":"fl","ĳ":"ij","œ":"oe","ﬆ":"st","ₐ":"a","ₑ":"e","ᵢ":"i","ⱼ":"j","ₒ":"o","ᵣ":"r","ᵤ":"u","ᵥ":"v","ₓ":"x"};
		String.prototype.latinise=function(){return this.replace(/[^A-Za-z0-9\[\] ]/g,function(a){return Latinise.latin_map[a]||a})};
		String.prototype.latinize=String.prototype.latinise;
		String.prototype.isLatin=function(){return this==this.latinise()}

		$scope.getAllQuestionsResponses = function(){
			$http({
		  method: 'GET',
		  url: 'api/v2/evaluacion/getallquestionsresponses'
		}).then(function successCallback(response) {
		    console.log("questions response ");
				console.log(response.data);
				$scope.questionsAndAnswers = response.data;
		  }, function errorCallback(response) {
		    // called asynchronously if an error occurs
		    // or server returns response with an error status.
		  });
		};


		$scope.getServices = function(placeId){
			$http({
			method: 'GET',
			url: 'api/v2/service/getPlaceServices/' + placeId
		}).then(function successCallback(response) {
				$scope.services = response.data;
				console.log("services");
				console.log($scope.services);
			}, function errorCallback(response) {
				console.log("error response " + response);
				Materialize.toast("Ha ocurrido un problema, inténtelo nuevamente mas tarde");
			});
		};
		$scope.getAllQuestionsResponses();
		$scope.getServices($scope.placeId);
    $scope.model = {
      key: '6Le6vhMUAAAAANvNw1nNOf6R_O8RuKFcCGv5IZzj'
    };

    $scope.setResponse = function (response) {
        // console.info('Response available');
        $scope.captchaResponse = response;
				console.log("captcha response " + response);
        $scope.formValidator();
    };

    $scope.setWidgetId = function (widgetId) {
        // console.info('Created widget ID: %s', widgetId);

        $scope.widgetId = widgetId;
    };

    $scope.cbExpiration = function() {
        console.info('Captcha expired. Resetting response object');

        vcRecaptchaService.reload($scope.widgetId);

        $scope.response = null;
     };

    // console.log('Cargo el return en');
    // console.log($rootScope.returnTo)




  function unCheckedCaptcha() {
    var flagC = true;
    // if (grecaptcha.getResponse().length == 0){
    if ((typeof $scope.captchaResponse === "undefined")  || ($scope.captchaResponse == null) || ($scope.captchaResponse.length == 0)){
      console.log('No checkeado captcha')
    }
    else{
      console.log('Si checkeado captcha')
      flagC = false;
    }
    return flagC;
  }

  // function test (response) {
  //   $scope.grecaptcha = response;
     // console.log(response);
  // }
	$scope.checkboxValidator = function(){
		var result = true;
		$scope.validCheckBoxes.forEach(function(checkbox){
			if (!checkbox) result = false;
		})
		return result;
	}

function validAge(){
	var response = true;
	var index = $scope.evaluation.responses.map(function(questions) {
			return questions.evaluation_column;
	}).indexOf("edad");
	if (index >= 0) {
		console.log("index edad > 0");
			qId = $scope.evaluation.responses[index].questionId;
			var edad = $("#selectbox_" + qId + " option:selected").text();
			if (edad == "10 a 19") {
					console.log("$scope.especificAge " + $scope.exactAgeInput);
					if ($scope.exactAgeInput === "null" || (typeof $scope.exactAgeInput === "undefined") || $scope.exactAgeInput < 10 || $scope.exactAgeInput > 19) {
						console.log("$scope.especificAge out of range");
							response = false;
					}
			}

		}
		else {
				console.log("index edad <> 0");
				response = false;
		}
		return response;
}

function validGenre(){
	var response = true;
	index = $scope.evaluation.responses.map(function(questions) {
			return questions.evaluation_column;
	}).indexOf("genero");
	if (index < 0) response = false;

return response;
}

function validWhatAreYouLookingFor(){
	var response = true;
	var index = $scope.evaluation.responses.map(function(questions) {
			return questions.evaluation_column;
	}).indexOf("que_busca");
	if (index < 0) reponse = false;
	return response;
}

$scope.closedPlaceFormValidator = function(){
	console.log("closedPlaceFormValidator");
	$scope.validForm = (validGenre() && validWhatAreYouLookingFor() && validAge() && $scope.checkboxValidator() && (!unCheckedCaptcha()) && ((typeof $scope.voto != "undefined" && $scope.voto !== "" && $scope.voto != "null")));
	console.log("$scope.validForm " + $scope.validForm);
	return $scope.validForm;
	}

	$scope.formValidator = function() {
		if ($scope.cerrado) {
				$scope.closedPlaceFormValidator();
				return;
		}
		else{
			var auxValid = true;
			if (typeof $scope.evaluation.responses == "undefined"){
				console.log("$scope.evaluation.responses undefined");
				auxValid = false;
			} else if($scope.evaluation.responses.length != $scope.selectedServiceQuestions.length){
					console.log("$scope.evaluation.responses.length != $scope.selectedServiceQuestions.length");
					auxValid = false;
			}else if(typeof $scope.voto == "undefined" || $scope.voto === "" || $scope.voto === "null"){
					console.log("$scope.evaluation.responses.length != $scope.selectedServiceQuestions.length");
					auxValid = false;
			}
			 else if(unCheckedCaptcha()){
				console.log("unCheckedCaptcha");
				auxValid = false;
			} else if (!$scope.checkboxValidator()){
				console.log("no valids checkboxes");
				auxValid = false;
			} else if($scope.exactAgeRequired){
				console.log("especificAge " + $("#edadExacta").val());
				console.log("$scope.exactAgeInput " + $scope.exactAgeInput);
				if ((typeof $scope.exactAgeInput === "undefined") || ($scope.exactAgeInput === "null") || ($scope.exactAgeInput < 10) || ($scope.exactAgeInput > 19)){
					console.log("especificAge invalid");
					auxValid = false;
				}
			}
			$scope.validForm = auxValid;

			return $scope.validForm;
		}

  }


    //para que funcione el select
    $(document).ready(function() {
        $('select').material_select();
    });


  var urlCopy = "api/v2/evaluacion/comentarios/" + $routeParams.id;
   $http.get(urlCopy).then(foundBacon);
    function foundBacon(response) {
       console.log('Copy evaluation establecimeito')
			 //console.log(response.data[0]);
      $scope.establecimiento = response.data[0].establecimiento;
   };


    $scope.iconList = [
        { id: '1', image: '1', imageDefault: '1', imageBacon: '1active', active: false, vote: 1 },
        { id: '2', image: '2', imageDefault: '2', imageBacon: '2active', active: false, vote: 2 },
        { id: '3', image: '3', imageDefault: '3', imageBacon: '3active', active: false, vote: 3 },
        { id: '4', image: '4', imageDefault: '4', imageBacon: '4active', active: false, vote: 4 },
        { id: '5', image: '5', imageDefault: '5', imageBacon: '5active', active: false, vote: 5 }];

    $scope.evaluation = {};
    var queBuscaste = [];

      $scope.setVote = function (id) {
         var pos = 0;
         for (var i = 0; i < $scope.iconList.length; i++) {
            $scope.iconList[i].active = false;
            $scope.iconList[i].image = $scope.iconList[i].imageDefault;
            if ($scope.iconList[i].id == id) pos = i;
         }
         console.warn("seleccionado pos:" + pos+ "Valor de voto:" + $scope.iconList[pos].vote);
         $scope.iconList[pos].active = true;
         $scope.iconList[pos].image = $scope.iconList[pos].imageBacon;
         //$scope.evaluation.voto = $scope.iconList[pos].vote;
				 $scope.voto = $scope.iconList[pos].vote;
				 console.log("$scope.respuestas.voto : " + $scope.voto);
         // formChange();
         $scope.formValidator();
      }

      $scope.cerrar = function () {
        // $window.location.reload();
        window.history.go(-3);

      }

			$scope.clicky = function(evaluation) {

				$scope.respuestas = {};
				$scope.respuestas.que_busca = "";
				var qId;
				var index = $scope.evaluation.responses.map(function(questions) {
						return questions.evaluation_column;
				}).indexOf("que_busca");
				if (index >= 0) {
						$scope.evaluation.responses[index].options.forEach(function(selectedOption) {
								queBuscaste.push(selectedOption.optionBody);
						})
						$scope.respuestas.que_busca = queBuscaste.join(", ");
				}
				console.log("$scope.respuestas.que_busca : " + $scope.respuestas.que_busca);


				$scope.respuestas.le_dieron = "";
				index = $scope.evaluation.responses.map(function(questions) {
						return questions.evaluation_column;
				}).indexOf("le_dieron");
				if (index >= 0) {
						qId = $scope.evaluation.responses[index].questionId;
						$scope.respuestas.le_dieron = $("#selectbox_" + qId + " option:selected").text();
				}

				console.log("$scope.respuestas.le_dieron : " + $scope.respuestas.le_dieron);


				$scope.respuestas.privacidad_ok = "";
				index = $scope.evaluation.responses.map(function(questions) {
						return questions.evaluation_column;
				}).indexOf("privacidad_ok");
				if (index >= 0) {
						qId = $scope.evaluation.responses[index].questionId;
						$scope.respuestas.privacidad_ok = $scope.responses[qId];
						if ($scope.respuestas.privacidad_ok.length > 0) {
								if ($scope.respuestas.privacidad_ok.toLowerCase() == "si") $scope.respuestas.privacidad_ok = 1;
								else $scope.respuestas.privacidad_ok = 0;

						} else console.log("$scope.respuestas.privacidad_ok.length  no es > 0");
				}
				console.log("$scope.respuestas.privacidad_ok : " + $scope.respuestas.privacidad_ok);

				$scope.respuestas.info_ok = "";
				index = $scope.evaluation.responses.map(function(questions) {
						return questions.evaluation_column;
				}).indexOf("info_ok");
				if (index >= 0) {
						qId = $scope.evaluation.responses[index].questionId;
						$scope.respuestas.info_ok = $scope.responses[qId];
						if ($scope.respuestas.info_ok.length > 0) {
							$scope.respuestas.info_ok = $scope.respuestas.info_ok.toLowerCase();
							$scope.respuestas.info_ok = $scope.respuestas.info_ok.latinize();
							console.log("$scope.respuestas.info_ok before magic" + $scope.respuestas.info_ok);
								if ($scope.respuestas.info_ok.indexOf("si") >= 0) $scope.respuestas.info_ok = 1;
								else $scope.respuestas.info_ok = 0;

						} else console.log("$scope.respuestas.info_ok.length  no es > 0");
				}
				console.log("$scope.respuestas.info_ok : " + $scope.respuestas.info_ok);

				$scope.respuestas.edad = "";
					index = $scope.evaluation.responses.map(function(questions) {
							return questions.evaluation_column;
					}).indexOf("edad");
					if (index >= 0) {
						console.log("entro en index edad > 0");
							qId = $scope.evaluation.responses[index].questionId;
							$scope.respuestas.edad = $("#selectbox_" + qId + " option:selected").text();
							if ($scope.respuestas.edad == "10 a 19") $scope.respuestas.edad = $scope.exactAgeInput;

					}
					else console.log("index edad NO ES > 0");
					console.log("$scope.respuestas.edad : " + $scope.respuestas.edad);
					console.log("$scope.respuestas.edadExacta : " + $scope.respuestas.edadExacta);


				$scope.respuestas.genero = "";
				index = $scope.evaluation.responses.map(function(questions) {
						return questions.evaluation_column;
				}).indexOf("genero");
				if (index >= 0) {
						qId = $scope.evaluation.responses[index].questionId;
						$scope.respuestas.genero = $("#selectbox_" + qId + " option:selected").text();
				}
				console.log("$scope.respuestas.genero : " + $scope.respuestas.genero);


				$scope.respuestas.es_gratuito = 0;
				index = $scope.evaluation.responses.map(function(questions) {
						return questions.evaluation_column;
				}).indexOf("es_gratuito");
				if (index >= 0) {
						qId = $scope.evaluation.responses[index].questionId;
						$scope.respuestas.es_gratuito = $scope.responses[qId];
						if ($scope.respuestas.es_gratuito.length > 0) {
								if ($scope.respuestas.es_gratuito.toLowerCase() == "si") $scope.respuestas.es_gratuito = 1;
								else $scope.respuestas.es_gratuito = 0;

						} else console.log("$scope.respuestas.es_gratuito.length  no es > 0");
				}
				console.log("$scope.respuestas.es_gratuito : " + $scope.respuestas.es_gratuito);


				$scope.respuestas.comodo = 0;
				index = $scope.evaluation.responses.map(function(questions) {
						return questions.evaluation_column;
				}).indexOf("comodo");
				if (index >= 0) {
						qId = $scope.evaluation.responses[index].questionId;
						$scope.respuestas.comodo = $scope.responses[qId];
						if ($scope.respuestas.comodo.length > 0) {
								if ($scope.respuestas.comodo.toLowerCase() == "si") $scope.respuestas.comodo = 1;
								else $scope.respuestas.comodo = 0;

						} else console.log("$scope.respuestas.comodo.length  no es > 0");
				}
				console.log("$scope.respuestas.comodo : " + $scope.respuestas.comodo);



				$scope.respuestas.informacion_vacunas = 0;
				index = $scope.evaluation.responses.map(function(questions) {
						return questions.evaluation_column;
				}).indexOf("informacion_vacunas");
				if (index >= 0) {
						qId = $scope.evaluation.responses[index].questionId;
						$scope.respuestas.informacion_vacunas = $scope.responses[qId];
						if ($scope.respuestas.informacion_vacunas.length > 0) {
								if ($scope.respuestas.informacion_vacunas.toLowerCase() == "si") $scope.respuestas.informacion_vacunas = 1;
								else $scope.respuestas.informacion_vacunas = 0;

						} else console.log("$scope.respuestas.informacion_vacunas.length  no es > 0");
				}
				console.log("$scope.respuestas.informacion_vacunas : " + $scope.respuestas.informacion_vacunas);

				$scope.respuestas.idPlace = $routeParams.id;
				console.log("$scope.respuestas.idPlace : " +  $scope.respuestas.idPlace);
				$scope.respuestas.voto = $scope.voto;
				console.log("$scope.respuestas.voto : " + $scope.respuestas.voto);

				$scope.respuestas.service = $scope.selectedService;
				$scope.services.forEach(function(service) {
						if (service.id == $scope.respuestas.service){
							$scope.respuestas.serviceShortName = service.shortname.toLowerCase();
							console.log(" $scope.respuestas.serviceShortName  " +  $scope.respuestas.serviceShortName );
						}
				})
				console.log("$scope.respuestas.service : " + $scope.respuestas.service);
				$scope.respuestas.comments = $("#comments").val();
				console.log("$scope.respuestas.comments : " + $("#comments").val());

				console.log("$scope.respuestas ");
				console.log($scope.respuestas);
				$http.post('api/v2/evaluacion/votar', $scope.respuestas)
						.then(function(response) {
										console.log("response.data");
										console.log(response.data);
										if (response.data.length === 0) {
												Materialize.toast('Calificación enviada!', 5000);
												document.location.href = "#voted/" + $scope.respuestas.idPlace;
												queBuscaste = [];
												$scope.responses = [];
												$scope.selectedServiceQuestions = [];
												$scope.evaluation.responses = [];
												$scope.respuestas = {};
										} else {
												for (var propertyName in response.data) {
														Materialize.toast(response.data[propertyName], 8000);
												}
										}
								},
								function(response) {
										console.log('fallo')
										Materialize.toast('Intenta nuevamente mas tarde.', 5000);
								});

		}

$scope.actualQuestion={};

$scope.selectedServiceChange = function() {
	queBuscaste = [];
	$scope.responses = [];
	$scope.evaluation.responses = [];
	$scope.respuestas = {};
  $scope.selectedServiceQuestions = [];
	$scope.validCheckBoxes = [];
	$scope.validForm = false;

    $("#evaluation").empty();

    $scope.cont = 0;
    var aux = false;
    $scope.questionsAndAnswers.forEach(function(question) {
        aux = false;
        question.services.forEach(function(service) {
            if (service.id == $scope.selectedService) aux = true;
        });
        console.log("aux : " + aux);
        if (aux) {
            $scope.selectedServiceQuestions.push(question);
            console.log("question.type : " + question.type);
						var htmlTitleSelectBox = "";
            if (question.type == 'selectbox') {
							if ((question.evaluation_column != "edad") && (question.evaluation_column != "genero") && (question.evaluation_column != "le_dieron")) htmlTitleSelectBox = '<div class="block" ng-hide="cerrado"><p class="blockTitle">' + question.body + ' </p><div id="exactAge_'+ question.id +'"><select class="blockContent browser-default right-alert" ng-model="responses['+ question.id +']" ng-change="selectBoxChange(' + question.id + ',\'' + question.evaluation_column + '\')" id="selectbox_' + question.id + '"></div>';
							else htmlTitleSelectBox = '<div class="block"><p class="blockTitle">' + question.body + ' </p><div id="exactAge_'+ question.id +'"><select class="blockContent browser-default right-alert" ng-model="responses['+ question.id +']" ng-change="selectBoxChange(' + question.id + ',\'' + question.evaluation_column + '\')" id="selectbox_' + question.id + '"></div>';
                var appendHtml = $compile(htmlTitleSelectBox)($scope);
                $("#evaluation").append(appendHtml);
							$('#selectbox_' + question.id + ' option[value="default"]').attr('selected', 'selected');
							$('#selectbox_' + question.id + ' option[value=default]').prop('selected', 'selected');
                question.options.forEach(function(option) {
                    var optionsHtml = '<option value="' + option.id + '">' + option.body + '</option> </select></div>';
                    var appendHtml = $compile(optionsHtml)($scope);
                    $("#selectbox_" + question.id).append(appendHtml);
                });
            } else if (question.type == 'checkbox') {
								var tittle = "";
								if ((question.evaluation_column != "que_busca"))
                tittle = '<div class="block" ng-hide="cerrado"><p class="blockTitle">' + question.body + '</p><p class="blockContent" id="checkbox_' + $scope.cont + '"></p></div>';
								else tittle = '<div class="block"><p class="blockTitle">' + question.body + '</p><p class="blockContent" id="checkbox_' + $scope.cont + '"></p></div>';

                $("#evaluation").append(tittle);
                question.options.forEach(function(option) {
                    var optionsHtml = '<input type="checkbox" name="' + question.id + '"  id="' + question.id + '' + option.id + '" value="' +
                    option.id + '" ng-model="responses[' + question.id + '][' + option.id + ']" ng-change="checkBoxChange(' + question.id + ',' + option.id + ',\'' + question.evaluation_column + '\',\'' + option.body + '\')"/><label for="' + question.id + '' + option.id + '">' + option.body + '</label><br>';
                    var appendHtml = $compile(optionsHtml)($scope);
                    $("#checkbox_" + $scope.cont).append(appendHtml);
                });

            } else if (question.type == 'radiobox') {
								var appendHtml = "";
                var tittle = '<div class="block" ng-hide="cerrado"><p class="blockTitle">' + question.body + '</p><p class="blockContent" id="radiobox_' + question.id + '"></p></div>';
								appendHtml = $compile(tittle)($scope);
                $("#evaluation").append(appendHtml);
                question.options.forEach(function(option) {
									var optionsHtml = '<input id="' + question.id + '' + option.id + '" ng-model="responses[' + question.id + ']" class="with-gap" name="' + question.id + '" type="radio" value="' + option.body + '"  ng-change="radioBoxChange(' + question.id + ',\'' + question.evaluation_column + '\')"/><label for="' + question.id + '' + option.id + '">' + option.body + '</label>'
                  appendHtml = $compile(optionsHtml)($scope);
                  $("#radiobox_" + question.id).append(appendHtml);
                });

            } else if (question.type == 'number') {
								var htmlQuestion =	'<div class="block"><p class="blockTitle">' + question.body + '</p>	 <div class="blockContent"><input type="number" name="edad" id="number_' + question.id + '" placeholder="Escribí en números" ng-model="responses[' + question.id + ']" class="validate" ng-change="numberBoxChange(' + question.id + ',\'' + question.evaluation_column + '\')" required="required"/>						  </div></div>'
                var appendHtml = $compile(htmlQuestion)($scope);
                $("#evaluation").append(appendHtml);
            };


            if (question.type == 'text') {
                var appendHtml = $compile('<div radio-Box></div>')($scope);
            };
            //divElement.append(appendHtml);
            console.log("question ");
            console.log(question);

            $scope.cont++;
        };
    });
}


$scope.checkBoxChange = function(questionId, optionId, evaluation_column, optionBody){
	console.log("questionId selected");
	console.log(questionId);
	console.log("evaluation_column");
	console.log(evaluation_column);
	if ((typeof $scope.evaluation.responses != "undefined") && ($scope.evaluation.responses != "null") && ($scope.evaluation.responses.length > 0)){
			console.log("responses.lenght > 0");
		var index = $scope.evaluation.responses.map(function(questions) { return questions.questionId; }).indexOf(questionId);
		console.log("map index " + index);
		if (index >= 0){
				console.log("index > 0 : " + index );
		//	var indexAux = $scope.evaluation.responses[index].options.indexOf(optionId);
			var indexAux = $scope.evaluation.responses[index].options.map(function(option) { return option.optionId; }).indexOf(optionId);
			if (indexAux >= 0) {
				console.log("indexAux > 0 " + indexAux );
				$scope.evaluation.responses[index].options.splice(indexAux, 1);
				if (($scope.evaluation.responses[index].options.length != 0)) $scope.validCheckBoxes[questionId] = true;
				else $scope.validCheckBoxes[questionId] = false;
			}
			else{
				$scope.evaluation.responses[index].options.push({'optionId':optionId, 'optionBody': optionBody});
				if (($scope.evaluation.responses[index].options.length != 0)) $scope.validCheckBoxes[questionId] = true;
				else $scope.validCheckBoxes[questionId] = false;
			}
		}
		else {
			$scope.evaluation.responses.push({'questionId': questionId, 'questionType': 'checkbox','evaluation_column': evaluation_column, 'options': [{'optionBody':optionBody,'optionId':optionId}]});
			$scope.validCheckBoxes[questionId] = true;
		}
	}
	else {
		$scope.evaluation.responses = [{'questionId': questionId, 'questionType': 'checkbox', 'evaluation_column': evaluation_column,'options': [{'optionBody':optionBody,'optionId':optionId}]}];
		$scope.validCheckBoxes[questionId] = true;
	}

	console.log("$scope.evaluation.responses");
	console.log($scope.evaluation.responses);
	if ($scope.cerrado) $scope.closedPlaceFormValidator();
	else $scope.formValidator();
}

$scope.selectBoxChange = function(questionId, evaluation_column){
	console.log("questionId selected");
	console.log(questionId);
	if ((typeof $scope.evaluation.responses != "undefined") && ($scope.evaluation.responses != "null") && ($scope.evaluation.responses.length > 0)){
		console.log("responses.lenght > 0");
		var index = $scope.evaluation.responses.map(function(questions) { return questions.questionId; }).indexOf(questionId);
		console.log("map index " + index);
		if (index >= 0){
			console.log("index > 0");
			$scope.evaluation.responses[index].questionId = questionId;
			$scope.evaluation.responses[index].questionType = "selectbox";
			$scope.evaluation.responses[index].evaluation_colmun = evaluation_column;
			$scope.evaluation.responses[index].options[0] = $scope.responses[questionId];
		}
		else $scope.evaluation.responses.push({'questionId': questionId, 'questionType': 'selectbox', 'evaluation_column': evaluation_column, 'options': [$scope.responses[questionId]]});
	}
	else $scope.evaluation.responses = [{'questionId': questionId, 'questionType': 'selectbox' , 'evaluation_column': evaluation_column,'options': [$scope.responses[questionId]]}];

	console.log("$scope.evaluation.responses");
	console.log($scope.evaluation.responses);
	if (evaluation_column == 'edad'){
		var edad = $("#selectbox_" + questionId + " option:selected").text();
		if (edad == "10 a 19") {
			var htmlEdadEspecifica =	'<div class="block" id="exactAgeBlock"><p class="blockTitle">Edad específica</p>	 <div class="blockContent"><input type="number" name="edadExacta" id="edadExacta" placeholder="Escribí en números" ng-model="exactAgeInput" class="validate" ng-blur="formValidator()" ng-change="formValidator()" min="10" max="19" step="1" required="required"/></div></div>'
			var appendHtml = $compile(htmlEdadEspecifica)($scope);
			$("#exactAge_" + questionId).append(appendHtml);
			$scope.exactAgeRequired = true;
		} else {
				$("#exactAgeBlock").remove();
				$scope.exactAgeRequired = false;
				$scope.exactAgeInput = "";
		}
	}
	if (evaluation_column == 'le_dieron') {
		console.log("entra a le dieron");
		var leDieronText = $("#selectbox_" + questionId + " option:selected").text();
		console.log("leDieronText : " + leDieronText.toLowerCase());
		leDieronText = leDieronText.toLowerCase();
		leDieronText = leDieronText.latinize();
		if (leDieronText.indexOf("cerrado") >= 0) $scope.cerrado = true;
		else $scope.cerrado = false;
		console.log("cerrado " + $scope.cerrado);
	}
	if ($scope.cerrado) $scope.closedPlaceFormValidator();
	else $scope.formValidator();
}

$scope.radioBoxChange = function(questionId, evaluation_column){
	//var resp = $('input[name="' + aux + '"]:checked').val();
	console.log("questionId selected");
	console.log(questionId);
	if ((typeof $scope.evaluation.responses != "undefined") && ($scope.evaluation.responses != "null") && ($scope.evaluation.responses.length > 0)){
		console.log("responses.lenght > 0");
		var index = $scope.evaluation.responses.map(function(questions) { return questions.questionId; }).indexOf(questionId);
		console.log("map index " + index);
		if (index >= 0){
			console.log("index > 0");
			$scope.evaluation.responses[index].questionId = questionId;
			$scope.evaluation.responses[index].questionType = "rabiobox";
			$scope.evaluation.responses[index].evaluation_column = evaluation_column;
			$scope.evaluation.responses[index].options[0] = $scope.responses[questionId];
		}
		else $scope.evaluation.responses.push({'questionId': questionId, 'questionType': 'rabiobox', 'evaluation_column': evaluation_column,'options': [$scope.responses[questionId]]});
	}
	else $scope.evaluation.responses = [{'questionId': questionId, 'questionType': 'rabiobox' , 'evaluation_column': evaluation_column,'options': [$scope.responses[questionId]]}];

	console.log("$scope.evaluation.responses");
	console.log($scope.evaluation.responses);

	if ($scope.cerrado) $scope.closedPlaceFormValidator();
	else $scope.formValidator();
}

$scope.numberBoxChange = function(questionId, evaluation_column){
	//var resp = $('input[name="' + aux + '"]:checked').val();
	console.log("value");
	var number = $("#number_" + questionId).val();
	console.log(number);
	console.log("cuestionId " + questionId);
	if ((typeof $scope.evaluation.responses != "undefined") && ($scope.evaluation.responses != "null") && ($scope.evaluation.responses.length > 0)){
		console.log("responses.lenght > 0");
		var index = $scope.evaluation.responses.map(function(questions) { return questions.questionId; }).indexOf(questionId);
		console.log("map index " + index);
		if (index >= 0){
			console.log("entra por index >= 0");
			$scope.evaluation.responses[index] = {'questionId': questionId, 'questionType': 'number' ,'evaluation_column': evaluation_column, 'options': [number]};
		}
		else{
			console.log("entra por index < 0");
			$scope.evaluation.responses.push({'questionId': questionId, 'questionType': 'number' ,'evaluation_column': evaluation_column,'options': [number]});
		}
	}
	else{
		console.log("entra por scope undefined");
		$scope.evaluation.responses = [{'questionId': questionId, 'questionType': 'number', 'evaluation_column': evaluation_column,'options': [number]}];
	}

	console.log("$scope.evaluation.responses");
	console.log($scope.evaluation.responses);
	if ($scope.cerrado) $scope.closedPlaceFormValidator();
	else $scope.formValidator();
}

});


dondev2App.directive('checkBox', function() {
  return {

    templateUrl: './scripts/home/controllers/evaluation/my-checkbox.html'
}
});
