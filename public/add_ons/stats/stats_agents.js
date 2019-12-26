$(function () {
    let dates = undefined;
    let agent_name = '';
    const params = window.location.href.split('?')[1];
    const paramsList = params.split('&');
    for (let param of paramsList) {
        const p = param.split('=');
        if (p[0] === 'agent_name') {
            agent_name = p[1];
        }
    }


    $.ajax({
        url: 'agents/dates',
        data: {
            agent_name: agent_name
        },
        method: 'GET',
        success: function (response) {
            let data = response.dates;
            $('.tree-view').each(function (index, item) {
                new Tree('#' + $(this).attr('id'), {
                    data: [{id: '-1', text: 'Dates', children: data}],
                    closeDepth: 2,
                    loaded: function () {
                    },
                    onChange: function () {
                        dates = this.values;
                    }
                });
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });

    /// ====================== REGIONS ==========================

    let statsRegions = {element_dt: undefined, element: $('#statsRegions'), columns: undefined, routeCol: 'agents/regions/columns/Resultat_Appel', routeData: 'agents/regions/Resultat_Appel'};
    let statsRegionsChart = {element_chart: undefined, element_id: 'statsRegionsChart', data: undefined};
    getColumns(statsRegions, statsRegionsChart);
    $('#refreshRegions').on('click', function () {
        statsRegions.element_dt = InitDataTable(statsRegions, {
            dates,
            agent_name: agent_name
        });
    });

    /// ====================== CALLS STATS AGENCIES / WEEKS ==========================

    let callsStatesAgencies = {element_dt: undefined, element: $('#callsStatesAgencies'), columns: undefined, routeCol: 'agents/regionsCallState/columns/Nom_Region', routeData: 'agents/regionsCallState/Nom_Region'};
    let callsStatesAgenciesChart = {element_chart: undefined, element_id: 'callsStatesAgenciesChart', data: undefined};
    getColumns(callsStatesAgencies, callsStatesAgenciesChart);
    $('#refreshCallStatesAgencies').on('click', function () {
        callsStatesAgencies.element_dt = InitDataTable(callsStatesAgencies, {
            dates,
            agent_name: agent_name
        });
    });


    let callsStatesWeeks = {element_dt: undefined, element: $('#callsStatesWeeks'), columns: undefined, routeCol: 'agents/regionsCallState/columns/Date_Heure_Note_Semaine', routeData: 'agents/regionsCallState/Date_Heure_Note_Semaine'};
    let callsStatesWeeksChart = {element_chart: undefined, element_id: 'callsStatesWeeksChart', data: undefined};
    getColumns(callsStatesWeeks, callsStatesWeeksChart);
    $('#refreshCallStatesWeeks').on('click', function () {
        callsStatesWeeks.element_dt = InitDataTable(callsStatesWeeks, {
            dates,
            agent_name: agent_name
        });
    });


    /// ====================== CALL STATS Joignables / Injoignable ==========================

    let statscallsPos = {element_dt: undefined, element: $('#statsCallsPos'), columns: undefined, routeCol: 'agents/clientsByCallState/columns/Joignable', routeData: 'agents/clientsByCallState/Joignable'};
    let statsCallsPosChart = {element_chart: undefined, element_id: 'statsCallsPosChart', data: undefined};
    getColumns(statscallsPos, statsCallsPosChart);
    $('#refreshCallResultPos').on('click', function () {
        statscallsPos.element_dt = InitDataTable(statscallsPos, {
            dates,
            agent_name: agent_name
        });
    });

    let statscallsNeg = {element_dt: undefined, element: $('#statsCallsNeg'), columns: undefined, routeCol: 'agents/clientsByCallState/columns/Injoignable', routeData: 'agents/clientsByCallState/Injoignable'};
    let statscallsNegChart = {element_chart: undefined, element_id: 'statscallsNegChart', data: undefined};
    getColumns(statscallsNeg, statscallsNegChart);
    $('#refreshCallResultNeg').on('click', function () {
        statscallsNeg.element_dt = InitDataTable(statscallsNeg, {
            dates,
            agent_name: agent_name
        });
    });

    /// ====================== FOLDERS CODE / TYPE ==========================

    let statsFoldersByType = {element_dt: undefined, element: $('#statsTypes'), columns: undefined, routeCol: 'agents/nonValidatedFolders/columns/Code_Type_Intervention', routeData: 'agents/nonValidatedFolders/Code_Type_Intervention'};
    getColumns(statsFoldersByType);
    $('#refreshFoldersByType').on('click', function () {
        statsFoldersByType.element_dt = InitDataTable(statsFoldersByType, {
            dates,
            agent_name: agent_name
        });
    });

    let statsFoldersByCode = {element_dt: undefined, element: $('#statsCodes'), columns: undefined, routeCol: 'agents/nonValidatedFolders/columns/Code_Intervention', routeData: 'agents/nonValidatedFolders/Code_Intervention'};
    getColumns(statsFoldersByCode);
    $('#refreshFoldersByCode').on('click', function () {
        statsFoldersByCode.element_dt = InitDataTable(statsFoldersByCode, {
            dates,
            agent_name: agent_name
        });
    });

    // getColumns('nonValidatedFoldersColumn', 'nonValidatedFolders', stats, stats_dt);


    /// ====================== FUNCTIONS ==========================
    function dynamicColors() {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgb(" + r + "," + g + "," + b + ")";
    };

    function getColumns(object, objectChart = null) {
        $.ajax({
            url: object.routeCol,
            data: {
                agent_name: agent_name
            },
            method: 'GET',
            success: function (response) {
                object.columns = response.columns;
                object.element_dt = InitDataTable(object, { agent_name: agent_name});
                if (objectChart !== null && objectChart !== undefined) {
                    let labels = response.columns.map((column) => {
                        return column.name;
                    });
                    let column = labels[0];
                    labels.pop();
                    labels.shift();
                    let datasets = response.data.map((item) => {
                        let regions = Object.values(item.values).map((value) => {
                            return parseFloat(isNaN(value) ? value.replace('%', '') : value);
                        });
                        return {label: item[column], backgroundColor: dynamicColors(), data: regions};
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
                                    stacked: false,
                                }],
                                yAxes: [{
                                    stacked: false
                                }]
                            }
                        }
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    function InitDataTable(object, data = null) {
        console.log(data);
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
                url: object.routeData,
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
