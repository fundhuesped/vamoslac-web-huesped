<form class="col s12 m6">
    <label translate="establishment"></label>
    <div class="row">
        <div class="input-field col s12">
            <input id="establecimiento" type="text" name="establecimiento" class="validate" ng-model="place.establecimiento"
            ng-change="formChange()">
            <label for="establecimiento" translate="form_establishment_name"></label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <input id="tipo" type="text" name="tipo"
            class="validate" ng-model="place.tipo"
            ng-change="formChange()">
            <label for="tipo" translate="form_establishment_type"></label>
        </div>
    </div>

    <label translate="address"></label>
    <div class="row">
        <div class="input-field col s12">
            <input id="calle" type="text" name="calle" class="validate" ng-model="place.calle" ng-change="formChange()">
            <label for="calle" translate="form_establishment_street"></label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <input id="altura" type="text" name="altura" class="validate" ng-model="place.altura" ng-change="formChange()">
            <label for="altura" translate="form_establishment_street_height"></label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <input id="cruce" type="text" name="cruce" class="validate" ng-model="place.cruce" ng-change="formChange()">
            <label for="cruce" translate="form_establishment_street_intersection"></label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <input id="piso_dpto" type="text" name="piso_dpto" class="validate" ng-model="place.piso_dpto" ng-change="formChange()">
            <label for="piso_dpto" translate="form_establishment_floor"></label>
        </div>
    </div>

    <label translate="location"></label>
    <div class="row">
        <div class="input-field col s12">
            <select id="select_pais" ng-change="loadProvinces()" ng-options="v.id as v.nombre_pais for v in countries" ng-model="place.idPais" material-select watch>
                <option value="" disabled selected translate="select_country"></option>
            </select>
            <label for="country" translate="country"></label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <select id="select_provincia" ng-change="loadDistricts()" ng-options="v.id as v.nombre_provincia for v in provinces" ng-model="place.idProvincia" material-select watch>
                <option value="" disabled selected translate="select_state"></option>
            </select>
            <label for="state" translate="state"></label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <select id="select_partido" ng-change="loadCities()" ng-options="v.id as v.nombre_partido for v in districts" ng-model="place.idPartido" material-select watch>
                <option value="" disabled selected translate="select_department"></option>
            </select>
            <label for="district" translate="district"></label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <select id="select_ciudad" ng-options="v.id as v.nombre_ciudad for v in cities" ng-model="place.idCiudad" material-select watch>
                <option value="" disabled selected translate="select_city"></option>
            </select>
            <label for="city" translate="city"></label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12">
            <input id="barrio_localidad" type="text" name="barrio_localidad" class="validate" ng-model="place.barrio_localidad" ng-change="formChange()">
            <label for="barrio_localidad" translate="neighborhood"></label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12">
            <input id="autocomplete" placeholder="" type="text" class="validate" autocomplete="chrome-off" ng-model="inputAutocomplete"/>
            <label for="autocomplete" translate="panel_detail_general_suggest"></label>
        </div>
    </div>
    <div class="row" ng-hide="!outputAutocomplete">
        <div class="input-field col s9">
            <input id="autocomplete_output" type="text" ng-model="outputAutocomplete" disabled class="new-autocomplete" />
            <label for="autocomplete_output" translate="panel_warning_autocomplete" class="new-autocomplete"></label>
        </div>
        <div class="col s3">
            <button class="waves-effect waves-light btn btn-large full tooltipped" ng-click="cancelNewCity()"
            data-position="bottom" data-tooltip="[['cancel' | translate]]">
                <i class="mdi-navigation-cancel"></i>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12">
            <textarea id="observacion" type="text" name="observacion"
            class="validate materialize-textarea" ng-model="place.observacion" ng-change="formChange()"></textarea>
            <label for="observacion" translate="form_observation_input"></label>
        </div>
    </div>

    <br>

    <div class="row uploader-information">
        <div class="col s12">
            <h5 class="form-section-title" translate="uploader_info"></h5>
        </div>
        <div class="col s12" ng-if="!place.uploader_name && !place.uploader_email && !place.uploader_tel">
            <h6><span class="bold-type no-info" translate="no_uploader_information"></span></h6>
        </div>
        <div ng-if="place.uploader_name || place.uploader_email || place.uploader_tel">
            <div class="col s12">
                <p><span class="bold-type">[[ "name" | translate ]]:</span>[[place.uploader_name]]</p>
            </div>
            <div class="col s12">
                <p><span class="bold-type">[[ "email" | translate ]]:</span>[[place.uploader_email]]</p>
            </div>
            <div class="col s12">
                <p><span class="bold-type">[[ "tel" | translate ]]:</span>[[place.uploader_tel]]</p>
            </div>
        </div>
    </div>

{{--     <div class="row">
        <div class="valign-demo  valign-wrapper">
            <div class="valign full-width actions">
                <p>
                    <input type="checkbox" name="place.mac" id="filled-in-box-mac" ng-checked="isCheckBoxChecked(place.mac)" ng-model="place.mac"/>
                    <label for="filled-in-box-mac" translate="panel_detail_general_other_mac"></label>
                </p>
            </div>
        </div>
    </div> --}}
</form>

<div class="col s12 m6">
    <div class="row">
        <label translate="panel_detail_general_map_localization"></label>
        <input id="latitude" type="text" name="latitude"
        class="validate" ng-model="place.latitude" ng-change="onLatLonInputChange()">
        <input id="longitude" type="text" name="longitude" class="validate" ng-model="place.longitude" ng-change="onLatLonInputChange()">

        <ng-map id="mapEditor" lazy-init="true" zoom="14">
            <marker ng-repeat="pos in positions" position="[[pos.latitude]],[[pos.longitude]]" on-dragend="onDragEnd()" draggable="true">
            </marker>
        </ng-map>
        <br>
        <div class="row">
            <div class="valign-demo  valign-wrapper">
                <div class="valign full-width actions">

                    <button class="waves-effect waves-light btn btn-large full" ng-disabled="spinerflag" ng-click="clicky()">
                        <div class="preloader-wrapper small active" ng-cloak ng-show="spinerflag">
                            <div class="spinner-layer spinner-red-only">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div><div class="gap-patch">
                                    <div class="circle"></div>
                                </div><div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div>

                        <div class="" ng-cloak ng-show="!spinerflag">
                            <i class="mdi-content-save left"></i>
                            <span translate="save"></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="valign-demo  valign-wrapper">
                <div class="valign full-width actions">
                    <button class="waves-effect waves-light btn btn-large full" ng-href="" ng-disabled="spinerflag" ng-click="clickyApr()">

                        <div class="preloader-wrapper small active" ng-cloak ng-show="spinerflag">
                            <div class="spinner-layer spinner-red-only">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div><div class="gap-patch">
                                    <div class="circle"></div>
                                </div><div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div>

                        <div class="" ng-cloak ng-show="!spinerflag">
                            <i class="mdi-action-done  left"></i>
                            <span translate="approve"></span>
                        </div>

                    </button>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="valign-demo  valign-wrapper">
                <div class="valign full-width actions">
                    <button class="waves-effect waves-light btn btn-large full" ng-disabled="spinerflag" ng-click="clickyDis()">

                        <div class="preloader-wrapper small active" ng-cloak ng-show="spinerflag">
                            <div class="spinner-layer spinner-red-only">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div><div class="gap-patch">
                                    <div class="circle"></div>
                                </div><div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div>

                        <div class="" ng-cloak ng-show="!spinerflag">
                            <i class="mdi-av-not-interested  left"></i>
                            <span translate="reject"></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
