// Obtenido de: https://jsfiddle.net/rkLjf5x9

dondev2App.factory('autocompleteService', function() {

  var autocomplete;
  var componentForm = {
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    administrative_area_level_2: 'short_name',
    country: 'long_name',
  };

  var factory = {
    initAutocomplete: function(cb) {
      if(!document.getElementById('autocomplete'))
        return;
      autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('autocomplete'), {types: ['geocode']});
      autocomplete.setFields(['address_component']);
      autocomplete.addListener('place_changed', cb);
    },
    fillInCity: function(){
      var autocompleteForm = [];
      var place = autocomplete.getPlace();
      for (var i = 0; i < place.address_components.length; i++) {
        var addressType = place.address_components[i].types[0];
        if (componentForm[addressType]) {
          var val = place.address_components[i][componentForm[addressType]];
          autocompleteForm[i] = val;
        }
      }
      return autocompleteForm;
    }
  }

  return factory;
});