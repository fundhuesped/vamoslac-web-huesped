<!-- NAV BAR DESKTOP/MOBILE-->
<nav ng-controller="navController">
  <div class="nav-wrapper nav-index">
    <a href="/#/" class="brand-logo">
      <!-- WEBSITE LOGO -->
      <img class="logoTop" src="images/logo_blanco.svg">
      <!-- MOBILE BURGER BUTTON -->
      <a data-activates="mobile-demo" class="button-collapse">
        <i class="mdi-navigation-menu"></i>
      </a>
      <!-- DESKTOP NAVBAR -->
      <ul class="right hide-on-med-and-down">
        <li>
          <a class="modal-trigger tooltipped" href="#modal" data-position="bottom" data-tooltip="[['useful_info' | translate]]">
            <i class="mdi-action-info"></i>
          </a>
        </li>
        <li>
          <a class="tooltipped" href="/#/localizar/all/listado" data-position="bottom" data-tooltip="[['nearby_locations' | translate]]">
            <i class="mdi-maps-place"></i>
          </a>
        </li>
        <li>
          <a class="tooltipped" href="/form" data-position="bottom" data-tooltip="[['suggest_new_place' | translate]]">
            <i class="mdi-content-add-circle-outline"></i>
          </a>
        </li>
        <li>
          <a class="tooltipped" href="/listado-paises" data-position="bottom" data-tooltip="[['places_lists_mundito' | translate]]">
            <i class="mdi-action-language"></i>
          </a>
        </li>
        <li>
          <select name="language1" id="language1" ng-model="selectedLanguage" ng-change="changeLanguage()">
            <option value="" disabled><span>LANG</span></option>
            <option value="en" name="en" ng-selected="selectedLanguage == 'en'">EN</option>
            <option value="es" name="es" ng-selected="selectedLanguage == 'es'">ES</option>
          </select>
        </li>
      </ul>
      <!-- POP NAVIGATION -->
      <ul ng-cloak ng-show="navigating" class="left wow fadeIn nav-wrapper">
        <li><a href="" onclick="window.history.back();"><i class="mdi-navigation-chevron-left right"></i></a></li>
      </ul>

      <!-- MOBILE NAVBAR -->
      <ul class="side-nav" id="mobile-demo">
        <!-- LANG -->
        <li>
          <a href="javascript:void(0);">
            <i class="fa fa-language fa-2x" aria-hidden="true"></i>
            <div style="position: absolute;top: 0; left:30%; width: 25%;">
              <select name="language2" id="language2" ng-model="selectedLanguage" ng-change="changeLanguage()">
                <option value="en" name="en" ng-selected="selectedLanguage == 'en'">EN</option>
                <option value="es" name="es" ng-selected="selectedLanguage == 'es'">ES</option>
              </select>
            </div>
          </a>
        </li>
        <!-- ABOUT -->
        <li>
          <a href="/#/acerca">
            <i class="mdi-action-info left"></i><span translate="about"></span>
          </a>
        </li>
        <!-- GEOLOCALIZATION SHOW EVERY PLACE -->
        <li>
          <a href="/#/localizar/all/listado">
            <i class="mdi-maps-place left"></i>
            <span translate="closer"></span>
          </a>
        </li>
        <!-- FORM SUGGEST -->
        <li>
          <a href="/form">
            <i class="mdi-content-add-circle-outline left"></i><span translate="seggest"></span>
          </a>
        </li>
        <!-- COUNTRY LIST -->
        <li>
          <a href="/listado-paises">
            <i class="mdi-action-language left"></i>
            <span translate="list"></span>
          </a>
        </li>
      </ul>
    </div>
  </a>
</nav>
<!-- END NAV BAR DESKTOP/MOBILE -->
