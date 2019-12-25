$(function () {
    var dates = undefined;

    $.ajax({
        url: 'dates',
        method: 'GET',
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

    let statsRegions = {element_dt: undefined, element: $('#statsRegions'), columns: undefined, routeCol: 'regions/columns/Resultat_Appel', routeData: 'regions/Resultat_Appel'};
    let statsRegionsChart = {element_chart: undefined, element_id: 'statsRegionsChart', data: undefined, chartTitle: 'Résultats Appels (Clients Joints)'};
    getColumns(statsRegions, statsRegionsChart, true, true);
    $('#refreshRegions').on('click', function () {
        getColumns(statsRegions, statsRegionsChart, true, true, dates);
    });

    /// ====================== CALLS STATS AGENCIES / WEEKS ==========================

    let callsStatesAgencies = {element_dt: undefined, element: $('#callsStatesAgencies'), columns: undefined, routeCol: 'regionsCallState/columns/Nom_Region', routeData: 'regionsCallState/Nom_Region'};
    let callsStatesAgenciesChart = {element_chart: undefined, element_id: 'callsStatesAgenciesChart', data: undefined, chartTitle: 'Résultats Appels Préalables par agence'};
    getColumns(callsStatesAgencies, callsStatesAgenciesChart, true, false);
    $('#refreshCallStatesAgencies').on('click', function () {
        getColumns(callsStatesAgencies, callsStatesAgenciesChart, true, false, dates);
    });


    let callsStatesWeeks = {element_dt: undefined, element: $('#callsStatesWeeks'), columns: undefined, routeCol: 'regionsCallState/columns/Date_Heure_Note_Semaine', routeData: 'regionsCallState/Date_Heure_Note_Semaine'};
    let callsStatesWeeksChart = {element_chart: undefined, element_id: 'callsStatesWeeksChart', data: undefined, chartTitle: 'Résultats Appels Préalables par semaine'};
    getColumns(callsStatesWeeks, callsStatesWeeksChart, true, false);
    $('#refreshCallStatesWeeks').on('click', function () {
        getColumns(callsStatesWeeks, callsStatesWeeksChart,true, false, dates);
    });


    /// ====================== CALL STATS Joignables / Injoignable ==========================

    let statscallsPos = {element_dt: undefined, element: $('#statsCallsPos'), columns: undefined, routeCol: 'clientsByCallState/columns/Joignable', routeData: 'clientsByCallState/Joignable'};
    let statsCallsPosChart = {element_chart: undefined, element_id: 'statsCallsPosChart', data: undefined, chartTitle: 'Code Interventions liés aux RDV Confirmés (Clients Joignables)'};
    getColumns(statscallsPos, statsCallsPosChart, true, false);
    $('#refreshCallResultPos').on('click', function () {
        getColumns(statscallsPos, statsCallsPosChart,true, false, dates);
    });

    let statscallsNeg = {element_dt: undefined, element: $('#statsCallsNeg'), columns: undefined, routeCol: 'clientsByCallState/columns/Injoignable', routeData: 'clientsByCallState/Injoignable'};
    let statscallsNegChart = {element_chart: undefined, element_id: 'statscallsNegChart', data: undefined, chartTitle: 'Code Interventions liés aux RDV Confirmés (Clients Injoignables)'};
    getColumns(statscallsNeg, statscallsNegChart, true, false);
    $('#refreshCallResultNeg').on('click', function () {
        getColumns(statscallsNeg, statscallsNegChart,true, false, dates);
    });

    /// ====================== FOLDERS CODE / TYPE ==========================

    let statsFoldersByType = {element_dt: undefined, element: $('#statsFoldersByType'), columns: undefined, routeCol: 'nonValidatedFolders/columns/Code_Type_Intervention', routeData: 'nonValidatedFolders/Code_Type_Intervention'};
    let statsFoldersByTypeChart = {element_chart: undefined, element_id: 'statsFoldersByTypeChart', data: undefined, chartTitle: 'Répartition des dossiers non validés par Code Type intervention'};
    getColumns(statsFoldersByType, statsFoldersByTypeChart, true, false);
    $('#refreshFoldersByType').on('click', function () {
        getColumns(statsFoldersByType, statsFoldersByTypeChart, true, false, dates);
    });

    let statsFoldersByCode = {element_dt: undefined, element: $('#statsFoldersByCode'), columns: undefined, routeCol: 'nonValidatedFolders/columns/Code_Intervention', routeData: 'nonValidatedFolders/Code_Intervention'};
    let statsFoldersByCodeChart = {element_chart: undefined, element_id: 'statsFoldersByCodeChart', data: undefined, chartTitle: 'Répartition des dossiers non validés par code intervention'};
    getColumns(statsFoldersByCode, statsFoldersByCodeChart, true, false);
    $('#refreshFoldersByCode').on('click', function () {
        getColumns(statsFoldersByCode, statsFoldersByCodeChart, true, false, dates);
    });

    /// ====================== CALL PERIMETERS ==========================

    let statsPerimeters = {element_dt: undefined, element: $('#statsPerimeters'), columns: undefined, routeCol: 'clientsByPerimeter/columns', routeData: 'clientsByPerimeter'};
    let statsPerimetersChart = {element_chart: undefined, element_id: 'statsPerimetersChart', data: undefined, chartTitle: 'Production Globale CAM'};
    getColumns(statsPerimeters, statsPerimetersChart, true, false);
    $('#refreshPerimeters').on('click', function () {
        getColumns(statsPerimeters, statsPerimetersChart,true, false, dates);
    });

    /// ====================== FUNCTIONS ==========================

    function getColumns(object, objectChart = null, callInitDT = true, pagination = false, data = null) {
        $.ajax({
            url: object.routeCol,
            method: 'GET',
            data: {data},
            success: function (response) {
                object.columns = response.columns;
                if (callInitDT) {
                    object.element_dt = InitDataTable(object, pagination, data);
                }
                if (objectChart !== null && objectChart !== undefined) {
                    InitChart(objectChart, response.columns, response.data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    function InitChart(objectChart, columns, data) {
        let labels = columns.map((column) => {
            return column.name;
        });
        let column = labels[0];
        labels.pop();
        labels.shift();
        let datasets = data.map((item) => {
            let regions = Object.values(item.values).map((value) => {
                return parseFloat(isNaN(value) ? value.replace('%', '') : value);
            });
            let _dataItem = {label: item[column], backgroundColor: dynamicColors(), data: regions};
            return _dataItem;
        });
        var ctx = document.getElementById(objectChart.element_id).getContext('2d');
        let barChartData = {labels, datasets};
        if (objectChart.element_chart !== null && objectChart.element_chart !== undefined) {
            objectChart.element_chart.destroy();
        }
        objectChart.element_chart = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                title: {
                    display: true,
                    text: objectChart.chartTitle
                },
                tooltips: {
                    mode: 'index',
                    intersect: true
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: false,
                    }],
                    yAxes: [{
                        stacked: false
                    }]
                }
            }
        });
    }

    function InitDataTable(object, pagination = false, data = null) {
        if ($.fn.DataTable.isDataTable(object.element_dt)) {
            object.element_dt.destroy();
        }
        return object.element.DataTable({
            responsive: true,
            info: false,
            processing: true,
            serverSide: true,
            searching: false,
            bPaginate: pagination,
            ajax: {
                url: object.routeData,
                data: {data},
            },
            columns: object.columns
        });
    }

    function dynamicColors() {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgb(" + r + "," + g + "," + b + ")";
    };

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }
});
