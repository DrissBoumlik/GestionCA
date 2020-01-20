<label class="my-1 mr-2" for="inlineFormCustomSelectPref">Filter :</label>
<select class="custom-select my-1 mr-sm-2" id="filterDashboard">
    <option selected>Choisir...</option>
    <option value="1" disabled>Traitement assistance</option>
    <option value="2" disabled>Appel Post</option>
    <option value="appels-gem" {{ (request()->is('appels_prealables') ? 'selected' : '') }}>Appel GEM</option>
    <option value="appels-pralables" {{ (request()->is('dashboard/appels_prealables') ? 'selected' : '') }}>Appel préalable</option>
    <option value="production_globale_cam">Appel CAM</option>
    <option value="appels-clture">Clôture OT & Traitement BL</option>
</select>
