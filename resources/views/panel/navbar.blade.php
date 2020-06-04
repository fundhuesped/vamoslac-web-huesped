<nav ng-controller="navController">
  <div class="nav-wrapper">
    <ul class="right hide-on-med-and-down">
      <li class="tooltipped" data-position="bottom" data-tooltip="[['go_to_panel' | translate]]">
        <a href=" {{ URL::to('/panel') }}"><i class="mdi-action-home"></i></a>
      </li>
      <li class="tooltipped" data-position="bottom" data-tooltip="[['go_to_importer' | translate]]">
        <a href=" {{ URL::to('/panel/importer') }}"><i class="mdi-communication-import-export"></i></a>
      </li>
      <li class="tooltipped" data-position="bottom" data-tooltip="[['manage_localities' | translate]]">
        <a href=" {{ URL::to('/panel/city-list') }}"><i class="mdi-maps-place left"></i></a>
      </li>
      <li class="tooltipped" data-position="bottom" data-tooltip="[['manage_users' | translate]]">
        <a href=" {{ URL::to('/panel/admin-list') }}"><i class="mdi-action-accessibility"></i></a>
      </li>
      <li>
        <select name="language1" id="language1" ng-model="selectedLanguage" ng-change="changeLanguage()">
          <option value="" disabled><span>LANG</span></option>
            <option value="en" name="en" ng-selected="selectedLanguage == 'en'">EN</option>
            <option value="es" name="es" ng-selected="selectedLanguage == 'es'">ES</option>
        </select>
      </li>
      @if(Auth::check() != null)
      <li class="tooltipped" data-position="bottom" data-tooltip="[['close_session' | translate]]">
        <a href=" {{ URL::to('admin/logout') }}"><i class="mdi-content-backspace"></i></a>
      </li>
      @endif
    </ul>
  </div>
</nav>