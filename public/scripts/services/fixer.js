// This code is for fixing external libraries bugs.

//Materialize has a bug on select wrappers: they not change color to green when an option is selected.
function addSelectedToSelectWrapper(idDom){
	if ($('#' + idDom).length > 0) {
		if($('#' + idDom).find(':selected').prop('disabled') == false){
			$('#'+ idDom).removeClass('validate ng-pristine ng-untouched initialized ng-invalid ng-invalid-required ng-valid-pattern');
			$('#'+ idDom).addClass('validate ng-touched ng-dirty ng-valid-parse ng-valid ng-valid-required valid');
		}
	}
};