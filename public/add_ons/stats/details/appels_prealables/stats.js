$(function () {
    let dates = undefined;
    let resultatAppel = undefined;
    let groupement = undefined;
    let gpmtAppelPre = undefined;
    let codeTypeIntervention = undefined;
    let codeIntervention = undefined;
    let nomRegion = undefined;
    let codeRdvInterventionConfirm = undefined;
    let codeRdvIntervention = undefined;
    let agent_name = '';
    let agence_code = '';


    const agence_name_element = $('#agence_name');
    if (agence_name_element) {
        if (agence_name_element.val()) {
            agence_code = agence_name_element.val();
        }
    }
    const agent_name_element = $('#agent_name');
    if (agent_name_element) {
        if (agent_name_element.val()) {
            agent_name = agent_name_element.val();
        }
    }
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
            url: 'Groupement',
            elements: [
                {
                    id: '#stats-groupement-filter',
                    text: 'Groupement', values: (v) => {
                        nomRegion = undefined;
                        gpmtAppelPre = undefined;
                        codeTypeIntervention = undefined;
                        codeIntervention = undefined;
                        resultatAppel = undefined;
                        codeRdvInterventionConfirm = undefined;
                        codeRdvIntervention = undefined;
                        groupement = v;
                    }, class: '.tree-groupement-view'
                },
                {
                    id: '#stats-regions-filter',
                    text: 'Groupement', values: (v) => {
                        nomRegion = undefined;
                        gpmtAppelPre = undefined;
                        codeTypeIntervention = undefined;
                        codeIntervention = undefined;
                        resultatAppel = undefined;
                        codeRdvInterventionConfirm = undefined;
                        codeRdvIntervention = undefined;
                        groupement = v;
                    }, class: '.tree-region-view'
                }
            ]
        },
        {
            url: 'Gpmt_Appel_Pre',
            elements: [
                {
                    id: '#stats-call-regions-filter', class: '.tree-call-region-view',
                    text: 'Résultats Appels Préalables par agence', values: (v) => {
                        nomRegion = undefined;
                        groupement = undefined;
                        resultatAppel = undefined;
                        codeTypeIntervention = undefined;
                        codeIntervention = undefined;
                        codeRdvInterventionConfirm = undefined;
                        codeRdvIntervention = undefined;
                        gpmtAppelPre = v;
                    }
                },
                {
                    id: '#stats-weeks-regions-filter', class: '.tree-weeks-region-view',
                    text: 'Résultats Appels Préalables par semaine', values: (v) => {
                        nomRegion = undefined;
                        groupement = undefined;
                        resultatAppel = undefined;
                        codeTypeIntervention = undefined;
                        codeIntervention = undefined;
                        codeRdvInterventionConfirm = undefined;
                        codeRdvIntervention = undefined;
                        gpmtAppelPre = v;
                    }
                }
            ]
        },
        {
            url: 'Code_Type_Intervention',
            elements: [
                {
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
                        codeRdvInterventionConfirm = undefined;
                        codeRdvIntervention = undefined;
                    }
                }
            ],
        },
        {
            url: 'Code_Intervention',
            elements: [
                {
                    id: '#code-intervention-filter', class: '.tree-code-intervention-view',
                    text: 'Intervention', values: (v) => {
                        nomRegion = undefined;
                        groupement = undefined;
                        resultatAppel = undefined;
                        codeTypeIntervention = undefined;
                        codeIntervention = v;
                        gpmtAppelPre = undefined;
                        codeRdvInterventionConfirm = undefined;
                        codeRdvIntervention = undefined;
                    }
                }
            ]
        },
        {
            url: 'Nom_Region',
            elements: [
                {
                    id: '#nom-region-filter', class: '.tree-nom-region-view',
                    text: 'Region',
                    values: (v) => {
                        groupement = undefined;
                        resultatAppel = undefined;
                        codeTypeIntervention = undefined;
                        codeIntervention = undefined;
                        gpmtAppelPre = undefined;
                        codeRdvInterventionConfirm = undefined;
                        codeRdvIntervention = undefined;
                        nomRegion = v;
                    }
                },

                {
                    id: '#code-rdv-intervention-confirm-filter', class: '.tree-code-rdv-intervention-confirm-view',
                    text: 'Region',
                    values: (v) => {
                        groupement = undefined;
                        resultatAppel = undefined;
                        codeTypeIntervention = undefined;
                        codeIntervention = undefined;
                        gpmtAppelPre = undefined;
                        nomRegion = undefined;
                        codeRdvInterventionConfirm = v;
                        codeRdvIntervention = undefined;

                    }
                },

                {
                    id: '#code-rdv-intervention-filter', class: '.tree-code-rdv-intervention-view',
                    text: 'Region',
                    values: (v) => {
                        groupement = undefined;
                        resultatAppel = undefined;
                        codeTypeIntervention = undefined;
                        codeIntervention = undefined;
                        gpmtAppelPre = undefined;
                        nomRegion = undefined;
                        codeRdvInterventionConfirm = undefined;
                        codeRdvIntervention = v;
                    }
                }
            ],
        },
    ];

    let treeData = undefined;
    let datesFilterList = [];
    let datesFilterValues = [];

    let datesFilterListExist = false;
    let datesFilterValuesExist = false;


    let filterList = [];
    let filterValues = [];

    let filterListExist = false;
    let filterValuesExist = false;

    $.ajax({
        url: APP_URL + '/dates',
        method: 'GET',
        success: function (response) {
            datesFilterListExist = true;
            let treeData = response.dates;

            $('.tree-view').each(function (index, item) {
                let treeId = '#' + $(this).attr('id');
                new Tree(treeId, {
                    data: [{id: '-1', text: 'Dates', children: treeData}],
                    closeDepth: 1,
                    loaded: function () {
                        // this.values = ['2019-12-02', '2019-12-03'];
                        // console.log(this.selectedNodes);
                        // console.log(this.values);
                        // this.disables = ['0-0-0', '0-0-1', '0-0-2']

                        datesFilterList[treeId] = this;
                        //datesFilterList.push([treeId, this]);
                        // console.log(datesFilterList);
                    },
                    onChange: function () {
                        dates = this.values;
                    }
                });
            });
            if (datesFilterListExist && datesFilterValuesExist) {
                assignFilter(datesFilterList, datesFilterValues);
            }
            $('.treejs-node .treejs-nodes .treejs-switcher').click();
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });

    for (let p of paramFiltreList) {
        $.ajax({
            url: `${APP_URL}/stats/filter/${p.url}`,
            data: getData,
            method: 'GET',
            success: function (response) {
                const data = response.data.map(function (d) {
                    return {
                        id: d,
                        text: d
                    };
                });
                for (let element of p.elements) {
                    $(element.class).each(function (index, item) {
                        new Tree(element.id, {
                            data: [{id: '-1', text: element.text, children: data}],
                            closeDepth: 1,
                            loaded: function () {
                            },
                            onChange: function () {
                                element.values(this.values);
                            }
                        });
                        // $(this).find('.treejs-switcher').first().parent().first().addClass('treejs-node__close')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    const filterData = () => {
        // console.log(agence_code, agent_name);
        return {
            dates,
            resultatAppel,
            gpmtAppelPre,
            codeTypeIntervention,
            codeIntervention,
            codeRdvIntervention,
            codeRdvInterventionConfirm,
            groupement,
            nomRegion,
            agent_name,
            agence_code
        };
    };

    //<editor-fold desc="SELECTED FILTER">
    let statsCallsPrealable = {
        element_dt: undefined,
        element: $('#statsCallsPrealable'),
        columns: undefined,
        data: undefined,
        treeElement: '#tree-view-1',
        routeCol: 'appels-pralables/regions/details/groupement/columns?key_groupement=Appels-pralables',
        routeData: 'appels-pralables/regions/details/groupement?key_groupement=Appels-pralables',
        objChart: {
            element_chart: undefined,
            element_id: 'statsCallsPrealableChart',
            data: undefined,
            chartTitle: '===='
        }
    };
    getColumns(statsCallsPrealable, filterData(), {
        removeTotal: false,
        refreshMode: false,
        removeTotalColumn: false
    });
    $('#refreshCallsPrealable').on('click', function () {
        getColumns(statsCallsPrealable, filterData(), {
            removeTotal: false,
            refreshMode: true,
            removeTotalColumn: false
        });
    });
    //</editor-fold>

    //<editor-fold desc="CALLS STATS AGENCIES / WEEKS">
    let callsStatesAgencies = {
        element_dt: undefined,
        element: $('#callsStatesAgencies'),
        columns: undefined,
        data: undefined,
        treeElement: '#tree-view-2',
        filterTreeElement: '#stats-call-regions-filter',
        routeCol: 'appels-pralables/regionsCallState/columns/Nom_Region',
        routeData: 'appels-pralables/regionsCallState/Nom_Region',
        objChart: {
            element_chart: undefined,
            element_id: 'callsStatesAgenciesChart',
            data: undefined,
            chartTitle: 'Résultats Appels Préalables par agence'
        }
    };
    getColumns(callsStatesAgencies, filterData(), {removeTotalColumn: true, removeTotal: true});
    $('#refreshCallStatesAgencies').on('click', function () {
        getColumns(callsStatesAgencies, filterData(), {
            removeTotal: true,
            refreshMode: true,
            removeTotalColumn: true
        });
    });


    let callsStatesWeeks = {
        element_dt: undefined,
        element: $('#callsStatesWeeks'),
        columns: undefined,
        data: undefined,
        treeElement: '#tree-view-3',
        filterTreeElement: '#stats-weeks-regions-filter',
        routeCol: 'appels-pralables/regionsCallState/columns/Date_Heure_Note_Semaine',
        routeData: 'appels-pralables/regionsCallState/Date_Heure_Note_Semaine',
        objChart: {
            element_chart: undefined,
            element_id: 'callsStatesWeeksChart',
            data: undefined,
            chartTitle: 'Résultats Appels Préalables par semaine'
        }
    };
    getColumns(callsStatesWeeks, filterData(), {removeTotalColumn: true, removeTotal: true});
    $('#refreshCallStatesWeeks').on('click', function () {
        getColumns(callsStatesWeeks, filterData(), {
            removeTotal: true,
            refreshMode: true,
            removeTotalColumn: true
        });
    });
    //</editor-fold>

    //<editor-fold desc="CALL STATS Joignables / Injoignable">
    let statscallsPos = {
        element_dt: undefined,
        element: $('#statsCallsPos'),
        columns: undefined,
        data: undefined,
        treeElement: '#tree-view-4',
        filterTreeElement: '#code-rdv-intervention-confirm-filter',
        routeCol: 'appels-pralables/clientsByCallState/columns/Joignable',
        routeData: 'appels-pralables/clientsByCallState/Joignable',
        objChart: {
            element_chart: undefined,
            element_id: 'statsCallsPosChart',
            data: undefined,
            chartTitle: 'Code Interventions liés aux RDV Confirmés (Clients Joignables)'
        }
    };
    getColumns(statscallsPos, filterData(), {removeTotal: false, refreshMode: false, removeTotalColumn: true});
    $('#refreshCallResultPos').on('click', function () {
        getColumns(statscallsPos, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true
        });
    });

    let statscallsNeg = {
        element_dt: undefined,
        element: $('#statsCallsNeg'),
        columns: undefined,
        data: undefined,
        treeElement: '#tree-view-5',
        filterTreeElement: '#code-rdv-intervention-filter',
        routeCol: 'appels-pralables/clientsByCallState/columns/Injoignable',
        routeData: 'appels-pralables/clientsByCallState/Injoignable',
        objChart: {
            element_chart: undefined,
            element_id: 'statscallsNegChart',
            data: undefined,
            chartTitle: 'Code Interventions liés aux RDV Confirmés (Clients Injoignables)'
        }
    };
    getColumns(statscallsNeg, filterData(), {removeTotal: false, refreshMode: false, removeTotalColumn: true});
    $('#refreshCallResultNeg').on('click', function () {
        getColumns(statscallsNeg, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true
        });
    });
    //</editor-fold>

    //<editor-fold desc="FUNCTIONS">
    function getColumns(object, data = null, params = {
        removeTotal: true,
        refreshMode: false,
        details: false,
        removeTotalColumn: false,
        pagination: false
    }) {
        // if refreshmode is enabled then store the new filter in local storage
        if (params.refreshMode) {
            // localStorage.setItem(object.filterTreeElement, JSON.stringify(data));
            data = {...data, refreshMode: true}; //{dates: data, refreshMode: true};
        }
        // Search if filter stored in local storage
        // let savedData = JSON.parse(localStorage.getItem(object.filterTreeElement));
        // if (savedData !== null) {
        //     data = savedData;
        // }

        $.ajax({
            url: APP_URL + '/' + object.routeCol,
            method: 'GET',
            data: data,
            success: function (response) {
                let datesFilterValuesExist = true;
                let filters = response.filter;
                if (filters !== null && filters !== undefined) {
                    datesFilterValues.push([object.treeElement, filters.date_filter]);
                    // if (datesFilterList !== null && datesFilterList !== undefined && datesFilterList.length > 0) {
                    //     datesFilterList[object.treeElement].values = datesFilterValues[object.treeElement];
                    // }
                    if (datesFilterListExist && datesFilterValuesExist) {
                        assignFilter(datesFilterList, datesFilterValues);
                    }
                }
                // console.log(filters.date_filter);
                object.columns = [...response.columns];
                object.data = [...response.data];
                if (params.details) {
                    $(object.element).find('thead tr').prepend('<th></th>');
                }
                if (data !== null && data !== undefined) {
                    try {
                        object.element_dt = InitDataTable(object, data, {
                            removeTotal: params.removeTotal,
                            removeTotalColumn: params.removeTotalColumn,
                            details: params.details,
                            pagination: params.pagination
                        });
                        if (params.details) {
                            object.element.on('click', 'td.details-control', function () {
                                const tr = $(this).closest('tr');
                                const row = object.element_dt.row(tr);
                                if (row.child.isShown()) {
                                    // This row is already open - close it
                                    destroyChild(row);
                                    tr.removeClass('shown');
                                } else {
                                    // Open this row
                                    data = {...data, key_groupement: tr.find('td:nth-child(2)').text()};
                                    object.objDetail.element = 'details-' + $('tr').index(tr);
                                    createChild(row, object.objDetail, data); // class is for background colour
                                    tr.addClass('shown');
                                }
                            });
                        }
                    } catch (error) {
                        console.log(error);
                    }
                }
                // if (object.objChart !== null && object.objChart !== undefined) {
                //     try {
                //         InitChart(object.objChart, response.columns, response.data, removeTotal, removeTotalColumn);
                //     } catch (error) {
                //         console.log(error);
                //     }
                // }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                console.log(APP_URL + '/' + object.routeCol);
                console.log('===========');
            }
        });
    }

    function InitDataTable(object, data = null, params = {
        removeTotal: true,
        removeTotalColumn: false,
        details: false,
        pagination: false
    }) {
        if ($.fn.DataTable.isDataTable(object.element_dt)) {
            object.element.off('click', 'td.details-control');
            object.element_dt.destroy();
        }
        if (params.details) {
            object.objDetail.columns = [...object.columns];
            object.objDetail.columns = object.objDetail.columns.map(function (item, index) {
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
        return object.element.DataTable({
            language: frLang,
            responsive: true,
            info: false,
            processing: true,
            serverSide: true,
            searching: false,
            // ordering: false,
            bPaginate: params.pagination,
            ajax: {
                url: APP_URL + '/' + object.routeData,
                data: data,
            },
            columns: object.columns,
            initComplete: function (settings, response) {
                if (object.objChart !== null && object.objChart !== undefined) {
                    try {
                        InitChart(object.objChart, object.columns, response.data, {
                            removeTotal: params.removeTotal,
                            removeTotalColumn: params.removeTotalColumn,
                            details: params.details
                        });
                    } catch (error) {
                        console.log(error);
                    }
                }
            }
        });

        // if (object.objChart !== null && object.objChart !== undefined) {
        //     try {
        //         InitChart(object.objChart, object.columns, object.data, removeTotal, removeTotalColumn);
        //     } catch (error) {
        //         console.log(error);
        //     }
        // }
    }

    function InitChart(objectChart, columns, data, params = {
        removeTotal: true,
        removeTotalColumn: false,
        details: false
    }) {
        // console.log(objectChart.chartTitle);
        // console.log(columns);
        // console.log(data);
        let labels = [...columns];
        labels = labels.map((column) => {
            return column.data;
        });
        if (params.details) {
            labels.shift();
        }
        let column = labels.shift();
        if (params.removeTotalColumn) {
            labels.pop();
        }
        let datasets = [...data];
        if (params.removeTotal) {
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
        // if (objectChart.element_chart !== null && objectChart.element_chart !== undefined) {
        //     objectChart.element_chart.destroy();
        // }
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
                },
                plugins: {
                    // Change options for ALL labels of THIS CHART
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        font: {
                            weight: 'bold',
                            // size: 14
                        },
                        rotation: -45,
                        display: function (context) {
                            // if(context.dataset.data.length > 10) {
                            //     return false;
                            // }
                            return context.dataset.data[context.dataIndex] !== 0;
                        }
                    }
                }
            }
        });
    }

    function assignFilter(datesFilterList, datesFilterValues) {
        for (let [key, value] of datesFilterValues) {
            if (key in datesFilterList) {
                datesFilterList[key].values = value;
            }
        }
    }

    //</editor-fold>

    //<editor-fold desc="FUNCTIONS TOOLS">
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
    }

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }

    function createChild(row, objectChild, data = null) {
        // This is the table we'll convert into a DataTable
        var tableDom = '<table id="' + objectChild.element + '" class="table-details table table-bordered table-valign-middle capitalize"/>';
        var canvasDom = '<div class="col-12"><canvas id="' + objectChild.element + '-Chart"/></div>';
        objectChild.objChart.element_id = objectChild.element + '-Chart';
        objectChild.element = $(tableDom);

        // ');

        let createdChild = tableDom;
        // Display it the child row
        row.child(objectChild.element).show();
        objectChild.element.after(canvasDom);
        // row.child(objectChild.element).show();
        InitDataTable(objectChild, data, {removeTotal: false, removeTotalColumn: false, details: false});
    }

    function destroyChild(row) {
        var table = $("table", row.child());
        table.detach();
        table.DataTable().destroy();

        // And then hide the row
        row.child.hide();
    }

    //</editor-fold>

    //<editor-fold desc="GLOBAL FILTER">
    $('#filterDashboard').on('change', function () {
        let url = $(this).val();
        if (url) {
            window.location = APP_URL + '/dashboard/' + url;
        }
    });

    $("#refreshAll").on('click', function () {
        getColumns(statsCallsPrealable, filterData(), {
            removeTotal: false,
            refreshMode: true,
            removeTotalColumn: false
        });
        getColumns(callsStatesAgencies, filterData(), {
            removeTotal: true,
            refreshMode: true,
            removeTotalColumn: true
        });
        getColumns(callsStatesWeeks, filterData(), {
            removeTotal: true,
            refreshMode: true,
            removeTotalColumn: true
        });
        getColumns(statscallsPos, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true
        });
        getColumns(statscallsNeg, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true
        });
    });
    //</editor-fold>

});
