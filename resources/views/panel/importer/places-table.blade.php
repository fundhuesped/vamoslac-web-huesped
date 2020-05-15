@isset($datos)
<div class="row">
	<table class="striped responsive-table">
		<thead>
			<tr>
				<td>Id</td>
				<td style="min-width: 15em;">Nombre establecimiento</td>
				<td style="min-width: 8em;">Tipo</td>
				<td>Calle</td>
				<td>Altura</td>
				<td>Barrio/Localidiad</td>
				<td>Ciudad</td>
				<td>Partido</td>
				<td>Provincia</td>
				<td>Pais</td>
				<td>Latitud</td>
				<td>Longitud</td>
				<td>Aprobado</td>
				<td style="min-width: 9em;">Servicios</td>
			</tr>
		</thead>
		<tbody>
			@if (count($datos) > 0 )
			@foreach ($datos as $p)
			<tr>
				<td>{{$p['placeId']}}</td>
				<td>
					@include('panel.importer.table-field', ['err' => $p['error_repetidos'], 'data' => $p['establecimiento'] ,'err_info' => 'Establecimiento ingresado repetido/unificable. Verifique'])
				</td>
				<td>{{$p['tipo']}}</td>
				<td>{{$p['calle']}}</td>
				<td>{{$p['altura']}}</td>
				<td>{{$p['barrio_localidad']}}</td>
				<td>{{$p['ciudad']}}</td>
				<td>{{$p['partido_comuna']}}</td>
				<td>{{$p['provincia_region']}}</td>
				<td>{{$p['pais']}}</td>
				<td>{{$p['latitude']}}</td>
				<td>{{$p['longitude']}}</td>
				<td class="text-center">{{$p['aprobado']}}</td>
				<td class="services2">
					<img ng-hide="{{ $p['friendly_condones'] }}" ng-show="{{ $p['condones'] }}" title="Este lugar distribuye preservativos" src="../../images/condones.svg">
					<img ng-show="{{ $p['friendly_condones'] }}" title="Este lugar distribuye preservativos" src="../../images/condones_friendly.svg">
					<img ng-hide="{{ $p['friendly_prueba'] }}" ng-show="{{ $p['prueba'] }}" title="Este lugar puede hacer prueba de HIV" src="../../images/vih.svg">
					<img ng-show="{{ $p['friendly_prueba'] }}" title="Este lugar puede hacer prueba de HIV" src="../../images/vih_friendly.svg">
					<img ng-hide="{{ $p['friendly_mac'] }}" ng-show="{{ $p['mac'] }}" title="Este lugar cuenta con métodos anticonceptivos" src="../../images/mac.svg">
					<img ng-show="{{ $p['friendly_mac'] }}" title="Este lugar cuenta con métodos anticonceptivos" src="../../images/mac_friendly.svg">
					<img ng-hide="{{ $p['friendly_ile'] }}" ng-show="{{ $p['ile'] }}" title="Este lugar cuenta con interrupción legal del embarazo" src="../../images/ile.svg">
					<img ng-show="{{ $p['friendly_ile'] }}" title="Este lugar cuenta con interrupción legal del embarazo" src="../../images/ile_friendly.svg">
					<img ng-hide="{{ $p['friendly_dc'] }}" ng-show="{{ $p['dc'] }}" title="Este lugar cuenta con detección dle cáncer" src="../../images/deteccion.svg">
					<img ng-show="{{ $p['friendly_dc'] }}" title="Este lugar cuenta con detección dle cáncer" src="../../images/deteccion_friendly.svg">
					<img ng-hide="{{ $p['friendly_ssr'] }}" ng-show="{{ $p['ssr'] }}" title="Este lugar cuenta con servicios de salud sexual y reproductiva" src="../../images/salud.svg">
					<img ng-show="{{ $p['friendly_ssr'] }}" title="Este lugar cuenta con servicios de salud sexual y reproductiva" src="../../images/salud_friendly.svg">
				</td>
			</tr>
			@endforeach
			@else
			<tr>
				<td><em translate="importer_confirmfastid_notfoundlabel"></em></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>				
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>		
			@endif
		</tbody>
	</table>
</div>
@endisset