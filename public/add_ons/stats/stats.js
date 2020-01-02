$(function () {
    let dates = undefined;
    let resultatAppel = undefined;
    let groupement = undefined;
    let gpmtAppelPre = undefined;
    let codeTypeIntervention = undefined;
    let codeIntervention = undefined;
    let nomRegion = undefined;
    let agent_name = '';
    let agence_code = '';

    const params = window.location.href.split('?')[1];
    if (params) {
        const paramsList = params.split('&');
        for (let param of paramsList) {
            const p = param.split('=');
            if (p[0] === 'agence_code') {
                agence_code = p[1];
            }
            if (p[0] === 'agent_name') {
                agent_name = p[1];
            }
        }
    }

    const getData = {};
    if (agence_code) {
        getData['agence_code'] = agence_code;
    }
    if (agent_name) {
        getData['agent_name'] = agent_name;
    }

    const paramFiltreList = [
        {
            url: 'Groupement', id: '#stats-groupement-filter',
            text: 'Groupement', values: (v) => {
                nomRegion = undefined;
                gpmtAppelPre = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = undefined;
                resultatAppel = undefined;
                groupement = v;
            }, class: '.tree-groupement-view'
        },
        {
            url: 'Groupement', id: '#stats-regions-filter',
            text: 'Groupement', values: (v) => {
                nomRegion = undefined;
                gpmtAppelPre = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = undefined;
                resultatAppel = undefined;
                groupement = v;
            }, class: '.tree-region-view'
        },
        {
            url: 'Gpmt_Appel_Pre', id: '#stats-call-regions-filter', class: '.tree-call-region-view',
            text: 'Résultats Appels Préalables par agence', values: (v) => {
                nomRegion = undefined;
                groupement = undefined;
                resultatAppel = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = undefined;
                gpmtAppelPre = v;
            }
        },
        {
            url: 'Gpmt_Appel_Pre', id: '#stats-weeks-regions-filter', class: '.tree-weeks-region-view',
            text: 'Résultats Appels Préalables par semaine', values: (v) => {
                nomRegion = undefined;
                groupement = undefined;
                resultatAppel = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = undefined;
                gpmtAppelPre = v;
            }
        },
        {
            url: 'Code_Type_Intervention',
            id: '#code-type-intervention-filter',
            class: '.tree-code-type-intervention-view',
            text: 'Type Intervention',
            values: (v) => {
                nomRegion = undefined;
                groupement = undefined;
                resultatAppel = undefined;
                codeTypeIntervention = v;
                codeIntervention = undefined;
                gpmtAppelPre = undefined;
            }
        },
        {
            url: 'Code_Intervention', id: '#code-intervention-filter', class: '.tree-code-intervention-view',
            text: 'Intervention', values: (v) => {
                nomRegion = undefined;
                groupement = undefined;
                resultatAppel = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = v;
                gpmtAppelPre = undefined;
            }
        },
        {
            url: 'Nom_Region', id: '#nom-region-filter', class: '.tree-nom-region-view',
            text: 'Region', values: (v) => {
                groupement = undefined;
                resultatAppel = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = undefined;
                gpmtAppelPre = undefined;
                nomRegion = v;
            }
        },
    ];



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
                        console.log(this);
                        console.log($(this));
                        console.log($(this)[0].container);
                        dates = this.values;
                    }
                });
                // $(this).find('.treejs-switcher').first().parent().first().addClass('treejs-node__close')
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });

    for (let p of paramFiltreList) {
        $.ajax({
            url: `stats/filter/${p.url}`,
            data: getData,
            method: 'GET',
            success: function (response) {
                const data = response.data.map(function (d) {
                    return {
                        id: d,
                        text: d
                    };
                });
                $(p.class).each(function (index, item) {
                    new Tree(p.id, {
                        data: [{id: '-1', text: p.text, children: data}],
                        closeDepth: 1,
                        loaded: function () {
                        },
                        onChange: function () {
                            p.values(this.values);
                        }
                    });
                    // $(this).find('.treejs-switcher').first().parent().first().addClass('treejs-node__close')
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    const filterData = (refreshMode = false) => {
        console.log(agence_code, agent_name);
        return {
            dates,
            resultatAppel,
            gpmtAppelPre,
            codeTypeIntervention,
            codeIntervention,
            groupement,
            nomRegion,
            agent_name,
            agence_code,
            refreshMode: refreshMode
        };
    };

    /// ====================== REGIONS ==========================
    let statsRegions = {
        element_dt: undefined,
        element: $('#statsRegions'),
        columns: undefined,
        data: undefined,
        routeCol: 'regions/columns/Groupement',
        routeData: 'regions/Groupement'
    };
    let statsRegionsDetails = {
        element: undefined,
        columns: undefined,
        routeData: 'regions/Groupement' // $('#stateregdet-url').attr('url')
    };
    let statsRegionsChart = {
        element_chart: undefined,
        element_id: 'statsRegionsChart',
        data: undefined,
        chartTitle: 'Résultats Appels (Clients Joints)'
    };

    getColumns(statsRegions, statsRegionsChart, true, true, null, false, false, true, false);
    $('#refreshRegions').on('click', function () {
        let data = {dates, refreshMode: true};
        getColumns(statsRegions, statsRegionsChart, true, true, filterData(true), false, true, true, false);
    });

    let statsFolders = {
        element_dt: undefined,
        element: $('#statsFolders'),
        columns: undefined,
        data: undefined,
        routeCol: 'folders/columns/Groupement',
        routeData: 'folders/Groupement'
    };
    let statsFoldersChart = {
        element_chart: undefined,
        element_id: 'statsFoldersChart',
        data: undefined,
        chartTitle: 'Répartition des dossiers traités sur le périmètre validation, par catégorie de traitement'
    };
    getColumns(statsFolders, statsFoldersChart, true, true, filterData());
    $('#refreshFolders').on('click', function () {
        getColumns(statsFolders, statsFoldersChart, true, true, filterData());
    });


    /// ====================== CALLS STATS AGENCIES / WEEKS ==========================

    let callsStatesAgencies = {
        element_dt: undefined,
        element: $('#callsStatesAgencies'),
        columns: undefined,
        data: undefined,
        routeCol: 'regionsCallState/columns/Nom_Region',
        routeData: 'regionsCallState/Nom_Region'
    };
    let callsStatesAgenciesChart = {
        element_chart: undefined,
        element_id: 'callsStatesAgenciesChart',
        data: undefined,
        chartTitle: 'Résultats Appels Préalables par agence'
    };
    getColumns(callsStatesAgencies, callsStatesAgenciesChart, true, false, filterData());
    $('#refreshCallStatesAgencies').on('click', function () {
        getColumns(callsStatesAgencies, callsStatesAgenciesChart, true, false, filterData());
    });


    let callsStatesWeeks = {
        element_dt: undefined,
        element: $('#callsStatesWeeks'),
        columns: undefined,
        data: undefined,
        routeCol: 'regionsCallState/columns/Date_Heure_Note_Semaine',
        routeData: 'regionsCallState/Date_Heure_Note_Semaine'
    };
    let callsStatesWeeksChart = {
        element_chart: undefined,
        element_id: 'callsStatesWeeksChart',
        data: undefined,
        chartTitle: 'Résultats Appels Préalables par semaine'
    };
    getColumns(callsStatesWeeks, callsStatesWeeksChart, true, false, filterData());
    $('#refreshCallStatesWeeks').on('click', function () {
        getColumns(callsStatesWeeks, callsStatesWeeksChart, true, false, filterData());
    });


    /// ====================== CALL STATS Joignables / Injoignable ==========================

    let statscallsPos = {
        element_dt: undefined,
        element: $('#statsCallsPos'),
        columns: undefined,
        data: undefined,
        routeCol: 'clientsByCallState/columns/Joignable',
        routeData: 'clientsByCallState/Joignable'
    };
    let statsCallsPosChart = {
        element_chart: undefined,
        element_id: 'statsCallsPosChart',
        data: undefined,
        chartTitle: 'Code Interventions liés aux RDV Confirmés (Clients Joignables)'
    };
    getColumns(statscallsPos, statsCallsPosChart, true, false, filterData());
    $('#refreshCallResultPos').on('click', function () {
        getColumns(statscallsPos, statsCallsPosChart, true, false, filterData());
    });

    let statscallsNeg = {
        element_dt: undefined,
        element: $('#statsCallsNeg'),
        columns: undefined,
        data: undefined,
        routeCol: 'clientsByCallState/columns/Injoignable',
        routeData: 'clientsByCallState/Injoignable'
    };
    let statscallsNegChart = {
        element_chart: undefined,
        element_id: 'statscallsNegChart',
        data: undefined,
        chartTitle: 'Code Interventions liés aux RDV Confirmés (Clients Injoignables)'
    };
    $('#refreshCallResultNeg').on('click', function () {
        let data = {dates, refreshMode: true};
        getColumns(statscallsNeg, statscallsNegChart, true, false, filterData(true));
    });
    getColumns(statscallsNeg, statscallsNegChart, true, false, filterData());

    /// ====================== FOLDERS CODE / TYPE ==========================

    let statsFoldersByType = {
        element_dt: undefined,
        element: $('#statsFoldersByType'),
        columns: undefined,
        data: undefined,
        routeCol: 'nonValidatedFolders/columns/Code_Type_Intervention',
        routeData: 'nonValidatedFolders/Code_Type_Intervention'
    };
    let statsFoldersByTypeChart = {
        element_chart: undefined,
        element_id: 'statsFoldersByTypeChart',
        data: undefined,
        chartTitle: 'Répartition des dossiers non validés par Code Type intervention'
    };
    getColumns(statsFoldersByType, statsFoldersByTypeChart, true, false, filterData());
    $('#refreshFoldersByType').on('click', function () {
        getColumns(statsFoldersByType, statsFoldersByTypeChart, true, false, filterData());
    });

    let statsFoldersByCode = {
        element_dt: undefined,
        element: $('#statsFoldersByCode'),
        columns: undefined,
        data: undefined,
        routeCol: 'nonValidatedFolders/columns/Code_Intervention',
        routeData: 'nonValidatedFolders/Code_Intervention'
    };
    let statsFoldersByCodeChart = {
        element_chart: undefined,
        element_id: 'statsFoldersByCodeChart',
        data: undefined,
        chartTitle: 'Répartition des dossiers non validés par code intervention'
    };
    getColumns(statsFoldersByCode, statsFoldersByCodeChart, true, false, filterData());
    $('#refreshFoldersByCode').on('click', function () {
        getColumns(statsFoldersByCode, statsFoldersByCodeChart, true, false, filterData());
    });

    /// ====================== CALL PERIMETERS ==========================

    let statsPerimeters = {
        element_dt: undefined,
        element: $('#statsPerimeters'),
        columns: undefined,
        data: undefined,
        routeCol: 'clientsByPerimeter/columns',
        routeData: 'clientsByPerimeter'
    };
    let statsPerimetersChart = {
        element_chart: undefined,
        element_id: 'statsPerimetersChart',
        data: undefined,
        chartTitle: 'Production Globale CAM'
    };
    getColumns(statsPerimeters, statsPerimetersChart, true, false, filterData());
    $('#refreshPerimeters').on('click', function () {
        getColumns(statsPerimeters, statsPerimetersChart, true, false, filterData());
    });

    /// ====================== FUNCTIONS ==========================

    function getColumns(object, objectChart = null, callInitDT = true, pagination = false, data = null, removeTotal = true, refreshMode = false, details = false, removeTotalColumn = true) {
        $.ajax({
            url: object.routeCol,
            method: 'GET',
            data: data,
            success: function (response) {
                object.columns = [...response.columns];
                object.data = [...response.data];
                if (details) {
                    $(object.element).find('thead tr').prepend('<th></th>');
                }
                if (callInitDT) {
                    if (refreshMode) {
                        data = {...data, refreshMode: true}; //{dates: data, refreshMode: true};
                    }
                    object.element_dt = InitDataTable(object, pagination, data, details);

                    object.element.on('click', 'td.details-control', function () {
                        var tr = $(this).closest('tr');
                        var row = object.element_dt.row(tr);
                        if (row.child.isShown()) {
                            // This row is already open - close it
                            destroyChild(row);
                            tr.removeClass('shown');
                        } else {
                            // Open this row
                            data = {key_groupement: tr.find('td:nth-child(2)').text()};
                            statsRegionsDetails.element = 'details-' + $('tr').index(tr);
                            statsRegionsDetails.routeData = $('#stateregdet-url').attr('url');

                            createChild(row, statsRegionsDetails, data); // class is for background colour
                            tr.addClass('shown');
                        }
                    });
                }
                if (objectChart !== null && objectChart !== undefined) {
                    InitChart(objectChart, response.columns, response.data, removeTotal, removeTotalColumn);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    function InitChart(objectChart, columns, data, removeTotal = true, removeTotalColumn = false) {
        // console.log(objectChart.chartTitle);
        // console.log(columns);
        // console.log(data);
        let labels = [...columns];
        labels = labels.map((column) => {
            return column.data;
        });
        let column = labels.shift();
        if (removeTotalColumn) {
            labels.pop();
        }
        let datasets = [...data];
        if (removeTotal) {
            datasets.pop();
        }
        let uniqueColors = [];
        datasets = datasets.map((item) => {
            let regions = item.values.map((value) => {
                return parseFloat(isNaN(value) ? value.replace('%', '') : value);
            });
            let _dataItem = {label: item[column], backgroundColor: dynamicColors(uniqueColors), data: regions};
            // let _dataItem = {label: item[column], backgroundColor: dynamicColors(uniqueColors), data: regions, fill: false, borderColor: dynamicColors(uniqueColors)};
            return _dataItem;
        });

        var ctx = document.getElementById(objectChart.element_id).getContext('2d');
        let ChartData = {labels, datasets};
        if (objectChart.element_chart !== null && objectChart.element_chart !== undefined) {
            objectChart.element_chart.destroy();
        }
        objectChart.element_chart = new Chart(ctx, {
            type: 'bar',
            data: ChartData,
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

    function InitDataTable(object, pagination = false, data = null, details = false) {
        if ($.fn.DataTable.isDataTable(object.element_dt)) {
            object.element_dt.destroy();
        }
        if (details) {
            statsRegionsDetails.columns = [...object.columns];
            statsRegionsDetails.columns = statsRegionsDetails.columns.map(function (item, index) {
                if (index === 0) {
                    return {...item, data: 'Resultat_Appel', name: 'Resultat_Appel', title: 'Resultat Appel'};
                }
                return {...item, title: item.name};
            });

            object.columns.unshift({
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '',
                width: '10%'
            });
        }
        const table = object.element.DataTable({
            responsive: true,
            info: false,
            processing: true,
            serverSide: true,
            searching: false,
            bPaginate: pagination,
            ajax: {
                url: object.routeData,
                data: data,
            },
            columns: object.columns
        });
        return table;
    }

    function dynamicColors(uniqueColors) {
        let color = {
            r: Math.floor(Math.random() * 255),
            g: Math.floor(Math.random() * 255),
            b: Math.floor(Math.random() * 255)
        };
        let exists = false;
        do {
            exists = uniqueColors.some(function (uniqueColor) {
                return uniqueColor.r === color.r && uniqueColor.g === color.g && uniqueColor.b === color.b;
            });
        } while (exists);
        uniqueColors.push(color);
        return "rgb(" + color.r + "," + color.g + "," + color.b + ")";
    };

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }

    function createChild(row, object, data = null) {
        // This is the table we'll convert into a DataTable
        object.element = $('<table id="' + object.element + '" class="table-details table table-bordered table-valign-middle capitalize"/>');
        // Display it the child row
        row.child(object.element).show();

        InitDataTable(object, false, data);
    }

    function destroyChild(row) {
        var table = $("table", row.child());
        table.detach();
        table.DataTable().destroy();

        // And then hide the row
        row.child.hide();
    }
});
