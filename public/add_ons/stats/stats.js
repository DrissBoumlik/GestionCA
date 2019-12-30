$(function () {
    let dates = undefined;
    let resultatAppel = undefined;
    let groupement = undefined;
    let gpmtAppelPre = undefined;
    let codeTypeIntervention = undefined;
    let codeIntervention = undefined;
    const paramFiltreList = [
        {
            url: 'Groupement', id: '#stats-groupement-filter',
            text: 'Groupement', values: (v) => {
                gpmtAppelPre = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = undefined;
                resultatAppel = undefined;
                groupement = v;
            }, class: '.tree-groupement-view'
        },
        {
            url: 'Resultat_Appel', id: '#stats-regions-filter',
            text: 'Resultat Appel', values: (v) => {
                gpmtAppelPre = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = undefined;
                resultatAppel = v;
                groupement = undefined;
            }, class: '.tree-region-view'
        },
        {
            url: 'Gpmt_Appel_Pre', id: '#stats-call-regions-filter', class: '.tree-call-region-view',
            text: 'Résultats Appels Préalables par agence', values: (v) => {
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
                groupement = undefined;
                resultatAppel = undefined;
                codeTypeIntervention = undefined;
                codeIntervention = v;
                gpmtAppelPre = undefined;
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
            // data: {
            //     agence_code: agence_code
            //     agence_code: agence_code
            // },
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
        return {
            dates,
            resultatAppel,
            gpmtAppelPre,
            codeTypeIntervention,
            codeIntervention,
            groupement,
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
    let statsRegionsChart = {
        element_chart: undefined,
        element_id: 'statsRegionsChart',
        data: undefined,
        chartTitle: 'Résultats Appels (Clients Joints)'
    };
    getColumns(statsRegions, statsRegionsChart, true, true, null, false);
    $('#refreshRegions').on('click', function () {
        let data = {dates, refreshMode: true};
        getColumns(statsRegions, statsRegionsChart, true, true, filterData(true), false, true);
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
    getColumns(statsFolders, statsFoldersChart, true, true);
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
    getColumns(callsStatesAgencies, callsStatesAgenciesChart, true, false);
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
    getColumns(callsStatesWeeks, callsStatesWeeksChart, true, false);
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
    getColumns(statscallsPos, statsCallsPosChart, true, false);
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
    getColumns(statscallsNeg, statscallsNegChart, true, false);

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
    getColumns(statsFoldersByType, statsFoldersByTypeChart, true, false);
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
    getColumns(statsFoldersByCode, statsFoldersByCodeChart, true, false);
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
    getColumns(statsPerimeters, statsPerimetersChart, true, false);
    $('#refreshPerimeters').on('click', function () {
        getColumns(statsPerimeters, statsPerimetersChart, true, false, filterData());
    });

    /// ====================== FUNCTIONS ==========================

    function getColumns(object, objectChart = null, callInitDT = true, pagination = false, data = null, removeTotal = true, refreshMode = false) {
        $.ajax({
            url: object.routeCol,
            method: 'GET',
            data: data,
            success: function (response) {
                object.columns = response.columns;
                if (callInitDT) {
                    if (refreshMode) {
                        data = {dates: data, refreshMode: true};
                    }
                    object.element_dt = InitDataTable(object, pagination, data);
                }
                if (objectChart !== null && objectChart !== undefined) {
                    InitChart(objectChart, response.columns, response.data, removeTotal);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('error');
            }
        });
    }

    function InitChart(objectChart, columns, data, removeTotal = true) {
        // console.log(objectChart.chartTitle);
        // console.log(columns);
        // console.log(data);
        let labels = Object.values({...columns});
        labels = labels.map((column) => {
            return column.name;
        });
        let column = labels.shift();
        labels.pop();
        let datasets = Object.values({...data});
        if (removeTotal) {
            datasets.pop();
        }
        // debugger
        let uniqueColors = [];
        datasets = datasets.map((item) => {
            // debugger
            let regions = Object.values(item.values).map((value) => {
                // debugger
                return parseFloat(isNaN(value) ? value.replace('%', '') : value);
            });
            let _dataItem = {label: item[column], backgroundColor: dynamicColors(uniqueColors), data: regions};
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
});
