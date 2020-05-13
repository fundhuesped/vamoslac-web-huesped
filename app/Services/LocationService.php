<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Places;
use DB;

class LocationService extends Controller{

  private function updateAvailability($table, $id){
    DB::table($table)->where('id',$id)->update(['habilitado' => 1]);
  }

  public function updateLocationAvailability(Places $place){
    $this->updateAvailability('pais',$place->idPais);
    $this->updateAvailability('provincia',$place->idProvincia);
    $this->updateAvailability('partido',$place->idPartido);
    $this->updateAvailability('ciudad',$place->idCiudad);
  }

  public function getOrCreateNewLocation(Places $place,$request){
    $nombre_pais = $request['nombre_pais'];
    $nombre_provincia = $request['nombre_provincia'];
    $nombre_partido = $request['nombre_partido'];
    $nombre_ciudad = $request['nombre_ciudad'];

    $place->idPais = DB::table('pais')
    ->where('pais.nombre_pais', '=', $nombre_pais)
    ->value('id');

    if ( !$place->idPais ){
      $place->idPais = DB::table('pais')->max('id') + 1;
      DB::table('pais')->insert([
        'id' => $place->idPais,
        'nombre_pais' => $nombre_pais,
        'habilitado' => 1,
        'created_at' => date("Y-m-d H:i:s")
      ]);
    }
    else{
      $this->updateAvailability('pais',$place->idPais);
    }

    $place->idProvincia = DB::table('provincia')
    ->join('pais','pais.id','=','provincia.idPais')
    ->where('pais.nombre_pais', '=', $nombre_pais)
    ->where('provincia.nombre_provincia', '=', $nombre_provincia)
    ->value('provincia.id');

    if ( !$place->idProvincia ){
      $place->idProvincia = DB::table('provincia')->max('id') + 1;
      DB::table('provincia')->insert([
        'id' => $place->idProvincia,
        'nombre_provincia' => $nombre_provincia,
        'habilitado' => 1,
        'created_at' => date("Y-m-d H:i:s"),
        'idPais'    => $place->idPais
      ]);
    }
    else{
      $this->updateAvailability('provincia',$place->idProvincia);
    }

    $place->idPartido = DB::table('partido')
    ->join('provincia','provincia.id','=','partido.idProvincia')
    ->join('pais','pais.id','=','partido.idPais')
    ->where('pais.nombre_pais', '=', $nombre_pais)
    ->where('provincia.nombre_provincia', '=', $nombre_provincia)
    ->where('partido.nombre_partido', '=', $nombre_partido)
    ->value('partido.id');

    if ( !$place->idPartido ){
      $place->idPartido = DB::table('partido')->max('id') + 1;
      DB::table('partido')->insert([
        'id' => $place->idPartido,
        'nombre_partido' => $nombre_partido,
        'habilitado' => 1,
        'created_at' => date("Y-m-d H:i:s"),
        'idPais'    => $place->idPais,
        'idProvincia'  => $place->idProvincia,
      ]);
    }
    else{
      $this->updateAvailability('partido',$place->idPartido);
    }

    $place->idCiudad = DB::table('ciudad')
    ->join('partido','partido.id','=','ciudad.idPartido')
    ->join('provincia','provincia.id','=','ciudad.idProvincia')
    ->join('pais','pais.id','=','ciudad.idPais')
    ->where('pais.nombre_pais', '=', $nombre_pais)
    ->where('provincia.nombre_provincia', '=', $nombre_provincia)
    ->where('partido.nombre_partido', '=', $nombre_partido)
    ->where('ciudad.nombre_ciudad', '=', $nombre_ciudad)
    ->value('ciudad.id');

    if ( !$place->idCiudad ){
      $place->idCiudad = DB::table('ciudad')->max('id') + 1;
      DB::table('ciudad')->insert([
        'id' => $place->idCiudad,
        'nombre_ciudad' => $nombre_ciudad,
        'habilitado' => 1,
        'created_at' => date("Y-m-d H:i:s"),
        'idPais'    => $place->idPais,
        'idProvincia'  => $place->idProvincia,
        'idPartido'  => $place->idPartido,
      ]);
    }
    else{
      $this->updateAvailability('ciudad',$place->idCiudad);
    }

  }
}