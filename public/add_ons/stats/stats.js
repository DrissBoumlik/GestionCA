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

    // const paramFiltreList = [
    //     {
    //         url: 'Groupement',
    //         elements: [
    //             {
    //                 id: '#stats-groupement-filter',
    //                 text: 'Groupement', values: (v) => {
    //                     groupement = v;
    //                 }, class: '.tree-groupement-view'
    //             },
    //             {
    //                 id: '#stats-regions-filter',
    //                 text: 'Groupement', values: (v) => {
    //                     groupement = v;
    //                 }, class: '.tree-region-view'
    //             }
    //         ]
    //     },
    //     {
    //         url: 'Gpmt_Appel_Pre',
    //         elements: [
    //             {
    //                 id: '#stats-call-regions-filter', class: '.tree-call-region-view',
    //                 text: 'Résultats Appels Préalables par agence', values: (v) => {
    //                     gpmtAppelPre = v;
    //                 }
    //             },
    //             {
    //                 id: '#stats-weeks-regions-filter', class: '.tree-weeks-region-view',
    //                 text: 'Résultats Appels Préalables par semaine', values: (v) => {
    //                     gpmtAppelPre = v;
    //                 }
    //             }
    //         ]
    //     },
    //     {
    //         url: 'Code_Type_Intervention',
    //         elements: [
    //             {
    //                 id: '#code-type-intervention-filter',
    //                 class: '.tree-code-type-intervention-view',
    //                 text: 'Type Intervention',
    //                 values: (v) => {
    //                     codeTypeIntervention = v;
    //                 }
    //             }
    //         ],
    //     },
    //     {
    //         url: 'Code_Intervention',
    //         elements: [
    //             {
    //                 id: '#code-intervention-filter', class: '.tree-code-intervention-view',
    //                 text: 'Intervention', values: (v) => {
    //                     codeIntervention = v;
    //                 }
    //             }
    //         ]
    //     },
    //     {
    //         url: 'Nom_Region',
    //         elements: [
    //             {
    //                 id: '#nom-region-filter', class: '.tree-nom-region-view',
    //                 text: 'Region',
    //                 values: (v) => {
    //                     nomRegion = v;
    //                 }
    //             },
    //
    //             {
    //                 id: '#code-rdv-intervention-confirm-filter', class: '.tree-code-rdv-intervention-confirm-view',
    //                 text: 'Region',
    //                 values: (v) => {
    //                     codeRdvInterventionConfirm = v;
    //
    //                 }
    //             },
    //
    //             {
    //                 id: '#code-rdv-intervention-filter', class: '.tree-code-rdv-intervention-view',
    //                 text: 'Region',
    //                 values: (v) => {
    //                     codeRdvIntervention = v;
    //                 }
    //             }
    //         ],
    //     },
    // ];

    // if (false) {
    //     paramFiltreList.forEach(function (p) {
    //         $.ajax({
    //             url: `${APP_URL}/stats/filter/${p.url}`,
    //             data: getData,
    //             method: 'GET',
    //             success: function (response) {
    //                 const data = response.data.map(function (d) {
    //                     return {
    //                         id: d,
    //                         text: d
    //                     };
    //                 });
    //                 p.elements.forEach(function (element) {
    //                     $(element.class).each(function (index, item) {
    //                         new Tree(element.id, {
    //                             data: [{id: '-1', text: element.text, children: data}],
    //                             closeDepth: 1,
    //                             loaded: function () {
    //                             },
    //                             onChange: function () {
    //                                 console.log(this.values);
    //                                 element.values(this.values);
    //                             }
    //                         });
    //                         // $(this).find('.treejs-switcher').first().parent().first().addClass('treejs-node__close')
    //                     });
    //                 });
    //             },
    //             error: function (jqXHR, textStatus, errorThrown) {
    //             }
    //         });
    //     });
    // }

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
                        let object = globalElements.filter(function (element) {
                            return element.filterElement.dates === treeId;
                        });
                        if (object.length) {
                            object = object[0];
                            object.filterTree.datesTreeObject = this;
                            if (object.filterTree.dates) {
                                object.filterTree.datesTreeObject.values = object.filterTree.dates;
                            }
                        }

                        //datesFilterList.push([treeId, this]);
                        // console.log(datesFilterList);
                    },
                    onChange: function () {
                        dates = this.values;
                    }
                });
            });
            // if (datesFilterListExist && datesFilterValuesExist) {
            //     assignFilter(datesFilterList, datesFilterValues);
            // }
            $('.treejs-node .treejs-nodes .treejs-switcher').click();
            $('.refresh-form button').removeClass('d-none');
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });


    const filterData = () => {
        // console.log(agence_code, agent_name);
        return {
            dates,
            // resultatAppel,
            // gpmtAppelPre,
            // codeTypeIntervention,
            // codeIntervention,
            // codeRdvIntervention,
            // codeRdvInterventionConfirm,
            // groupement,
            // nomRegion,
            agent_name,
            agence_code
        };
    };

    //<editor-fold desc="REGIONS / FOLDERS">
    let statsRegions = {
        columnName: 'Nom_Region',
        rowName: 'Groupement',
        element_dt: undefined,
        element: $('#statsRegions'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-0', rows: '#stats-groupement-filter'},
        routeCol: 'regions/columns/Groupement',
        routeData: 'regions/Groupement',
        objChart: {
            element_chart: undefined,
            element_id: 'statsRegionsChart',
            data: undefined,
            chartTitle: 'Résultats Appels'
        },
        objDetail: {
            columnName: 'Nom_Region',
            rowName: 'Resultat_Appel',
            element_dt: undefined,
            element: undefined,
            columns: undefined,
            routeCol: 'regions/details/groupement/columns',
            routeData: 'regions/details/groupement',
            objChart: {
                element_chart: undefined,
                element_id: 'statsRegionsChart',
                data: undefined,
                chartTitle: 'Résultats Appels'
            },
        }
        // children: {
        //     routeData: 'regions/details/groupement',

        // items: [{
        //     element: undefined,
        //     columns: undefined,
        // }]
    };
    if (elementExists(statsRegions)) {
        getColumns(statsRegions, filterData(), {
            removeTotal: false,
            refreshMode: false,
            details: true,
            removeTotalColumn: false,
            pagination: false
        });
        $('#refreshRegions').on('click', function () {
            getColumns(statsRegions, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: true,
                removeTotalColumn: false,
                pagination: false
            });
        });
    }
    let statsFolders = {
        columnName: 'Nom_Region',
        rowName: 'Groupement',
        element_dt: undefined,
        element: $('#statsFolders'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-1', rows: '#stats-regions-filter'},
        routeCol: 'regions/details/groupement/columns?key_groupement=Appels-clture',
        routeData: 'regions/details/groupement?key_groupement=Appels-clture',
        objChart: {
            element_chart: undefined,
            element_id: 'statsFoldersChart',
            data: undefined,
            chartTitle: 'Répartition des dossiers traités par périmètre'
        }
    };
    if (elementExists(statsFolders)) {
        getColumns(statsFolders, filterData(), {
            removeTotalColumn: true,
            removeTotal: true,
            refreshMode: false,
            details: false,
            pagination: false
        });
        $('#refreshFolders').on('click', function () {
            getColumns(statsFolders, filterData(), {
                removeTotal: false,
                refreshMode: true,
                removeTotalColumn: true,
                details: false,
                pagination: false
            });
        });
    }
    //</editor-fold>

    //<editor-fold desc="CALLS STATS AGENCIES / WEEKS">
    let callsStatesAgencies = {
        columnName: 'Nom_Region',
        rowName: 'Gpmt_Appel_Pre',
        element_dt: undefined,
        element: $('#callsStatesAgencies'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-2', rows: '#stats-call-regions-filter'},
        routeCol: 'regionsCallState/columns/Nom_Region',
        routeData: 'regionsCallState/Nom_Region',
        objChart: {
            element_chart: undefined,
            element_id: 'callsStatesAgenciesChart',
            data: undefined,
            chartTitle: 'Résultats Appels Préalables par agence'
        }
    };
    if (elementExists(callsStatesAgencies)) {
        getColumns(callsStatesAgencies, filterData(), {
            removeTotalColumn: true,
            removeTotal: true,
            refreshMode: false,
            details: false,
            pagination: false
        });
        $('#refreshCallStatesAgencies').on('click', function () {
            getColumns(callsStatesAgencies, filterData(), {
                removeTotal: true,
                refreshMode: true,
                removeTotalColumn: true,
                details: false,
                pagination: false
            });
        });
    }


    let callsStatesWeeks = {
        columnName: 'Date_Heure_Note_Semaine',
        rowName: 'Gpmt_Appel_Pre',
        element_dt: undefined,
        element: $('#callsStatesWeeks'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-3', rows: '#stats-weeks-regions-filter'},
        routeCol: 'regionsCallState/columns/Date_Heure_Note_Semaine',
        routeData: 'regionsCallState/Date_Heure_Note_Semaine',
        objChart: {
            element_chart: undefined,
            element_id: 'callsStatesWeeksChart',
            data: undefined,
            chartTitle: 'Résultats Appels Préalables par semaine'
        }
    };
    if (elementExists(callsStatesWeeks)) {
        getColumns(callsStatesWeeks, filterData(), {
            removeTotalColumn: true,
            removeTotal: true,
            refreshMode: false,
            details: false,
            pagination: false
        });
        $('#refreshCallStatesWeeks').on('click', function () {
            getColumns(callsStatesWeeks, filterData(), {
                removeTotal: true,
                refreshMode: true,
                removeTotalColumn: true,
                details: false,
                pagination: false
            });
        });
    }
    //</editor-fold>

    //<editor-fold desc="CALL STATS Joignables / Injoignable">
    let statscallsPos = {
        columnName: 'Code_Intervention',
        rowName: 'Nom_Region',
        element_dt: undefined,
        element: $('#statsCallsPos'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-4', rows: '#code-rdv-intervention-confirm-filter'},
        routeCol: 'clientsByCallState/columns/Joignable',
        routeData: 'clientsByCallState/Joignable',
        objChart: {
            element_chart: undefined,
            element_id: 'statsCallsPosChart',
            data: undefined,
            chartTitle: 'Code Interventions liés aux RDV Confirmés (Clients Joignables)'
        }
    };
    if (elementExists(statscallsPos)) {
        getColumns(statscallsPos, filterData(), {
            removeTotal: false,
            refreshMode: false,
            removeTotalColumn: true,
            details: false,
            pagination: false
        });
        $('#refreshCallResultPos').on('click', function () {
            getColumns(statscallsPos, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: true,
                pagination: false
            });
        });
    }

    let statscallsNeg = {
        columnName: 'Code_Intervention',
        rowName: 'Nom_Region',
        element_dt: undefined,
        element: $('#statsCallsNeg'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-5', rows: '#code-rdv-intervention-filter'},
        routeCol: 'clientsByCallState/columns/Injoignable',
        routeData: 'clientsByCallState/Injoignable',
        objChart: {
            element_chart: undefined,
            element_id: 'statscallsNegChart',
            data: undefined,
            chartTitle: 'Code Interventions liés aux RDV Confirmés (Clients Injoignables)'
        }
    };
    if (elementExists(statscallsNeg)) {
        getColumns(statscallsNeg, filterData(), {
            removeTotal: false,
            refreshMode: false,
            removeTotalColumn: true,
            details: false,
            pagination: false
        });
        $('#refreshCallResultNeg').on('click', function () {
            getColumns(statscallsNeg, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: true,
                pagination: false
            });
        });
    }
    //</editor-fold>

    //<editor-fold desc="FOLDERS CODE / TYPE">
    let statsFoldersByType = {
        columnName: 'Nom_Region',
        rowName: 'Code_Type_Intervention',
        element_dt: undefined,
        element: $('#statsFoldersByType'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-6', rows: '#code-type-intervention-filter'},
        routeCol: 'nonValidatedFolders/columns/Code_Type_Intervention',
        routeData: 'nonValidatedFolders/Code_Type_Intervention',
        objChart: {
            element_chart: undefined,
            element_id: 'statsFoldersByTypeChart',
            data: undefined,
            chartTitle: 'Répartition des dossiers non validés par Code Type intervention'
        }
    };
    if (elementExists(statsFoldersByType)) {
        getColumns(statsFoldersByType, filterData());
        $('#refreshFoldersByType').on('click', function () {
            getColumns(statsFoldersByType, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: false,
                pagination: false
            });
        });
    }

    let statsFoldersByCode = {
        columnName: 'Nom_Region',
        rowName: 'Code_Intervention',
        element_dt: undefined,
        element: $('#statsFoldersByCode'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-7', rows: '#code-intervention-filter'},
        routeCol: 'nonValidatedFolders/columns/Code_Intervention',
        routeData: 'nonValidatedFolders/Code_Intervention',
        objChart: {
            element_chart: undefined,
            element_id: 'statsFoldersByCodeChart',
            data: undefined,
            chartTitle: 'Répartition des dossiers non validés par code intervention'
        }
    };
    if (elementExists(statsFoldersByCode)) {
        getColumns(statsFoldersByCode, filterData());
        $('#refreshFoldersByCode').on('click', function () {
            getColumns(statsFoldersByCode, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: false,
                pagination: false
            });
        });
    }
    //</editor-fold>

    //<editor-fold desc="CALL PERIMETERS">
    let statsPerimeters = {
        columnName: 'Groupement',
        rowName: 'Nom_Region',
        element_dt: undefined,
        element: $('#statsPerimeters'),
        columns: undefined,
        data: undefined,
        filterTree: {dates: undefined, rows: undefined, datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-8', rows: '#nom-region-filter'},
        routeCol: 'clientsByPerimeter/columns',
        routeData: 'clientsByPerimeter',
        objChart: {
            element_chart: undefined,
            element_id: 'statsPerimetersChart',
            data: undefined,
            chartTitle: 'Production Globale CAM'
        }
    };
    if (elementExists(statsPerimeters)) {
        getColumns(statsPerimeters, filterData(), {
            removeTotalColumn: true,
            removeTotal: true,
            refreshMode: false,
            details: false,
            pagination: false
        });
        $('#refreshPerimeters').on('click', function () {
            getColumns(statsPerimeters, filterData(), {
                removeTotal: true,
                refreshMode: true,
                removeTotalColumn: true,
                details: false,
                pagination: false
            });
        });
    }
    //</editor-fold>

    let globalElements = [statsRegions, statsFolders, callsStatesAgencies, callsStatesWeeks, statscallsPos, statscallsNeg, statsFoldersByType, statsFoldersByCode, statsPerimeters];

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
        data = {...data, 'rowFilter': object.filterTree.rows}; //object.filterTree.rows
        $.ajax({
            url: APP_URL + '/' + object.routeCol,
            method: 'GET',
            data: data,
            success: function (response) {
                if (response.filter) {
                    object.filterTree.dates = response.filter.date_filter;
                    if (object.filterTree.datesTreeObject) {
                        object.filterTree.datesTreeObject.values = object.filterTree.dates;
                    }
                }
                let rowsFilterData = response.rows.map(function (d, index) {
                    return {
                        id: d,
                        text: d
                    };
                });
                new Tree(object.filterElement.rows, {
                    data: [{id: '-1', text: response.rowsFilterHeader, children: rowsFilterData}],
                    closeDepth: 1,
                    loaded: function () {
                        if (response.filter && response.filter.rows_filter) {
                            this.values = object.filterTree.rows = response.filter.rows_filter;
                            console.log(this.values);
                        }
                    },
                    onChange: function () {
                        object.filterTree.rows = this.values;
                        console.log(this.values);
                    }
                });

                let datesFilterValuesExist = true;
                let filters = response.filter;
                if (filters !== null && filters !== undefined) {
                    object.filterTree.dates = filters.date_filter;
                    datesFilterValues.push([object.filterElement.dates, filters.date_filter]);
                    // if (datesFilterList !== null && datesFilterList !== undefined && datesFilterList.length > 0) {
                    //     datesFilterList[object.treeElement].values = datesFilterValues[object.treeElement];
                    // }
                    if (datesFilterListExist && datesFilterValuesExist) {
                        assignFilter(datesFilterList, datesFilterValues);
                    }
                }
                // console.log(filters.date_filter);

                let reformattedColumns = [...response.columns].map(function (column) {

                    return {
                        ...column,
                        render: function (data, type, full, meta) {
                            let newData = data;
                            if (newData !== null) {
                                newData = newData.toString();
                                if (newData.indexOf('/') !== -1) {
                                    newData = newData.split('/').join('<br/>');
                                    // newData = newData[0] + '<br/>' + newData[1];
                                }
                            } else {
                                newData = '';
                            }

                            let classHasTotalCol = (params.removeTotalColumn) ? 'hasTotal' : '';

                            let rowClass = full.isTotal ? '' : 'pointer detail-data';
                            return '<span class="' + rowClass + ' ' + classHasTotalCol + '">' + newData + '<\span>';
                        }
                    };
                });

                // object.columns = [...response.columns];
                object.columns = [...reformattedColumns];
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
                        // CELL CLICK
                        let tableId = '#' + object.element.attr('id');
                        $(tableId + ' tbody').on('click', 'td', function () {
                            let agent_name = $('#agent_name').val();
                            let agence_name = $('#agence_name').val();
                            let col = object.element_dt.cell(this).index().column + 1;
                            let row = object.element_dt.cell(this).index().row + 1;
                            let colText = $(tableId + " thead th:nth-child(" + col + ")").text();
                            let rowText = $(tableId + " tbody tr:nth-child(" + row + ") td:" + (params.details ? "nth-child(2)" : "first-child")).text();
                            if (object.columnName === 'Date_Heure_Note_Semaine') {
                                colText = colText.split('_')[0];
                            }
                            let lastRowIndex = object.element_dt.rows().count();
                            let lastColumnIndex = object.element_dt.columns().count();

                            if (((params.details && col > 2) || (!params.details && col > 1))
                                && ((params.removeTotal && row < lastRowIndex) || (!params.removeTotal && row <= lastRowIndex))
                                && ((params.removeTotalColumn && col < lastColumnIndex) || (!params.removeTotalColumn && col <= lastColumnIndex))) {
                                window.location = APP_URL + '/all-stats?' +
                                    'row=' + object.rowName +
                                    '&rowValue=' + rowText +
                                    '&col=' + object.columnName +
                                    '&colValue=' + colText +
                                    '&agent=' + (agent_name === undefined || agent_name === null ? '' : agent_name) +
                                    '&agence=' + (agence_name === undefined || agence_name === null ? '' : agence_name) +
                                    '&dates=' + (dates === undefined || dates === null ? '' : dates) +
                                    (object.routeData.includes('nonValidatedFolders') ? '&Resultat_Appel=Appels clôture - CRI non conforme' : '');
                            }
                            // console.log(colText + ' --- ' + rowText)
                        });
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
            columns: object.data.length ? object.columns : [{title: 'Résultats'}],
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

    function elementExists(object) {
        if (object !== null && object !== undefined) {
            if (object.element !== null && object.element !== undefined) {
                return object.element.length;
            } else {
                return object.length;
            }
        }
        return false;
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
        getColumns(objectChild, data, {
            removeTotal: false,
            removeTotalColumn: false,
            details: false,
            refreshMode: false,
            pagination: false
        });
        // InitDataTable(objectChild, data, {removeTotal: false, removeTotalColumn: false, details: false});
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
            window.location = APP_URL + '/' + url;
        }
    });

    $("#refreshAll").on('click', function () {
        getColumns(statsRegions, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: true,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statsFolders, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(callsStatesAgencies, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(callsStatesWeeks, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statscallsPos, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statscallsNeg, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statsFoldersByType, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statsFoldersByCode, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
        getColumns(statsPerimeters, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: false,
            pagination: false
        });
    });
    //</editor-fold>
    $("#printElement").on("click",function () {
        let statsRegions = document.getElementById('statsRegions');
        let statsRegionsChart = document.getElementById('statsRegionsChart');
        let statsFolders = document.getElementById('statsFolders');
        let statsFoldersChart = document.getElementById('statsFoldersChart');
        let callsStatesAgencies = document.getElementById('callsStatesAgencies');
        let callsStatesAgenciesChart = document.getElementById('callsStatesAgenciesChart');
        let callsStatesWeeks = document.getElementById('callsStatesWeeks');
        let callsStatesWeeksChart = document.getElementById('callsStatesWeeksChart');
        let statsCallsPosChart = document.getElementById('statsCallsPosChart');
        let statscallsNegChart = document.getElementById('statscallsNegChart');
        let statsFoldersByTypeChart = document.getElementById('statsFoldersByTypeChart');
        let statsFoldersByCodeChart = document.getElementById('statsFoldersByCodeChart');
        let statsPerimetersChart = document.getElementById('statsPerimetersChart');
        //creates image
        let statsRegionsChartImg = statsRegionsChart.toDataURL("image/jpeg", 1.0);
        let statsFoldersChartImg = statsFoldersChart.toDataURL("image2/jpeg", 1.0);
        let callsStatesAgenciesChartImg = callsStatesAgenciesChart.toDataURL("image3/jpeg", 1.0);
        let callsStatesWeeksChartImg = callsStatesWeeksChart.toDataURL("image4/jpeg", 1.0);
        let statsCallsPosChartImg = statsCallsPosChart.toDataURL("image5/jpeg", 1.0);
        let statscallsNegChartImg = statscallsNegChart.toDataURL("image6/jpeg", 1.0);
        let statsFoldersByTypeChartImg = statsFoldersByTypeChart.toDataURL("image7/jpeg", 1.0);
        let statsFoldersByCodeChartImg = statsFoldersByCodeChart.toDataURL("image8/jpeg", 1.0);
        let statsPerimetersChartImg = statsPerimetersChart.toDataURL("image9/jpeg", 1.0);
        //creates PDF from img
        let doc = new jsPDF('landscape');
        doc. text( 10 , 10, 'Résultats Appels' );
        doc.autoTable({ html: '#statsRegions' });
        doc.addImage(statsRegionsChartImg, 'JPEG', 10 , (statsRegions.offsetHeight * 0.3) + (20 * 0.3)  , 280, 150 );
        doc.addPage();
        doc. text( 10 , 10, 'Répartition des dossiers traités par périmètre' );
        doc.autoTable({ html: '#statsFolders' });
        doc.addImage(statsFoldersChartImg, 'JPEG', 10 , (statsFolders.offsetHeight * 0.3)  , 280, 150 );
        doc.addPage();
        doc. text( 10 , 10, 'Résultats Appels Préalables par agence' );
        doc.autoTable({ html: '#callsStatesAgencies' });
        doc.addImage(callsStatesAgenciesChartImg, 'JPEG', 10 , (callsStatesAgencies.offsetHeight * 0.2) , 280, 100 );
        doc.addPage();
        doc. text( 10 , 10, 'Résultats Appels Préalables par semaine' );
        doc.autoTable({ html: '#callsStatesWeeks' });
        doc.addImage(callsStatesWeeksChartImg, 'JPEG', 10 , (callsStatesWeeks.offsetHeight * 0.2) , 280, 100 );
        doc.addPage();
        doc. text( 10 , 10, 'Code Interventions liés aux RDV Confirmés (Clients Joignables)' );
        doc.autoTable({ html: '#statsCallsPos' });
        doc.addPage();
        doc. text( 10 , 10, 'la charte de Code Interventions liés aux RDV Confirmés (Clients Joignables)' );
        doc.addImage(statsCallsPosChartImg, 'JPEG', 10 , 30 , 280, 100 );
        doc.addPage();
        doc. text( 10 , 10, 'Code Interventions liés aux RDV Non Confirmés (Clients Injoignables)' );
        doc.autoTable({ html: '#statsCallsNeg' });
        doc.addPage();
        doc. text( 10 , 10, 'la charte de Code Interventions liés aux RDV Non Confirmés (Clients Injoignables)' );
        doc.addImage(statscallsNegChartImg, 'JPEG', 10 , 30 , 280, 100 );
        doc.addPage();
        doc. text( 10 , 10, 'Répartition des dossiers non validés par Code Type intervention' );
        doc.autoTable({ html: '#statsFoldersByType', pageBreak : 'auto' });
        doc.addPage();
        doc. text( 10 , 10, 'la charte de Répartition des dossiers non validés par Code Type intervention' );
        doc.addImage(statsFoldersByTypeChartImg, 'JPEG', 10 , 30 , 280, 100 );
        doc.addPage();
        doc. text( 10 , 10, 'Répartition des dossiers non validés par code intervention' );
        doc.autoTable({ html: '#statsFoldersByCode', pageBreak : 'auto' });
        doc.addPage();
        doc. text( 10 , 10, 'la charte de Répartition des dossiers non validés par code intervention' );
        doc.addImage(statsFoldersByCodeChartImg, 'JPEG', 10 , 30 , 280, 100 );
        doc.addPage();
        doc. text( 10 , 10, 'Production Globale CAM' );
        doc.autoTable({ html: '#statsPerimeters', pageBreak : 'auto' });
        doc.addPage();
        doc. text( 10 , 10, 'la charte deProduction Globale CAM' );
        doc.addImage(statsPerimetersChartImg, 'JPEG', 10 , 30 , 280, 100 );
        doc.save('canvas.pdf');
    })
});
