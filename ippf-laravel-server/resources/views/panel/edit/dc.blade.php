<form class="col s12 m6">
                    <div class="row">
                      <div class="row">
<p>

                      <input  type="checkbox"
                      name="place.dc"
                      id="filled-in-box-dc"
                      ng-checked="isCheckBoxChecked(place.dc)"
                      ng-model="place.dc"/>
                      <label for="filled-in-box-dc">¿Cuenta con Detección de Cancer?</label>
                    </p>

                  <p>
                    <input  type="checkbox"
                    name="friendly_dc"
                    id="friendly_dc"
                    ng-model="place.friendly_dc"/>
                    <label for="friendly_dc">¿Adolecente Friendly?</label>
                  </p>


                      <div class="input-field col s12">
                        <input id="responsable_dc" type="text"
                        name="responsable_dc" class="validate"
                        ng-model="place.responsable_dc"
                        ng-change="formChange()">
                        <label for="responsable_dc">Responsable</label>
                      </div>
                    </div>
                     <div class="row">
                      <div class="input-field col s12">
                        <input id="ubicacion_dc" type="text"
                        name="ubicacion_dc" class="validate"
                        ng-model="place.ubicacion_dc"
                        ng-change="formChange()">
                        <label for="ubicacion_dc">Ubicación</label>
                      </div>
                    </div>


                  <div class="row">
                      <div class="input-field col s12">
                        <input id="horario_dc" type="text"
                        name="horario_dc" class="validate"
                        ng-model="place.horario_dc"
                        ng-change="formChange()">
                        <label for="horario_dc">Horario</label>
                      </div>
                    </div>
                    <div class="row">
                      <div class="input-field col s12">
                        <input id="mail_dc" type="email"
                        name="mail_dc" class="validate"
                        ng-model="place.mail_dc"
                        ng-change="formChange()">
                        <label for="mail_dc">Mail</label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="input-field col s12">
                        <input id="tel_dc" type="text"
                        name="tel_dc" class="validate"
                        ng-model="place.tel_dc" ng-change="formChange()">
                        <label for="tel_dc">Teléfono</label>
                      </div>
                    </div>

                         <div class="row">
                      <div class="input-field col s12">
                        <input id="web_dc" type="text"
                        name="web_dc" class="validate"
                        ng-model="place.web_dc" ng-change="formChange()">
                        <label for="web_dc">Web</label>
                      </div>
                    </div>

                    <div class="row">
                      <div class="input-field col s12">
                        <textarea id="comentarios_dc" type="text"
                        name="comentarios_dc"
                        class="validate materialize-textarea"
                        ng-model="place.comentarios_dc"
                        ng-change="formChange()"></textarea>
                        <label for="comentarios_dc">Observación</label>
                      </div>
                    </div>
                     </div>
                      </div>

                         </form>
