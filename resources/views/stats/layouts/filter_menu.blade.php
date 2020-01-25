<label class="my-1 mr-2" for="inlineFormCustomSelectPref">Filter :</label>
<select class="custom-select my-1 mr-sm-2" id="filterDashboard">
    <option selected>Choisir...</option>
    <option value="dashboard" {{ (request()->is('dashboard') || request()->is('/')) ? 'selected' : '' }}>Dashboard</option>
    <option value="1" disabled>Traitement assistance</option>
    <option value="2" disabled>Appel Post</option>
    <option value="dashboard/appels-gem" {{ request()->is('dashboard/appels-gem') ? 'selected' : '' }}>Appel GEM</option>
    <option value="dashboard/appels-pralables" {{ request()->is('dashboard/appels-pralables') ? 'selected' : '' }}>Appel préalable</option>
    <option value="dashboard/production_globale_cam" {{ request()->is('dashboard/production_globale_cam') ? 'selected' : '' }}>Appel CAM</option>
    <option value="dashboard/appels-clture" {{ request()->is('dashboard/appels-clture') ? 'selected' : '' }}>Clôture OT & Traitement BL</option>
</select>
