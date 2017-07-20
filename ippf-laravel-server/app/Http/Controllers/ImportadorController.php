<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use Input;
use Storage;
use DB;
use Config;
use App\Pais;
use App\Provincia;
use App\Partido;
use App\Places;
use App\Evaluation;
use League\Csv\Writer;
use League\Csv\Reader;
use Session;
use Image;
use ImageServiceProvider;
use Validator;
use Redirect;
use Exception;
use App\Exceptions\CustomException;
use App\Exceptions\ImporterException;
use App\Exceptions\CsvException;


use App\PlaceLog;
use PHPExcel_Cell;

use SplTempFileObject;
use SplFileObject;
use SplFileInfo;
use Auth;
class ImportadorController extends Controller {
	public $csvColumns = 'id,establecimiento,tipo,calle,altura,piso_dpto,cruce,barrio_localidad,partido_comuna,provincia_region,pais,aprobado,observacion,formattedAddress,latitude,longitude,habilitado,confidence,condones,prueba,mac,ile,ssr,dc,es_rapido,tel_testeo,mail_testeo,horario_testeo,responsable_testeo,web_testeo,ubicacion_testeo,observaciones_testeo,tel_distrib,mail_distrib,horario_distrib,responsable_distrib,web_distrib,ubicacion_distrib,comentarios_distrib,tel_mac,mail_mac,horario_mac,responsable_mac,web_mac,ubicacion_mac,comentarios_mac,tel_ile,mail_ile,horario_ile,responsable_ile,web_ile,ubicacion_ile,comentarios_ile,tel_ssr,mail_ssr,horario_ssr,responsable_ssr,web_ssr,ubicacion_ssr,comentarios_ssr,tel_dc,mail_dc,horario_dc,responsable_dc,web_dc,ubicacion_dc,comentarios_dc,servicetype_ile,servicetype_mac,servicetype_condones,servicetype_prueba,servicetype_ssr,servicetype_dc,friendly_ile,friendly_mac,friendly_condones,friendly_prueba,friendly_ssr,friendly_dc';
	public $csvColumns_arrayFormat = array('id','establecimiento','tipo','calle','altura','piso_dpto','cruce','barrio_localidad','partido_comuna','provincia_region','pais','aprobado','observacion','formattedAddress','latitude','longitude','habilitado','confidence','condones','prueba','mac','ile','ssr','dc','es_rapido','tel_testeo','mail_testeo','horario_testeo','responsable_testeo','web_testeo','ubicacion_testeo','observaciones_testeo','tel_distrib','mail_distrib','horario_distrib','responsable_distrib','web_distrib','ubicacion_distrib','comentarios_distrib','tel_mac','mail_mac','horario_mac','responsable_mac','web_mac','ubicacion_mac','comentarios_mac','tel_ile','mail_ile','horario_ile','responsable_ile','web_ile','ubicacion_ile','comentarios_ile','tel_ssr','mail_ssr','horario_ssr','responsable_ssr','web_ssr','ubicacion_ssr','comentarios_ssr','tel_dc','mail_dc','horario_dc','responsable_dc','web_dc','ubicacion_dc','comentarios_dc','servicetype_ile','servicetype_mac','servicetype_condones','servicetype_prueba','servicetype_ssr','servicetype_dc','friendly_ile','friendly_mac','friendly_condones','friendly_prueba','friendly_ssr','friendly_dc');
	//public $placeColumns = array('placeId','establecimiento','tipo','calle','altura','piso_dpto','cruce','barrio_localidad','partido_comuna','provincia_region','pais','aprobado','observacion','formattedAddress','latitude','longitude','habilitado','confidence','condones','prueba','mac','ile','ssr','dc','es_rapido','tel_testeo','mail_testeo','horario_testeo','responsable_testeo','web_testeo','ubicacion_testeo','observaciones_testeo','tel_distrib','mail_distrib','horario_distrib','responsable_distrib','web_distrib','ubicacion_distrib','comentarios_distrib','tel_mac','mail_mac','horario_mac','responsable_mac','web_mac','ubicacion_mac','comentarios_mac','tel_ile','mail_ile','horario_ile','responsable_ile','web_ile','ubicacion_ile','comentarios_ile','tel_ssr','mail_ssr','horario_ssr','responsable_ssr','web_ssr','ubicacion_ssr','comentarios_ssr','tel_dc','mail_dc','horario_dc','responsable_dc','web_dc','ubicacion_dc','comentarios_dc','servicetype_ile','servicetype_mac','servicetype_condones','servicetype_prueba','servicetype_ssr','servicetype_dc','friendly_ile','friendly_mac','friendly_condones','friendly_prueba','friendly_ssr','friendly_dc');
	public function convertPlaceObjectToArray($placeObject,$status){

		return   array(
						'status' => $status,
						'placeId' => isset($placeObject->id) ? $placeObject->id : $placeObject->placeId,
						'pais' => $placeObject->pais,
						'provincia_region' => $placeObject->provincia_region,
						'partido_comuna' => $placeObject->partido_comuna,
						'barrio_localidad' => $placeObject->barrio_localidad,
						'establecimiento' => $placeObject->establecimiento,
						'tipo' => $placeObject->tipo,
						'calle' => $placeObject->calle,
						'altura' => $placeObject->altura,
						'piso_dpto' => $placeObject->piso_dpto,
						'cruce' => $placeObject->cruce,
						'aprobado' => $placeObject->aprobado,
						'observacion' => $placeObject->observacion,
						'latitude' => $placeObject->latitude,
						'longitude' => $placeObject->longitude,
						'confidence' => $placeObject->confidence,
						'formattedAddress' => $placeObject->formattedAddress,
						'habilitado' => $placeObject->habilitado,
						//'vacunatorio' => $book->vacunatorio,
						//'infectologia' => $book->infectologia,
						'condones' => $placeObject->condones,
						'prueba' => $placeObject->prueba,
						'ssr' => $placeObject->ssr,
						'dc' => $placeObject->dc,
						'es_rapido' => $placeObject->es_rapido,
						'tel_testeo' => $placeObject->tel_testeo,
						'mail_testeo' => $placeObject->mail_testeo,
						'horario_testeo' => $placeObject->horario_testeo,
						'responsable_testeo' => $placeObject->responsable_testeo,
						'web_testeo' => $placeObject->web_testeo,
						'ubicacion_testeo' => $placeObject->ubicacion_testeo,
						'observaciones_testeo' => $placeObject->observaciones_testeo,
						'tel_distrib' => $placeObject->tel_distrib,
						'mail_distrib' => $placeObject->mail_distrib,
						'horario_distrib' => $placeObject->horario_distrib,
						'responsable_distrib' => $placeObject->responsable_distrib,
						'web_distrib' => $placeObject->web_distrib,
						'ubicacion_distrib' => $placeObject->ubicacion_distrib,
						'comentarios_distrib' => $placeObject->comentarios_distrib,
					/*  'tel_infectologia' => $book->tel_infectologia,
						'mail_infectologia' => $book->mail_infectologia,
						'horario_infectologia' => $book->horario_infectologia,
						'responsable_infectologia' => $book->responsable_infectologia,
						'web_infectologia' => $book->web_infectologia,
						'ubicacion_infectologia' => $book->ubicacion_infectologia,
						'comentarios_infectologia' => $book->comentarios_infectologia,
						'tel_vac' => $book->tel_vac,
						'mail_vac' => $book->mail_vac,
						'horario_vac' => $book->horario_vac,
						'responsable_vac' => $book->responsable_vac,
						'web_vac' => $book->web_vac,
						'ubicacion_vac' => $book->ubicacion_vac, //posible problema
						'comentarios_vac' => $book->comentarios_vac,*/
						'tel_mac' => $placeObject->tel_mac,
						'mail_mac' => $placeObject->mail_mac,
						'horario_mac' => $placeObject->horario_mac,
						'responsable_mac' => $placeObject->responsable_mac,
						'web_mac' => $placeObject->web_mac,
						'ubicacion_mac' => $placeObject->ubicacion_mac,
						'comentarios_mac' => $placeObject->comentarios_mac,
						'tel_ile' => $placeObject->tel_ile,
						'mail_ile' => $placeObject->mail_ile,
						'horario_ile' => $placeObject->horario_ile,
						'responsable_ile' => $placeObject->responsable_ile,
						'web_ile' => $placeObject->web_ile,
						'ubicacion_ile' => $placeObject->ubicacion_ile,
						'comentarios_ile' => $placeObject->comentarios_ile,
						'mac' => $placeObject->mac,
						'ile' => $placeObject->ile,
						'tel_ssr' => $placeObject->tel_ssr,
						'mail_ssr' => $placeObject->mail_ssr,
						'horario_ssr' => $placeObject->horario_ssr,
						'responsable_ssr' => $placeObject->responsable_ssr,
						'web_ssr' => $placeObject->web_ssr,
						'ubicacion_ssr' => $placeObject->ubicacion_ssr,
						'comentarios_ssr' => $placeObject->comentarios_ssr,
						'tel_dc' => $placeObject->tel_dc,
						'mail_dc' => $placeObject->mail_dc,
						'horario_dc' => $placeObject->horario_dc,
						'responsable_dc' => $placeObject->responsable_dc,
						'web_dc' => $placeObject->web_dc,
						'ubicacion_dc' => $placeObject->ubicacion_dc,
						'comentarios_dc' => $placeObject->comentarios_dc,
						'servicetype_dc' => $placeObject->servicetype_dc,
						'servicetype_ile' => $placeObject->servicetype_ile,
						'servicetype_mac' => $placeObject->servicetype_mac,
						'servicetype_ssr' => $placeObject->servicetype_ssr,
						'servicetype_prueba' => $placeObject->servicetype_prueba,
						'servicetype_condones' => $placeObject->servicetype_condones,
						'friendly_ile' => $placeObject->friendly_ile,
						'friendly_mac' => $placeObject->friendly_mac,
						'friendly_condones' => $placeObject->friendly_condones,
						'friendly_prueba' => $placeObject->friendly_prueba,
						'friendly_ssr' => $placeObject->friendly_ssr,
						'friendly_dc' => $placeObject->friendly_dc
		); //del array
	}



	public function exportNuevos(Request $request){
		$datosNuevos = 0;
		if (session('datosNuevos') != null) $datosNuevos = session('datosNuevos');
		$csv= $this->insertDataIntoCsv_places($datosNuevos);
		$csv->output('huspedDatosNuevos.csv');
	}

	public function insertDataIntoCsv_places($data){

		$csv = Writer::createFromFileObject(new SplTempFileObject());
		//header
        //$csv->insertOne('id,establecimiento,tipo,calle,altura,piso_dpto,cruce,barrio_localidad,partido_comuna,provincia_region,pais,aprobado,observacion,formattedAddress,latitude,longitude,habilitado,confidence,condones,prueba,vacunatorio,infectologia,mac,ile,es_rapido,tel_testeo,mail_testeo,horario_testeo,responsable_testeo,web_testeo,ubicacion_testeo,observaciones_testeo,tel_distrib,mail_distrib,horario_distrib,responsable_distrib,web_distrib,ubicacion_distrib,comentarios_distrib,tel_infectologia,mail_infectologia,horario_infectologia,responsable_infectologia,web_infectologia,ubicacion_infectologia,comentarios_infectologia,tel_vac,mail_vac,horario_vac,responsable_vac,web_vac,ubicacion_vac,comentarios_vac,tel_mac,mail_mac,horario_mac,responsable_mac,web_mac,ubicacion_mac,comentarios_mac,tel_ile,mail_ile,horario_ile,responsable_ile,web_ile,ubicacion_ile,comentarios_ile');
				$csv->insertOne($this->csvColumns);
        //body
        foreach ($data as $key => $p) {
        	$p['condones']= $this->parseToExport($p['condones']);
        	$p['prueba']= $this->parseToExport($p['prueba']);
      //  	$p['vacunatorio']= $this->parseToExport($p['vacunatorio']);
      //  	$p['infectologia']= $this->parseToExport($p['infectologia']);
        	$p['mac']= $this->parseToExport($p['mac']);
        	$p['ile']= $this->parseToExport($p['ile']);
					$p['ssr']= $this->parseToExport($p['ssr']);
		      $p['dc']= $this->parseToExport($p['dc']);
        	$p['es_rapido']= $this->parseToExport($p['es_rapido']);
					$p['friendly_ile']= $this->parseToExport($p['friendly_ile']);
					$p['friendly_mac']= $this->parseToExport($p['friendly_mac']);
					$p['friendly_condones']= $this->parseToExport($p['friendly_condones']);
					$p['friendly_prueba']= $this->parseToExport($p['friendly_prueba']);
					$p['friendly_ssr']= $this->parseToExport($p['friendly_ssr']);
					$p['friendly_dc']= $this->parseToExport($p['friendly_dc']);

        	$csv->insertOne([
	        	$p['placeId'],
	        	$p['establecimiento'],
	        	$p['tipo'],
	        	$p['calle'],
	        	$p['altura'],
				$p['piso_dpto'],
				$p['cruce'],
				$p['barrio_localidad'],
				$p['partido_comuna'],
				$p['provincia_region'],
				$p['pais'],
				$p['aprobado'],//
				$p['observacion'],
				$p['formattedAddress'],
				$p['latitude'],
				$p['longitude'],
				$p['habilitado'],
				$p['confidence'],
				$p['condones'],
				$p['prueba'],
		//		$p['vacunatorio'],
			//	$p['infectologia'],
				$p['mac'],
				$p['ile'],
				$p['ssr'],
				$p['dc'],
				$p['es_rapido'],
				$p['tel_testeo'],
				$p['mail_testeo'],
				$p['horario_testeo'],
				$p['responsable_testeo'],
				$p['web_testeo'],
				$p['ubicacion_testeo'],
				$p['observaciones_testeo'],
				$p['tel_distrib'],
				$p['mail_distrib'],
				$p['horario_distrib'],
				$p['responsable_distrib'],
				$p['web_distrib'],
				$p['ubicacion_distrib'],
				$p['comentarios_distrib'],
			/*	$p['tel_infectologia'],
				$p['mail_infectologia'],
				$p['horario_infectologia'],
				$p['responsable_infectologia'],
				$p['web_infectologia'],
				$p['ubicacion_infectologia'],
				$p['comentarios_infectologia'],
				$p['tel_vac'],
				$p['mail_vac'],
				$p['horario_vac'],
				$p['responsable_vac'],
				$p['web_vac'],
				$p['ubicacion_vac'],
				$p['comentarios_vac'],*/
				$p['tel_mac'],
				$p['mail_mac'],
				$p['horario_mac'],
				$p['responsable_mac'],
				$p['web_mac'],
				$p['ubicacion_mac'],
				$p['comentarios_mac'],
				$p['tel_ile'],
				$p['mail_ile'],
				$p['horario_ile'],
				$p['responsable_ile'],
				$p['web_ile'],
				$p['ubicacion_ile'],
				$p['comentarios_ile'],
				$p['tel_ssr'],
					$p['mail_ssr'],
					$p['horario_ssr'],
					$p['responsable_ssr'],
					$p['web_ssr'],
					$p['ubicacion_ssr'],
					$p['comentarios_ssr'],
					$p['tel_dc'],
					$p['mail_dc'],
					$p['horario_dc'],
					$p['responsable_dc'],
					$p['web_dc'],
					$p['ubicacion_dc'],
					$p['comentarios_dc'],
					strtolower($p['servicetype_ile']),
					strtolower($p['servicetype_mac']),
					strtolower($p['servicetype_condones']),
					strtolower($p['servicetype_prueba']),
					strtolower($p['servicetype_ssr']),
					strtolower($p['servicetype_dc']),
					$p['friendly_ile'],
					$p['friendly_mac'],
					$p['friendly_condones'],
					$p['friendly_prueba'],
					$p['friendly_ssr'],
					$p['friendly_dc']
			]);
        }
        //descarga
		return $csv;
	}

	public function insertArraObejectsDataIntoCsv_places($data){

		$csv = Writer::createFromFileObject(new SplTempFileObject());
		//header
        //$csv->insertOne('id,establecimiento,tipo,calle,altura,piso_dpto,cruce,barrio_localidad,partido_comuna,provincia_region,pais,aprobado,observacion,formattedAddress,latitude,longitude,habilitado,confidence,condones,prueba,vacunatorio,infectologia,mac,ile,es_rapido,tel_testeo,mail_testeo,horario_testeo,responsable_testeo,web_testeo,ubicacion_testeo,observaciones_testeo,tel_distrib,mail_distrib,horario_distrib,responsable_distrib,web_distrib,ubicacion_distrib,comentarios_distrib,tel_infectologia,mail_infectologia,horario_infectologia,responsable_infectologia,web_infectologia,ubicacion_infectologia,comentarios_infectologia,tel_vac,mail_vac,horario_vac,responsable_vac,web_vac,ubicacion_vac,comentarios_vac,tel_mac,mail_mac,horario_mac,responsable_mac,web_mac,ubicacion_mac,comentarios_mac,tel_ile,mail_ile,horario_ile,responsable_ile,web_ile,ubicacion_ile,comentarios_ile');
				$csv->insertOne($this->csvColumns);
        //body
        foreach ($data as $key => $p) {
        	$p->condones = $this->parseToExport($p->condones);
        	$p->prueba= $this->parseToExport($p->prueba);
      //  	$p['vacunatorio']= $this->parseToExport($p['vacunatorio']);
      //  	$p['infectologia']= $this->parseToExport($p['infectologia']);
        	$p->mac= $this->parseToExport($p->mac);
        	$p->ile = $this->parseToExport($p->ile);
					$p->ssr = $this->parseToExport($p->ssr);
		      $p->dc= $this->parseToExport($p->dc);
        	$p->es_rapido= $this->parseToExport($p->es_rapido);
					$p->friendly_ile= $this->parseToExport($p->friendly_ile);
					$p->friendly_mac= $this->parseToExport($p->friendly_mac);
					$p->friendly_condones= $this->parseToExport($p->friendly_condones);
					$p->friendly_prueba= $this->parseToExport($p->friendly_prueba);
					$p->friendly_ssr= $this->parseToExport($p->friendly_ssr);
					$p->friendly_dc= $this->parseToExport($p->friendly_dc);

        	$csv->insertOne([
	        	$p->placeId,
	        	$p->establecimiento,
	        	$p->tipo,
	        	$p->calle,
	        	$p->altura,
				$p->piso_dpto,
				$p->cruce,
				$p->barrio_localidad,
				//$p->partido_comuna,
				$p->nombre_partido,
				//$p->provincia_region,
				$p->nombre_provincia,
				$p->nombre_pais,
				$p->aprobado,
				$p->observacion,
				$p->formattedAddress,
				$p->latitude,
				$p->longitude,
				$p->habilitado,
				$p->confidence,
				$p->condones,
				$p->prueba,
		//		$p['vacunatorio'],
			//	$p['infectologia'],
				$p->mac,
				$p->ile,
				$p->ssr,
				$p->dc,
				$p->es_rapido,
				$p->tel_testeo,
				$p->mail_testeo,
				$p->horario_testeo,
				$p->responsable_testeo,
				$p->web_testeo,
				$p->ubicacion_testeo,
				$p->observaciones_testeo,
				$p->tel_distrib,
				$p->mail_distrib,
				$p->horario_distrib,
				$p->responsable_distrib,
				$p->web_distrib,
				$p->ubicacion_distrib,
				$p->comentarios_distrib,
			/*	$p['tel_infectologia'],
				$p['mail_infectologia'],
				$p['horario_infectologia'],
				$p['responsable_infectologia'],
				$p['web_infectologia'],
				$p['ubicacion_infectologia'],
				$p['comentarios_infectologia'],
				$p['tel_vac'],
				$p['mail_vac'],
				$p['horario_vac'],
				$p['responsable_vac'],
				$p['web_vac'],
				$p['ubicacion_vac'],
				$p['comentarios_vac'],*/
				$p->tel_mac,
				$p->mail_mac,
				$p->horario_mac,
				$p->responsable_mac,
				$p->web_mac,
				$p->ubicacion_mac,
				$p->comentarios_mac,
				$p->tel_ile,
				$p->mail_ile,
				$p->horario_ile,
				$p->responsable_ile,
				$p->web_ile,
				$p->ubicacion_ile,
				$p->comentarios_ile,
				$p->tel_ssr,
					$p->mail_ssr,
					$p->horario_ssr,
					$p->responsable_ssr,
					$p->web_ssr,
					$p->ubicacion_ssr,
					$p->comentarios_ssr,
					$p->tel_dc,
					$p->mail_dc,
					$p->horario_dc,
					$p->responsable_dc,
					$p->web_dc,
					$p->ubicacion_dc,
					$p->comentarios_dc,
					strtolower($p->servicetype_ile),
					strtolower($p->servicetype_mac),
					strtolower($p->servicetype_condones),
					strtolower($p->servicetype_prueba),
					strtolower($p->servicetype_ssr),
					strtolower($p->servicetype_dc),
					$p->friendly_ile,
					$p->friendly_mac,
					$p->friendly_condones,
					$p->friendly_prueba,
					$p->friendly_ssr,
					$p->friendly_dc

			]);
        }
        //descarga
		return $csv;
	}

	public function exportReptidos(Request $request){
		$datosRepetidos = 0;
		if (session('datosRepetidos') != null)
			$datosRepetidos = session('datosRepetidos');
			$csv= $this->insertDataIntoCsv_places($datosRepetidos);
        //descarga
      $csv->output('huspedDatosRepetidos.csv');
	}

	public function exportInompletos(Request $request){
		$datosIncompletos = 0;
		if (session('datosIncompletos') != null)
			$datosIncompletos = session('datosIncompletos');
			$csv= $this->insertDataIntoCsv_places($datosIncompletos);
        //descarga
        $csv->output('huspedDatosIncompletos.csv');
	}

	public function exportActualizar(Request $request){
		$cantidadDatosActualizar = 0;
		if (session('datosActualizar') != null)
			$datosActualizar = session('datosActualizar');
		else $datosActualizar =[];
		$csv= $this->insertDataIntoCsv_places($datosActualizar);
        //descarga
        $csv->output('huspedDatosActualizar.csv');
	}

	public function exportBadActualizar(Request $request){
		$datosBadActualizar = 0;
		if (session('datosBadActualizar') != null)
			$datosBadActualizar = session('datosBadActualizar');
			$csv= $this->insertDataIntoCsv_places($datosBadActualizar);
        //descarga
        $csv->output('huspedDatosIdInvalido.csv');
	}

	public function exportUnificar(Request $request){
		$datosUnificar = 0;
		if (session('datosUnificar') != null)
			$datosUnificar = session('datosUnificar');
			$csv= $this->insertDataIntoCsv_places($datosUnificar);
        //descarga
        $csv->output('huspedDatosUnificar.csv');
	}

	public function exportBC(Request $request){
		$datosDescartados = 0;
		if (session('datosDescartados') != null)
			$datosDescartados = session('datosDescartados');
			$csv= $this->insertDataIntoCsv_places($datosDescartados);
        //descarga
        $csv->output('huspedDatosBajaConf.csv');
	}

	public function index()
	{
		return view('panel.importer.index');
	}

	public function picker() //llamo a la vista
	{
		return view('panel.importer.picker');
	}

	public function parseToExport($string){
		if ($string == 1)  {
			$string = "SI";
		}
		else{
			$string = "NO";
		}
		return $string;
	}
	public function parseToImport($string){
		$string = trim($string);
		if ( $string == "SI") {
			$string = 1;
		}
		else{
			$string = 0;
		}
		return $string;
	}


    /**
     * Retrive an object with arrays of id's service evalautons.
     *
     * @param  string  $id
     * @return void
     */
    public function exportarEvaluacionesPorServicios($id){
    	$evaluations = new EvaluationRESTController;
		$evals = $evaluations->showPanelServiceEvaluations($id);

		$copyCSV = ucwords($id).".csv";

		$csv = Writer::createFromFileObject(new SplTempFileObject());
		//header
        $csv->insertOne('Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,Edad,Género,Puntuación,Comentario,¿Aprobado?,id,Fecha');


        //body
		for ($i=0; $i < sizeof($evals); $i++) {

			$evals[$i]->info_ok = $this->parseToExport($evals[$i]->info_ok);
			$evals[$i]->privacidad_ok = $this->parseToExport($evals[$i]->privacidad_ok);
			$evals[$i]->aprobado = $this->parseToExport($evals[$i]->aprobado);

			$csv->insertOne([
    			$evals[$i]->id,
    			$evals[$i]->que_busca,
    			$evals[$i]->le_dieron,
    			$evals[$i]->info_ok,
    			$evals[$i]->privacidad_ok,
    			$evals[$i]->edad,
    			$evals[$i]->genero,
    			$evals[$i]->voto,
    			$evals[$i]->comentario,
    			$evals[$i]->aprobado,
    			$evals[$i]->idPlace,
    			$evals[$i]->created_at ]);
			}

        $csv->output($copyCSV);


    }




	public function exportarEvaluaciones($id){
		$evaluations = new EvaluationRESTController;
		$evals = $evaluations->showPanelEvaluations($id);

		$csv = Writer::createFromFileObject(new SplTempFileObject());
		//header
        $csv->insertOne('Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,Edad,Género,Puntuación,Comentario,¿Aprobado?,id,Fecha');


        //body
		for ($i=0; $i < sizeof($evals); $i++) {

			$evals[$i]->info_ok = $this->parseToExport($evals[$i]->info_ok);
			$evals[$i]->privacidad_ok = $this->parseToExport($evals[$i]->privacidad_ok);
			$evals[$i]->aprobado = $this->parseToExport($evals[$i]->aprobado);

			$csv->insertOne([
    			$evals[$i]->id,
    			$evals[$i]->que_busca,
    			$evals[$i]->le_dieron,
    			$evals[$i]->info_ok,
    			$evals[$i]->privacidad_ok,
    			$evals[$i]->edad,
    			$evals[$i]->genero,
    			$evals[$i]->voto,
    			$evals[$i]->comentario,
    			$evals[$i]->aprobado,
    			$evals[$i]->idPlace,
    			$evals[$i]->created_at ]);
			}

        $csv->output('Evaluaciones.csv');
	}


	public function parseService($service){
		$resu = "Sin especificar";
		$serviceCtrl = new ServiceController();
		$services = $serviceCtrl->getAllServices();
		foreach ($services as $s) {
			if ($s->shortname == $service) $resu = $s->name;
		}
		return $resu;
		/*
		switch ($service) {
			case 'mac':
				$resu = "Servicio de Salud Sexual y Reproductiva";
				break;
			case 'ILE':
				$resu = "Interrupción Legal de Embarazo";
				break;
			case 'cdi':
				$resu = "Centro de Infectología";
				break;
			case 'vacunatorios':
				$resu = "Vacunatorios";
				break;
			case 'prueba':
				$resu = "Prueba VIH";
				break;
			case 'condones':
				$resu = "Condones";
				break;
			default:
				$resu = "Sin especificar";
				break;
		}

		return $resu;
		*/
	}


	//recibo un int
	//chequeo si esta entre 10 y 19 para crear la columna auxilar edad_especifica
	public function parseEdadEspecifica($edad){
		$edadEspecifica="";
		if ( ($edad > 9) && ($edad < 20) ){
			$edadEspecifica = "Entre  10 y 19";
		}
		else{
			$edadEspecifica = $edad;
		}

		return $edadEspecifica;

	}



	public function exportarEvaluacionesFull(){
		$evaluations = DB::table('evaluation')
   			->join('places','evaluation.idPlace','=','places.placeId')
			->join('pais','pais.id','=','places.idPais')
	    	->join('provincia','provincia.id','=','places.idProvincia')
	    	->join('partido','partido.id','=','places.idPartido')
	    	->select('evaluation.*','places.*','partido.nombre_partido','provincia.nombre_provincia','pais.nombre_pais','evaluation.created_at as fechaEvaluacion')
	    	->get();


		$csv = Writer::createFromFileObject(new SplTempFileObject());
		//header
		//$csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,vacunatorio,infectologia,mac,ile,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,Edad,Edad Especifica,Género,Puntuación,Comentario,¿Aprobado?,Fecha,Servicio,¿Es Gratuito?,¿Es Cómodo?,Información de Vacunas');
		$csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,mac,ile,ssr,dc,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,Edad,Edad Especifica,Género,Puntuación,Comentario,¿Aprobado?,Fecha,Servicio,¿Es Gratuito?,¿Es Cómodo?,Información de Vacunas');

        //body
		foreach ($evaluations as $key => $p) {
			$p = (array)$p;
			$p['service']= $this->parseService($p['service']);
			$p['es_gratuito']= $this->parseToExport($p['es_gratuito']);
			$p['edadEspecifica']= $this->parseEdadEspecifica($p['edad']);
			$p['condones']= $this->parseToExport($p['condones']);
			$p['prueba']= $this->parseToExport($p['prueba']);
			//$p['vacunatorio']= $this->parseToExport($p['vacunatorio']);
			//$p['infectologia']= $this->parseToExport($p['infectologia']);
			$p['mac']= $this->parseToExport($p['mac']);
			$p['ile']= $this->parseToExport($p['ile']);
			$p['ssr']= $this->parseToExport($p['ssr']);
			$p['dc']= $this->parseToExport($p['dc']);
			$p['es_rapido']= $this->parseToExport($p['es_rapido']);
			$p['info_ok']= $this->parseToExport($p['info_ok']);
			$p['privacidad_ok']= $this->parseToExport($p['privacidad_ok']);
			$p['aprobado']= $this->parseToExport($p['aprobado']);
			$p['comodo']= $this->parseToExport($p['comodo']);
			$p['informacion_vacunas']= $this->parseToExport($p['informacion_vacunas']);
			$p['direccion']= $p['calle']." ".$p['altura'];


			$csv->insertOne([
		    	$p['placeId'],
		    	$p['establecimiento'],
		    	$p['direccion'],
				$p['barrio_localidad'],
				$p['nombre_partido'],
				$p['nombre_provincia'],
				$p['nombre_pais'],

				$p['condones'],
				$p['prueba'],
			//	$p['vacunatorio'],
			//	$p['infectologia'],
				$p['mac'],
				$p['ile'],
				$p['ssr'],
				$p['dc'],
				$p['es_rapido'],
		    	$p['id'],
		    	$p['que_busca'],
		    	$p['le_dieron'],
				$p['info_ok'],
				$p['privacidad_ok'],
				$p['edadEspecifica'],
				$p['edad'],
				$p['genero'],
				$p['voto'],
				$p['comentario'],
				$p['aprobado'],
				$p['fechaEvaluacion'],
				$p['service'],
				$p['es_gratuito'],
				$p['comodo'],
				$p['informacion_vacunas']
				]);
		}

        $csv->output('Evaluaciones-completo.csv');
	}
//=====================================================================================
//en caso de que escriba (segunda opt)
public function exportarPanelSearch($search){
	$placesController = new PlacesRESTController;
	$places = $placesController->search($search);
	$csv = $this->insertDataIntoCsv_places($places);
	//descarga
	$csv->output('Establecimientos.csv');
}


public function exportarPanelEvalSearch($search){
	$placesController = new PlacesRESTController;
	$places = $placesController->search($search);

	$csv = Writer::createFromFileObject(new SplTempFileObject());
	//header
    //$csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,vacunatorio,infectologia,mac,ile,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,Edad,Género,Puntuación,Comentario,¿Aprobado?,Fecha');
		$csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,mac,ile,ssr,dc,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,Edad,Género,Puntuación,Comentario,¿Aprobado?,Fecha');

    //body
	foreach ($places as $key => $value) {

		$evaluations = DB::table('evaluation')
			->join('places','evaluation.idPlace','=','places.placeId')
			->join('pais','pais.id','=','places.idPais')
	    	->join('provincia','provincia.id','=','places.idProvincia')
	    	->join('partido','partido.id','=','places.idPartido')
			->where('evaluation.idPlace',$value->placeId)
			->select('places.placeId','places.establecimiento','places.calle','places.altura','places.barrio_localidad','places.condones','places.prueba','places.mac','places.ile','places.ssr','places.dc','places.es_rapido','evaluation.id','evaluation.que_busca','evaluation.le_dieron','evaluation.info_ok','evaluation.privacidad_ok','evaluation.edad','evaluation.genero','evaluation.voto','evaluation.comentario','evaluation.aprobado','pais.nombre_pais','provincia.nombre_provincia','partido.nombre_partido','evaluation.created_at')
			->get();

		foreach ($evaluations as $p) {
   	 		$p = (array)$p;
			$p['condones']= $this->parseToExport($p['condones']);
			$p['prueba']= $this->parseToExport($p['prueba']);
		//	$p['vacunatorio']= $this->parseToExport($p['vacunatorio']);
		//	$p['infectologia']= $this->parseToExport($p['infectologia']);
			$p['mac']= $this->parseToExport($p['mac']);
			$p['ile']= $this->parseToExport($p['ile']);
			$p['ssr']= $this->parseToExport($p['ssr']);
			$p['dc']= $this->parseToExport($p['dc']);
			$p['es_rapido']= $this->parseToExport($p['es_rapido']);
			$p['info_ok']= $this->parseToExport($p['info_ok']);
			$p['privacidad_ok']= $this->parseToExport($p['privacidad_ok']);
			$p['aprobado']= $this->parseToExport($p['aprobado']);
			$p['direccion']= $p['calle']." ".$p['altura'];

			$csv->insertOne([
		    	$p['placeId'],
		    	$p['establecimiento'],
		    	$p['direccion'],
				$p['barrio_localidad'],
				$p['nombre_partido'],
				$p['nombre_provincia'],
				$p['nombre_pais'],

				$p['condones'],
				$p['prueba'],
			//	$p['vacunatorio'],
			//	$p['infectologia'],
				$p['mac'],
				$p['ile'],
				$p['ssr'],
				$p['dc'],
				$p['es_rapido'],

		    	$p['id'],
		    	$p['que_busca'],
		    	$p['le_dieron'],
				$p['info_ok'],
				$p['privacidad_ok'],
				$p['edad'],
				$p['genero'],
				$p['voto'],
				$p['comentario'],
				$p['aprobado'],
				$p['created_at']
				]);
		}
	}

        //descarga
        $csv->output('Evaluaciones.csv');
}

public function activePlacesExport(Request $request){

		//$request_params = $request->all();

		$request_params = Input::all();
		$idPais = $request_params['idPais'];
		$idProvincia = $request_params['idProvincia'];
		$idPartido = $request_params['idPartido'];
		$placesController = new PlacesRESTController;
		$places = $placesController->getAprobedPlaces($idPais, $idProvincia, $idPartido);

	//	var_dump($places);
		if ((isset($places)) && (count($places) > 0)){
			$copyCSV = "establecimientos_".$places[0]->nombre_partido."_".$places[0]->nombre_provincia."_".$places[0]->nombre_pais.".csv";
		}
		else $copyCSV = "nodata.csv";

		$csv = $this->insertArraObejectsDataIntoCsv_places($places);
		//descarga
		$csv->output($copyCSV);
}

public function activePlacesEvaluationsExport(Request $request){

			$request_params = Input::all();
			$idPais = $request_params['idPais'];
			$idProvincia = $request_params['idProvincia'];
			$idPartido = $request_params['idPartido'];
			$serviciosString = $request_params['selectedServiceList'];
			$servicios = explode(',', $serviciosString);
			$placesController = new PlacesRESTController;
			$places = $placesController->showApprovedFilterByService($idPais,$idProvincia,$idPartido, $servicios);
			if (count($places) > 0){
			$copyCSV = "evaluaciones_".$places[0]->nombre_partido."_".$places[0]->nombre_provincia."_".$places[0]->nombre_pais.".csv";
			}
			else {
				$copyCSV = "nodata.csv";
			}
			$csv = Writer::createFromFileObject(new SplTempFileObject());
			//header
		  //$csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,vacunatorio,infectologia,mac,ile,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad, Gratuito, Cómodo, Información Vacunas Edad, Edad, Edad Especifica,Género,Puntuación,Comentario,¿Aprobado?,Fecha,Servicio');
			$csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,mac,ile,ssr,dc,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad, Gratuito, Cómodo, Información Vacunas Edad, Edad, Edad Especifica,Género,Puntuación,Comentario,¿Aprobado?,Fecha,Servicio');
			//body
			foreach ($places as $key => $value) {

			$evaluations = DB::table('evaluation')
				->join('places','evaluation.idPlace','=','places.placeId')
				->join('pais','pais.id','=','places.idPais')
		    	->join('provincia','provincia.id','=','places.idProvincia')
		    	->join('partido','partido.id','=','places.idPartido')
				->where('evaluation.idPlace',$value->placeId)
				->select('places.placeId','places.establecimiento','places.calle','places.altura','places.barrio_localidad','places.condones','places.prueba','places.mac','places.ile','places.ssr','places.dc','places.es_rapido','evaluation.id','evaluation.que_busca','evaluation.le_dieron','evaluation.info_ok','evaluation.privacidad_ok','evaluation.es_gratuito','evaluation.comodo','evaluation.informacion_vacunas','evaluation.edad','evaluation.genero','evaluation.voto','evaluation.comentario','evaluation.aprobado','pais.nombre_pais','provincia.nombre_provincia','partido.nombre_partido','evaluation.created_at','evaluation.service')
				->get();

			foreach ($evaluations as $p) {
	   	 		$p = (array)$p;
	   	 		$p['edadEspecifica']= $this->parseEdadEspecifica($p['edad']);
				$p['condones']= $this->parseToExport($p['condones']);
				$p['prueba']= $this->parseToExport($p['prueba']);
		//		$p['vacunatorio']= $this->parseToExport($p['vacunatorio']);
			//	$p['infectologia']= $this->parseToExport($p['infectologia']);
				$p['mac']= $this->parseToExport($p['mac']);
				$p['ile']= $this->parseToExport($p['ile']);
				$p['ssr']= $this->parseToExport($p['ssr']);
				$p['dc']= $this->parseToExport($p['dc']);
				$p['es_rapido']= $this->parseToExport($p['es_rapido']);
				$p['info_ok']= $this->parseToExport($p['info_ok']);
				$p['privacidad_ok']= $this->parseToExport($p['privacidad_ok']);
				$p['aprobado']= $this->parseToExport($p['aprobado']);
				$p['direccion']= $p['calle']." ".$p['altura'];
				$p['es_gratuito']= $this->parseToExport($p['es_gratuito']);
				$p['service']= $this->parseService($p['service']);
				$p['comodo']= $this->parseToExport($p['comodo']);
				$p['informacion_vacunas']= $this->parseToExport($p['informacion_vacunas']);

				$csv->insertOne([
			    	$p['placeId'],
			    	$p['direccion'],
			    	$p['establecimiento'],
					$p['barrio_localidad'],
					$p['nombre_partido'],
					$p['nombre_provincia'],
					$p['nombre_pais'],

					$p['condones'],
					$p['prueba'],
			//		$p['vacunatorio'],
				//	$p['infectologia'],
					$p['mac'],
					$p['ile'],
					$p['ssr'],
					$p['dc'],
					$p['es_rapido'],

			    	$p['id'],
			    	$p['que_busca'],
			    	$p['le_dieron'],
					$p['info_ok'],
					$p['privacidad_ok'],
					$p['es_gratuito'],
					$p['comodo'],
					$p['informacion_vacunas'],
					$p['edadEspecifica'],
					$p['edad'],
					$p['genero'],
					$p['voto'],
					$p['comentario'],
					$p['aprobado'],
					$p['created_at'],
					$p['service']
					]);
			}
		}

	        //descarga
	        $csv->output($copyCSV);
}

//recibe placeId y selectedServiceList
//genera un csv, de las evaluaciones del lugar filtradas por los servicios que seleccionó (selectedServiceList)
public function evaluationsExportFilterByService(Request $request){
			$request_params = Input::all();
			$placeId = $request_params['placeId'];
			$serviciosString = $request_params['selectedServiceList'];
			$services = explode(',', $serviciosString);
			$placesRESTController = new PlacesRESTController;
			$evaluations = $placesRESTController->getPlaceEvaluationsFilterByService($placeId, $services);
			if (count($evaluations) > 0){
			$copyCSV = "evaluaciones_".$evaluations[0]->establecimiento.".csv";
			}
			else {
				$copyCSV = "nodata.csv";
			}
			$csv = Writer::createFromFileObject(new SplTempFileObject());
			//header
		  $csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,mac,ile,ssr,dc,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,es_gratuito,comodo,Información_vacunas_edad,Edad,Edad Especifica,Género,Puntuación,Comentario,¿Aprobado?,Fecha,Servicio');
			//body

			foreach ($evaluations as $p) {
	   	 		$p = (array)$p;
						if (in_array($p['service'], $services)) {
				$p['service']= $this->parseService($p['service']);
				$p['edadEspecifica']= $this->parseEdadEspecifica($p['edad']);
				$p['condones']= $this->parseToExport($p['condones']);
				$p['prueba']= $this->parseToExport($p['prueba']);
				$p['ssr']= $this->parseToExport($p['ssr']);
				$p['dc']= $this->parseToExport($p['dc']);
				$p['mac']= $this->parseToExport($p['mac']);
				$p['ile']= $this->parseToExport($p['ile']);
				$p['ssr']= $this->parseToExport($p['ssr']);
				$p['dc']= $this->parseToExport($p['dc']);
				$p['es_rapido']= $this->parseToExport($p['es_rapido']);
				$p['info_ok']= $this->parseToExport($p['info_ok']);
				$p['privacidad_ok']= $this->parseToExport($p['privacidad_ok']);
				$p['aprobado']= $this->parseToExport($p['aprobado']);
				$p['direccion']= $p['calle']." ".$p['altura'];
					$p['es_gratuito']= $this->parseToExport($p['es_gratuito']);
					$p['comodo']= $this->parseToExport($p['comodo']);
					$p['informacion_vacunas']= $this->parseToExport($p['informacion_vacunas']);
				$csv->insertOne([
			    	$p['placeId'],
			    	$p['establecimiento'],
						$p['direccion'],
					$p['barrio_localidad'],
					$p['nombre_partido'],
					$p['nombre_provincia'],
					$p['nombre_pais'],
					$p['condones'],
					$p['prueba'],
					$p['mac'],
					$p['ile'],
					$p['ssr'],
					$p['dc'],
					$p['es_rapido'],

			    	$p['id'],
			    	$p['que_busca'],
			    	$p['le_dieron'],
					$p['info_ok'],
					$p['privacidad_ok'],
					$p['es_gratuito'],
					$p['comodo'],
					$p['informacion_vacunas'],
					$p['edadEspecifica'],
					$p['edad'],
					$p['genero'],
					$p['voto'],
					$p['comentario'],
					$p['aprobado'],
					$p['created_at'],
					$p['service']

					]);
			}
		}
	        //descarga
	        $csv->output($copyCSV);
}

public function exportarPanelEvalFormed($pid,$cid,$bid){
	$placesController = new PlacesRESTController;
	$places = $placesController->showApproved($pid,$cid,$bid);

	$copyCSV = "evaluaciones_".$places[0]->nombre_partido."_".$places[0]->nombre_provincia."_".$places[0]->nombre_pais.".csv";

	$csv = Writer::createFromFileObject(new SplTempFileObject());
	//header
  //  $csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,vacunatorio,infectologia,mac,ile,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,es_gratuito,comodo,Información_vacunas_edad,Edad,Género,Puntuación,Comentario,¿Aprobado?,Fecha');
	$csv->insertOne('id-establecimiento,nombre-establecimiento,direccion,barrio_localidad,partido,provincia,pais,condones,prueba,mac,ile,ssr,dc,es_rapido,Id Evaluación,¿Que buscó?,¿Se lo dieron?,Información clara,Privacidad,es_gratuito,comodo,Información_vacunas_edad,Edad,Género,Puntuación,Comentario,¿Aprobado?,Fecha');

    //body
	foreach ($places as $key => $value) {

		$evaluations = DB::table('evaluation')
			->join('places','evaluation.idPlace','=','places.placeId')
			->join('pais','pais.id','=','places.idPais')
	    	->join('provincia','provincia.id','=','places.idProvincia')
	    	->join('partido','partido.id','=','places.idPartido')
			->where('evaluation.idPlace',$value->placeId)
			->select('places.placeId','places.establecimiento','places.calle','places.altura','places.barrio_localidad','places.condones','places.prueba','places.mac','places.ile','places.ssr','places.dc','places.es_rapido','evaluation.id','evaluation.que_busca','evaluation.le_dieron','evaluation.info_ok','evaluation.privacidad_ok','evaluation.es_gratuito','evaluation.comodo','evaluation.información_vacunas','evaluation.edad','evaluation.genero','evaluation.voto','evaluation.comentario','evaluation.aprobado','pais.nombre_pais','provincia.nombre_provincia','partido.nombre_partido','evaluation.created_at')
			->get();

		foreach ($evaluations as $p) {
   	 		$p = (array)$p;
			$p['condones']= $this->parseToExport($p['condones']);
			$p['prueba']= $this->parseToExport($p['prueba']);
		//	$p['vacunatorio']= $this->parseToExport($p['vacunatorio']);
		//	$p['infectologia']= $this->parseToExport($p['infectologia']);
			$p['mac']= $this->parseToExport($p['mac']);
			$p['ile']= $this->parseToExport($p['ile']);
			$p['ssr']= $this->parseToExport($p['ssr']);
			$p['dc']= $this->parseToExport($p['dc']);
			$p['es_rapido']= $this->parseToExport($p['es_rapido']);
			$p['info_ok']= $this->parseToExport($p['info_ok']);
			$p['privacidad_ok']= $this->parseToExport($p['privacidad_ok']);
			$p['aprobado']= $this->parseToExport($p['aprobado']);
			$p['direccion']= $p['calle']." ".$p['altura'];

			$csv->insertOne([
		    	$p['placeId'],
		    	$p['direccion'],
		    	$p['establecimiento'],
				$p['barrio_localidad'],
				$p['nombre_partido'],
				$p['nombre_provincia'],
				$p['nombre_pais'],
				$p['condones'],
				$p['prueba'],
			//	$p['vacunatorio'],
			//	$p['infectologia'],
				$p['mac'],
				$p['ile'],
				$p['ssr'],
				$p['dc'],
				$p['es_rapido'],
		    	$p['id'],
		    	$p['que_busca'],
		    	$p['le_dieron'],
				$p['info_ok'],
				$p['privacidad_ok'],
				$p['es_gratuito'],
				$p['comodo'],
				$p['informacion_vacunas'],
				$p['edad'],
				$p['genero'],
				$p['voto'],
				$p['comentario'],
				$p['aprobado'],
				$p['created_at']
				]);
		}
	}

        //descarga
        $csv->output($copyCSV);
}



public function exportarPanelFormed($pid,$cid,$bid){
		$placesController = new PlacesRESTController;
		$places = $placesController->showApproved($pid,$cid,$bid);

		$copyCSV = "establecimientos_".$places[0]->nombre_partido."_".$places[0]->nombre_provincia."_".$places[0]->nombre_pais.".csv";
		$csv = $this->insertArraObejectsDataIntoCsv_places($places);
		//descarga
		$csv->output($copyCSV);
	}

//==============================================================================================================
function download_csv_results($results, $name = NULL)
{
    if( ! $name)
    {
        $name = md5(uniqid() . microtime(TRUE) . mt_rand()). '.csv';
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename='. $name);
    header('Pragma: no-cache');
    header("Expires: 0");
    header("Content-Transfer-Encoding: UTF-8");

    $outstream = fopen("php://output", "w");

    foreach($results as $result)
    {
        fputcsv($outstream, $result);
    }

    fclose($outstream);
}

function joinFiles(array $files, $result) {
    if(!is_array($files)) {
        throw new Exception('`$files` must be an array');
    }

    $wH = fopen($result, "w+");

    foreach($files as $file) {
        $fh = fopen($file, "r");
        while(!feof($fh)) {
            fwrite($wH, fgets($fh));
        }
        fclose($fh);
        unset($fh);
        fwrite($wH,""); //usually last line doesn't have a newline
    }
    fclose($wH);
    unset($wH);
}


/**
* Export sample csv template with correct structures
* @return .csv
*/
public function exportarMuestra(){
	$csv = Writer::createFromFileObject(new SplTempFileObject());
	//header
   //$csv->insertOne('id,establecimiento,tipo,calle,altura,piso_dpto,cruce,barrio_localidad,partido_comuna,provincia_region,pais,aprobado,observacion,formattedAddress,latitude,longitude,habilitado,confidence,condones,prueba,vacunatorio,infectologia,mac,ile,es_rapido,tel_testeo,mail_testeo,horario_testeo,responsable_testeo,web_testeo,ubicacion_testeo,observaciones_testeo,tel_distrib,mail_distrib,horario_distrib,responsable_distrib,web_distrib,ubicacion_distrib,comentarios_distrib,tel_infectologia,mail_infectologia,horario_infectologia,responsable_infectologia,web_infectologia,ubicacion_infectologia,comentarios_infectologia,tel_vac,mail_vac,horario_vac,responsable_vac,web_vac,ubicacion_vac,comentarios_vac,tel_mac,mail_mac,horario_mac,responsable_mac,web_mac,ubicacion_mac,comentarios_mac,tel_ile,mail_ile,horario_ile,responsable_ile,web_ile,ubicacion_ile,comentarios_ile');
	 $csv->insertOne($this->csvColumns);

   $csv->output('Template.csv');

}

public function exportar(){

	//en base a una tabla, creo un CVS.
 //    // header('Content-Type: application/csv');
	// header("Cache-Control: public");
 //    header("Content-Description: File Transfer");
 //    header("Content-Disposition: attachment; filename=Huesped.csv");
 //    // header("Content-Type: application/zip");
	// header('Content-Type: text/csv; charset=utf-8');
 //    // header("Content-Transfer-Encoding: UTF-8");
	// // header("Content-Disposition: attachment; filename=Huesped.csv");

		// contenedor de nombres
		$names = array();
		array_push($names,storage_path("encabezado.csv"));


		//genero primero el header del csv
		// $encabezado = array('id','establecimiento','tipo','calle','altura','piso_dpto','cruce','barrio_localidad','nombre_partido','nombre_provincia','nombre_pais','aprobado','observacion','formattedAddress','latitude','longitude','habilitado','condones','prueba','vacunatorio','infectologia','mac','ile','es_rapido','tel_testeo','mail_testeo','horario_testeo','responsable_testeo','web_testeo','ubicacion_testeo','observaciones_testeo','tel_distrib','mail_distrib','horario_distrib','responsable_distrib','web_distrib','ubicacion_distrib','comentarios_distrib','tel_infectologia','mail_infectologia','horario_infectologia','responsable_infectologia','web_infectologia','ubicacion_infectologia','comentarios_infectologia','tel_vac','mail_vac','horario_vac','responsable_vac','web_vac','ubicacion_vac','comentarios_vac','tel_mac','mail_mac','horario_mac','responsable_mac','web_mac','ubicacion_mac','comentarios_mac','tel_ile','mail_ile','horario_ile','responsable_ile','web_ile','ubicacion_ile','comentarios_ile');

		$encabezado = $this->csvColumns_arrayFormat;


		$file1 = fopen(storage_path("encabezado.csv"),"w");
		// $file1 = fopen("encabezado.csv","w");
			fputcsv($file1,$encabezado);
		fclose($file1);


		//armo el techo de grupos
	    $n = DB::table('places')
	    	->join('pais','pais.id','=','places.idPais')
	    	->join('provincia','provincia.id','=','places.idProvincia')
	    	->join('partido','partido.id','=','places.idPartido')
	    	->count();

	    $n = $n / 1000;
	    $n = ceil($n);


	    //agrupo los files segun la cantidad de grupos que tenga.
	    for ($i=0; $i < $n; $i++) {
    	array_push($names, storage_path("file".$i.".csv") );
    	// array_push($names,"file".$i.".csv");
			$placeColumns = array('placeId','establecimiento','tipo','calle','altura','piso_dpto','cruce','barrio_localidad','partido.nombre_partido','provincia.nombre_provincia','pais.nombre_pais','aprobado','observacion','formattedAddress','latitude','longitude','places.habilitado','confidence','condones','prueba','mac','ile','ssr','dc','es_rapido','tel_testeo','mail_testeo','horario_testeo','responsable_testeo','web_testeo','ubicacion_testeo','observaciones_testeo','tel_distrib','mail_distrib','horario_distrib','responsable_distrib','web_distrib','ubicacion_distrib','comentarios_distrib','tel_mac','mail_mac','horario_mac','responsable_mac','web_mac','ubicacion_mac','comentarios_mac','tel_ile','mail_ile','horario_ile','responsable_ile','web_ile','ubicacion_ile','comentarios_ile','tel_ssr','mail_ssr','horario_ssr','responsable_ssr','web_ssr','ubicacion_ssr','comentarios_ssr','tel_dc','mail_dc','horario_dc','responsable_dc','web_dc','ubicacion_dc','comentarios_dc','servicetype_ile','servicetype_mac','servicetype_condones','servicetype_prueba','servicetype_ssr','servicetype_dc','friendly_ile','friendly_mac','friendly_condones','friendly_prueba','friendly_ssr','friendly_dc');
		    $places = DB::table('places')
		    	->join('pais','pais.id','=','places.idPais')
		    	->join('provincia','provincia.id','=','places.idProvincia')
		    	->join('partido','partido.id','=','places.idPartido')
		    	->skip($i*1000)
		    	->take(1000)
		        ->select($placeColumns)
		        ->get();





			$file = fopen(storage_path("file".$i.".csv"),"w");
			// $file = fopen("file".$i.".csv","w");

			foreach ($places as $line){
				$line->condones = $this->parseToExport($line->condones);
				$line->prueba = $this->parseToExport($line->prueba);
			//	$line->vacunatorio = $this->parseToExport($line->vacunatorio);
			//	$line->infectologia = $this->parseToExport($line->infectologia);
				$line->mac = $this->parseToExport($line->mac);
				$line->ile = $this->parseToExport($line->ile);
				$line->ssr = $this->parseToExport($line->ssr);
				$line->dc = $this->parseToExport($line->dc);
				$line->es_rapido = $this->parseToExport($line->es_rapido);
				$line->friendly_ile = $this->parseToExport($line->friendly_ile);
				$line->friendly_mac = $this->parseToExport($line->friendly_mac);
				$line->friendly_prueba = $this->parseToExport($line->friendly_prueba);
				$line->friendly_condones = $this->parseToExport($line->friendly_condones);
				$line->friendly_ssr = $this->parseToExport($line->friendly_ssr);
				$line->friendly_dc = $this->parseToExport($line->friendly_dc);

				$line = (array)$line;
				// $line = implode(", ",$line);
			 	// fputcsv($file,explode(',',$line));
			 	fputcsv($file,$line);
			}
			fclose($file);
	    }
	    //cuando termina esto, ya tengo los files

		//uno los ficheros recien creados (ya estan en names)
		$this->joinFiles($names, storage_path('Huésped.csv'));
		// $this->joinFiles($names, "Huesped.csv");


		// $fName = "Huesped.csv";
		$fName = storage_path("Huésped.csv");
		if (file_exists($fName)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($fName).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: private');
			header('Content-Length: ' . filesize($fName));
			readfile($fName);
		exit;
		}
		// readfile("Huesped.csv");

}
//==============================================================================================================
public function get_numeric_score($data) {
	switch($data){
	        case "ROOFTOP":
	            return 0.9;
	        break;
	        case "RANGE_INTERPOLATED":
	            return 0.7;
	        break;
	        case "GEOMETRIC_CENTER":
	            return 0.5;
	        break;
	        case "APPROXIMATE":
	            return 0.25;
	        break;
	        default:
	            return 0;
	    }
}
public function elimina_acentos($text) {
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $text = strtolower($text);
        $patron = array (
            // Espacios, puntos y comas por guion
            //'/[\., ]+/' => ' ',
            // Vocales
            '/\+/' => '',
            '/&agrave;/' => 'a',
            '/&egrave;/' => 'e',
            '/&igrave;/' => 'i',
            '/&ograve;/' => 'o',
            '/&ugrave;/' => 'u',
            '/&aacute;/' => 'a',
            '/&eacute;/' => 'e',
            '/&iacute;/' => 'i',
            '/&oacute;/' => 'o',
            '/&uacute;/' => 'u',
            '/&acirc;/' => 'a',
            '/&ecirc;/' => 'e',
            '/&icirc;/' => 'i',
            '/&ocirc;/' => 'o',
            '/&ucirc;/' => 'u',
            '/&atilde;/' => 'a',
            '/&etilde;/' => 'e',
            '/&itilde;/' => 'i',
            '/&otilde;/' => 'o',
            '/&utilde;/' => 'u',
            '/&auml;/' => 'a',
            '/&euml;/' => 'e',
            '/&iuml;/' => 'i',
            '/&ouml;/' => 'o',
            '/&uuml;/' => 'u',
            '/&auml;/' => 'a',
            '/&euml;/' => 'e',
            '/&iuml;/' => 'i',
            '/&ouml;/' => 'o',
            '/&uuml;/' => 'u',
            // Otras letras y caracteres especiales
            '/&aring;/' => 'a',
            '/&ntilde;/' => 'n',
            // Agregar aqui mas caracteres si es necesario
        );
        $text = preg_replace(array_keys($patron),array_values($patron),$text);
        return $text;
}
//==============================================================================================================
	// function to geocode address, it will return false if unable to geocode address
public function geocode($book){

	//ya tiene lat&long
	if ( ($book->latitude) != null  && ($book->longitude) != null) {
		$address = $book->latitude.','.$book->longitude;


		try {
			 $url = "https://maps.google.com.ar/maps/api/geocode/json?latlng={$address}&key=AIzaSyBoXKGMHwhiMfdCqGsa6BPBuX43L-2Fwqs";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);

			$resp = json_decode($response,true);
			$location = json_decode($response);

		}catch(Exception $e){
			throw new ImporterException($e->getMessage());
		}





	    // // response status will be 'OK', if able to geocode given address
	    if($resp['status']=='OK'){
				    $geoResults = [];
					foreach($location->results as $result){
					    $geoResult = [];
					    if ($location->status == "OK"){
					    	foreach ($location->results[0]->address_components as $address) {
						        if ($address->types[0] == 'country') {
						            $geoResult['country'] = $address->long_name;
						        }
						        if ($address->types[0] == 'administrative_area_level_1') {
						            $geoResult['state'] = $address->long_name;
						        }
						        if ($address->types[0] == 'administrative_area_level_1') {
						            $geoResult['esCABA'] = $address->short_name;
						        }
						        if ($address->types[0] == 'administrative_area_level_2') {
						            $geoResult['partido'] = $address->long_name; //partido
						        }
						        if ($address->types[0] == 'locality') {  		//barrio_localidad (CABA), ciudad (Entre rios)
						            $geoResult['city'] = $address->long_name;
						        }
						        if ($address->types[0] == 'political') { //solo en caba y reemplazaria a locality(city)
						            $geoResult['county'] = $address->long_name;  //barrio_localidad
						        }
						        if ($address->types[0] == 'route') {
						            $geoResult['route'] = $address->short_name;
						        }
						        if ($address->types[0] == 'street_number') {
						            $geoResult['street_number'] = $address->long_name;
						        }
					        $geoResult['lati'] = $result->geometry->location->lat;
					        $geoResult['longi'] = $result->geometry->location->lng;
					        $geoResult['formatted_address'] = $resp['results'][0]['formatted_address'];
					        $geoResult['accurracy'] = $this->get_numeric_score($result->geometry->location_type);
					    	}
					    }


					if (isset($geoResult['route']))
						if ($geoResult['route'] == "Unnamed Road") $geoResult['route'] = "Calle sin nombre";
					    $geoResults = $geoResult;
					}

				$faltaAlgo = false;
				if (!isset($geoResults['state'])) $faltaAlgo = true;
				// if (!isset($geoResults['city']) && (!isset($geoResults['county']))  ){
				if (!isset($geoResults['city']) ) $faltaAlgo = true;
				if (!isset($geoResults['county']) ) $faltaAlgo = true;
				// elseif (!isset($geoResults['county'])) {
				// 	$geoResults['county'] = $geoResults['city'];
				// }

				// if ($geoResults['esCABA'] == "CABA")
				// 	$geoResults['partido'] = $geoResults['city'];
				// 	$geoResults['city'] = $geoResults['county'];

				if ($faltaAlgo) 	return false;
				else 	return $geoResults;
			    }
				else{
			            return false;
			    	}
	}
	else{ //sin geolocalizar
		$address = $book->calle;
		if (is_numeric($book->altura))
			$address = $address.' '.$book->altura;
		if (($book->partido_comuna != $book->barrio_localidad) && isset($book->barrio_localidad) )
			$address = $address.' '.$book->barrio_localidad;
		$address = $address.' '.$book->partido_comuna;
		$address = $address.' '.$book->provincia_region;
		$address = $address.' '.$book->pais;
		$basicString = $this->elimina_acentos($address);
		$address = urlencode($basicString);

		try {
			$url = "https://maps.google.com.ar/maps/api/geoco22de/json?address={$address}&key=AIzaSyBoXKGMHwhiMfdCqGsa6BPBuX43L-2Fwqs";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);

			$resp = json_decode($response,true);
			$location = json_decode($response);

		}catch(Exception $e){
			throw new ImporterException($e->getMessage());
		}

	    // // response status will be 'OK', if able to geocode given address
	    if($resp['status']=='OK'){
		    $geoResults = [];
			//	$geoResults['confidence'] = 1;
			foreach($location->results as $result){
			    $geoResult = [];
			    if ($location->status == "OK"){
			    	foreach ($result->address_components as $address) {
				        if ($address->types[0] == 'country') {
				            $geoResult['country'] = $address->long_name;
				        }
				        if ($address->types[0] == 'administrative_area_level_1') {
				            $geoResult['state'] = $address->long_name;
				        }
				        if ($address->types[0] == 'administrative_area_level_1') {
				            $geoResult['esCABA'] = $address->short_name;
				        }
				        if ($address->types[0] == 'administrative_area_level_2') {
				            $geoResult['partido'] = $address->long_name; //partido
				        }
				        if ($address->types[0] == 'locality') {  		//barrio_localidad (CABA), ciudad (Entre rios)
				            $geoResult['city'] = $address->long_name;
				        }
				        if ($address->types[0] == 'political') { //solo en caba
				            $geoResult['county'] = $address->long_name;  //barrio_localidad
				        }
				        if ($address->types[0] == 'route') {
				            $geoResult['route'] = $address->short_name;
				        }
				        if ($address->types[0] == 'street_number') {
				            $geoResult['street_number'] = $address->long_name;
				        }
				        $geoResult['lati'] = $result->geometry->location->lat;
				        $geoResult['longi'] = $result->geometry->location->lng;
				        $geoResult['formatted_address'] = $resp['results'][0]['formatted_address'];
				        $geoResult['accurracy'] = $this->get_numeric_score($result->geometry->location_type);
			    	}

			    }
				    $geoResults = $geoResult;
				} // foreach location result


			//new aunque tendria que fallar aca.
				if (!isset($geoResults['state']))
					if (isset($geoResults['city']))
					$geoResults['state']=$geoResults['city'];

				// if (isset($geoResults['esCABA'])){ //solamente a caba le mando barrio|barrio|provincia|pais
				// 	if (isset($geoResults['county']))
				// 							$geoResults['partido'] = $geoResults['county'];
				if (isset($geoResults['esCABA']) && ($geoResult['esCABA'] == "CABA") ){ //solamente a caba le mando barrio|barrio|provincia|pais
					if (isset($geoResults['county']))
						$geoResults['partido'] = $geoResults['county'];

				if (isset($geoResults['county']))
					$geoResults['city'] = $geoResults['county'];
				}

			if (!$geoResults){

				return $this->geocodeExtra($book);
			}
			else {
				$faltaAlgo = false;
				// if (!isset($geoResults['route'])) $faltaAlgo = true;
				if (!isset($geoResults['partido'])) $faltaAlgo = true;
				if (!isset($geoResults['state'])) $faltaAlgo = true;
				if (!isset($geoResults['country'])) $faltaAlgo = true;

				if ($faltaAlgo)
					return false;
				else{
					if (isset($geoResults['route']))
						$geoResults['route'] = $this->matchValues($book->calle,$geoResults['route']);
						if ($geoResults['route'] != $book->calle)
							$geoResults['accurracy'] = 0;

					if (isset($geoResults['country']))
						$geoResults['country'] = $this->matchValues($book->pais,$geoResults['country']);
						if ($geoResults['country'] != $book->pais)
							$geoResults['accurracy'] = 0;

					if (isset($geoResults['state']))
						$geoResults['state'] = $this->matchValues($book->provincia_region,$geoResults['state']);
						if ($geoResults['state'] != $book->provincia_region)
							$geoResults['accurracy'] = 0;

					return $geoResults; //desp de la primera geoLoc, salgo con los datos obtenidos. "xq algo tengo"
				}
			}

		} //if resp[0] == OK
		else{ // si no puedo geolocalizar xq la calle es random
			$resu = $this->geocodeExtra($book);

			if ($resu){
				if (isset($resu['country']))
					$resu['country'] = $this->matchValues($book->pais,$resu['country']);
					if ($resu['country'] != $book->pais)
							$resu['accurracy'] = 0;

				if (isset($resu['state']))
					$resu['state'] = $this->matchValues($book->provincia_region,$resu['state']);
					if ($resu['state'] != $book->provincia_region)
							$resu['accurracy'] = 0;
				return $resu;
			}
			else
				return false;
		}
	}

}

public function matchValues($bookData, $googleData){
	// 0-0
	$result = $googleData;
	$pureBookData   = $this->elimina_acentos($bookData);
	$pureGoogleData = $this->elimina_acentos($googleData);

	// 1) 1-0
	if (is_null($googleData))
		$result = $bookData;

	// 2) 1-1
	if ($pureBookData != $pureGoogleData)
		$result = $bookData;

	// 3) 0-1
	if (is_null($bookData))
		$result = $googleData;

	return $result;
}


function curl_get_contents($url)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

public function geocodeExtra($book){
	$address = "";
	if (!is_null($book->barrio_localidad))
		$address = $book->barrio_localidad;

	if ( (!is_null($book->partido_comuna)) )
		$address = $address.' '.$book->partido_comuna;

	if (!is_null($book->provincia_region))
		$address = $address.' '.$book->provincia_region;

	if (!is_null($book->pais))
		$address = $address.' '.$book->pais;

	$basicString = $this->elimina_acentos($address);

	$address = urlencode($basicString);

		try {
			$url = "https://maps.google.com.ar/maps/api/geocode/json?key=AIzaSyBoXKGMHwhiMfdCqGsa6BPBuX43L-2Fwqs&address={$address}";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);

			$resp = json_decode($response,true);
			$location = json_decode($response);

		}catch(Exception $e){
			throw new ImporterException($e->getMessage());
		}


	if($resp['status']=='OK'){
	$geoResults = [];
	foreach($location->results as $result){
				    $geoResult = [];
				    if ($location->status == "OK"){
				    	foreach ($result->address_components as $address) {
					        if ($address->types[0] == 'country') {
					            $geoResult['country'] = $address->long_name;
					        }
					        if ($address->types[0] == 'administrative_area_level_1') {
					            $geoResult['state'] = $address->long_name;
					        }
					        if ($address->types[0] == 'administrative_area_level_1') {
					            $geoResult['esCABA'] = $address->short_name;
					        }
					        if ($address->types[0] == 'administrative_area_level_2') {
					            $geoResult['partido'] = $address->long_name; //partido
					        }
					        if ($address->types[0] == 'locality') {  		//barrio_localidad (CABA), ciudad (Entre rios)
					            $geoResult['city'] = $address->long_name;
					        }
					        if ($address->types[0] == 'political') { //solo en caba
					            $geoResult['county'] = $address->long_name;  //barrio_localidad
					        }
					        if ($address->types[0] == 'route') {
					            $geoResult['route'] = $address->short_name;
					        }
					        if ($address->types[0] == 'street_number') {
					            $geoResult['street_number'] = $address->long_name;
					        }
				        $geoResult['lati'] = $result->geometry->location->lat;
				        $geoResult['longi'] = $result->geometry->location->lng;
				        $geoResult['formatted_address'] = $resp['results'][0]['formatted_address'];
				        $geoResult['accurracy'] = $this->get_numeric_score($result->geometry->location_type);
				    	}
				    }
				    $geoResults = $geoResult;

				}


				if (!isset($geoResults['city']))
					if (isset($geoResults['partido']))
						$geoResults['city'] = $geoResults['partido'];

				if (isset($geoResults['esCABA'])){ //solamente a caba le mando barrio|barrio|provincia|pais
					if (isset($geoResults['county']))
											$geoResults['partido'] = $geoResults['county'];
				if (isset($geoResults['county']))
					$geoResults['city'] = $geoResults['county'];
				}

				$faltaAlgo = false;
				if (!isset($geoResults['country'])) $faltaAlgo = true;
				if (!isset($geoResults['partido'])) $faltaAlgo = true;
				if (!isset($geoResults['city'])) $faltaAlgo = true;


			if ($faltaAlgo)
			 	return false;
			else{
				//si google normaliza distinto dev los datos del csv
				return $geoResults;
			}
	} // del resp satatus OK
	else { //esto es xq yha no tiene datos de ese lugar
		return false;
	}
}


public function esRepetido($book,$latLng){
	$resultado = false;
	$existePlace = DB::table('places')
    	->join('pais','pais.id','=','places.idPais')
    	->join('provincia','provincia.id','=','places.idProvincia')
        ->join('partido','partido.id','=','places.idPartido')

		->where('places.calle','=', $latLng['route'])
		->where('places.barrio_localidad','=', $latLng['city']) // no usar debdio a google maps (almagro, etc)
		->where('partido.nombre_partido', '=', $latLng['partido']) // comuna 1,2,3,4
		->where('provincia.nombre_provincia', '=', $latLng['state']) // caba
		->where('pais.nombre_pais', '=', $latLng['country'])
		->where('places.aprobado','=', $book->aprobado)
		// ->orWhereNull('places.aprobado')
		->where('places.establecimiento','=', $book->establecimiento)
		// ->orWhereNull('places.establecimiento')
		->where('places.tipo','=', $book->tipo)
		// ->orWhereNull('places.tipo')
		->where('places.altura','=', $book->altura)
		// ->orWhereNull('places.altura')
		->where('places.piso_dpto','=', $book->piso_dpto)
		// ->orWhereNull('places.piso_dpto')
		->where('places.cruce','=', $book->cruce)
		// ->orWhereNull('places.cruce')
		->where('places.observacion','=', $book->observacion)
		// ->orWhereNull('places.observacion')
		->where('places.habilitado','=', $book->habilitado)
		// ->orWhereNull('places.habilitado')
		->where('places.condones','=', $book->condones)
		// ->orWhereNull('places.condones')
		->where('places.prueba','=', $book->prueba)
		// ->orWhereNull('places.prueba')
	//	->where('places.vacunatorio','=', $book->vacunatorio)
		// ->orWhereNull('places.vacunatorio')
	//	->where('places.infectologia','=', $book->infectologia)
		// ->orWhereNull('places.infectologia')
		->where('places.mac','=', $book->mac)
		// ->orWhereNull('places.mac')
		->where('places.ile','=', $book->ile)
		->where('places.ssr','=', $book->ssr)
		->where('places.dc','=', $book->dc)
		// ->orWhereNull('places.ile')
		->where('places.es_rapido','=', $book->es_rapido)
		// ->orWhereNull('places.es_rapido')
		->where('places.tel_testeo','=', $book->tel_testeo)
		// ->orWhereNull('places.tel_testeo')
		->where('places.mail_testeo','=', $book->mail_testeo)
		// ->orWhereNull('places.mail_testeo')
		->where('places.horario_testeo','=', $book->horario_testeo)
		// ->orWhereNull('places.horario_testeo')
		->where('places.responsable_testeo','=', $book->responsable_testeo)
		// ->orWhereNull('places.responsable_testeo')
		->where('places.web_testeo','=', $book->web_testeo)
		// ->orWhereNull('places.web_testeo')
		->where('places.ubicacion_testeo','=', $book->ubicacion_testeo)
		// ->orWhereNull('places.ubicacion_testeo')
		->where('places.observaciones_testeo','=', $book->observaciones_testeo)
		// ->orWhereNull('places.observaciones_testeo')
		->where('places.tel_distrib','=', $book->tel_distrib)
		// ->orWhereNull('places.tel_distrib')
		->where('places.mail_distrib','=', $book->mail_distrib)
		// ->orWhereNull('places.mail_distrib')
		->where('places.horario_distrib','=', $book->horario_distrib)
		// ->orWhereNull('places.horario_distrib')
		->where('places.responsable_distrib','=', $book->responsable_distrib)
		// ->orWhereNull('places.responsable_distrib')
		->where('places.web_distrib','=', $book->web_distrib)
		// ->orWhereNull('places.web_distrib')
		->where('places.ubicacion_distrib','=', $book->ubicacion_distrib)
		// ->orWhereNull('places.ubicacion_distrib')
		->where('places.comentarios_distrib','=', $book->comentarios_distrib)
		// ->orWhereNull('places.comentarios_distrib')
		->where('places.tel_ssr','=', $book->tel_ssr)
		// ->orWhereNull('places.tel_infectologia')
		->where('places.mail_ssr','=', $book->mail_ssr)
		// ->orWhereNull('places.mail_infectologia')
		->where('places.horario_ssr','=', $book->horario_ssr)
		// ->orWhereNull('places.horario_infectologia')
		->where('places.responsable_ssr','=', $book->responsable_ssr)
		// ->orWhereNull('places.responsable_infectologia')
		->where('places.web_ssr','=', $book->web_ssr)
		// ->orWhereNull('places.web_infectologia')
		->where('places.ubicacion_ssr','=', $book->ubicacion_ssr)
		// ->orWhereNull('places.ubicacion_infectologia')
		->where('places.comentarios_ssr','=', $book->comentarios_ssr)
		// ->orWhereNull('places.comentarios_infectologia')
		->where('places.tel_dc','=', $book->tel_dc)
		// ->orWhereNull('places.tel_vac')
		->where('places.mail_dc','=', $book->mail_dc)
		// ->orWhereNull('places.mail_vac')
		->where('places.horario_dc','=', $book->horario_dc)
		// ->orWhereNull('places.horario_vac')
		->where('places.responsable_dc','=', $book->responsable_dc)
		// ->orWhereNull('places.responsable_vac')
		->where('places.web_dc','=', $book->web_dc)
		// ->orWhereNull('places.web_vac')
		->where('places.ubicacion_dc','=', $book->ubicacion_dc)
		// ->orWhereNull('places.ubicacion_vac')
		->where('places.comentarios_dc','=', $book->comentarios_dc)
		// ->orWhereNull('places.comentarios_vac')
		->where('places.tel_mac','=', $book->tel_mac)
		// ->orWhereNull('places.tel_mac')
		->where('places.mail_mac','=', $book->mail_mac)
		// ->orWhereNull('places.mail_mac')
		->where('places.horario_mac','=', $book->horario_mac)
		// ->orWhereNull('places.horario_mac')
		->where('places.responsable_mac','=', $book->responsable_mac)
		// ->orWhereNull('places.responsable_mac')
		->where('places.web_mac','=', $book->web_mac)
		// ->orWhereNull('places.web_mac')
		->where('places.ubicacion_mac','=', $book->ubicacion_mac)
		// ->orWhereNull('places.ubicacion_mac')
		->where('places.comentarios_mac','=', $book->comentarios_mac)
		// ->orWhereNull('places.comentarios_mac')

		->where('places.tel_ile','=', $book->tel_ile)
		// ->orWhereNull('places.tel_ile')
		->where('places.mail_ile','=', $book->mail_ile)
		// ->orWhereNull('places.mail_ile')
		->where('places.horario_ile','=', $book->horario_ile)
		// ->orWhereNull('places.horario_ile')
		->where('places.responsable_ile','=', $book->responsable_ile)
		// ->orWhereNull('places.responsable_ile')
		->where('places.web_ile','=', $book->web_ile)
		// ->orWhereNull('places.web_ile')
		->where('places.ubicacion_ile','=', $book->ubicacion_ile)
		// ->orWhereNull('places.ubicacion_ile')
		->where('places.comentarios_ile','=', $book->comentarios_ile)

		->where('places.servicetype_ile','=', strtolower($book->servicetype_ile))
		->where('places.servicetype_mac','=', strtolower($book->servicetype_mac))
		->where('places.servicetype_condones','=', strtolower($book->servicetype_condones))
		->where('places.servicetype_prueba','=', strtolower($book->servicetype_prueba))
		->where('places.servicetype_ssr','=', strtolower($book->servicetype_ssr))
		->where('places.servicetype_dc','=', strtolower($book->servicetype_dc))

		->where('places.friendly_dc','=', strtolower($book->friendly_dc))
		->where('places.friendly_ile','=', strtolower($book->friendly_ile))
		->where('places.friendly_ssr','=', strtolower($book->friendly_ssr))
		->where('places.friendly_mac','=', strtolower($book->friendly_mac))
		->where('places.friendly_prueba','=', strtolower($book->friendly_prueba))
		->where('places.friendly_condones','=', strtolower($book->friendly_condones))
		// ->orWhereNull('places.comentarios_ile')
		->first();

    if ($existePlace)
    	$resultado = true;

	return $resultado;
}
public function esRepetidoNoGeo($book){
	$resultado = false; //hacer filtro previo, para que los nulos los reemplace por ""
	$existePlace = DB::table('places')
    	->join('pais','pais.id','=','places.idPais')
    	->join('provincia','provincia.id','=','places.idProvincia')
        ->join('partido','partido.id','=','places.idPartido')
		->where('places.establecimiento', $book->establecimiento)
		->where('places.tipo', $book->tipo)
		->where('places.calle', $book->calle)
		->where('places.altura', $book->altura)
		->where('places.piso_dpto', $book->piso_dpto)
		->where('places.cruce', $book->cruce)
		->where('places.barrio_localidad', $book->barrio_localidad)
		->where('partido.nombre_partido',  $book->partido_comuna)
		->where('provincia.nombre_provincia',  $book->provincia_region)
		->where('pais.nombre_pais',  $book->pais)
		->where('places.aprobado', $book->aprobado)
		->where('places.observacion', $book->observacion)
		->where('places.habilitado', $book->habilitado)
		->where('places.latitude', $book->latitude)
		->where('places.longitude', $book->longitude)

		->where('places.condones', $book->condones)
		->where('places.prueba', $book->prueba)
	//	->where('places.vacunatorio', $book->vacunatorio)
	//	->where('places.infectologia', $book->infectologia)
		->where('places.mac', $book->mac)
		->where('places.ile', $book->ile)
		->where('places.ssr', $book->ssr)
		->where('places.dc', $book->dc)
		->where('places.es_rapido', $book->es_rapido)

		->where('places.tel_testeo', $book->tel_testeo)
		->where('places.mail_testeo', $book->mail_testeo)
		->where('places.horario_testeo', $book->horario_testeo)
		->where('places.responsable_testeo', $book->responsable_testeo)
		->where('places.web_testeo', $book->web_testeo)
		->where('places.ubicacion_testeo', $book->ubicacion_testeo)
		->where('places.observaciones_testeo', $book->observaciones_testeo)

		->where('places.tel_distrib', $book->tel_distrib)
		->where('places.mail_distrib', $book->mail_distrib)
		->where('places.horario_distrib', $book->horario_distrib)
		->where('places.responsable_distrib', $book->responsable_distrib)
		->where('places.web_distrib', $book->web_distrib)
		->where('places.ubicacion_distrib', $book->ubicacion_distrib)
		->where('places.comentarios_distrib', $book->comentarios_distrib)

		->where('places.tel_ssr','=', $book->tel_ssr)
		->where('places.mail_ssr','=', $book->mail_ssr)
		->where('places.horario_ssr','=', $book->horario_ssr)
		->where('places.responsable_ssr','=', $book->responsable_ssr)
		->where('places.web_ssr','=', $book->web_ssr)
		->where('places.ubicacion_ssr','=', $book->ubicacion_ssr)
		->where('places.comentarios_ssr','=', $book->comentarios_ssr)

		->where('places.tel_dc','=', $book->tel_dc)
		->where('places.mail_dc','=', $book->mail_dc)
		->where('places.horario_dc','=', $book->horario_dc)
		->where('places.responsable_dc','=', $book->responsable_dc)
		->where('places.web_dc','=', $book->web_dc)
		->where('places.ubicacion_dc','=', $book->ubicacion_dc)
		->where('places.comentarios_dc','=', $book->comentarios_dc)

		->where('places.tel_mac', $book->tel_mac)
		->where('places.mail_mac', $book->mail_mac)
		->where('places.horario_mac', $book->horario_mac)
		->where('places.responsable_mac', $book->responsable_mac)
		->where('places.web_mac', $book->web_mac)
		->where('places.ubicacion_mac', $book->ubicacion_mac)
		->where('places.comentarios_mac', $book->comentarios_mac)

		->where('places.tel_ile', $book->tel_ile)
		->where('places.mail_ile', $book->mail_ile)
		->where('places.horario_ile', $book->horario_ile)
		->where('places.responsable_ile', $book->responsable_ile)
		->where('places.web_ile', $book->web_ile)
		->where('places.ubicacion_ile', $book->ubicacion_ile)
		->where('places.comentarios_ile', $book->comentarios_ile)

		->where('places.servicetype_ile','=', strtolower($book->servicetype_ile))
		->where('places.servicetype_mac','=', strtolower($book->servicetype_mac))
		->where('places.servicetype_condones','=', strtolower($book->servicetype_condones))
		->where('places.servicetype_prueba','=', strtolower($book->servicetype_prueba))
		->where('places.servicetype_ssr','=', strtolower($book->servicetype_ssr))
		->where('places.servicetype_dc','=', strtolower($book->servicetype_dc))

		->where('places.friendly_dc','=', strtolower($book->friendly_dc))
		->where('places.friendly_ile','=', strtolower($book->friendly_ile))
		->where('places.friendly_ssr','=', strtolower($book->friendly_ssr))
		->where('places.friendly_mac','=', strtolower($book->friendly_mac))
		->where('places.friendly_prueba','=', strtolower($book->friendly_prueba))
		->where('places.friendly_condones','=', strtolower($book->friendly_condones))

		->first();

    if ($existePlace)
    	$resultado = true;

	return $resultado;
}

public function correctLatLongFormat($value){
	$resu = false;

	if (is_numeric($value))
		$resu = true;

	return $resu;
}

public function esIncompleto($book){
	$resultado = false;
	if (
		(is_null($book->establecimiento)) ||
		(is_null($book->calle)) ||
		(is_null($book->pais)) ||
		(is_null($book->provincia_region)) ||
		(is_null($book->partido_comuna)) ){
		$resultado = true;
	}
    return $resultado;
}
public function esIncompletoNoGeo($book){
	$resultado = false;
	if (
		(is_null($book->latitude)) ||
		(!$this->correctLatLongFormat($book->latitude)) ||
		(!$this->correctLatLongFormat($book->longitude)) ||
		(is_null($book->longitude)) ){
		$resultado = true;
	}
    return $resultado;
}
public function esUnificable($book,$latLng){
	//LOGICA --> !esRepetido, y coincide con todos los datos MENOS los servicios
	$resultado = false;
    $existePlace = DB::table('places')
        ->join('pais','pais.id','=','places.idPais')
    	->join('provincia','provincia.id','=','places.idProvincia')
        ->join('partido','partido.id','=','places.idPartido')
		->where('places.establecimiento','=', $book->establecimiento)
		->where('places.tipo','=', $book->tipo)
		->where('places.calle','=', $latLng['route'])
		->where('places.altura','=', $book->altura)
		->where('places.piso_dpto','=', $book->piso_dpto)
		->where('places.cruce','=', $book->cruce)//este rompe con
		->where('places.barrio_localidad','=', $latLng['city']) // no usar debdio a google maps (almagro, etc)
		->where('provincia.nombre_provincia', '=', $latLng['state']) // caba
		->where('partido.nombre_partido', '=', $latLng['partido']) // comuna 1,2,3,4
		->where('pais.nombre_pais', '=', $latLng['country'])
		// ->where('places.observacion','=', $book->observacion)
		// ->where('places.aprobado','=', $book->aprobado)
		// ->where('places.habilitado','=', $book->habilitado)
		->first();
		if ( (!$this->esRepetido($book,$latLng)) && ($existePlace) )
			$resultado = true;
    return $resultado;
}
public function esUnificableNoGeo($book){
	$resultado = false;

    $existePlace = DB::table('places')
        ->join('pais','pais.id','=','places.idPais')
    	->join('provincia','provincia.id','=','places.idProvincia')
        ->join('partido','partido.id','=','places.idPartido')
		->where('places.establecimiento','=', $book->establecimiento)
		->where('places.tipo','=', $book->tipo)
		->where('places.calle','=', $book->calle)
		->where('places.altura','=', $book->altura)
		->where('places.piso_dpto','=', $book->piso_dpto)
		->where('places.cruce','=', $book->cruce)//este rompe con
		->where('places.barrio_localidad','=', $book->barrio_localidad) // no usar debdio a google maps (almagro, etc)
		->where('partido.nombre_partido', '=', $book->partido_comuna) // comuna 1,2,3,4
		->where('provincia.nombre_provincia', '=', $book->provincia_region) // caba
		->where('pais.nombre_pais', '=', $book->pais)
		->first();
		if ( (!$this->esRepetidoNoGeo($book)) && ($existePlace) )
			$resultado = true;
    return $resultado;
}
public function esBajaConfianza($book,$latLng){
//LOGICA --> si no se puede geolocalizar o la acurracy es baja   ( ver )
	$resultado = false;
	$faltaAlgo = false;
	// if (!isset($latLng['route'])) $resultado = true;
	if (!isset($latLng['partido'])) $resultado = true;
	if (!isset($latLng['city'])) $resultado = true;
	if ($latLng == false)
		$resultado = true;
    return $resultado;
}
public function esNuevo($book,$latLng){
	//LOGICA --> si no es ninguna de las otras opciones
	$resultado = false;
	if ( (!$this->esRepetido($book,$latLng)) && (!$this->esUnificable($book,$latLng)) && (!$this->esIncompleto($book)) && (!$this->esBajaConfianza($book,$latLng)))
			$resultado = true;
    return $resultado;
}
public function esNuevoNoGeo($book){
	$resultado = false;
	if ( (!$this->esRepetidoNoGeo($book)) && (!$this->esIncompletoNoGeo($book)) )
			$resultado = true;
    return $resultado;
}
//=================================================================================================================
//=================================================================================================================
//	RUTA PREVIEW, VISUALIZO LOS NUEVOS DATOS sin geolocalizar
//=================================================================================================================
//=================================================================================================================
//==========FUNCION que valida si el CSV ingresado es valido =================//
public function validarCsv (Request $request){
	$request_params = $request->all();
	if ($request->hasFile('file'))
		$ext = $request->file('file')->getClientOriginalExtension();
		if (isset($ext))
			$request_params['tmp'] = ($ext == "csv") ? 1234 : 1234567;

    $rules = array(
		  'tmp' => 'required|max:4'
    );
	$messages = array(
	'required'    => 'Se debe ingresar un archivo antes de continuar!',
	'max'    => 'La extension del archivo tiene que ser .csv y estar separado por comas (",") ');
	$validator = Validator::make($request_params,$rules,$messages);
	if ($validator->fails()) {
		return redirect('panel/importer/picker')
					->withErrors($validator->messages())
					->withInput();
	}
}


public function csvPrimeraFila(Request $request) {
$tmpFile = Input::file('file')->getClientOriginalName();
Storage::disk('local')->put($tmpFile, \File::get($request->file('file') ) );
Excel::load(storage_path().'/app/'.$tmpFile, function($reader) {
	$_SESSION['primeraFila'] = $reader->get()[0];
  });
  $primeraFila = $_SESSION['primeraFila'];
  session()->forget('primeraFila');
  return $primeraFila;
}



/**
	 * Create an array with all the columns in the templateCsv and compare one by one
	 * @param  array $rowColumns
	 * @return bool if correct format true, else false;
	 */
public function checkAllColumns($rowColumns){
// tambien se puede hacer con $correctCvs == $rowColumns.
//var_dump($rowColumns);
	$correctCvs = array(
		'0' => "id",
		'1' => "establecimiento",
		'2' => "tipo",
		'3' => "calle",
		'4' => "altura",
		'5' => "piso_dpto",
		'6' => "cruce",
		'7' => "barrio_localidad",
		'8' => "partido_comuna",
		'9' => "provincia_region",
		'10' => "pais",
		'11' => "aprobado",
		'12' => "observacion",
		'13' => "formattedaddress",
		'14' => "latitude",
		'15' => "longitude",
		'16' => "habilitado",
		'17' => "confidence",
		'18' => "condones",
		'19' => "prueba",
		'20' => "mac",
		'21' => "ile",
		'22' => "ssr",
		'23' => "dc",
		'24' => "es_rapido",
		'25' => "tel_testeo",
		'26' => "mail_testeo",
		'27' => "horario_testeo",
		'28' => "responsable_testeo",
		'29' => "web_testeo",
		'30' => "ubicacion_testeo",
		'31' => "observaciones_testeo",
		'32' => "tel_distrib",
		'33' => "mail_distrib",
		'34' => "horario_distrib",
		'35' => "responsable_distrib",
		'36' => "web_distrib",
		'37' => "ubicacion_distrib",
		'38' => "comentarios_distrib",
		'39' => "tel_mac",
		'40' => "mail_mac",
		'41' => "horario_mac",
		'42' => "responsable_mac",
		'43' => "web_mac",
		'44' => "ubicacion_mac",
		'45' => "comentarios_mac",
		'46' => "tel_ile",
		'47' => "mail_ile",
		'48' => "horario_ile",
		'49' => "responsable_ile",
		'50' => "web_ile",
		'51' => "ubicacion_ile",
		'52' => "comentarios_ile",
		'53' => "tel_ssr",
		'54' => "mail_ssr",
		'55' => "horario_ssr",
		'56' => "responsable_ssr",
		'57' => "web_ssr",
		'58' => "ubicacion_ssr",
		'59' => "comentarios_ssr",
		'60' => "tel_dc",
		'61' => "mail_dc",
		'62' => "horario_dc",
		'63' => "responsable_dc",
		'64' => "web_dc",
		'65' => "ubicacion_dc",
		'66' => "comentarios_dc",
		'67' => "servicetype_ile",
		'68' => "servicetype_mac",
		'69' => "servicetype_condones",
		'70' => "servicetype_prueba",
		'71' => "servicetype_ssr",
		'72' => "servicetype_dc",
		'73' => "friendly_ile",
		'74' => "friendly_mac",
		'75' => "friendly_condones",
		'76' => "friendly_prueba",
		'77' => "friendly_ssr",
		'78' => "friendly_dc"
	 );

	$status = true;
	$failColumns = array();
	$columns = array();
	$failColumns['sizeProblem'] = "";


	if ( count($correctCvs) != count($rowColumns)){
		$status = false;
		$failColumns['sizeProblem'] = "Revise la cantidad de columnas ingresadas";
	}
	else {
		for ($i=0; $i < count($rowColumns) ; $i++) {
				if ($correctCvs[$i] != $rowColumns[$i] ){
					$status = false;
					array_push($columns, $correctCvs[$i] );
					continue;
				}
			}
	}

	$failColumns['columns'] = $columns;
	$failColumns['status'] = $status;
	return $failColumns;
}

public function importCsv(Request $request){
	$request_params = $request->all();
	if ($request->hasFile('file'))
		$ext = $request->file('file')->getClientOriginalExtension();

		//cantidad de filas
		$rows = \Excel::load($request->file('file')->getRealPath(), function($reader) {})->get()->toArray();
		$rowCount = count($rows);
		$rowColumns =  array_keys($rows[0]);
		$validateResult = $this->checkAllColumns($rowColumns);

		try {
			if ($rowCount > 400)
				abort(310, "Máximo de Centros Superado");
			else
				if (!$validateResult['status'])
					abort(311, "Problema en la estructura del csv");

		}catch(Exception $e){
			if ($e->getMessage() == "Problema en la estructura del csv")
				throw new CustomException($validateResult, $e->getMessage(),$e->getCode());
			else
				throw new CsvException($e->getMessage());
		}



		if (isset($ext))
			$request_params['tmp'] = ($ext == "csv") ? 1234 : 1234567;

    $rules = array(
		  'tmp' => 'required|max:4'
    );
	$messages = array(
	'required'    => 'Se debe ingresar un archivo antes de continuar!',
	'max'    => 'La extension del archivo tiene que ser .csv y estar separado por comas (",") ');
	$validator = Validator::make($request_params,$rules,$messages);
	if ($validator->fails()) {
		return redirect('panel/importer/picker')
					->withErrors($validator->messages())
					->withInput();
	}
	$params = $request_params;

	$book = $this->csvPrimeraFila($request);

	$tmpFile = Input::file('file')->getClientOriginalName();
	$_SESSION['csvname'] = $tmpFile;
	session(['csvname' => $tmpFile]);
	Storage::disk('local')->put($tmpFile, \File::get($request->file('file')));
	if(!is_null($book['id'])){
		$_SESSION['Actualizar'] = array();
		$_SESSION['cActualizar'] = 0;
		Excel::load(storage_path().'/app/'.$tmpFile, function($reader){
				foreach ($reader->get() as $book) {
					array_push($_SESSION['Actualizar'],$this->agregarActualizar($book));
					$_SESSION['cActualizar']++;
				}
		});

		$datosActualizar = $_SESSION['Actualizar'];
		$cantidadActualizar = $_SESSION['cActualizar'];
		session(['datosActualizar' => $_SESSION['Actualizar']]);

		return view('panel.importer.confirmFast-id',compact('datosActualizar','cantidadActualizar'));
	} else {
		if( (!is_null($book['latitude']))  && (!is_null($book['longitude'])) ) {
           return $this->preAddNoGeo($request);
		} else {
           return $this->preAdd($request);
		}
	}
}

public function confirmAddWhitId(Request $request) {
	$datosActualizar = $request->session()->get('datosActualizar');
	$datosBadActualizar = array();
	$cantidadBadActualizar = 0;
	$csvName = session('csvname');
	session()->forget('datosActualizar');
	$contador = 0;
//creo nuevo tag de importación
	$placeTag = new PlaceLog();
	$placeTag->modification_date = date("Y/m/d");
	$placeTag->entry_type = "update_import";
	$placeTag->user_id = Auth::user()->id;
	$placeTag->csvname = $csvName;
	$placeTag->save();
	session()->forget('csvname');

	for ($i=0; $i < count($datosActualizar); $i++) {
		$existePais = DB::table('pais')
					->where('pais.nombre_pais', '=', $datosActualizar[$i]['pais'])
					->select('pais.id as pais')
					->first();
				$existeProvincia = DB::table('provincia')
					->join('pais','pais.id','=','provincia.idPais')
					->where('pais.nombre_pais', '=', $datosActualizar[$i]['pais'])
					->where('provincia.nombre_provincia', '=', $datosActualizar[$i]['provincia_region'])
					->select('provincia.id as provincia','pais.id as pais')
					->first();
				$existePartido = DB::table('partido')
					->join('provincia','provincia.id','=','partido.idProvincia')
					->join('pais','pais.id','=','partido.idPais')
					->where('pais.nombre_pais', '=', $datosActualizar[$i]['pais'])
					->where('provincia.nombre_provincia', '=', $datosActualizar[$i]['provincia_region'])
					->where('partido.nombre_partido', '=', $datosActualizar[$i]['partido_comuna'])
					->select('partido.id as partido','provincia.id as provincia','pais.id as pais')
					->first();
				$finalIdPais =0;
				$finalIdProvincia = 0;
				$finalIdPartido = 0;
				if ($existePais) $finalIdPais = $existePais->pais;
				if ($existeProvincia) {
					$finalIdPais = $existeProvincia->pais;
					$finalIdProvincia = $existeProvincia->provincia;
				}
				if ($existePartido) {
					$finalIdPais = $existePartido->pais;
					$finalIdPartido = $existePartido->partido;
					$finalIdProvincia = $existePartido->provincia;
				}

		if (!$existePais) {
		//PAIS
			$pais = new Pais;
			$pais->nombre_pais = $datosActualizar[$i]['pais'];
			$pais->save();
			$finalIdPais = $pais->id;
		}//del existe pais
		if (!$existeProvincia) { //CASO 2, no existe la provincia en la BD
		//PROVINCIA
			$provincia = new Provincia;
			$provincia->nombre_provincia = $datosActualizar[$i]['provincia_region'];
			$provincia->idPais = $finalIdPais;
			$provincia->save();
			$finalIdProvincia = $provincia->id;
		}//del provincia
		if (!$existePartido) {  //CASO 3, no existe partido en la BD
		//PARTIDO
			$partido = new Partido;
			$partido->nombre_partido = $datosActualizar[$i]['partido_comuna'];
			$partido->idPais = $finalIdPais;
			$partido->habilitado = 1;
			$partido->idProvincia = $finalIdProvincia;
			$partido->save();
			$finalIdPartido = $partido->id;
		}

		//$datosActualizar[$i]['vacunatorio'] = $this->parseToImport($datosActualizar[$i]['vacunatorio']);
		//$datosActualizar[$i]['infectologia'] = $this->parseToImport($datosActualizar[$i]['infectologia']);
		$datosActualizar[$i]['condones'] = $this->parseToImport($datosActualizar[$i]['condones']);
		$datosActualizar[$i]['prueba'] = $this->parseToImport($datosActualizar[$i]['prueba']);
		$datosActualizar[$i]['mac'] = $this->parseToImport($datosActualizar[$i]['mac']);
		$datosActualizar[$i]['ile'] = $this->parseToImport($datosActualizar[$i]['ile']);
		$datosActualizar[$i]['ssr'] = $this->parseToImport($datosActualizar[$i]['ssr']);
		$datosActualizar[$i]['dc'] = $this->parseToImport($datosActualizar[$i]['dc']);
		$datosActualizar[$i]['es_rapido'] = $this->parseToImport($datosActualizar[$i]['es_rapido']);

		$datosActualizar[$i]['friendly_dc'] = $this->parseToImport($datosActualizar[$i]['friendly_dc']);
		$datosActualizar[$i]['friendly_ssr'] = $this->parseToImport($datosActualizar[$i]['friendly_ssr']);
		$datosActualizar[$i]['friendly_mac'] = $this->parseToImport($datosActualizar[$i]['friendly_mac']);
		$datosActualizar[$i]['friendly_ile'] = $this->parseToImport($datosActualizar[$i]['friendly_ile']);
		$datosActualizar[$i]['friendly_prueba'] = $this->parseToImport($datosActualizar[$i]['friendly_prueba']);
		$datosActualizar[$i]['friendly_condones'] = $this->parseToImport($datosActualizar[$i]['friendly_condones']);

		$datosActualizar[$i]['aprobado'] = 1;
		$datosActualizar[$i]['habilitado'] = 1;

		//PLACES
			$places = Places::find($datosActualizar[$i]['placeId']);
			// echo "existe el id?";

			if (is_null($places)){
				array_push($datosBadActualizar,$this->agregarBadActualizar($datosActualizar[$i]));
				$cantidadBadActualizar++;
				unset($datosActualizar[$i]);
			}
			else{

			$places->idPais = $finalIdPais;
			$places->idProvincia = $finalIdProvincia;
			$places->idPartido = $finalIdPartido;
			$places->establecimiento = $datosActualizar[$i]['establecimiento'];
			$places->tipo = $datosActualizar[$i]['tipo'];
			$places->calle = $datosActualizar[$i]['calle'];
			$places->altura = $datosActualizar[$i]['altura'];
			$places->piso_dpto = $datosActualizar[$i]['piso_dpto'];
			$places->cruce = $datosActualizar[$i]['cruce'];
			$places->barrio_localidad = $datosActualizar[$i]['barrio_localidad'];
			$places->aprobado = 1;
			$places->observacion = $datosActualizar[$i]['observacion'];
			$places->confidence = $datosActualizar[$i]['confidence'];
			$places->formattedAddress = $datosActualizar[$i]['formattedAddress'];
			$places->latitude = $datosActualizar[$i]['latitude'];
			$places->longitude = $datosActualizar[$i]['longitude'];
			$places->habilitado = 1;
		//	$places->vacunatorio = $datosActualizar[$i]['vacunatorio'];
		//	$places->infectologia = $datosActualizar[$i]['infectologia'];
			$places->condones = $datosActualizar[$i]['condones'];
			$places->prueba = $datosActualizar[$i]['prueba'];
			$places->ile = $datosActualizar[$i]['ile']; //lo agregue no estaba
			$places->ssr = $datosActualizar[$i]['ssr'];
			$places->dc = $datosActualizar[$i]['dc'];
			$places->mac = $datosActualizar[$i]['mac'];
			$places->es_rapido = $datosActualizar[$i]['es_rapido'];
			$places->tel_testeo = $datosActualizar[$i]['tel_testeo'];
			$places->mail_testeo = $datosActualizar[$i]['mail_testeo'];
			$places->horario_testeo = $datosActualizar[$i]['horario_testeo'];
			$places->responsable_testeo = $datosActualizar[$i]['responsable_testeo'];
			$places->web_testeo = $datosActualizar[$i]['web_testeo'];
			$places->ubicacion_testeo = $datosActualizar[$i]['ubicacion_testeo'];
			$places->observaciones_testeo = $datosActualizar[$i]['observaciones_testeo'];
			$places->tel_distrib = $datosActualizar[$i]['tel_distrib'];
			$places->mail_distrib = $datosActualizar[$i]['mail_distrib'];
			$places->horario_distrib = $datosActualizar[$i]['horario_distrib'];
			$places->responsable_distrib = $datosActualizar[$i]['responsable_distrib'];
			$places->web_distrib = $datosActualizar[$i]['web_distrib'];
			$places->ubicacion_distrib = $datosActualizar[$i]['ubicacion_distrib'];
			$places->comentarios_distrib = $datosActualizar[$i]['comentarios_distrib'];
		/*	$places->tel_infectologia = $datosActualizar[$i]['tel_infectologia'];
			$places->mail_infectologia = $datosActualizar[$i]['mail_infectologia'];
			$places->horario_infectologia = $datosActualizar[$i]['horario_infectologia'];
			$places->responsable_infectologia = $datosActualizar[$i]['responsable_infectologia'];
			$places->web_infectologia = $datosActualizar[$i]['web_infectologia'];
			$places->ubicacion_infectologia = $datosActualizar[$i]['ubicacion_infectologia'];
			$places->comentarios_infectologia = $datosActualizar[$i]['comentarios_infectologia'];
			$places->tel_vac = $datosActualizar[$i]['tel_vac'];
			$places->mail_vac = $datosActualizar[$i]['mail_vac'];
			$places->horario_vac = $datosActualizar[$i]['horario_vac'];
			$places->responsable_vac = $datosActualizar[$i]['responsable_vac'];
			$places->web_vac = $datosActualizar[$i]['web_vac'];
			$places->ubicacion_vac = $datosActualizar[$i]['ubicacion_vac'];
			$places->comentarios_vac = $datosActualizar[$i]['comentarios_vac'];*/
			$places->tel_mac = $datosActualizar[$i]['tel_mac'];
			$places->mail_mac = $datosActualizar[$i]['mail_mac'];
			$places->horario_mac = $datosActualizar[$i]['horario_mac'];
			$places->responsable_mac = $datosActualizar[$i]['responsable_mac'];
			$places->web_mac = $datosActualizar[$i]['web_mac'];
			$places->ubicacion_mac = $datosActualizar[$i]['ubicacion_mac'];
			$places->comentarios_mac = $datosActualizar[$i]['comentarios_mac'];
			$places->tel_ile = $datosActualizar[$i]['tel_ile'];
			$places->mail_ile = $datosActualizar[$i]['mail_ile'];
			$places->horario_ile = $datosActualizar[$i]['horario_ile'];
			$places->responsable_ile = $datosActualizar[$i]['responsable_ile'];
			$places->web_ile = $datosActualizar[$i]['web_ile'];
			$places->ubicacion_ile = $datosActualizar[$i]['ubicacion_ile'];
			$places->comentarios_ile = $datosActualizar[$i]['comentarios_ile'];

			$places->tel_ssr = $datosActualizar[$i]['tel_ssr'];
			$places->mail_ssr = $datosActualizar[$i]['mail_ssr'];
			$places->horario_ssr = $datosActualizar[$i]['horario_ssr'];
			$places->responsable_ssr = $datosActualizar[$i]['responsable_ssr'];
			$places->web_ssr = $datosActualizar[$i]['web_ssr'];
			$places->ubicacion_ssr = $datosActualizar[$i]['ubicacion_ssr'];
			$places->comentarios_ssr = $datosActualizar[$i]['comentarios_ssr'];
			$places->tel_dc = $datosActualizar[$i]['tel_dc'];
			$places->mail_dc = $datosActualizar[$i]['mail_dc'];
			$places->horario_dc = $datosActualizar[$i]['horario_dc'];
			$places->responsable_dc = $datosActualizar[$i]['responsable_dc'];
			$places->web_dc = $datosActualizar[$i]['web_dc'];
			$places->ubicacion_dc = $datosActualizar[$i]['ubicacion_dc'];
			$places->comentarios_dc = $datosActualizar[$i]['comentarios_dc'];

			$places->servicetype_dc = strtolower($datosActualizar[$i]['servicetype_dc']);
			$places->servicetype_ssr = strtolower($datosActualizar[$i]['servicetype_ssr']);
			$places->servicetype_mac = strtolower($datosActualizar[$i]['servicetype_mac']);
			$places->servicetype_ile = strtolower($datosActualizar[$i]['servicetype_ile']);
			$places->servicetype_prueba = strtolower($datosActualizar[$i]['servicetype_prueba']);
			$places->servicetype_condones = strtolower($datosActualizar[$i]['servicetype_condones']);

			$places->friendly_dc = $datosActualizar[$i]['friendly_dc'];
			$places->friendly_ile = $datosActualizar[$i]['friendly_ile'];
			$places->friendly_mac = $datosActualizar[$i]['friendly_mac'];
			$places->friendly_ssr = $datosActualizar[$i]['friendly_ssr'];
			$places->friendly_prueba = $datosActualizar[$i]['friendly_prueba'];
			$places->friendly_condones = $datosActualizar[$i]['friendly_condones'];
			//$places->ile = $datosActualizar[$i]['ile'];

			//guardo el id del tag en el place
			$places->logId = $placeTag->id;
			$places->save();
			}//del else
			$contador++;
	}//del for


	$cantidadActualizar = sizeof($datosActualizar);
	session(['datosBadActualizar' => $datosBadActualizar]);
	session(['datosActualizar' => $datosActualizar]);

	return view('panel.importer.results-id',compact('datosActualizar','cantidadActualizar','datosBadActualizar','cantidadBadActualizar'));
}


public function preAddNoGeo(Request $request) {

		$_SESSION['NuevosPaises']= array();
		$_SESSION['NuevosProvincia']= array();
		$_SESSION['NuevosPartido']= array();
		$_SESSION['NuevosPlaces']= array();
		$_SESSION['cPais']=0;
		$_SESSION['cProvincia']=0;
		$_SESSION['cPartido']=0;
	   	$tmpFile = Input::file('file')->getClientOriginalName();
	   	$_SESSION['nombreFile'] = $tmpFile;
			session(['csvname' => $tmpFile]);
	   	Storage::disk('local')->put($tmpFile, \File::get($request->file('file') ) );
	   	//Cargo en memoria el csv para desp meterlo en la DB
		Excel::load(storage_path().'/app/'.$tmpFile, function($reader){
			foreach ($reader->get() as $book) {
				if($this->esIncompleto($book))
					continue;
				else{
			                $existePais = DB::table('pais')
			                    ->where('pais.nombre_pais', '=',$book->pais)
			                    ->first();
			                $existeProvincia = DB::table('provincia')
			                    ->join('pais','pais.id','=','provincia.idPais')
			                    ->where('pais.nombre_pais', '=',$book->pais)
			                    ->where('provincia.nombre_provincia', '=', $book->provincia_region)
			                    ->first();
			                if (!isset($latLng['partido'])) $latLng['partido'] = '';
			                $existePartido = DB::table('partido')
			                	->join('provincia','provincia.id','=','partido.idProvincia')
			                	->join('pais','pais.id','=','partido.idPais')
			                    ->where('pais.nombre_pais', '=', $book->pais)
			                    ->where('provincia.nombre_provincia', '=', $book->provincia_region)
			                    ->where('partido.nombre_partido', '=', $book->partido_comuna)
			                    ->first();
			                if (!isset($latLng['route'])) $latLng['route'] = '';
			                $existePlace = DB::table('places')
			                	->join('pais','pais.id','=','places.idPais')
			                	->join('provincia','provincia.id','=','places.idProvincia')
			                    ->join('partido','partido.id','=','places.idProvincia')
			                    ->where('places.establecimiento', 'like', '%' .$book->establecimiento.'%')
			                    ->where('places.tipo', 'like', '%' .$book->tipo.'%')
			                    ->where('places.calle', '=',$latLng['route'])
			                    ->where('places.altura', 'like', '%' .$book->altura.'%')
			                    ->where('places.piso_dpto', 'like', '%' .$book->piso_dpto.'%')
			                    ->where('places.cruce', 'like', '%' .$book->cruce.'%')
			                    ->where('places.latitude', '=', $book->latitude)
			                    ->where('places.longitude', '=', $book->longitude)
			                    ->where('pais.nombre_pais', '=', $book->pais)
			                    ->where('provincia.nombre_provincia', '=', $book->provincia_region)
			                    ->where('partido.nombre_partido', '=', $book->partido_comuna)
			                    ->first();
							if (!$existePais) { //si es nuevo el pais en la BD lo agarro
								//Ahora me fijo si existe en mi variable session
								$salida = true;
									foreach ($_SESSION['NuevosPaises'] as $key => $value) {
											if ( $value ==  $book->pais ){
												$salida = false;
											}
									}
								if ($salida) {
									array_push($_SESSION['NuevosPaises'],$book->pais);
									$_SESSION['cPais']++;
								}
							}
							if (!$existeProvincia) { //si no existe la prov en lectura vs bd
								$salida = true;
									foreach ($_SESSION['NuevosProvincia'] as $key => $value) {
											if ( $value ==  $book->provincia_region ){
												$salida = false;
											}
									}
								if ($salida) {
									array_push($_SESSION['NuevosProvincia'],$book->provincia_region);
									$_SESSION['cProvincia']++;
								}
							}//del if
							if (!$existePartido) {
								$salida = true; // si no existe el partido hay que recorrer la session y agregar si no esta
									foreach ($_SESSION['NuevosPartido'] as $key => $value)
									{
										if ( $value['Partido'] ==  $book->partido_comuna && $value['Provincia'] == $book->provincia_region )
											$salida = false;
									}
								//desp que recorrio todo, si esta en true aun es xq no hay existe
								if ($salida) {
									array_push($_SESSION['NuevosPartido'],array('Partido'=>$book->partido_comuna,'Provincia'=>$book->provincia_region));
									$_SESSION['cPartido']++;
								}
							}
	            }// del else qe no es incompleto
			}//del for each
		});//del exel::load
		//Armo los datos para mostrar
		$nuevosPaises = $_SESSION['NuevosPaises'];
		$nuevosProvincias =$_SESSION['NuevosProvincia'];
		$nuevosPartidos =$_SESSION['NuevosPartido'];
		$cantidadPais = $_SESSION['cPais'];
		$cantidadProvincia = $_SESSION['cProvincia'];
		$cantidadPartido = $_SESSION['cPartido'];
		$nombreFile =  $_SESSION['nombreFile'];
		return view('panel.importer.preview-ng',compact('nuevosPaises','nuevosProvincias','nuevosPartidos','nombreFile','cantidadPais','cantidadProvincia','cantidadPartido'));
}
//=================================================================================================================
//=================================================================================================================
//	RUTA PREVIEW, VISUALIZO LOS NUEVOS DATOS
//=================================================================================================================
//=================================================================================================================
public function preAdd(Request $request) {
	// $request_params = $request->all();
	// if ($request->hasFile('file'))
	// 	$ext = $request->file('file')->getClientOriginalExtension();
	// 	if (isset($ext))
	// 		$request_params['tmp'] = ($ext == "csv") ? 1234 : 1234567;

 //    $rules = array(
	// 	  'tmp' => 'required|max:4'
 //    );
	// $messages = array(
	// 'required'    => 'Se debe ingresar un archivo antes de continuar!',
	// 'max'    => 'La extension del archivo tiene que ser .csv y estar separado por comas (",") ');
	// $validator = Validator::make($request_params,$rules,$messages);
	// if ($validator->fails()) {
	// 	return redirect('panel/importer/picker')
	// 				->withErrors($validator->messages())
	// 				->withInput();
	// }
	// $params = $request_params;

	// session(['datosNuevos' => array()]); //usando el helper
		$_SESSION['NuevosPaises']= array();
		$_SESSION['NuevosProvincia']= array();
		$_SESSION['NuevosPartido']= array();
		$_SESSION['NuevosPlaces']= array();
		$_SESSION['cPais']=0;
		$_SESSION['cProvincia']=0;
		$_SESSION['cPartido']=0;
	   	$tmpFile = Input::file('file')->getClientOriginalName();
	   	$_SESSION['nombreFile'] = $tmpFile;
			$_SESSION['csvname'] = $tmpFile;
			session(['csvname' => $tmpFile]);
	   	Storage::disk('local')->put($tmpFile, \File::get($request->file('file') ) );
	   	//Cargo en memoria el csv para desp meterlo en la DB
		Excel::load(storage_path().'/app/'.$tmpFile, function($reader){
			foreach ($reader->get() as $book) {
				if($this->esIncompleto($book))
					continue;
				else{
						//verificar como queda formado address para ver si es localizable
			            $latLng = new ImportadorController();
			            $latLng = $latLng->geocode($book); // [lati,longi,formatted_address]
			            //retorno
			            if ($latLng){
			            //si se puede localizar arranca la joda de las bds
			                $existePais = DB::table('pais')
			                    ->where('pais.nombre_pais', '=',$latLng['country'])
			                    ->first();
			                $existeProvincia = DB::table('provincia')
			                    ->join('pais','pais.id','=','provincia.idPais')
			                    ->where('pais.nombre_pais', '=',$latLng['country'])
			                    ->where('provincia.nombre_provincia', '=', $latLng['state'])
			                    ->first();
			                if (!isset($latLng['partido'])) $latLng['partido'] = '';
			                $existePartido = DB::table('partido')
			                	->join('provincia','provincia.id','=','partido.idProvincia')
			                	->join('pais','pais.id','=','partido.idPais')
			                    ->where('pais.nombre_pais', '=', $latLng['country'])
			                    ->where('provincia.nombre_provincia', '=', $latLng['state'])
			                    ->where('partido.nombre_partido', '=', $latLng['partido'])
			                    ->first();
			                if (!isset($latLng['route'])) $latLng['route'] = '';
			                $existePlace = DB::table('places')
			                	->join('pais','pais.id','=','places.idPais')
			                	->join('provincia','provincia.id','=','places.idProvincia')
			                    ->join('partido','partido.id','=','places.idProvincia')
			                    ->where('places.establecimiento', 'like', '%' .$book->establecimiento.'%')
			                    ->where('places.tipo', 'like', '%' .$book->tipo.'%')
			                    ->where('places.calle', '=',$latLng['route'])
			                    ->where('places.altura', 'like', '%' .$book->altura.'%')
			                    ->where('places.piso_dpto', 'like', '%' .$book->piso_dpto.'%')
			                    ->where('places.cruce', 'like', '%' .$book->cruce.'%')
			                    ->where('places.latitude', '=', $latLng['lati'])
			                    ->where('places.longitude', '=', $latLng['longi'])
			                    ->where('pais.nombre_pais', '=', $latLng['country'])
			                    ->where('provincia.nombre_provincia', '=', $latLng['state'])
			                    ->where('partido.nombre_partido', '=', $latLng['partido'])
			                    ->first();
							if (!$existePais) { //si es nuevo el pais en la BD lo agarro
								//Ahora me fijo si existe en mi variable session
								$salida = true;
									foreach ($_SESSION['NuevosPaises'] as $key => $value) {
											// if ( $value ==  $book->pais ){
											if ( $value ==  $latLng['country'] ){
												$salida = false;
												//break;
											}
									}
								if ($salida) {
									// array_push($_SESSION['NuevosPaises'],$book->pais);
									array_push($_SESSION['NuevosPaises'],$latLng['country']);
									$_SESSION['cPais']++;
								}
							}
							if (!$existeProvincia) { //si no existe la prov en lectura vs bd
								$salida = true;
									foreach ($_SESSION['NuevosProvincia'] as $key => $value) {
											// if ( $value ==  $book->provincia_region ){
											if ( $value ==  $latLng['state'] ){
												$salida = false;
												//break;
											}
									}
								if ($salida) {
									// array_push($_SESSION['NuevosProvincia'],$book->provincia_region);
									array_push($_SESSION['NuevosProvincia'],$latLng['state']);
									$_SESSION['cProvincia']++;
								}
							}//del if
							if (!$existePartido) {
								$salida = true;
									foreach ($_SESSION['NuevosPartido'] as $key => $value) {
									if (isset($latLng['city'])){ //aca ver esto
												if ( $value['Partido'] ==  $latLng['city'] && $value['Provincia'] == $latLng['state'] )
													$salida = false;
												}
											else
												if (isset($latLng['county']))
											 	if ( $value['Partido'] ==  $latLng['county'] && $value['Provincia'] == $latLng['state'] ){
											 		$salida = false;
											 	}
									}
								if ($salida) {
									if (isset($latLng['city'])){
									array_push($_SESSION['NuevosPartido'],array('Partido'=>$latLng['city'],'Provincia'=>$latLng['state']));
									$_SESSION['cPartido']++;
									}
									else
									{
										array_push($_SESSION['NuevosPartido'],array('Partido'=>'','Provincia'=>$latLng['state']));
										$_SESSION['cPartido']++;
									}
									}
							}
							// elseif (!$existePlace) {
							// }
			            } //del if (%LatLng)
	            }// del else qe no es incompleto
			}//del for each
		});//del exel::load
		//Armo los datos para mostrar
		$nuevosPaises =$_SESSION['NuevosPaises'];
		$nuevosProvincias =$_SESSION['NuevosProvincia'];
		$nuevosPartidos =$_SESSION['NuevosPartido'];
		$cantidadPais = $_SESSION['cPais'];
		$cantidadProvincia = $_SESSION['cProvincia'];
		$cantidadPartido = $_SESSION['cPartido'];
		$nombreFile =  $_SESSION['nombreFile'];
		return view('panel.importer.preview',compact('nuevosPaises','nuevosProvincias','nuevosPartidos','nombreFile','cantidadPais','cantidadProvincia','cantidadPartido'));

}
//=================================================================================================================
//=================================================================================================================
//	PRE ADD   confirmation
//=================================================================================================================
//=================================================================================================================
public function confirmAddNoGeo(Request $request){ //vista results, agrego a BD
	$_SESSION['Nuevos'] = array();
	$_SESSION['Repetidos'] = array();
	$_SESSION['Unificar']= array();
	$_SESSION['Descartados']= array();
	$_SESSION['Incompletos']= array();
   	//Cargo en memoria el csv para desp meterlo en la DB
	Excel::load(storage_path().'/app/'.$request->fileName, function($reader){

		foreach ($reader->get() as $book) {
			// //cambio los SI, NO por 0,1

		//	$book->vacunatorioOri = $book->vacunatorio;
		//	$book->infectologiaOri = $book->infectologia;
			$book->condonesOri = $book->condones;
			$book->pruebaOri = $book->prueba;
			$book->macOri = $book->mac;
			$book->ileOri = $book->ile;
			$book->ssrOri = $book->ssr;
			$book->dcOri = $book->dc;
			$book->es_rapidoOri = $book->es_rapido;

	//		$book->vacunatorio = $this->parseToImport($book->vacunatorio);
		//	$book->infectologia = $this->parseToImport($book->infectologia);
			$book->condones = $this->parseToImport($book->condones);
			$book->prueba = $this->parseToImport($book->prueba);
			$book->mac = $this->parseToImport($book->mac);
			$book->ile = $this->parseToImport($book->ile);
			$book->ssr = $this->parseToImport($book->ssr);
			$book->dc = $this->parseToImport($book->dc);
			$book->es_rapido = $this->parseToImport($book->es_rapido);

			$book->friendly_dc = $this->parseToImport($book->friendly_dc);
			$book->friendly_ssr = $this->parseToImport($book->friendly_ssr);
			$book->friendly_mac = $this->parseToImport($book->friendly_mac);
			$book->friendly_ile = $this->parseToImport($book->friendly_ile);
			$book->friendly_prueba = $this->parseToImport($book->friendly_prueba);
			$book->friendly_condones = $this->parseToImport($book->friendly_condones);

			$faltaAlgo = false;
			if (!isset($book->calle)) $faltaAlgo = true;
			if (!isset($book->barrio_localidad)) $faltaAlgo = true;
			if (!isset($book->partido_comuna)) $faltaAlgo = true;
			if (!isset($book->pais)) $faltaAlgo = true;
			//just in case
			if (!isset($book->latitude)) $faltaAlgo = true;
			if (!isset($book->longitude)) $faltaAlgo = true;

			$latLng = $this->geocode($book);
			if ($this->esIncompletoNoGeo($book)){
			    array_push($_SESSION['Incompletos'],$this->agregarIncompleto($book));
			}
			elseif ($this->esRepetidoNoGeo($book)){
			    array_push($_SESSION['Repetidos'],$this->agregarRepetidoNoGeo($book));
			}
			elseif ($this->esUnificableNoGeo($book)){
			    array_push($_SESSION['Unificar'],$this->agregarUnificableNoGeo($book));
			}
			elseif ($this->esNuevoNoGeo($book)){
			    array_push($_SESSION['Nuevos'],$this->agregarNuevoNoGeo($book,$latLng));
			}

		}//del for each
	});//del exel::load
	$datosNuevos = $_SESSION['Nuevos'];
	$cantidadNuevos = sizeof($datosNuevos);
	session(['datosNuevos' => $_SESSION['Nuevos']]);
	$datosRepetidos = $_SESSION['Repetidos'];
	$cantidadRepetidos = sizeof($_SESSION['Repetidos']);
	session(['datosRepetidos' => $_SESSION['Repetidos']]); //usando el helper
	$datosIncompletos = $_SESSION['Incompletos'];
	$cantidadIncompletos = sizeof($datosIncompletos);
	session(['datosIncompletos' => $datosIncompletos]); //usando el helper
	$datosUnificar = $_SESSION['Unificar'];
	$cantidadUnificar = sizeof($datosUnificar);
	session(['datosUnificar' => $datosUnificar]); //usando el helper
	$datosDescartados = $_SESSION['Descartados'];
	$cantidadDescartados = sizeof($datosDescartados);
	session(['datosDescartados' => $datosDescartados]); //usando el helper
	return view('panel.importer.confirmFast-ng',compact('datosNuevos','cantidadNuevos','datosRepetidos','cantidadRepetidos','datosDescartados','cantidadDescartados','datosIncompletos','cantidadIncompletos','datosUnificar','cantidadUnificar'));
}
//=================================================================================================================
//=================================================================================================================
//	PRE ADD   confirmation
//=================================================================================================================
//=================================================================================================================
public function confirmAdd(Request $request){ //vista results, agrego a BD
	$_SESSION['Nuevos'] = array();
	$_SESSION['Repetidos'] = array();
	$_SESSION['Unificar']= array();
	$_SESSION['Descartados']= array();
	$_SESSION['Incompletos']= array();

   	//Cargo en memoria el csv para desp meterlo en la DB
	Excel::load(storage_path().'/app/'.$request->fileName, function($reader){
		foreach ($reader->get() as $book) {
			$address = $book->calle;
			if (is_numeric($book->altura))
				$address = $address.' '.$book->altura;
			if ($book->partido_comuna != $book->barrio_localidad)
				$address = $address.' '.$book->barrio_localidad;
			$address = $address.' '.$book->partido_comuna;
			$address = $address.' '.$book->provincia_region;
			$address = $address.' '.$book->pais;

			$latLng = new ImportadorController();
            $latLng = $latLng->geocode($book); // [lati,longi,formatted_address]

      //$book->vacunatorioOri = $book->vacunatorio;
			//$book->infectologiaOri = $book->infectologia;
			$book->condonesOri = $book->condones;
			$book->pruebaOri = $book->prueba;
			$book->macOri = $book->mac;
			$book->ileOri = $book->ile;
			$book->ssrOri = $book->ssr;
			$book->dcOri = $book->dc;
			$book->es_rapidoOri = $book->es_rapido;

		//	$book->vacunatorio = $this->parseToImport($book->vacunatorio);
		//	$book->infectologia = $this->parseToImport($book->infectologia);
			$book->condones = $this->parseToImport($book->condones);
			$book->prueba = $this->parseToImport($book->prueba);
			$book->mac = $this->parseToImport($book->mac);
			$book->ile = $this->parseToImport($book->ile);
			$book->ssr = $this->parseToImport($book->ssr);
			$book->dc = $this->parseToImport($book->dc);
			$book->es_rapido = $this->parseToImport($book->es_rapido);

			$book->friendly_dc = $this->parseToImport($book->friendly_dc);
			$book->friendly_ssr = $this->parseToImport($book->friendly_ssr);
			$book->friendly_mac = $this->parseToImport($book->friendly_mac);
			$book->friendly_ile = $this->parseToImport($book->friendly_ile);
			$book->friendly_prueba = $this->parseToImport($book->friendly_prueba);
			$book->friendly_condones = $this->parseToImport($book->friendly_condones);

			$faltaAlgo = false;

			if (!isset($latLng['state'])) $faltaAlgo = true;
			if (!isset($latLng['route'])) $latLng['route'] = $book->calle;
			if (!isset($latLng['city'])) $latLng['city'] = $book->partido_comuna;

			if (!isset($latLng['county'])) {
				if (isset($latLng['city']))
					$latLng['county'] = $latLng['city'];
				else
					$faltaAlgo = true;
			}

			if (!isset($latLng['partido'])) {
				if (isset($latLng['county']))
					$latLng['partido'] = $latLng['county'];
				else
					$faltaAlgo = true;
			}

			if ($this->esIncompleto($book)){
			    array_push($_SESSION['Incompletos'],$this->agregarIncompleto($book));
			}
			elseif ($this->esBajaConfianza($book,$latLng) || $faltaAlgo) {
			    array_push($_SESSION['Descartados'],$this->agregarBajaConfianza($book));
			}
			elseif ($this->esRepetido($book,$latLng)){
			    array_push($_SESSION['Repetidos'],$this->agregarRepetido($book,$latLng));
			}
			elseif ($this->esUnificable($book,$latLng)){
			    array_push($_SESSION['Unificar'],$this->agregarUnificable($book,$latLng));
			}
			elseif ($this->esNuevo($book,$latLng)){
			    array_push($_SESSION['Nuevos'],$this->agregarNuevo($book,$latLng));
			}


		}//del for each
	});//del exel::load
	$datosNuevos = $_SESSION['Nuevos'];
	$cantidadNuevos = sizeof($datosNuevos);
	session(['datosNuevos' => $_SESSION['Nuevos']]); //uasort(array, cmp_function)sando el helper
	// session(['cantidadNuevos' => $cantidadNuevos]); //usando el helper
	$datosRepetidos = $_SESSION['Repetidos'];
	$cantidadRepetidos = sizeof($_SESSION['Repetidos']);
	session(['datosRepetidos' => $_SESSION['Repetidos']]); //usando el helper
	// $cantidadRepetidos = $_SESSION['CantidadRepetidos'];
	// session(['cantidadRepetidos' => $cantidadRepetidos]); //usando el helper
	$datosIncompletos = $_SESSION['Incompletos'];
	$cantidadIncompletos = sizeof($datosIncompletos);
	session(['datosIncompletos' => $datosIncompletos]); //usando el helper
	// $cantidadIncompletos = $_SESSION['CantidadIncompletos'];
	// session(['cantidadIncompletos' => $cantidadIncompletos]); //usando el helper
	$datosUnificar = $_SESSION['Unificar'];
	$cantidadUnificar = sizeof($datosUnificar);
	session(['datosUnificar' => $datosUnificar]); //usando el helper
	// $cantidadUnificar = $_SESSION['CantidadUnificar'];
	// session(['cantidadUnificar' => $cantidadUnificar]); //usando el helper
	$datosDescartados = $_SESSION['Descartados'];
	$cantidadDescartados = sizeof($datosDescartados);
	session(['datosDescartados' => $datosDescartados]); //usando el helper
	// $cantidadDescartados = $_SESSION['CantidadDescartados'];
	// session(['cantidadDescartados' => $cantidadDescartados]); //usando el helper
	return view('panel.importer.confirmFast',compact('datosNuevos','cantidadNuevos','datosRepetidos','cantidadRepetidos','datosDescartados','cantidadDescartados','datosIncompletos','cantidadIncompletos','datosUnificar','cantidadUnificar'));
}
//=================================================================================================================
//=================================================================================================================
//	post ADD   results
//=================================================================================================================
//=================================================================================================================
public function posAdd(Request $request){ //vista results, agrego a BD
	$datosNuevos = $request->session()->get('datosNuevos');
	$cantidadNuevos = sizeof($request->session()->get('datosNuevos') );
	$datosRepetidos = $request->session()->get('datosRepetidos');
	$cantidadRepetidos = sizeof($request->session()->get('datosRepetidos'));
	$datosDescartados = $request->session()->get('datosDescartados');
	$cantidadDescartados = sizeof($request->session()->get('datosDescartados'));
	$datosUnificar = $request->session()->get('datosUnificar');
	$cantidadUnificar = sizeof($request->session()->get('datosUnificar'));
	$datosIncompletos = $request->session()->get('datosIncompletos');
	$cantidadIncompletos = sizeof($request->session()->get('datosIncompletos'));
	$csvName = session('csvname');

	if (session()->get('datosNuevos') != null){

		//creo nuevo tag de importación
			$placeTag = new PlaceLog();
			$placeTag->modification_date = date("Y/m/d");
			$placeTag->entry_type = "import";
			$placeTag->user_id = Auth::user()->id;
			$placeTag->csvname = $csvName;
			$placeTag->save();
			session()->forget('csvname');


		session()->forget('datosNuevos');
		$contador = 0;
		foreach ($datosNuevos as $book) {
				$existePais = DB::table('pais')
					->where('pais.nombre_pais', '=', $book['pais'])
					->select('pais.id as pais')
					->first();
				$existeProvincia = DB::table('provincia')
					->join('pais','pais.id','=','provincia.idPais')
					->where('pais.nombre_pais', '=', $book['pais'])
					->where('provincia.nombre_provincia', '=', $book['provincia_region'])
					->select('provincia.id as provincia','pais.id as pais')
					->first();
				$existePartido = DB::table('partido')
					->join('provincia','provincia.id','=','partido.idProvincia')
					->join('pais','pais.id','=','partido.idPais')
					->where('pais.nombre_pais', '=', $book['pais'])
					->where('provincia.nombre_provincia', '=', $book['provincia_region'])
					->where('partido.nombre_partido', '=', $book['partido_comuna'])
					->select('partido.id as partido','provincia.id as provincia','pais.id as pais')
					->first();
				$finalIdPais =0;
				$finalIdProvincia = 0;
				$finalIdPartido = 0;
				if ($existePais) $finalIdPais = $existePais->pais;
				if ($existeProvincia) {
					$finalIdPais = $existeProvincia->pais;
					$finalIdProvincia = $existeProvincia->provincia;
				}
				if ($existePartido) {
					$finalIdPais = $existePartido->pais;
					$finalIdPartido = $existePartido->partido;
					$finalIdProvincia = $existePartido->provincia;
				}
		if (!$existePais) {
		//PAIS
					$pais = new Pais;
					$pais->nombre_pais = $book['pais'];
					$pais->save();
					$finalIdPais = $pais->id;
		}//del existe pais
		if (!$existeProvincia) { //CASO 2, no existe la provincia en la BD
		//PROVINCIA
			$provincia = new Provincia;
			$provincia->nombre_provincia = $book['provincia_region'];
			$provincia->idPais = $finalIdPais;
			$provincia->save();
			$finalIdProvincia = $provincia->id;
		}//del provincia
		if (!$existePartido) {  //CASO 3, no existe partido en la BD
		//PARTIDO
			$partido = new Partido;
			$partido->nombre_partido = $book['partido_comuna'];
			$partido->idPais = $finalIdPais;
			$partido->habilitado = 1;
			$partido->idProvincia = $finalIdProvincia;
			$partido->save();
			$finalIdPartido = $partido->id;
		}
		//PLACES
			$places = new Places;
			$places->idPais = $finalIdPais;
			$places->idProvincia = $finalIdProvincia;
			$places->idPartido = $finalIdPartido;
			$places->establecimiento = $book['establecimiento'];
			$places->tipo = $book['tipo'];
			$places->calle = $book['calle'];
			$places->altura = $book['altura'];
			$places->piso_dpto = $book['piso_dpto'];
			$places->cruce = $book['cruce'];
			$places->barrio_localidad = $book['barrio_localidad'];
			$places->aprobado = $book['aprobado'];
			$places->observacion = $book['observacion'];
			$places->formattedAddress = $book['formattedAddress'];
			$places->latitude = $book['latitude'];
			$places->longitude = $book['longitude'];
			$places->habilitado = $book['habilitado'];
			$places->confidence = $book['confidence'];
		//	$places->vacunatorio = $book['vacunatorio'];
		//	$places->infectologia = $book['infectologia'];
			$places->condones = $book['condones'];
			$places->prueba = $book['prueba'];
			$places->mac = $book['mac'];
			$places->ile = $book['ile'];
			$places->ssr = $book['ssr'];
			$places->dc = $book['dc'];
			$places->es_rapido = $book['es_rapido'];
			$places->tel_testeo = $book['tel_testeo'];
			$places->mail_testeo = $book['mail_testeo'];
			$places->horario_testeo = $book['horario_testeo'];
			$places->responsable_testeo = $book['responsable_testeo'];
			$places->web_testeo = $book['web_testeo'];
			$places->ubicacion_testeo = $book['ubicacion_testeo'];
			$places->observaciones_testeo = $book['observaciones_testeo'];
			$places->tel_distrib = $book['tel_distrib'];
			$places->mail_distrib = $book['mail_distrib'];
			$places->horario_distrib = $book['horario_distrib'];
			$places->responsable_distrib = $book['responsable_distrib'];
			$places->web_distrib = $book['web_distrib'];
			$places->ubicacion_distrib = $book['ubicacion_distrib'];
			$places->comentarios_distrib = $book['comentarios_distrib'];

			$places->tel_mac = $book['tel_mac'];
			$places->mail_mac = $book['mail_mac'];
			$places->horario_mac = $book['horario_mac'];
			$places->responsable_mac = $book['responsable_mac'];
			$places->web_mac = $book['web_mac'];
			$places->ubicacion_mac = $book['ubicacion_mac'];
			$places->comentarios_mac = $book['comentarios_mac'];
			$places->tel_ile = $book['tel_ile'];
			$places->mail_ile = $book['mail_ile'];
			$places->horario_ile = $book['horario_ile'];
			$places->responsable_ile = $book['responsable_ile'];
			$places->web_ile = $book['web_ile'];
			$places->ubicacion_ile = $book['ubicacion_ile'];
			$places->comentarios_ile = $book['comentarios_ile'];

			$places->tel_ssr = $book['tel_ssr'];
			$places->mail_ssr = $book['mail_ssr'];
			$places->horario_ssr = $book['horario_ssr'];
			$places->responsable_ssr = $book['responsable_ssr'];
			$places->web_ssr = $book['web_ssr'];
			$places->ubicacion_ssr = $book['ubicacion_ssr'];
			$places->comentarios_ssr = $book['comentarios_ssr'];
			$places->tel_dc = $book['tel_dc'];
			$places->mail_dc = $book['mail_dc'];
			$places->horario_dc = $book['horario_dc'];
			$places->responsable_dc = $book['responsable_dc'];
			$places->web_dc = $book['web_dc'];
			$places->ubicacion_dc = $book['ubicacion_dc'];
			$places->comentarios_dc = $book['comentarios_dc'];

			$places->servicetype_dc = $book['servicetype_dc'];
			$places->servicetype_ssr = $book['servicetype_ssr'];
			$places->servicetype_mac = $book['servicetype_mac'];
			$places->servicetype_ile = $book['servicetype_ile'];
			$places->servicetype_prueba = $book['servicetype_prueba'];
			$places->servicetype_condones = $book['servicetype_condones'];

			$places->friendly_dc = $book['friendly_dc'];
			$places->friendly_ile = $book['friendly_ile'];
			$places->friendly_mac = $book['friendly_mac'];
			$places->friendly_ssr = $book['friendly_ssr'];
			$places->friendly_prueba = $book['friendly_prueba'];
			$places->friendly_condones = $book['friendly_condones'];

			$places->logId = $placeTag->id;
			$places->save();
			$book['placeId'] = $places->placeId;
			$datosNuevos[$contador]['placeId'] =$places->placeId;
			$contador++;
		}//foreach
		session(['datosNuevos' => $datosNuevos]);
	} //del if


	if (session()->get('datosUnificar') != null)
	foreach ($datosUnificar as $book) {
		$places = Places::find($book['placeId']);
	//	$places->vacunatorio = $book['vacunatorio'];
		//$places->infectologia = $book['infectologia'];
		$places->condones = $book['condones'];
		$places->prueba = $book['prueba'];
		$places->mac = $book['mac'];
		$places->ile = $book['ile'];
		$places->ssr = $book['ssr'];
		$places->dc = $book['dc'];
		$places->es_rapido = $book['es_rapido'];
		$places->tel_testeo = $book['tel_testeo'];
		$places->mail_testeo = $book['mail_testeo'];
		$places->horario_testeo = $book['horario_testeo'];
		$places->responsable_testeo = $book['responsable_testeo'];
		$places->web_testeo = $book['web_testeo'];
		$places->ubicacion_testeo = $book['ubicacion_testeo'];
		$places->observaciones_testeo = $book['observaciones_testeo'];
		$places->tel_distrib = $book['tel_distrib'];
		$places->mail_distrib = $book['mail_distrib'];
		$places->horario_distrib = $book['horario_distrib'];
		$places->responsable_distrib = $book['responsable_distrib'];
		$places->web_distrib = $book['web_distrib'];
		$places->ubicacion_distrib = $book['ubicacion_distrib'];
		$places->comentarios_distrib = $book['comentarios_distrib'];
	/*	$places->tel_infectologia = $book['tel_infectologia'];
		$places->mail_infectologia = $book['mail_infectologia'];
		$places->horario_infectologia = $book['horario_infectologia'];
		$places->responsable_infectologia = $book['responsable_infectologia'];
		$places->web_infectologia = $book['web_infectologia'];
		$places->ubicacion_infectologia = $book['ubicacion_infectologia'];
		$places->comentarios_infectologia = $book['comentarios_infectologia'];
		$places->tel_vac = $book['tel_vac'];
		$places->mail_vac = $book['mail_vac'];
		$places->horario_vac = $book['horario_vac'];
		$places->responsable_vac = $book['responsable_vac'];
		$places->web_vac = $book['web_vac'];
		$places->ubicacion_vac = $book['ubicacion_vac'];
		$places->comentarios_vac = $book['comentarios_vac'];*/
		$places->tel_mac = $book['tel_mac'];
		$places->mail_mac = $book['mail_mac'];
		$places->horario_mac = $book['horario_mac'];
		$places->responsable_mac = $book['responsable_mac'];
		$places->web_mac = $book['web_mac'];
		$places->ubicacion_mac = $book['ubicacion_mac'];
		$places->comentarios_mac = $book['comentarios_mac'];
		$places->tel_ile = $book['tel_ile'];
		$places->mail_ile = $book['mail_ile'];
		$places->horario_ile = $book['horario_ile'];
		$places->responsable_ile = $book['responsable_ile'];
		$places->web_ile = $book['web_ile'];
		$places->ubicacion_ile = $book['ubicacion_ile'];
		$places->comentarios_ile = $book['comentarios_ile'];

		$places->tel_ssr = $book['tel_ssr'];
		$places->mail_ssr = $book['mail_ssr'];
		$places->horario_ssr = $book['horario_ssr'];
		$places->responsable_ssr = $book['responsable_ssr'];
		$places->web_ssr = $book['web_ssr'];
		$places->ubicacion_ssr = $book['ubicacion_ssr'];
		$places->comentarios_ssr = $book['comentarios_ssr'];
		$places->tel_dc = $book['tel_dc'];
		$places->mail_dc = $book['mail_dc'];
		$places->horario_dc = $book['horario_dc'];
		$places->responsable_dc = $book['responsable_dc'];
		$places->web_dc = $book['web_dc'];
		$places->ubicacion_dc = $book['ubicacion_dc'];
		$places->comentarios_dc = $book['comentarios_dc'];

		$places->servicetype_dc = $book['servicetype_dc'];
		$places->servicetype_ssr = $book['servicetype_ssr'];
		$places->servicetype_mac = $book['servicetype_mac'];
		$places->servicetype_ile = $book['servicetype_ile'];
		$places->servicetype_prueba = $book['servicetype_prueba'];
		$places->servicetype_condones = $book['servicetype_condones'];

		$places->friendly_dc = $book['friendly_dc'];
		$places->friendly_ile = $book['friendly_ile'];
		$places->friendly_mac = $book['friendly_mac'];
		$places->friendly_ssr = $book['friendly_ssr'];
		$places->friendly_prueba = $book['friendly_prueba'];
		$places->friendly_condones = $book['friendly_condones'];

		$places->save();
	}

  return view('panel.importer.results',compact('datosNuevos','cantidadNuevos','datosRepetidos','cantidadRepetidos','datosDescartados','cantidadDescartados','datosIncompletos','cantidadIncompletos','datosUnificar','cantidadUnificar'));
}
//=================================================================================================================
//=================================================================================================================
//	STORE
//=================================================================================================================
//=================================================================================================================
// 	//Importar (Metodo llamado por el Btn Agregar)
// 	public function store(Request $request)
//     {
//     }
// //==============================================================================================================
// 	/**
// 	 * Show the form for creating a new resource.
// 	 *
// 	 * @return Response
// 	 */
// 	public function create()
// 	{
// 		//
// 	}
// 	/**
// 	 * Store a newly created resource in storage.
// 	 *
// 	 * @return Response
// 	 */
// 	/**
// 	 * Display the specified resource.
// 	 *
// 	 * @param  int  $id
// 	 * @return Response
// 	 */
// 	public function show($id)
// 	{
// 		//
// 	}
// 	/**
// 	 * Show the form for editing the specified resource.
// 	 *
// 	 * @param  int  $id
// 	 * @return Response
// 	 */
// 	public function edit($id)
// 	{
// 		//
// 	}
// 	/**
// 	 * Update the specified resource in storage.
// 	 *
// 	 * @param  int  $id
// 	 * @return Response
// 	 */
// 	public function update($id)
// 	{
// 		//
// 	}
// 	/**
// 	 * Remove the specified resource from storage.
// 	 *
// 	 * @param  int  $id
// 	 * @return Response
// 	 */
// 	public function destroy($id)
// 	{
// 		//
// 	}
	public function agregarBadActualizar($book){

        $book = (object)$book;
				//$book->status = 'ADD_BAU';
        return  $this->convertPlaceObjectToArray($book,'ADD_BAU');
    }

public function agregarActualizar($book){

	$book = (object)$book;
	//$book->status = 'ADD_ACT';
	//var_dump($this->convertPlaceObjectToArray($book,'ADD_ACT'));
	return  $this->convertPlaceObjectToArray($book,'ADD_ACT');
    }

	public function agregarIncompleto($book){

		$book = (object)$book;
		//$book->status = 'ADD_INC';
		return  $this->convertPlaceObjectToArray($book,'ADD_INC');
	}

	public function agregarBajaConfianza($book){

		$book = (object)$book;
	//	$book->status = 'ADD_BAC';
		return  $this->convertPlaceObjectToArray($book,'ADD_BAC');
	}

	public function agregarRepetido($book,$latLng){
		$existePlace = DB::table('places')
	    	->join('pais','pais.id','=','places.idPais')
	    	->join('provincia','provincia.id','=','places.idProvincia')
	        ->join('partido','partido.id','=','places.idPartido')
			->where('places.establecimiento','=', $book->establecimiento)
			->where('places.tipo','=', $book->tipo)
			->where('places.calle','=', $latLng['route'])
			->where('places.altura','=', $book->altura)
			->where('places.piso_dpto','=', $book->piso_dpto)
			->where('places.cruce','=', $book->cruce)
			->where('places.observacion','=', $book->observacion)
			->where('places.barrio_localidad','=', $latLng['city']) // no usar debdio a google maps (almagro, etc)
			->where('partido.nombre_partido', '=', $latLng['partido']) // comuna 1,2,3,4
			->where('provincia.nombre_provincia', '=', $latLng['state']) // caba
			->where('pais.nombre_pais', '=', $latLng['country'])
			->where('places.aprobado','=', $book->aprobado)
			->where('places.habilitado','=', $book->habilitado)
			->where('places.condones','=', $book->condones)
			->where('places.prueba','=', $book->prueba)
	//		->where('places.vacunatorio','=', $book->vacunatorio)
		//	->where('places.infectologia','=', $book->infectologia)
			->where('places.mac','=', $book->mac)
			->where('places.ile','=', $book->ile)
			->where('places.ssr','=', $book->ssr)
			->where('places.dc','=', $book->dc)
			->where('places.es_rapido','=', $book->es_rapido)
			->where('places.tel_testeo','=', $book->tel_testeo)
			->where('places.mail_testeo','=', $book->mail_testeo)
			->where('places.horario_testeo','=', $book->horario_testeo)
			->where('places.responsable_testeo','=', $book->responsable_testeo)
			->where('places.web_testeo','=', $book->web_testeo)
			->where('places.ubicacion_testeo','=', $book->ubicacion_testeo)
			->where('places.observaciones_testeo','=', $book->observaciones_testeo)
			->where('places.tel_distrib','=', $book->tel_distrib)
			->where('places.mail_distrib','=', $book->mail_distrib)
			->where('places.horario_distrib','=', $book->horario_distrib)
			->where('places.responsable_distrib','=', $book->responsable_distrib)
			->where('places.web_distrib','=', $book->web_distrib)
			->where('places.ubicacion_distrib','=', $book->ubicacion_distrib)
			->where('places.comentarios_distrib','=', $book->comentarios_distrib)
		/*	->where('places.tel_infectologia','=', $book->tel_infectologia)
			->where('places.mail_infectologia','=', $book->mail_infectologia)
			->where('places.horario_infectologia','=', $book->horario_infectologia)
			->where('places.responsable_infectologia','=', $book->responsable_infectologia)
			->where('places.web_infectologia','=', $book->web_infectologia)
			->where('places.ubicacion_infectologia','=', $book->ubicacion_infectologia)
			->where('places.comentarios_infectologia','=', $book->comentarios_infectologia)
			->where('places.tel_vac','=', $book->tel_vac)
			->where('places.mail_vac','=', $book->mail_vac)
			->where('places.horario_vac','=', $book->horario_vac)
			->where('places.responsable_vac','=', $book->responsable_vac)
			->where('places.web_vac','=', $book->web_vac)
			->where('places.ubicacion_vac','=', $book->ubicacion_vac)
			->where('places.comentarios_vac','=', $book->comentarios_vac)*/
			->where('places.tel_mac','=', $book->tel_mac)
			->where('places.mail_mac','=', $book->mail_mac)
			->where('places.horario_mac','=', $book->horario_mac)
			->where('places.responsable_mac','=', $book->responsable_mac)
			->where('places.web_mac','=', $book->web_mac)
			->where('places.ubicacion_mac','=', $book->ubicacion_mac)
			->where('places.comentarios_mac','=', $book->comentarios_mac)

			->where('places.tel_ile','=', $book->tel_ile)
			->where('places.mail_ile','=', $book->mail_ile)
			->where('places.horario_ile','=', $book->horario_ile)
			->where('places.responsable_ile','=', $book->responsable_ile)
			->where('places.web_ile','=', $book->web_ile)
			->where('places.ubicacion_ile','=', $book->ubicacion_ile)
			->where('places.comentarios_ile','=', $book->comentarios_ile)

			->where('places.tel_ssr','=', $book->tel_ssr)
			->where('places.mail_ssr','=', $book->mail_ssr)
			->where('places.horario_ssr','=', $book->horario_ssr)
			->where('places.responsable_ssr','=', $book->responsable_ssr)
			->where('places.web_ssr','=', $book->web_ssr)
			->where('places.ubicacion_ssr','=', $book->ubicacion_ssr)
			->where('places.comentarios_ssr','=', $book->comentarios_ssr)

			->where('places.tel_dc','=', $book->tel_dc)
			->where('places.mail_dc','=', $book->mail_dc)
			->where('places.horario_dc','=', $book->horario_dc)
			->where('places.responsable_dc','=', $book->responsable_dc)
			->where('places.web_dc','=', $book->web_dc)
			->where('places.ubicacion_dc','=', $book->ubicacion_dc)
			->where('places.comentarios_dc','=', $book->comentarios_dc)

			->where('places.servicetype_ile','=', $book->servicetype_ile)
			->where('places.servicetype_mac','=', $book->servicetype_mac)
			->where('places.servicetype_condones','=', $book->servicetype_condones)
			->where('places.servicetype_prueba','=', $book->servicetype_prueba)
			->where('places.servicetype_ssr','=', $book->servicetype_ssr)
			->where('places.servicetype_dc','=', $book->servicetype_dc)

			->where('places.friendly_dc','=', $book->friendly_dc)
			->where('places.friendly_ssr','=', $book->friendly_ssr)
			->where('places.friendly_mac','=', $book->friendly_mac)
			->where('places.friendly_ile','=', $book->friendly_ile)
			->where('places.friendly_prueba','=', $book->friendly_prueba)
			->where('places.friendly_condones','=', $book->friendly_condones)

			->first();

			if (!isset($latLng['confidence']) ) $latLng['confidence']=0;
			if (!isset($latLng['country']) ) $latLng['country']=0;
			if (!isset($latLng['partido']) ) $latLng['partido']=0;
			if (!isset($latLng['state']) ) $latLng['state']=0;
			if (!isset($latLng['city']) ) $latLng['city']=0;
			if (!isset($latLng['route']) ) $latLng['route']=0;
			if (!isset($latLng['lati']) ) $latLng['lati']=0;
			if (!isset($latLng['longi']) ) $latLng['longi']=0;

		return array(
				'status' => 'ADD_REPITED',
				'placeId' => $existePlace->placeId,
				'pais' => $latLng['country'],
				'establecimiento' => $book->establecimiento,
				'partido_comuna' => $latLng['partido'], //comuna 3
				'provincia_region' => $latLng['state'], //caba
				'barrio_localidad' => $latLng['city'], //
				'tipo' => $book->tipo,
				'calle' => $latLng['route'],
				'altura' => $book->altura,
				'piso_dpto' => $book->piso_dpto,
				'cruce' => $book->cruce,
				'aprobado' => $book->aprobado,
				'observacion' => $book->observacion,
				'latitude' => $latLng['lati'],
				'longitude' => $latLng['longi'],
				'confidence' => $latLng['confidence'],
				'formattedAddress' => $latLng['formatted_address'],
				'habilitado' => $book->habilitado,
			//	'vacunatorio' => $book->vacunatorio,
			//	'infectologia' => $book->infectologia,
				'condones' => $book->condones,
				'prueba' => $book->prueba,
				'ssr' => $book->ssr,
				'dc' => $book->dc,
				'es_rapido' => $book->es_rapido,
				'tel_testeo' => $book->tel_testeo,
				'mail_testeo' => $book->mail_testeo,
				'horario_testeo' => $book->horario_testeo,
				'responsable_testeo' => $book->responsable_testeo,
				'web_testeo' => $book->web_testeo,
				'ubicacion_testeo' => $book->ubicacion_testeo,
				'observaciones_testeo' => $book->observaciones_testeo,
				'tel_distrib' => $book->tel_distrib,
				'mail_distrib' => $book->mail_distrib,
				'horario_distrib' => $book->horario_distrib,
				'responsable_distrib' => $book->responsable_distrib,
				'web_distrib' => $book->web_distrib,
				'ubicacion_distrib' => $book->ubicacion_distrib,
				'comentarios_distrib' => $book->comentarios_distrib,
			/*	'tel_infectologia' => $book->tel_infectologia,
				'mail_infectologia' => $book->mail_infectologia,
				'horario_infectologia' => $book->horario_infectologia,
				'responsable_infectologia' => $book->responsable_infectologia,
				'web_infectologia' => $book->web_infectologia,
				'ubicacion_infectologia' => $book->ubicacion_infectologia,
				'comentarios_infectologia' => $book->comentarios_infectologia,
				'tel_vac' => $book->tel_vac,
				'mail_vac' => $book->mail_vac,
				'horario_vac' => $book->horario_vac,
				'responsable_vac' => $book->responsable_vac,
				'web_vac' => $book->web_vac,
				'ubicacion_vac' => $book->ubicacion_vac, //posible problema
				'comentarios_vac' => $book->comentarios_vac,*/
				'tel_mac' => $book->tel_mac,
				'mail_mac' => $book->mail_mac,
				'horario_mac' => $book->horario_mac,
				'responsable_mac' => $book->responsable_mac,
				'web_mac' => $book->web_mac,
				'ubicacion_mac' => $book->ubicacion_mac,
				'comentarios_mac' => $book->comentarios_mac,
				'tel_ile' => $book->tel_ile,
				'mail_ile' => $book->mail_ile,
				'horario_ile' => $book->horario_ile,
				'responsable_ile' => $book->responsable_ile,
				'web_ile' => $book->web_ile,
				'ubicacion_ile' => $book->ubicacion_ile,
				'comentarios_ile' => $book->comentarios_ile,
				'mac' => $book->mac,
				'ile' => $book->ile,
				'tel_ssr' => $book->tel_ssr,
				'mail_ssr' => $book->mail_ssr,
				'horario_ssr' => $book->horario_ssr,
				'responsable_ssr' => $book->responsable_ssr,
				'web_ssr' => $book->web_ssr,
				'ubicacion_ssr' => $book->ubicacion_ssr,
				'comentarios_ssr' => $book->comentarios_ssr,
				'tel_dc' => $book->tel_dc,
				'mail_dc' => $book->mail_dc,
				'horario_dc' => $book->horario_dc,
				'responsable_dc' => $book->responsable_dc,
				'web_dc' => $book->web_dc,
				'ubicacion_dc' => $book->ubicacion_dc,
				'comentarios_dc' => $book->comentarios_dc,
				'servicetype_dc' => $book->servicetype_dc,
				'servicetype_ssr' => $book->servicetype_ssr,
				'servicetype_mac' => $book->servicetype_mac,
				'servicetype_ile' => $book->servicetype_ile,
				'servicetype_prueba' => $book->servicetype_prueba,
				'servicetype_condones' => $book->servicetype_condones
		);
	}
	public function agregarRepetidoNoGeo($book){
		$existePlace = DB::table('places')
    	->join('pais','pais.id','=','places.idPais')
    	->join('provincia','provincia.id','=','places.idProvincia')
        ->join('partido','partido.id','=','places.idPartido')
		->where('places.establecimiento', $book->establecimiento)
		->where('places.tipo', $book->tipo)
		->where('places.calle', $book->calle)
		->where('places.altura', $book->altura)
		->where('places.piso_dpto', $book->piso_dpto)
		->where('places.cruce', $book->cruce)
		->where('places.barrio_localidad', $book->barrio_localidad)
		->where('partido.nombre_partido',  $book->partido_comuna)
		->where('provincia.nombre_provincia',  $book->provincia_region)
		->where('pais.nombre_pais',  $book->pais)
		->where('places.aprobado', $book->aprobado)
		->where('places.observacion', $book->observacion)
		->where('places.habilitado', $book->habilitado)
		->where('places.latitude', $book->latitude)
		->where('places.longitude', $book->longitude)

		->where('places.condones', $book->condones)
		->where('places.prueba', $book->prueba)
	//	->where('places.vacunatorio', $book->vacunatorio)
	//	->where('places.infectologia', $book->infectologia)
		->where('places.mac', $book->mac)
		->where('places.ile', $book->ile)
		->where('places.ssr', $book->ssr)
		->where('places.dc', $book->dc)
		->where('places.es_rapido', $book->es_rapido)

		->where('places.tel_testeo', $book->tel_testeo)
		->where('places.mail_testeo', $book->mail_testeo)
		->where('places.horario_testeo', $book->horario_testeo)
		->where('places.responsable_testeo', $book->responsable_testeo)
		->where('places.web_testeo', $book->web_testeo)
		->where('places.ubicacion_testeo', $book->ubicacion_testeo)
		->where('places.observaciones_testeo', $book->observaciones_testeo)

		->where('places.tel_distrib', $book->tel_distrib)
		->where('places.mail_distrib', $book->mail_distrib)
		->where('places.horario_distrib', $book->horario_distrib)
		->where('places.responsable_distrib', $book->responsable_distrib)
		->where('places.web_distrib', $book->web_distrib)
		->where('places.ubicacion_distrib', $book->ubicacion_distrib)
		->where('places.comentarios_distrib', $book->comentarios_distrib)
/*
		->where('places.tel_infectologia', $book->tel_infectologia)
		->where('places.mail_infectologia', $book->mail_infectologia)
		->where('places.horario_infectologia', $book->horario_infectologia)
		->where('places.responsable_infectologia', $book->responsable_infectologia)
		->where('places.web_infectologia', $book->web_infectologia)
		->where('places.ubicacion_infectologia', $book->ubicacion_infectologia)
		->where('places.comentarios_infectologia', $book->comentarios_infectologia)

		->where('places.tel_vac', $book->tel_vac)
		->where('places.mail_vac', $book->mail_vac)
		->where('places.horario_vac', $book->horario_vac)
		->where('places.responsable_vac', $book->responsable_vac)
		->where('places.web_vac', $book->web_vac)
		->where('places.ubicacion_vac', $book->ubicacion_vac)
		->where('places.comentarios_vac', $book->comentarios_vac)
*/
		->where('places.tel_mac', $book->tel_mac)
		->where('places.mail_mac', $book->mail_mac)
		->where('places.horario_mac', $book->horario_mac)
		->where('places.responsable_mac', $book->responsable_mac)
		->where('places.web_mac', $book->web_mac)
		->where('places.ubicacion_mac', $book->ubicacion_mac)
		->where('places.comentarios_mac', $book->comentarios_mac)

		->where('places.tel_ile', $book->tel_ile)
		->where('places.mail_ile', $book->mail_ile)
		->where('places.horario_ile', $book->horario_ile)
		->where('places.responsable_ile', $book->responsable_ile)
		->where('places.web_ile', $book->web_ile)
		->where('places.ubicacion_ile', $book->ubicacion_ile)
		->where('places.comentarios_ile', $book->comentarios_ile)

		->where('places.tel_ssr','=', $book->tel_ssr)
		->where('places.mail_ssr','=', $book->mail_ssr)
		->where('places.horario_ssr','=', $book->horario_ssr)
		->where('places.responsable_ssr','=', $book->responsable_ssr)
		->where('places.web_ssr','=', $book->web_ssr)
		->where('places.ubicacion_ssr','=', $book->ubicacion_ssr)
		->where('places.comentarios_ssr','=', $book->comentarios_ssr)

		->where('places.tel_dc','=', $book->tel_dc)
		->where('places.mail_dc','=', $book->mail_dc)
		->where('places.horario_dc','=', $book->horario_dc)
		->where('places.responsable_dc','=', $book->responsable_dc)
		->where('places.web_dc','=', $book->web_dc)
		->where('places.ubicacion_dc','=', $book->ubicacion_dc)
		->where('places.comentarios_dc','=', $book->comentarios_dc)

		->where('places.servicetype_ile','=', $book->servicetype_ile)
		->where('places.servicetype_mac','=', $book->servicetype_mac)
		->where('places.servicetype_condones','=', $book->servicetype_condones)
		->where('places.servicetype_prueba','=', $book->servicetype_prueba)
		->where('places.servicetype_ssr','=', $book->servicetype_ssr)
		->where('places.servicetype_dc','=', $book->servicetype_dc)

		->first();


 		$placeArray = $this->convertPlaceObjectToArray($book,'ADD_REPITED');
		$placeArray['placeId'] = $existePlace->placeId;
		return $placeArray;
/*
		return array(
				'status' => 'ADD_REPITED',
				'placeId' => $existePlace->placeId,
				'pais' => $book->pais,
				'establecimiento' => $book->establecimiento,
				'partido_comuna' => $book->partido_comuna, //comuna 3
				'provincia_region' => $book->provincia_region, //caba
				'barrio_localidad' => $book->barrio_localidad, //
				'tipo' => $book->tipo,
				'calle' => $book->calle,
				'altura' => $book->altura,
				'piso_dpto' => $book->piso_dpto,
				'cruce' => $book->cruce,
				'aprobado' => $book->aprobado,
				'observacion' => $book->observacion,
				'latitude' => $book->latitude,
				'longitude' => $book->longitude,
				'confidence' => $book->confidence,
				'formattedAddress' => $book->formatted_address,
				'habilitado' => $book->habilitado,
				'condones' => $book->condones,
				'prueba' => $book->prueba,
				'es_rapido' => $book->es_rapido,
				//'vacunatorio' => $book->vacunatorio,
				//'infectologia' => $book->infectologia,
				'tel_testeo' => $book->tel_testeo,
				'mail_testeo' => $book->mail_testeo,
				'horario_testeo' => $book->horario_testeo,
				'responsable_testeo' => $book->responsable_testeo,
				'web_testeo' => $book->web_testeo,
				'ubicacion_testeo' => $book->ubicacion_testeo,
				'observaciones_testeo' => $book->observaciones_testeo,
				'tel_distrib' => $book->tel_distrib,
				'mail_distrib' => $book->mail_distrib,
				'horario_distrib' => $book->horario_distrib,
				'responsable_distrib' => $book->responsable_distrib,
				'web_distrib' => $book->web_distrib,
				'ubicacion_distrib' => $book->ubicacion_distrib,
				'comentarios_distrib' => $book->comentarios_distrib,

				'tel_mac' => $book->tel_mac,
				'mail_mac' => $book->mail_mac,
				'horario_mac' => $book->horario_mac,
				'responsable_mac' => $book->responsable_mac,
				'web_mac' => $book->web_mac,
				'ubicacion_mac' => $book->ubicacion_mac,
				'comentarios_mac' => $book->comentarios_mac,
				'tel_ile' => $book->tel_ile,
				'mail_ile' => $book->mail_ile,
				'horario_ile' => $book->horario_ile,
				'responsable_ile' => $book->responsable_ile,
				'web_ile' => $book->web_ile,
				'ubicacion_ile' => $book->ubicacion_ile,
				'comentarios_ile' => $book->comentarios_ile,
				'mac' => $book->mac,
				'ile' => $book->ile,
				'ssr' => $book->ssr,
				'dc' => $book->dc,
				'tel_ssr' => $book->tel_ssr,
				'mail_ssr' => $book->mail_ssr,
				'horario_ssr' => $book->horario_ssr,
				'responsable_ssr' => $book->responsable_ssr,
				'web_ssr' => $book->web_ssr,
				'ubicacion_ssr' => $book->ubicacion_ssr,
				'comentarios_ssr' => $book->comentarios_ssr,
				'tel_dc' => $book->tel_dc,
				'mail_dc' => $book->mail_dc,
				'horario_dc' => $book->horario_dc,
				'responsable_dc' => $book->responsable_dc,
				'web_dc' => $book->web_dc,
				'ubicacion_dc' => $book->ubicacion_dc,
				'comentarios_dc' => $book->comentarios_dc,
				'servicetype_dc' => $book->servicetype_dc,
				'servicetype_ssr' => $book->servicetype_ssr,
				'servicetype_mac' => $book->servicetype_mac,
				'servicetype_ile' => $book->servicetype_ile,
				'servicetype_prueba' => $book->servicetype_prueba,
				'servicetype_condones' => $book->servicetype_condones
		);
	*/
	}
	public function agregarUnificable($book,$latLng){
		$existePlace = DB::table('places')
	        ->join('pais','pais.id','=','places.idPais')
	    	->join('provincia','provincia.id','=','places.idProvincia')
	        ->join('partido','partido.id','=','places.idPartido')
			->where('places.establecimiento','=', $book->establecimiento)
			->where('places.tipo','=', $book->tipo)
			->where('places.calle','=', $latLng['route'])
			->where('places.altura','=', $book->altura)
			->where('places.piso_dpto','=', $book->piso_dpto)
			->where('places.cruce','=', $book->cruce)//este rompe con
			->where('places.barrio_localidad','=', $latLng['city']) // no usar debdio a google maps (almagro, etc)
			->where('provincia.nombre_provincia', '=', $latLng['state']) // caba
			->where('partido.nombre_partido', '=', $latLng['partido']) // comuna 1,2,3,4
			->where('pais.nombre_pais', '=', $latLng['country'])
			// ->where('places.observacion','=', $book->observacion)
			// ->where('places.aprobado','=', $book->aprobado)
			// ->where('places.habilitado','=', $book->habilitado)
			->first();


			if (!isset($latLng['confidence']) ) $latLng['confidence']=0;
			if (!isset($latLng['country']) ) $latLng['country']=0;
			if (!isset($latLng['partido']) ) $latLng['partido']=0;
			if (!isset($latLng['state']) ) $latLng['state']=0;
			if (!isset($latLng['city']) ) $latLng['city']=0;
			if (!isset($latLng['route']) ) $latLng['route']=0;
			if (!isset($latLng['lati']) ) $latLng['lati']=0;
			if (!isset($latLng['longi']) ) $latLng['longi']=0;


		return array(
			'status' => 'ADD_UNI',
			'placeId' => $existePlace->placeId,
			'pais' => $latLng['country'],
			'provincia_region' => $latLng['state'],
			'partido_comuna' => $latLng['partido'],
			'barrio_localidad' => $latLng['city'],
			'establecimiento' => $book->establecimiento,
			'tipo' => $book->tipo,
			'calle' => $book->calle,
			'altura' => $book->altura,
			'piso_dpto' => $book->piso_dpto,
			'cruce' => $book->cruce,
			'aprobado' => $book->aprobado,
			'observacion' => $book->observacion,
			'latitude' => $latLng['lati'],
			'longitude' => $latLng['longi'],
			'confidence' => $latLng['confidence'],
			'formattedAddress' => $latLng['formatted_address'],
			'habilitado' => $book->habilitado,
		//	'vacunatorio' => $this->correctValueService($existePlace->vacunatorio,$book->vacunatorioOri),
		//	'infectologia' => $this->correctValueService($existePlace->infectologia,$book->infectologiaOri),
			'condones' => $this->correctValueService($existePlace->condones,$book->condonesOri),
			'prueba' => $this->correctValueService($existePlace->prueba,$book->pruebaOri),
			'ssr' => $this->correctValueService($existePlace->ssr,$book->ssrOri),
			'dc' => $this->correctValueService($existePlace->dc,$book->dcOri),
			'es_rapido' => $this->correctValueService($existePlace->es_rapido,$book->es_rapidoOri),
			'tel_testeo' => $this->correctValue($existePlace->tel_testeo,$book->tel_testeo),
			'mail_testeo' => $this->correctValue($existePlace->mail_testeo,$book->mail_testeo),
			'horario_testeo' => $this->correctValue($existePlace->horario_testeo,$book->horario_testeo),
			'responsable_testeo' => $this->correctValue($existePlace->responsable_testeo,$book->responsable_testeo),
			'web_testeo' => $this->correctValue($existePlace->web_testeo,$book->web_testeo),
			'ubicacion_testeo' => $this->correctValue($existePlace->ubicacion_testeo,$book->ubicacion_testeo),
			'observaciones_testeo' => $this->correctValue($existePlace->observaciones_testeo,$book->observaciones_testeo),
			'tel_distrib' => $this->correctValue($existePlace->tel_distrib,$book->tel_distrib),
			'mail_distrib' => $this->correctValue($existePlace->mail_distrib,$book->mail_distrib),
			'horario_distrib' => $this->correctValue($existePlace->horario_distrib,$book->horario_distrib),
			'responsable_distrib' => $this->correctValue($existePlace->responsable_distrib,$book->responsable_distrib),
			'web_distrib' => $this->correctValue($existePlace->web_distrib,$book->web_distrib),
			'ubicacion_distrib' => $this->correctValue($existePlace->ubicacion_distrib,$book->ubicacion_distrib),
			'comentarios_distrib' => $this->correctValue($existePlace->comentarios_distrib,$book->comentarios_distrib),
		/*	'tel_infectologia' => $this->correctValue($existePlace->tel_infectologia,$book->tel_infectologia),
			'mail_infectologia' => $this->correctValue($existePlace->mail_infectologia,$book->mail_infectologia),
			'horario_infectologia' => $this->correctValue($existePlace->horario_infectologia,$book->horario_infectologia),
			'responsable_infectologia' => $this->correctValue($existePlace->responsable_infectologia,$book->responsable_infectologia),
			'web_infectologia' => $this->correctValue($existePlace->web_infectologia,$book->web_infectologia),
			'ubicacion_infectologia' => $this->correctValue($existePlace->ubicacion_infectologia,$book->ubicacion_infectologia),
			'comentarios_infectologia' => $this->correctValue($existePlace->comentarios_infectologia,$book->comentarios_infectologia),
			'tel_vac' => $this->correctValue($existePlace->tel_vac,$book->tel_vac),
			'mail_vac' => $this->correctValue($existePlace->mail_vac,$book->mail_vac),
			'horario_vac' => $this->correctValue($existePlace->horario_vac,$book->horario_vac),
			'responsable_vac' => $this->correctValue($existePlace->responsable_vac,$book->responsable_vac),
			'web_vac' => $this->correctValue($existePlace->web_vac,$book->web_vac),
			'ubicacion_vac' => $this->correctValue($existePlace->ubicacion_vac,$book->ubicacion_vac),
			'comentarios_vac' => $this->correctValue($existePlace->comentarios_vac,$book->comentarios_vac),*/
			'tel_mac' => $this->correctValue($existePlace->tel_mac,$book->tel_mac),
			'mail_mac' => $this->correctValue($existePlace->mail_mac,$book->mail_mac),
			'horario_mac' => $this->correctValue($existePlace->horario_mac,$book->horario_mac),
			'responsable_mac' => $this->correctValue($existePlace->responsable_mac,$book->responsable_mac),
			'web_mac' => $this->correctValue($existePlace->web_mac,$book->web_mac),
			'ubicacion_mac' => $this->correctValue($existePlace->ubicacion_mac,$book->ubicacion_mac),
			'comentarios_mac' => $this->correctValue($existePlace->comentarios_mac,$book->comentarios_mac),
			'tel_ile' => $this->correctValue($existePlace->tel_ile,$book->tel_ile),
			'mail_ile' => $this->correctValue($existePlace->mail_ile,$book->mail_ile),
			'horario_ile' => $this->correctValue($existePlace->horario_ile,$book->horario_ile),
			'responsable_ile' => $this->correctValue($existePlace->responsable_ile,$book->responsable_ile),
			'web_ile' => $this->correctValue($existePlace->web_ile,$book->web_ile),
			'ubicacion_ile' => $this->correctValue($existePlace->ubicacion_ile,$book->ubicacion_ile),
			'comentarios_ile' => $this->correctValue($existePlace->comentarios_ile,$book->comentarios_ile),
			'mac' => $this->correctValueService($existePlace->mac,$book->macOri),
			'ile' => $this->correctValueService($existePlace->ile,$book->ileOri),

			'tel_ssr' => $this->correctValue($existePlace->tel_ssr,$book->tel_ssr),
			'mail_ssr' => $this->correctValue($existePlace->mail_ssr,$book->mail_ssr),
			'horario_ssr' => $this->correctValue($existePlace->horario_ssr,$book->horario_ssr),
			'responsable_ssr' => $this->correctValue($existePlace->responsable_ssr,$book->responsable_ssr),
			'web_ssr' => $this->correctValue($existePlace->web_ssr,$book->web_ssr),
			'ubicacion_ssr' => $this->correctValue($existePlace->ubicacion_ssr,$book->ubicacion_ssr),
			'comentarios_ssr' => $this->correctValue($existePlace->comentarios_ssr,$book->comentarios_ssr),
			'tel_dc' => $this->correctValue($existePlace->tel_dc,$book->tel_dc),
			'mail_dc' => $this->correctValue($existePlace->mail_dc,$book->mail_dc),
			'horario_dc' => $this->correctValue($existePlace->horario_dc,$book->horario_dc),
			'responsable_dc' => $this->correctValue($existePlace->responsable_dc,$book->responsable_dc),
			'web_dc' => $this->correctValue($existePlace->web_dc,$book->web_dc),
			'ubicacion_dc' => $this->correctValue($existePlace->ubicacion_dc,$book->ubicacion_dc),
			'comentarios_dc' => $this->correctValue($existePlace->comentarios_dc,$book->comentarios_dc),

			'mac' => $this->correctValueService($existePlace->mac,$book->macOri),
			'ile' => $this->correctValueService($existePlace->ile,$book->ileOri),

			'servicetype_dc' => $this->correctValue($existePlace->servicetype_dc,$book->servicetype_dc),
			'servicetype_ssr' => $this->correctValue($existePlace->servicetype_ssr,$book->servicetype_ssr),
			'servicetype_mac' => $this->correctValue($existePlace->servicetype_mac,$book->servicetype_mac),
			'servicetype_ile' => $this->correctValue($existePlace->servicetype_ile,$book->servicetype_ile),
			'servicetype_prueba' => $this->correctValue($existePlace->servicetype_prueba,$book->servicetype_prueba),
			'servicetype_condones' => $this->correctValue($existePlace->servicetype_condones,$book->servicetype_condones),

			'friendly_ssr' => $this->correctValue($existePlace->friendly_ssr,$book->friendly_ssr),
			'friendly_dc' => $this->correctValue($existePlace->friendly_dc,$book->friendly_dc),
			'friendly_ile' => $this->correctValue($existePlace->friendly_ile,$book->friendly_ile),
			'friendly_mac' => $this->correctValue($existePlace->friendly_mac,$book->friendly_mac),
			'friendly_prueba' => $this->correctValue($existePlace->friendly_prueba,$book->friendly_prueba),
			'friendly_condones' => $this->correctValue($existePlace->friendly_condones,$book->friendly_condones)
		);
	}

	public function correctValue($old,$new)
	{
	// echo "este";
		if (!isset($new) || $new == "" || $new == " " || $new == "  " || $new == "   " || $new == "    " || is_null($new)) {//si nuevo esta vacio no perder el viejo
			return $old;
		} else {
			return $new;
		}
	}
	public function correctValueService($old,$new){
		 $resu = 999;
		 if (trim($new) == "NO") $new = 0;
		 if (trim($new) == "SI") $new = 1;


		 if (is_null($new)) {//si nuevo esta vacio no perder el viejo
			$resu = $old;
		} else {
			$resu = $new;
		}

		return $resu;
	}

	public function agregarUnificableNoGeo($book){
		$existePlace = DB::table('places')
	        ->join('pais','pais.id','=','places.idPais')
	    	->join('provincia','provincia.id','=','places.idProvincia')
	        ->join('partido','partido.id','=','places.idPartido')
			->where('places.establecimiento','=', $book->establecimiento)
			->where('places.tipo','=', $book->tipo)
			->where('places.calle','=', $book->calle)
			->where('places.altura','=', $book->altura)
			->where('places.piso_dpto','=', $book->piso_dpto)
			->where('places.cruce','=', $book->cruce)//este rompe con
			->where('places.barrio_localidad','=', $book->barrio_localidad) // no usar debdio a google maps (almagro, etc)
			->where('partido.nombre_partido', '=', $book->partido_comuna) // comuna 1,2,3,4
			->where('provincia.nombre_provincia', '=', $book->provincia_region) // caba
			->where('pais.nombre_pais', '=', $book->pais)
			->first();


		return array(
			'status' => 'ADD_UNI',
			'placeId' => $existePlace->placeId,
			'pais' => $book->pais,
			'provincia_region' => $book->provincia_region,
			'partido_comuna' => $book->partido_comuna,
			'barrio_localidad' => $book->barrio_localidad,
			'establecimiento' => $book->establecimiento,
			'tipo' => $book->tipo,
			'calle' => $book->calle,
			'altura' => $book->altura,
			'piso_dpto' => $book->piso_dpto,
			'cruce' => $book->cruce,
			'aprobado' => $book->aprobado,
			'observacion' => $book->observacion,
			'latitude' => $book->latitude,
			'longitude' => $book->longitude,
			'confidence' => $book->confidence,
			'formattedAddress' => $book->formattedaddress,
			'habilitado' => $book->habilitado,
		//	'vacunatorio' => $this->correctValueService($existePlace->vacunatorio,$book->vacunatorioOri),
		//	'infectologia' => $this->correctValueService($existePlace->infectologia,$book->infectologiaOri),
			'condones' => $this->correctValueService($existePlace->condones,$book->condonesOri),
			'prueba' => $this->correctValueService($existePlace->prueba,$book->pruebaOri),
			'ssr' => $this->correctValueService($existePlace->ssr,$book->ssrOri),
			'dc' => $this->correctValueService($existePlace->dc,$book->dcOri),
			'es_rapido' => $this->correctValueService($existePlace->es_rapido,$book->es_rapidoOri),
			'tel_testeo' => $this->correctValue($existePlace->tel_testeo,$book->tel_testeo),
			'mail_testeo' => $this->correctValue($existePlace->mail_testeo,$book->mail_testeo),
			'horario_testeo' => $this->correctValue($existePlace->horario_testeo,$book->horario_testeo),
			'responsable_testeo' => $this->correctValue($existePlace->responsable_testeo,$book->responsable_testeo),
			'web_testeo' => $this->correctValue($existePlace->web_testeo,$book->web_testeo),
			'ubicacion_testeo' => $this->correctValue($existePlace->ubicacion_testeo,$book->ubicacion_testeo),
			'observaciones_testeo' => $this->correctValue($existePlace->observaciones_testeo,$book->observaciones_testeo),
			'tel_distrib' => $this->correctValue($existePlace->tel_distrib,$book->tel_distrib),
			'mail_distrib' => $this->correctValue($existePlace->mail_distrib,$book->mail_distrib),
			'horario_distrib' => $this->correctValue($existePlace->horario_distrib,$book->horario_distrib),
			'responsable_distrib' => $this->correctValue($existePlace->responsable_distrib,$book->responsable_distrib),
			'web_distrib' => $this->correctValue($existePlace->web_distrib,$book->web_distrib),
			'ubicacion_distrib' => $this->correctValue($existePlace->ubicacion_distrib,$book->ubicacion_distrib),
			'comentarios_distrib' => $this->correctValue($existePlace->comentarios_distrib,$book->comentarios_distrib),
		/*	'tel_infectologia' => $this->correctValue($existePlace->tel_infectologia,$book->tel_infectologia),
			'mail_infectologia' => $this->correctValue($existePlace->mail_infectologia,$book->mail_infectologia),
			'horario_infectologia' => $this->correctValue($existePlace->horario_infectologia,$book->horario_infectologia),
			'responsable_infectologia' => $this->correctValue($existePlace->responsable_infectologia,$book->responsable_infectologia),
			'web_infectologia' => $this->correctValue($existePlace->web_infectologia,$book->web_infectologia),
			'ubicacion_infectologia' => $this->correctValue($existePlace->ubicacion_infectologia,$book->ubicacion_infectologia),
			'comentarios_infectologia' => $this->correctValue($existePlace->comentarios_infectologia,$book->comentarios_infectologia),
			'tel_vac' => $this->correctValue($existePlace->tel_vac,$book->tel_vac),
			'mail_vac' => $this->correctValue($existePlace->mail_vac,$book->mail_vac),
			'horario_vac' => $this->correctValue($existePlace->horario_vac,$book->horario_vac),
			'responsable_vac' => $this->correctValue($existePlace->responsable_vac,$book->responsable_vac),
			'web_vac' => $this->correctValue($existePlace->web_vac,$book->web_vac),
			'ubicacion_vac' => $this->correctValue($existePlace->ubicacion_vac,$book->ubicacion_vac),
			'comentarios_vac' => $this->correctValue($existePlace->comentarios_vac,$book->comentarios_vac),*/
			'tel_mac' => $this->correctValue($existePlace->tel_mac,$book->tel_mac),
			'mail_mac' => $this->correctValue($existePlace->mail_mac,$book->mail_mac),
			'horario_mac' => $this->correctValue($existePlace->horario_mac,$book->horario_mac),
			'responsable_mac' => $this->correctValue($existePlace->responsable_mac,$book->responsable_mac),
			'web_mac' => $this->correctValue($existePlace->web_mac,$book->web_mac),
			'ubicacion_mac' => $this->correctValue($existePlace->ubicacion_mac,$book->ubicacion_mac),
			'comentarios_mac' => $this->correctValue($existePlace->comentarios_mac,$book->comentarios_mac),
			'tel_ile' => $this->correctValue($existePlace->tel_ile,$book->tel_ile),
			'mail_ile' => $this->correctValue($existePlace->mail_ile,$book->mail_ile),
			'horario_ile' => $this->correctValue($existePlace->horario_ile,$book->horario_ile),
			'responsable_ile' => $this->correctValue($existePlace->responsable_ile,$book->responsable_ile),
			'web_ile' => $this->correctValue($existePlace->web_ile,$book->web_ile),
			'ubicacion_ile' => $this->correctValue($existePlace->ubicacion_ile,$book->ubicacion_ile),
			'comentarios_ile' => $this->correctValue($existePlace->comentarios_ile,$book->comentarios_ile),
			'mac' => $this->correctValueService($existePlace->mac,$book->macOri),
			'ile' => $this->correctValueService($existePlace->ile,$book->ileOri),

			'tel_ssr' => $this->correctValue($existePlace->tel_ssr,$book->tel_ssr),
			'mail_ssr' => $this->correctValue($existePlace->mail_ssr,$book->mail_ssr),
			'horario_ssr' => $this->correctValue($existePlace->horario_ssr,$book->horario_ssr),
			'responsable_ssr' => $this->correctValue($existePlace->responsable_ssr,$book->responsable_ssr),
			'web_ssr' => $this->correctValue($existePlace->web_ssr,$book->web_ssr),
			'ubicacion_ssr' => $this->correctValue($existePlace->ubicacion_ssr,$book->ubicacion_ssr),
			'comentarios_ssr' => $this->correctValue($existePlace->comentarios_ssr,$book->comentarios_ssr),
			'tel_dc' => $this->correctValue($existePlace->tel_dc,$book->tel_dc),
			'mail_dc' => $this->correctValue($existePlace->mail_dc,$book->mail_dc),
			'horario_dc' => $this->correctValue($existePlace->horario_dc,$book->horario_dc),
			'responsable_dc' => $this->correctValue($existePlace->responsable_dc,$book->responsable_dc),
			'web_dc' => $this->correctValue($existePlace->web_dc,$book->web_dc),
			'ubicacion_dc' => $this->correctValue($existePlace->ubicacion_dc,$book->ubicacion_dc),
			'comentarios_dc' => $this->correctValue($existePlace->comentarios_dc,$book->comentarios_dc),
			'servicetype_dc' => $this->correctValue($existePlace->servicetype_dc,$book->servicetype_dc),
			'servicetype_ssr' => $this->correctValue($existePlace->servicetype_ssr,$book->servicetype_ssr),
			'servicetype_mac' => $this->correctValue($existePlace->servicetype_mac,$book->servicetype_mac),
			'servicetype_ile' => $this->correctValue($existePlace->servicetype_ile,$book->servicetype_ile),
			'servicetype_prueba' => $this->correctValue($existePlace->servicetype_prueba,$book->servicetype_prueba),
			'servicetype_condones' => $this->correctValue($existePlace->servicetype_condones,$book->servicetype_condones),

			'friendly_ssr' => $this->correctValue($existePlace->friendly_ssr,$book->friendly_ssr),
			'friendly_dc' => $this->correctValue($existePlace->friendly_dc,$book->friendly_dc),
			'friendly_ile' => $this->correctValue($existePlace->friendly_ile,$book->friendly_ile),
			'friendly_mac' => $this->correctValue($existePlace->friendly_mac,$book->friendly_mac),
			'friendly_prueba' => $this->correctValue($existePlace->friendly_prueba,$book->friendly_prueba),
			'friendly_condones' => $this->correctValue($existePlace->friendly_condones,$book->friendly_condones)


		);
	}
	public function agregarNuevo($book,$latLng){
		return array(
			'status' => 'ADD_NEW',
			'placeId' => $book->placeId,
			'establecimiento' => $book->establecimiento,
			'tipo' => $book->tipo,
			'calle' => $latLng['route'],
			'altura' => $book->altura,
			'piso_dpto' => $book->piso_dpto,
			'cruce' => $book->cruce,
			'barrio_localidad' => $latLng['city'], // almagro, balvanera, etc
			'partido_comuna' => $latLng['partido'], //comuna 3
			'provincia_region' => $latLng['state'], //caba
			'pais' => $latLng['country'],
			'aprobado' => 1,
			'observacion' => $book->observacion,
			'latitude' => $latLng['lati'],
			'longitude' => $latLng['longi'],
			'confidence' => $latLng['accurracy'],
			'formattedAddress' => $latLng['formatted_address'],
			'habilitado' => 1,
			'condones' => $book->condones,
			'prueba' => $book->prueba,
			//'vacunatorio' => $book->vacunatorio,
			//'infectologia' => $book->infectologia,
			'mac' => $book->mac,
			'ile' => $book->ile,
			'ssr' => $book->ssr,
			'dc' => $book->dc,
			'es_rapido' => $book->es_rapido,
			'tel_testeo' => $book->tel_testeo,
			'mail_testeo' => $book->mail_testeo,
			'horario_testeo' => $book->horario_testeo,
			'responsable_testeo' => $book->responsable_testeo,
			'web_testeo' => $book->web_testeo,
			'ubicacion_testeo' => $book->ubicacion_testeo,
			'observaciones_testeo' => $book->observaciones_testeo,
			'tel_distrib' => $book->tel_distrib,
			'mail_distrib' => $book->mail_distrib,
			'horario_distrib' => $book->horario_distrib,
			'responsable_distrib' => $book->responsable_distrib,
			'web_distrib' => $book->web_distrib,
			'ubicacion_distrib' => $book->ubicacion_distrib,
			'comentarios_distrib' => $book->comentarios_distrib,
		/*	'tel_infectologia' => $book->tel_infectologia,
			'mail_infectologia' => $book->mail_infectologia,
			'horario_infectologia' => $book->horario_infectologia,
			'responsable_infectologia' => $book->responsable_infectologia,
			'web_infectologia' => $book->web_infectologia,
			'ubicacion_infectologia' => $book->ubicacion_infectologia,
			'comentarios_infectologia' => $book->comentarios_infectologia,
			'tel_vac' => $book->tel_vac,
			'mail_vac' => $book->mail_vac,
			'horario_vac' => $book->horario_vac,
			'responsable_vac' => $book->responsable_vac,
			'web_vac' => $book->web_vac,
			'ubicacion_vac' => $book->ubicacion_vac, //posible problema
			'comentarios_vac' => $book->comentarios_vac,*/
			'tel_mac' => $book->tel_mac,
			'mail_mac' => $book->mail_mac,
			'horario_mac' => $book->horario_mac,
			'responsable_mac' => $book->responsable_mac,
			'web_mac' => $book->web_mac,
			'ubicacion_mac' => $book->ubicacion_mac,
			'comentarios_mac' => $book->comentarios_mac,
			'tel_ile' => $book->tel_ile,
			'mail_ile' => $book->mail_ile,
			'horario_ile' => $book->horario_ile,
			'responsable_ile' => $book->responsable_ile,
			'web_ile' => $book->web_ile,
			'ubicacion_ile' => $book->ubicacion_ile,
			'comentarios_ile' => $book->comentarios_ile,
			'tel_ssr' => $book->tel_ssr,
			'mail_ssr' => $book->mail_ssr,
			'horario_ssr' => $book->horario_ssr,
			'responsable_ssr' => $book->responsable_ssr,
			'web_ssr' => $book->web_ssr,
			'ubicacion_ssr' => $book->ubicacion_ssr,
			'comentarios_ssr' => $book->comentarios_ssr,
			'tel_dc' => $book->tel_dc,
			'mail_dc' => $book->mail_dc,
			'horario_dc' => $book->horario_dc,
			'responsable_dc' => $book->responsable_dc,
			'web_dc' => $book->web_dc,
			'ubicacion_dc' => $book->ubicacion_dc,
			'comentarios_dc' => $book->comentarios_dc,
			'servicetype_dc' => $book->servicetype_dc,
			'servicetype_ssr' => $book->servicetype_ssr,
			'servicetype_mac' => $book->servicetype_mac,
			'servicetype_ile' => $book->servicetype_ile,
			'servicetype_prueba' => $book->servicetype_prueba,
			'servicetype_condones' => $book->servicetype_condones,

			'friendly_condones' => $book->friendly_condones,
			'friendly_prueba' => $book->friendly_prueba,
			'friendly_mac' => $book->friendly_mac,
			'friendly_ile' => $book->friendly_ile,
			'friendly_ssr' => $book->friendly_ssr,
			'friendly_dc' => $book->friendly_dc
		);





	}
	public function agregarNuevoNoGeo($book,$latLng){
		$final = array();
		if (isset($latLng['city']))
			$final['city'] = $latLng['city'];
		else
			$final['city'] = $book->barrio_localidad;

		if (isset($latLng['partido']))
			$final['partido'] = $latLng['partido'];
		else
			$final['partido'] = $book->partido_comuna;

		if (isset($latLng['state']))
			$final['state'] = $latLng['state'];
		else
			$final['state'] = $book->provincia_region;

		if (isset($latLng['country']))
			$final['country'] = $latLng['country'];
		else
			$final['country'] = $book->pais;

		return array(
			'status' => 'ADD_NEW',
			'placeId' => $book->placeId,
			'establecimiento' => $book->establecimiento,
			'tipo' => $book->tipo,
			'calle' => $book->calle,
			'altura' => $book->altura,
			'piso_dpto' => $book->piso_dpto,
			'cruce' => $book->cruce,
			'barrio_localidad' => $final['city'], // almagro, balvanera, etc
			'partido_comuna' => $final['partido'], //comuna 3
			'provincia_region' => $final['state'], //caba
			'pais' => $final['country'],
			// 'barrio_localidad' => $book->barrio_localidad, // almagro, balvanera, etc
			// 'partido_comuna' => $book->partido_comuna, //comuna 3
			// 'provincia_region' => $book->provincia_region, //caba
			// 'pais' => $book->pais,
			'aprobado' => 1,
			'observacion' => $book->observacion,
			'latitude' => $book->latitude,
			'longitude' => $book->longitude,
			'confidence' => $latLng['accurracy'],
			'formattedAddress' => $book->formatted_address,
			'habilitado' => 1,
			'condones' => $book->condones,
			'prueba' => $book->prueba,
			'vacunatorio' => $book->vacunatorio,
			'infectologia' => $book->infectologia,
			'mac' => $book->mac,
			'ile' => $book->ile,
			'ssr' => $book->ssr,
			'dc' => $book->dc,
			'es_rapido' => $book->es_rapido,
			'tel_testeo' => $book->tel_testeo,
			'mail_testeo' => $book->mail_testeo,
			'horario_testeo' => $book->horario_testeo,
			'responsable_testeo' => $book->responsable_testeo,
			'web_testeo' => $book->web_testeo,
			'ubicacion_testeo' => $book->ubicacion_testeo,
			'observaciones_testeo' => $book->observaciones_testeo,
			'tel_distrib' => $book->tel_distrib,
			'mail_distrib' => $book->mail_distrib,
			'horario_distrib' => $book->horario_distrib,
			'responsable_distrib' => $book->responsable_distrib,
			'web_distrib' => $book->web_distrib,
			'ubicacion_distrib' => $book->ubicacion_distrib,
			'comentarios_distrib' => $book->comentarios_distrib,
	/*		'tel_infectologia' => $book->tel_infectologia,
			'mail_infectologia' => $book->mail_infectologia,
			'horario_infectologia' => $book->horario_infectologia,
			'responsable_infectologia' => $book->responsable_infectologia,
			'web_infectologia' => $book->web_infectologia,
			'ubicacion_infectologia' => $book->ubicacion_infectologia,
			'comentarios_infectologia' => $book->comentarios_infectologia,
			'tel_vac' => $book->tel_vac,
			'mail_vac' => $book->mail_vac,
			'horario_vac' => $book->horario_vac,
			'responsable_vac' => $book->responsable_vac,
			'web_vac' => $book->web_vac,
			'ubicacion_vac' => $book->ubicacion_vac, //posible problema
			'comentarios_vac' => $book->comentarios_vac,*/
			'tel_mac' => $book->tel_mac,
			'mail_mac' => $book->mail_mac,
			'horario_mac' => $book->horario_mac,
			'responsable_mac' => $book->responsable_mac,
			'web_mac' => $book->web_mac,
			'ubicacion_mac' => $book->ubicacion_mac,
			'comentarios_mac' => $book->comentarios_mac,
			'tel_ile' => $book->tel_ile,
			'mail_ile' => $book->mail_ile,
			'horario_ile' => $book->horario_ile,
			'responsable_ile' => $book->responsable_ile,
			'web_ile' => $book->web_ile,
			'ubicacion_ile' => $book->ubicacion_ile,
			'comentarios_ile' => $book->comentarios_ile,
			'tel_ssr' => $book->tel_ssr,
			'mail_ssr' => $book->mail_ssr,
			'horario_ssr' => $book->horario_ssr,
			'responsable_ssr' => $book->responsable_ssr,
			'web_ssr' => $book->web_ssr,
			'ubicacion_ssr' => $book->ubicacion_ssr,
			'comentarios_ssr' => $book->comentarios_ssr,
			'tel_dc' => $book->tel_dc,
			'mail_dc' => $book->mail_dc,
			'horario_dc' => $book->horario_dc,
			'responsable_dc' => $book->responsable_dc,
			'web_dc' => $book->web_dc,
			'ubicacion_dc' => $book->ubicacion_dc,
			'comentarios_dc' => $book->comentarios_dc,
			'servicetype_dc' => $book->servicetype_dc,
			'servicetype_ssr' => $book->servicetype_ssr,
			'servicetype_mac' => $book->servicetype_mac,
			'servicetype_ile' => $book->servicetype_ile,
			'servicetype_prueba' => $book->servicetype_prueba,
			'servicetype_condones' => $book->servicetype_condones,

			'friendly_condones' => $book->friendly_condones,
			'friendly_prueba' => $book->friendly_prueba,
			'friendly_mac' => $book->friendly_mac,
			'friendly_ile' => $book->friendly_ile,
			'friendly_ssr' => $book->friendly_ssr,
			'friendly_dc' => $book->friendly_dc
		);
	}

	public function cleardb(Request $request){ //elimina datos de la tabla paises, provincias, partidos y places
		$mode = env('MODE');
		if (($mode !== null) && ($mode !== 'production'))  {
			DB::statement('SET FOREIGN_KEY_CHECKS=0');
			DB::table('places')->truncate();
			DB::table('partido')->truncate();
			DB::table('provincia')->truncate();
			DB::table('pais')->truncate();
			DB::table('evaluation')->truncate();
			DB::statement('SET FOREIGN_KEY_CHECKS=1');
		}

		return(['mode' => $mode]);
	}

	public function getServerMode(Request $request){ //elimina datos de la tabla paises, provincias, partidos y places
		$mode = env('MODE');
		return(['mode' => $mode]);
	}

}