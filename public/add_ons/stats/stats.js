$(function () {
    var dates = undefined;

    $.ajax({
        url: 'getDates',
        method: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (response) {
            let data = response.dates;
            $('.tree-view').each(function (index, item) {
                new Tree('#' + $(this).attr('id'), {
                    data: [{id: '-1', text: 'Dates', children: data}],
                    closeDepth: 2,
                    loaded: function () {
                        // this.values = ['0-0-0', '0-1-1', '0-0-2'];
                        // console.log(this.selectedNodes);
                        // console.log(this.values);
                        // this.disables = ['0-0-0', '0-0-1', '0-0-2']
                    },
                    onChange: function () {
                        dates = this.values;
                    }
                });
                // $(this).find('.treejs-switcher').first().parent().first().addClass('treejs-node__close')
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });

    /// ====================== REGIONS ==========================

    let statsRegions = {element_dt: undefined, element: $('#statsRegions'), columns: undefined};
    let statsRegionsChart = {element_chart: undefined, element_id: 'statsRegionsChart', data: undefined};
    getColumns('getRegionsColumn/Resultat_Appel', 'getRegions/Resultat_Appel', statsRegions, statsRegionsChart);
    $('#refreshRegions').on('click', function () {
        statsRegions.element_dt = InitDataTable(statsRegions, 'getRegions/Resultat_Appel', {dates});
    });

    /// ====================== CALLS STATS ==========================

    let calls_states_agencies = {element_dt: undefined, element: $('#calls_states_agencies'), columns: undefined};
    getColumns('getRegionsCallStateColumn/Nom_Region', 'getRegionsCallState/Nom_Region', calls_states_agencies);
    $('#refreshCallStatesAgencies').on('click', function () {
        calls_states_agencies.element_dt = InitDataTable(calls_states_agencies, 'getRegionsCallState/Nom_Region', {dates});
    });


    let calls_states_weeks = {element_dt: undefined, element: $('#calls_states_weeks'), columns: undefined};
    getColumns('getRegionsCallStateColumn/Date_Heure_Note_Semaine', 'getRegionsCallState/Date_Heure_Note_Semaine', calls_states_weeks);
    $('#refreshCallStatesWeeks').on('click', function () {
        calls_states_weeks.element_dt = InitDataTable(calls_states_weeks, 'getRegionsCallState/Date_Heure_Note_Semaine', {dates});
    });

    /// ====================== FOLDERS ==========================

    let statsFoldersByType = {element_dt: undefined, element: $('#statsTypes'), columns: undefined};
    getColumns('getNonValidatedFoldersColumn/Code_Type_Intervention', 'getNonValidatedFolders/Code_Type_Intervention', statsFoldersByType);
    $('#refreshFoldersByType').on('click', function () {
        statsFoldersByType.element_dt = InitDataTable(statsFoldersByType, 'getNonValidatedFolders/Code_Type_Intervention', {dates});
    });

    let statsFoldersByCode = {element_dt: undefined, element: $('#statsCodes'), columns: undefined};
    getColumns('getNonValidatedFoldersColumn/Code_Intervention', 'getNonValidatedFolders/Code_Intervention', statsFoldersByCode);
    $('#refreshFoldersByCode').on('click', function () {
        statsFoldersByCode.element_dt = InitDataTable(statsFoldersByCode, 'getNonValidatedFolders/Code_Intervention', {dates});
    });

    /// ====================== CALL STATS ==========================

    let statscallsPos = {element_dt: undefined, element: $('#statsCallsPos'), columns: undefined};
    getColumns('getClientsByCallStateColumn/Joignable', 'getClientsByCallState/Joignable', statscallsPos);
    $('#refreshCallResultPos').on('click', function () {
        statscallsPos.element_dt = InitDataTable(statscallsPos, 'getClientsByCallState/Joignable', {dates});
    });

    let statscallsNeg = {element_dt: undefined, element: $('#statsCallsNeg'), columns: undefined};
    getColumns('getClientsByCallStateColumn/Injoignable', 'getClientsByCallState/Injoignable', statscallsNeg);
    $('#refreshCallResultNeg').on('click', function () {
        statscallsNeg.element_dt = InitDataTable(statscallsNeg, 'getClientsByCallState/Injoignable', {dates});
    });

    // getColumns('getNonValidatedFoldersColumn', 'getNonValidatedFolders', stats, stats_dt);


    /// ====================== FUNCTIONS ==========================
    function dynamicColors() {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgb(" + r + "," + g + "," + b + ")";
    };

    function getColumns(routeColumns, route, object, objectChart = null) {
        $.ajax({
            url: routeColumns,
            method: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (response) {
                object.columns = response.columns;
                object.element_dt = InitDataTable(object, route);
                console.log(response);
                if (objectChart !== null && objectChart !== undefined) {
                    // console.log(objectChart);
                    let labels = response.columns.map((column) => {
                        return column.name;
                    });
                    labels.pop();
                    labels.shift();
                    let column = response.data.column;
                    let datasets = response.data.data.map((item) => {
                        let regions = Object.values(item.regions).map((value) => {
                            return parseFloat(value.replace('%', ''));
                        });
                        let _dataItem = {label: item[column], backgroundColor: dynamicColors(), data: Object.values(regions)};
                        return _dataItem;
                    });

                    var ctx = document.getElementById(objectChart.element_id).getContext('2d');
                    let barChartData = {labels, datasets};
                    let chart = new Chart(ctx, {
                        type: 'bar',
                        data: barChartData,
                        options: {
                            title: {
                                display: true,
                                text: 'Résultats Appels (Clients Joints)'
                            },
                            tooltips: {
                                mode: 'index',
                                intersect: true
                            },
                            responsive: true,
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                }],
                                yAxes: [{
                                    stacked: false
                                }]
                            }
                        }
                    });
                }
                // if (objectChart !== null) {
                //     console.log(response.data);
                //     response.data.map(function (item) {
                //         console.log(item.regions);
                //     });
                //     let chartElement = document.getElementById(objectChart.element_id);
                //     let labels = response.columns;
                //     labels.pop();
                //     labels.shift();
                //     let chart = new Chart(chartElement, {
                //         type: 'bar',
                //         data: {
                //             labels: labels,
                //             datasets: response.data
                //         }
                //
                //
                //     });
                // }


            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    function InitDataTable(object, route, data = null) {
        if ($.fn.DataTable.isDataTable(object.element_dt)) {
            object.element_dt.destroy();
        }
        return object.element.DataTable({
            responsive: true,
            info: false,
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: route,
                data: data,
            },
            columns: object.columns
        });
    }

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }
});
