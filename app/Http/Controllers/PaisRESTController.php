<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Pais;
use App\Provincia;
use App\Partido;
use App\Ciudad;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class PaisRESTController extends Controller{

    public function create(){

    }

    public function store(Request $request){

    }

    public function show($id){
        return Pais::find($id);
    }

    public function edit($id){

    }

    public function update(Request $request, $id){

    }

    public function destroy($id){

    }

    public function getCountriesByUser(){
        $countries;
        if (\Auth::user()->roll == 'administrador') {
            $countries = Pais::all();
        }
        else{
            $userId = \Auth::user()->id;
            $countries = DB::table('pais')
            ->join('user_country', 'user_country.id_country', '=', 'pais.id')
            ->where('user_country.id_user', $userId)
            ->get();
        }
        return $countries;
    }

    public function getAll(){
        return Pais::all();
    }

    public function getProvinces($id){
        $provincias = Provincia::where('idPais', '=', $id)
        ->orderBy('nombre_provincia')
        ->get();
        return $provincias;
    }

    public function getDistricts($id){
        $partidos = Partido::where('idProvincia', $id)
        ->orderBy('nombre_partido')
        ->get();
        return $partidos;
    }     

    public function getCities($id){
        $cities = Ciudad::where('idPartido', $id)
        ->orderBy('nombre_ciudad')
        ->get();
        return $cities;
    }

    public function getCitiesForProvince($id){
        $cities = Ciudad::where('idProvincia', $id)
        ->orderBy('nombre_ciudad')
        ->get();
        return $cities;
    }

    public function showCountries(){
        $countries = DB::table('pais')
                    ->orderBy('nombre_pais')
                    ->where('habilitado',1)
                    ->get();
        return $countries;
    }
    public function showCountriesDetail(){
        $countries =  DB::table('pais')->orderBy('nombre_pais')->get();
        return view('seo.detail', compact('countries'));
    }

    public static function showByNombre($nombre){
        return Pais::where('nombre_pais', $nombre)->first();
    }
}
