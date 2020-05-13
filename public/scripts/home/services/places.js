'use strict';

/* Filters */
dondev2App.factory('placesFactory', function($http, $filter) {

	var factory = {

		countries:[],
		provinces:[],
		cities:[],
		ciudades:[],
		establecimientos:[],
		cercanos:[],

		getAll: function(cb){
			var places =[];
			cb(places);
		},

		forLocation: function(l,cb){
	  		$http.get('api/v1/places/geo/' + l.latitude + '/' + l.longitude)
				.success(function(cercanos){
					cb(cercanos);
			});
		},

		load: function(cb){
			cb();
		},

		getCountriesFilterByUser:function(cb){
			$http.get('api/v1/countries/byuser')
				.success(function(countries){
					factory.countries = countries;
					cb(countries);
			});
		},

		getCountries:function(cb){
			$http.get('api/v1/panel/countries/all')
				.success(function(countries){
					factory.countries = countries;
					cb(countries);
			});
		},

		getProvincesForCountry:function(p,cb){
			$http.get('api/v1/panel/countries/'+ p +'/provinces')
				.success(function(provinces){
					factory.provinces[p] = provinces;
					cb(provinces);
			});

		},

		getPartidosForProvince:function(p,cb){
			$http.get('api/v1/panel/provinces/'+ p + '/districts')
				.success(function(cities){
					factory.cities[p] = cities;
					cb(cities);
			});

		},

		getCitiesForPartidos: function(p,cb){
			$http.get('api/v1/panel/districts/'+ p.id +'/cities')
				.success(function(cities){
					factory.cities[p] = cities;
					cb(cities);
			});

		},

		getAllFor:function(s,cb){
			$http.get('api/v1/places/'+ s.pais +'/'+  s.provincia +'/'+ s.partido + '/' + s.ciudad + '/' + s.service)
				.success(function(places){
					cb(places);

			});
		},

		getCitiesForProvince: function(p,cb){
			$http.get('api/v1/panel/provinces/'+ p.id +'/cities')
				.success(function(cities){
					factory.cities[p] = cities;
					cb(cities);
			});

		},

		getPlacesByParty: function(p,cb){
			$http.get('api/v1/places/'+ p.partido + '/' + p.service)
				.success(function(establecimientos){
					factory.establecimientos[p] = establecimientos;
					cb(establecimientos);
			});
		},

		getPlacesByName: function(name, servicio, cb){
			$http.get('api/v1/places/search/'+ name + '/' + servicio )
				.success(function(establecimientos){
					factory.establecimientos[name] = establecimientos;
					cb(establecimientos);
					//console.log(establecimientos);
			});
		}
	}


	return factory;
});
