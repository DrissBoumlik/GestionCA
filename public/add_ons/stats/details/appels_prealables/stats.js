$(function () {
    $('#page-container').addClass('sidebar-mini');

    let dates = undefined;
    let agent_name = '';
    let agence_code = '';
    let ajaxRequests = 0;


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

    const filterData = () => {
        // console.log(agence_code, agent_name);
        return {
            // dates,
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

    let userObject = {
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-01', rows: ''},
    };

    //<editor-fold desc="SELECTED FILTER">
    let statsCallsPrealable = {
        columnName: 'Nom_Region',
        rowName: 'Resultat_Appel',
        element_dt: undefined,
        element: 'statsCallsPrealable',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-0', rows: '#stats-callResult-filter'},
        filterQuery: {
            queryJoin: ' and Resultat_Appel not like "=%" and Groupement not like "Non Renseigné" and Groupement not like "Appels post"',
            subGroupBy: ' GROUP BY Id_Externe, Nom_Region, Groupement, Key_Groupement, Resultat_Appel) groupedst ',
            queryGroupBy: 'group by st.Id_Externe, Nom_Region, Groupement, Key_Groupement, Resultat_Appel'
        },
        routeCol: 'regions/details/groupement/columns?key_groupement=appels-pralables',
        routeData: 'regions/details/groupement?key_groupement=appels-pralables',
        objChart: {
            element_chart: undefined,
            element_id: 'statsCallsPrealableChart',
            data: undefined,
            chartTitle: 'Type Résultats Appels'
        }
    };
    if (elementExists(statsCallsPrealable)) {
        getColumns(statsCallsPrealable, filterData(), {
            removeTotal: false,
            refreshMode: false,
            removeTotalColumn: false,
            details: false,
            pagination: false
        });
        $('#refreshCallsPrealable').on('click', function () {
            toggleLoader($('#refreshAll').parents('.col-12'));
            getColumns(statsCallsPrealable, filterData(), {
                removeTotal: false,
                refreshMode: true,
                removeTotalColumn: false,
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
        element: 'callsStatesAgencies',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-2', rows: '#stats-call-regions-filter'},
        filterQuery: {
            queryJoin: ' and Groupement not like "Non Renseigné" and Groupement like "Appels préalables" and Gpmt_Appel_Pre not like "Hors Périmètre"',
            subGroupBy: ' GROUP BY Id_Externe, Nom_region , Gpmt_Appel_Pre) groupedst',
            queryGroupBy: 'group by st.Id_Externe, Nom_region , Gpmt_Appel_Pre'
        },
        routeCol: 'regionsCallState/columns/Nom_Region?key_groupement=appels-pralables',
        routeData: 'regionsCallState/Nom_Region?key_groupement=appels-pralables',
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
            toggleLoader($('#refreshAll').parents('.col-12'));
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
        element: 'callsStatesWeeks',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-3', rows: '#stats-weeks-regions-filter'},
        filterQuery: {
            queryJoin: ' and Groupement not like "Non Renseigné" and Groupement like "Appels préalables" and Gpmt_Appel_Pre not like "Hors Périmètre"',
            subGroupBy: ' GROUP BY Id_Externe, Date_Heure_Note_Semaine , Gpmt_Appel_Pre, Date_Heure_Note_Annee) groupedst ',
            queryGroupBy: 'group by st.Id_Externe, Date_Heure_Note_Semaine , Gpmt_Appel_Pre, Date_Heure_Note_Annee'
        },
        routeCol: 'regionsCallState/columns/Date_Heure_Note_Semaine?key_groupement=appels-pralables',
        routeData: 'regionsCallState/Date_Heure_Note_Semaine?key_groupement=appels-pralables',
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
            toggleLoader($('#refreshAll').parents('.col-12'));
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
        element: 'statsCallsPos',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-4', rows: '#code-rdv-intervention-confirm-filter'},
        filterQuery: {
            queryJoin: ' and Resultat_Appel in ("Appels préalables - RDV confirmé","Appels préalables - RDV confirmé Client non informé","Appels préalables - RDV repris et confirmé")' +
                ' and Gpmt_Appel_Pre = "Joignable" ',
            subGroupBy: ' GROUP BY Id_Externe, Code_Intervention, Nom_Region) groupedst ',
            queryGroupBy: 'group by st.Id_Externe, Code_Intervention, Nom_Region'
        },
        routeCol: 'clientsByCallState/columns/Joignable?key_groupement=appels-pralables',
        routeData: 'clientsByCallState/Joignable?key_groupement=appels-pralables',
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
            toggleLoader($('#refreshAll').parents('.col-12'));
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
        element: 'statsCallsNeg',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-5', rows: '#code-rdv-intervention-filter'},
        filterQuery: {
            queryJoin: ' and Resultat_Appel in ("Appels préalables - Annulation RDV client non informé", "Appels préalables - Client sauvé","Appels préalables - Client Souhaite être rappelé plus tard","Appels préalables - Injoignable / Absence de répondeur","Appels préalables - Injoignable 2ème Tentative","Appels préalables - Injoignable 3ème Tentative","Appels préalables - Injoignable avec Répondeur","Appels préalables - Numéro erroné","Appels préalables - Numéro Inaccessible","Appels préalables - Numéro non attribué","Appels préalables - Numéro non Renseigné","Appels préalables - RDV annulé le client ne souhaite plus d’intervention","Appels préalables - RDV annulé Rétractation/Résiliation","Appels préalables - RDV planifié mais non confirmé","Appels préalables - RDV repris Mais non confirmé" ) ' +
                ' and Gpmt_Appel_Pre = "Injoignable" ',
            subGroupBy: ' GROUP BY Id_Externe, Code_Intervention, Nom_Region) groupedst ',
            queryGroupBy: 'group by st.Id_Externe, Code_Intervention, Nom_Region'
        },
        routeCol: 'clientsByCallState/columns/Injoignable?key_groupement=appels-pralables',
        routeData: 'clientsByCallState/Injoignable?key_groupement=appels-pralables',
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
            toggleLoader($('#refreshAll').parents('.col-12'));
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

    //<editor-fold desc="CALL STATS Joignables + Injoignable = Appel Prealables">
    let CallResultPrealable = {
        columnName: 'Code_Intervention',
        rowName: 'Nom_Region',
        element_dt: undefined,
        element: 'CallResultPrealable',
        columns: undefined,
        data: undefined,
        filterTree: {dates: [], rows: [], datesTreeObject: undefined},
        filterElement: {dates: '#tree-view-6', rows: '#CallResultPrealable-filter'},
        filterQuery: {
            queryJoin: ' and Gpmt_Appel_Pre in ("Joignable", "Injoignable")' +
                ' and Resultat_Appel in ("Appels préalables - RDV confirmé",' +
                '"Appels préalables - RDV confirmé Client non informé",' +
                '"Appels préalables - RDV repris et confirmé",' +
                '"Appels préalables - RDV confirmé",' +
                '"Appels préalables - RDV confirmé Client non informé",' +
                '"Appels préalables - RDV repris et confirmé",' +
                '"Appels préalables - Annulation RDV client non informé",' +
                '"Appels préalables - Client sauvé",' +
                '"Appels préalables - Client Souhaite être rappelé plus tard",' +
                '"Appels préalables - Injoignable / Absence de répondeur",' +
                '"Appels préalables - Injoignable 2ème Tentative",' +
                '"Appels préalables - Injoignable 3ème Tentative",' +
                '"Appels préalables - Injoignable avec Répondeur",' +
                '"Appels préalables - Numéro erroné",' +
                '"Appels préalables - Numéro Inaccessible",' +
                '"Appels préalables - Numéro non attribué",' +
                '"Appels préalables - Numéro non Renseigné",' +
                '"Appels préalables - RDV annulé le client ne souhaite plus d’intervention",' +
                '"Appels préalables - RDV annulé Rétractation/Résiliation",' +
                '"Appels préalables - RDV planifié mais non confirmé",' +
                '"Appels préalables - RDV repris Mais non confirmé") '
            ,
            subGroupBy: ' GROUP BY Id_Externe, Code_Intervention, Nom_Region) groupedst ',
            queryGroupBy: 'group by st.Id_Externe, Code_Intervention, Nom_Region'
        },
        routeCol: 'clientsWithCallStates/columns?key_groupement=appels-pralables',
        routeData: 'clientsWithCallStates?key_groupement=appels-pralables',
        objChart: {
            element_chart: undefined,
            element_id: 'CallResultPrealableChart',
            data: undefined,
            chartTitle: 'Global Résultat Appels Préalables'
        }
    };
    if (elementExists(CallResultPrealable)) {
        getColumns(CallResultPrealable, filterData(), {
            removeTotal: false,
            refreshMode: false,
            removeTotalColumn: true,
            details: false,
            pagination: false
        });
        $('#refreshCallResultPrealable').on('click', function () {
            toggleLoader($('#refreshAll').parents('.col-12'));
            getColumns(CallResultPrealable, filterData(), {
                removeTotal: false,
                refreshMode: true,
                details: false,
                removeTotalColumn: true,
                pagination: false
            });
        });
    }
    //</editor-fold>

    let globalElements = [userObject, statsCallsPrealable, callsStatesAgencies, callsStatesWeeks, statscallsPos, statscallsNeg, CallResultPrealable];

    let detailClick = false;

    getDatesFilter(globalElements);

    userFilter(userObject);

    //<editor-fold desc="FUNCTIONS">
    function getColumns(object, data = null, params = {
        removeTotal: true,
        refreshMode: false,
        details: false,
        removeTotalColumn: false,
        pagination: false
    }) {
        ajaxRequests++;
        if (params.refreshMode) {
            data = {...data, refreshMode: true};
        }
        if (object.filterTree && object.filterTree.rows) {
            data = {...data, 'rowFilter': object.filterTree.rows};
        }
        if (object.filterTree) {
            data = {...data, 'dates': object.filterTree.dates};
        }

        toggleLoader($('#' + object.element).parents('.col-12'));

        $.ajax({
            url: APP_URL + '/' + object.routeCol,
            method: 'GET',
            data: data,
            success: function (response) {
                if (object.filterElement) {
                    object.filterTree.dates = response.filter ? response.filter.date_filter : [];
                    if (object.filterTree.datesTreeObject && object.filterTree.dates) {
                        object.filterTree.datesTreeObject.values = object.filterTree.dates;
                        if (object.objDetail) {
                            object.objDetail.filterTree.dates = object.filterTree.dates;
                        }
                    }
                    if (response.rows && response.rows.length) {
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
                                    // console.log(this.values);
                                }
                            },
                            onChange: function () {
                                object.filterTree.rows = this.values;
                                // console.log(this.values);
                            }
                        });
                    }

                    let filters = response.filter;
                    if (filters !== null && filters !== undefined) {
                        object.filterTree.dates = filters.date_filter;
                    }
                }

                if (response.columns.length) {
                    let reformattedColumns = [...response.columns].map(function (column) {

                        return {
                            ...column,
                            render: function (data, type, full, meta) {
                                let newData = data;
                                if (newData !== null && newData !== undefined) {
                                    newData = newData.toString();
                                    if (newData.indexOf('|') !== -1) {
                                        newData = newData.split('|').join('<br/>');
                                        // newData = newData[0] + '<br/>' + newData[1];
                                    }
                                } else {
                                    newData = '';
                                }
                                let classHasTotalCol = params.removeTotalColumn ? 'hasTotal' : '';

                                let rowClass = full.isTotal ? '' : 'pointer detail-data';
                                return '<span class="' + rowClass + ' ' + classHasTotalCol + '">' + newData + '<\span>';
                            }
                        };
                    });

                    // object.columns = [...response.columns];
                    object.columns = [...reformattedColumns];
                } else {
                    object.columns = [{title: 'Résultats'}];
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
                            $('#' + object.element).on('click', 'td.details-control', function () {
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
                                    createChild(row, object, data); // class is for background colour
                                    tr.addClass('shown');
                                }
                            });
                        }
                        // CELL CLICK
                        let tableId = '#' + object.element;
                        $(tableId + ' tbody').on('click', 'td', function () {
                            let agent_name = $('#agent_name').val();
                            let agence_name = $('#agence_name').val();
                            let col = object.element_dt.cell(this).index().column + 1;
                            let row = object.element_dt.cell(this).index().row + 1;
                            let colText = $(tableId + " > thead > tr > th:nth-child(" + col + ")").text();
                            let rowText = $(tableId + " > tbody > tr:nth-child(" + row + ") td:" + (params.details ? "nth-child(2)" : "first-child")).text();
                            if (object.columnName === 'Date_Heure_Note_Semaine') {
                                colText = colText.split('_')[0];
                            }
                            let lastRowIndex = object.element_dt.rows().count();
                            let lastColumnIndex = object.element_dt.columns().count();

                            if (((params.details && col > 2) || (!params.details && col > 1))
                                && ((params.removeTotal && row < lastRowIndex) || (!params.removeTotal && row <= lastRowIndex))
                                && ((params.removeTotalColumn && col < lastColumnIndex) || (!params.removeTotalColumn && col <= lastColumnIndex))) {
                                let dates = object.filterTree.dates;
                                window.location = APP_URL + '/all-stats?' +
                                    'row=' + object.rowName +
                                    '&rowValue=' + rowText +
                                    '&col=' + object.columnName +
                                    '&colValue=' + colText +
                                    '&agent=' + (agent_name === undefined || agent_name === null ? '' : agent_name) +
                                    '&agence=' + (agence_name === undefined || agence_name === null ? '' : agence_name) +
                                    '&dates=' + (dates === undefined || dates === null ? '' : dates) +
                                    '&queryJoin=' + (object.filterQuery.queryJoin === undefined || object.filterQuery.queryJoin === null ? '' : object.filterQuery.queryJoin) +
                                    '&subGroupBy=' + (object.filterQuery.subGroupBy === undefined || object.filterQuery.subGroupBy === null ? '' : object.filterQuery.subGroupBy) +
                                    '&queryGroupBy=' + (object.filterQuery.queryGroupBy === undefined || object.filterQuery.queryGroupBy === null ? '' : object.filterQuery.queryGroupBy) +
                                    (object.routeData.includes('nonValidatedFolders') ? '&Resultat_Appel=Appels clôture - CRI non conforme' : '');
                            }
                            // console.log(colText + ' --- ' + rowText)
                        });
                    } catch (error) {
                        console.log(error);
                        // swal(
                        //     'Error!',
                        //     "Aucun résultat n'a été trouvé",
                        //     'error'
                        // );
                        Swal.fire({
                            // position: 'top-end',
                            type: 'error',
                            title: "Vous devez actualiser la page",
                            showConfirmButton: true,
                            customClass: {
                                confirmButton: 'btn btn-success m-1',
                            },
                            confirmButtonText: 'Ok',
                        });
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                console.log(APP_URL + '/' + object.routeCol);
            }
        });
    }

    function InitDataTable(object, data = null, params = {
        removeTotal: true,
        removeTotalColumn: false,
        details: false,
        pagination: false
    }) {
        let table = $('#' + object.element);
        if ($.fn.DataTable.isDataTable(table)) {
            table.off('click', 'td.details-control');
            table.DataTable().destroy();
            let tableID = object.element;
            let tableParent = table.parents('.card-body');
            table.remove();
            $('#' + tableID + '_wrapper').remove();
            let newTable = object.columns.reduce(function (accumulator, current) {
                return accumulator + '<th>' + current.title + '</th>';
            }, params.details ? '<th></th>' : '');

            newTable = '<table id="' + tableID + '" class="table table-bordered table-striped table-valign-middle capitalize">' +
                '<thead>' + newTable + '</thead><tbody></tbody></table>';
            tableParent.append(newTable);
            table = $('#' + object.element);
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

        return table.DataTable({
            destroy: true,
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
                ajaxRequests--;
                object.data = [...response.data];
                if (ajaxRequests === 0) {
                    toggleLoader($('#refreshAll').parents('.col-12'), true);
                }
                if (object.objChart !== null && object.objChart !== undefined) {
                    try {
                        InitChart(object.objChart, object.columns, response.data, {
                            removeTotal: params.removeTotal,
                            removeTotalColumn: params.removeTotalColumn,
                            details: params.details
                        });
                        let parent = $('#' + object.element).parents('.col-12');
                        toggleLoader(parent, true);
                    } catch (error) {
                        console.log(error);
                    }
                }
            }
        });
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

        let chartID = objectChart.element_id;
        let chart = $('#' + chartID);

        if (!detailClick) {
            let chartParent = chart.parents('.col-12');
            chartParent.children('.chartjs-size-monitor').remove();
            chart.remove();
            let newChart = '<canvas id="' + chartID + '">';
            chartParent.append(newChart);
        } else {
            detailClick = false;
        }

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
                maintainAspectRatio: false,
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

    //</editor-fold>

    //<editor-fold desc="FUNCTIONS TOOLS">

    function createChild(row, object, data = null) {
        detailClick = true;
        // This is the table we'll convert into a DataTable
        let objectChild = object.objDetail;
        var tableDom = '<table id="' + objectChild.element + '" class="table-details table table-bordered table-valign-middle capitalize"/>';
        var canvasDom = '<div class="col-12"><canvas id="' + objectChild.element + '-Chart"/></div>';
        objectChild.objChart.element_id = objectChild.element + '-Chart';

        let objectChildItem = {...objectChild};
        object.children.push(objectChildItem);

        let createdChild = tableDom;
        // Display it the child row
        row.child($(tableDom)).show();
        let table = $('#' + objectChildItem.element);
        table.after(canvasDom);
        // row.child(objectChild.element).show();
        data = {...data, 'route': object.routeData};
        getColumns(objectChildItem, data, {
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
        let url = APP_URL + '/' + $(this).val();
        if ($('#filterDashboard').prop('selectedIndex') && url !== window.location.href) {
            window.location = url;
        }
    });

    $("#refreshAll").on('click', function () {

        toggleLoader($(this).parents('.col-12'));

        globalElements.map(function (element) {
            element.filterTree.dates = userObject.filterTree.dates;
            element.filterTree.datesTreeObject.values = userObject.filterTree.dates;
        });
        userFilter(true);
        getColumns(statsCallsPrealable, filterData(), {
            removeTotal: false,
            refreshMode: true,
            removeTotalColumn: false,
            details: false,
            pagination: false
        });
        getColumns(callsStatesAgencies, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true,
            pagination: false
        });
        getColumns(callsStatesWeeks, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true,
            pagination: false
        });
        getColumns(statscallsPos, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true,
            pagination: false
        });
        getColumns(statscallsNeg, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true,
            pagination: false
        });

        getColumns(CallResultPrealable, filterData(), {
            removeTotal: false,
            refreshMode: true,
            details: false,
            removeTotalColumn: true,
            pagination: false
        });
    });
    //</editor-fold>
    $("#printElement").on("click", function () {
        toggleLoader($('body'));

        setTimeout(function (){
            let logo = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAkACQAAD/4RCKRXhpZgAATU0AKgAAAAgABAE7AAIAAAAIAAAISodpAAQAAAABAAAIUpydAAEAAAAQAAAQcuocAAcAAAgMAAAAPgAAAAAc6gAAAAgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHNvcyBnc20AAAHqHAAHAAAIDAAACGQAAAAAHOoAAAAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHMAbwBzACAAZwBzAG0AAAD/4QpgaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49J++7vycgaWQ9J1c1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCc/Pg0KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyI+PHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj48cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0idXVpZDpmYWY1YmRkNS1iYTNkLTExZGEtYWQzMS1kMzNkNzUxODJmMWIiIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIvPjxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSJ1dWlkOmZhZjViZGQ1LWJhM2QtMTFkYS1hZDMxLWQzM2Q3NTE4MmYxYiIgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIj48ZGM6Y3JlYXRvcj48cmRmOlNlcSB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPjxyZGY6bGk+c29zIGdzbTwvcmRmOmxpPjwvcmRmOlNlcT4NCgkJCTwvZGM6Y3JlYXRvcj48L3JkZjpEZXNjcmlwdGlvbj48L3JkZjpSREY+PC94OnhtcG1ldGE+DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/eHBhY2tldCBlbmQ9J3cnPz7/2wBDAAcFBQYFBAcGBQYIBwcIChELCgkJChUPEAwRGBUaGRgVGBcbHichGx0lHRcYIi4iJSgpKywrGiAvMy8qMicqKyr/2wBDAQcICAoJChQLCxQqHBgcKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKir/wAARCABWAVkDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwDzGiiiugyCiiigAooooAKKKKACiiigAooooAKmgtnuDiNowc4AZwufzqGigZYZbvTbr5hJbTLyDyprWgmg8QKbe92x6hj9zcAYEh/ut7+9VLC6W5QaffHdE/EUjdYW7YPp6iqEiSWty0b5SWJyD7EGuaUed2ekls/6/FHRGXIrrWL3X9fgye3uLrR9SEiZjnhbDKf1BrY8T20NzDb61ZDEV0MSKP4X/wA/yqlrEhvoLXUSBvmUxykd3Xv+IIqbTJ/tHhvUrGQ5EaieMehB5rKSbcay0a0fpez+56msWkpUej1Xrv8AitDDoooruOEKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKckbyttiRnb0UZNADaKvR6LqkufL067bHpAx/pSSaNqcX+s067X6wMP6UDKVFOeN4m2yIyN6MMGm0CLWm2TajqUNoh2mVsZ9B1J/Ku9PgjSvsnlqsnmY/1u85z646VwemXzabqUN2g3GNskeo6EflXenxtpQtDIHcyY/wBVsOfz6V4eZ/XeeP1e9vLv5nu5WsFyT+s2v59vLzPP760ewv5rWQ/NE5XI7+9SamxkuI5m+9LCjMfU4wT+lR3929/qE11IMNK5bHp6CoCxbGSTgYGa9iKdk5b2PGk1dqOxeEsZ8OtCXHmC6DKvtsOT+eKXSSQ93jp9kkz+VZ9SRTNCJAv/AC0TYfp/kU3FNNCUmmmR0VOljdyLuS1mZfURkioWVkYq4KkdQRjFWQJRRVqHTL64x5FlcSZ6bImOf0oAq0Vof2BrH/QLvP8AwHb/AAqtNZXVuM3FtNEP9uMr/OgZBRRRQIKKKt2ulahe/wDHnY3M/wD1ziZv5CgZUorZ/wCEP8Sbc/2DqOPX7K/+FU7nRtTsxm7066gHrJCy/wAxQBSooooEafh/QL3xNrcOlaWqNczZKh22jgEnJ+grtf8AhRPjL+5Y/wDgT/8AWq18AbD7T49uLth8tpZOQfRmZVH6bq+jazlJplJHytr/AMJ/EnhrRZtU1QWa20ON2yfLHJwMDFcTX0T8f9T+zeDLOwVsPeXQJHqqDJ/UrXztVRbaEwoooqhBRRRQAUUUUAFFFFABRRRQAUUUUAFSW9vLdXEdvbRtLNKwVEQZLE9AKjr2j4CeEYrq4uPEt7Hv+zt5NoGHAbHzN9QDgfU0m7Ia1NLwZ8CLSG3hvPGDtPOwDfYonwiezMOSfXHHua9V0/QdJ0qERabplpaovQRQqv8AStCuD+JvxIi8D2MdvaIs+q3IzFG33Y1/vt/Qd6yu2y9Ed506UEZ618jah8RfF+pXRnm8Q6hET0W2naFR/wABQgV33wz+L9zb3clj4z1PzLMRZiuZgWdWHYkDLZ96bgxcx7RqvhrRdctmg1XTLa5Rh/HGNw+h6g+4r5z+Kfw6XwRqMNxpzvJpl2SI9/LRMP4Se/HQ17b/AMLa8Ff9ByL/AL9v/hXDfFzxp4Y8T+B/s2k6nFc3cd1G6RhSCRyD1HoaI3TB2OK8H6RayaEJ7q2ileV2IMiA8Dj+hrd/sXTP+gfbf9+hTtJt/smkWsAGNkSg/XHNWZXEUTyN0VST+FfA4nE1KleUoyer7n6FhsNTp0IRlFaJdDnvCmj2Ws/Ga0sUtITaQMXlj2DY2xCTkdOuBX0H/wAIp4e/6AWm/wDgIn+FeP8AwCsTe+JNc1uYZZYxErH1dtx/9BH517tX3FOLp04wfRI+DqzVSpKa6tnn/wAQfBFrqmgQ6X4f0awgu7y5RDcJbKvkIMszkgZ7Y/EVc8J/Czw34WgRhaJf3uPmurlQxz/sqeF/Dn3rrb2+tdNs5Lq/uI7eCMZeSRtqj8a4HUfjj4OsJWjilu74rwTawZB+hYrmtdWrIy0PQlhiRdqRoq+gUCvALf4d3vxG8f6zqkjfYdHW8dBMF5k2nbtQfhyelemeG/it4Z8V6lHpunyXUV3MDsjngxnAJPIJHAHrXX2NlDp9jFaWqBIol2qB/OjVBuc9oHw58L+HIlFjpUMkw63FwvmyE+uT0/DFdMkaRriNFQeijFQajqFtpOm3F/fyCK3t4zJI57AV84eLPjP4i1u8kTR7h9KsQxEawnEjD1Z+oP0xQk5Boj6YqOa2guFK3EMcqnqHUEH86+UtF+J/i7RbxJl1q7vIw2XhvJTMrj0+bJH4Yr6Ch+Kfg6S3jeTXbWN2UFkJPynHTpQ4tBe5znxD+EGlarptxqPh21Wy1KJC/lQjEc+BnG3oD7ivBvD3h3UfE+sxaZpMHmTyHknhY17sx7AV9Pj4oeDD/wAzBa/mf8KPAPhew0HT7u9sVRm1S5kuRKo6xFiYwPbaQfxpqTS1Fa5leD/g94f8NxpPqEKarqAHMs65jQ/7KHj8Tk13yRxW8eI0SJFHRQFAp9eT/Ejwn4+8Xao8Gmz20GjR8RQC5KGT/afjn2Hap3eo9j0oa5pRm8oanZmT+5565/LNXGRJUw6q6nsRkGvmz/hRXjLOcWOfX7T/APWr174YaD4m8N6Jcaf4nuY50SQG02ymQouOVyR0zjA+tNpLqFxnjH4T+H/FFtJJb20enajglLi3UKGP+2o4P161xvwS8Imz1XxD/bdjG09rItptlQNtIJLYz6/LzXtleJfFPxvrvgvxo8OgTxQRXkCTyhoVYs4+XOT7KKE29Adtz2a2sLSzZjaWsMBYYYxxhc/lU9YPgi81LUfBOmX2tyCS8uoRM5CBRhjleB/skVvVIyC5srW82/a7aGfbnb5kYbGfTNeEftASWdtqej6bZW0MDJC88nlxhchiFXp/uNVXxL8aPFFn4o1K10y4thaQXLxRBoATtViOv4V5/wCJfE+peLNWGo6zIklwIxECibQFGSBj8TWkYtO5LZkUUUVoQFFFFABRRRQAUUUUAFFFFABRRRQAV9W/CWzSy+F+jiPrLG0zH1LOT/LA/CvlKvo34GeKrfUfCY0GWUC908sVRjy8RbII9cE49uKiexUdz1Kvmf45W13F8RpJrlW8maBDAxHBUDBA+hr6YrN1vw9pXiOzFrrVlFdxA5UOOVPqD1FRF2ZTVz4yr0Hwd8H9a8W6N/aYuIdPt3bEPnqxMo7sAO3v35r2ez+D/guyvBcppXmsDkJNKzp/3yTiu2RFjjVI1CIowqqMAD0FU59hKJ4Cf2eNY7a3Y/8Aft65bxN8ObrwdrmlWd5f213JevkJCGBVQRycjp1/KvpLxH4m0vwrpL3+sXCxRjhE/ikb+6o7mvnSDX7vxt8RJ9avshY0PkxZyIk6Ko/Mn6k1z4iq6dCdTsmdGGpKrXhT7tHVgYGBWb4iuPsvh68kBwfL2j6nj+taVcr49ujHpMFup/10mT9AP8cV8NgaftcTCHn/AME+8x1X2WGnPy/4B6n8BtO+yfD97srhry6dgfULhR+oNem1heCdJGh+B9I0/btaK1QyD/bYbm/8eJrdr756s/PFsfNvxv8AFF1qfjKTRlmYWOnhR5QPytIRksfU849q8yrV8UakNY8WapqKHKXN3JIh/wBksdv6YrKrZaIhnqXwCtEn8d3E7gFrezYrnsSQK+ja+U/hZ4ot/Cvji3ur9tlpOpgmfH3A3RvoCBn2r6qjkSWNZImV0cBlZTkEHuDWc9yo7HC/Ga1vLv4Y3y2Ks+x45JlXqY1bJ/Lg/hXy5X28yh1KuAykYII4IrjL74SeDNQumnl0hYnY5IhkZFJ+gOKIysDVz5u8K+FtQ8X65Hpulp8zcySsDsiX+8xr0c/s76v21yy/79vXteh+HNJ8N2ZttEsYrSNjltg5c+pPU1pMwVSzEAAZJPahzfQOU+fZvgBqVpGZ7rXLEQpgudj5xn6da+gIYkghSGJQscahVUdgBgCvCvi38U473GheGZw8Ucge5u0OQ7KchVPcZGSe+K9Z8G+K7Pxh4dg1GzdfM2hbiEHmKTHIP9PaiV7XY1Y3J5lt7eSZwxWNC5CjJwBngV55/wAL08G/89rz/wABjXo1ec658EPCusXkl1D9q0+SRtzLbONhP+6wOPwqVbqDuJ/wvXwb/wA9L3/wHP8AjR/wvXwb/wA9L3/wHP8AjRo3wO8KaXcpPcfatRdDkLcyDZn6KBn8a7oaNpYGBptpj/rgv+FP3Q1OCb47+DwcD7e3uLf/AOvXk/j/AMQWfxB+IVk+keaLeRIrZfMXa2Sxzx+NezePvFHhrwVpb+ZZWMupyIfs9qsKEk9mbjha8I+H+3U/ilpMl6VzNe+a/GAW5b+dVG24n2Pq62gS1tIbeJQscKKiqOwAwBUOq3y6Zo15fP8AdtoHlP8AwFSf6VbqO5t4bu1lt7mNZYZUKSIwyGUjBBrMo+JpHaWRpJDlmJZj6k02vpjxb4C8F6D4P1XU10K2WS3tnaMlm+/jC9/Uivmet07mbVgooopiCiiigAooooAKKKKACiiigAooooAKns725068jurGeS3njOUkjbDKfrUFFAHqugfHvXtOiWHWrSDVEXgSZ8qT8SAQfyrpYv2idPI/faDcqf8AYmU/0FeC0VPKirs97l/aI04L+40K6ZvR5lA/rXO6z+0Br15G0ej2FrpwP/LRiZnH0zgfoa8moo5UF2X9X1vUtevDdaxezXc3ZpGzgegHb8Km0XXptEaUwQxyGQAEvnjFZVFTUpwqwcJq6ZVOpOlJTg7NHVf8J9qGf+Pa3x9G/wAaytV8QXWrXME1xHEPJOVRQcHnvzWVRWFLBYelLnhBJnRVxuIqx5JzbR6x/wANB+IlgCRaVpYZQAGZZCPy3j+dNn+P/iC4spIG0zTkaSMoZEEg2kjGR81eU0V08qOS7CiiiqEFdf4U+JviTwkqQ2N0LizX/l0uQXQD27r+BrkKKNxnudr+0VEUX7b4fdW/iMVyCPwyoq3/AMNEaTj/AJAl5n/roleA0VPKh3Z7df8A7RLlCNM0BQ3Z7i4yB/wED+tefeJfiZ4n8Uo0V/feTbN1t7YeWh9j3P4k1yVFNRSFdhWho2van4fvRd6Ney2k3QmM8MPQjoR9az6KYHrOk/tA69axqmradaX+P40Jhc/XGR+grcX9oqDHzeHZAfa6B/8AZa8KoqeVDuz265/aKfbi08OqG9ZbrI/ILXKaz8bPF2qq0cE8GnRNxi1jw3/fTEn8sV55RRyoV2S3NzPeXD3F3M800hy8kjFmY+5NFtczWd1HcWsjRTRMHR1OCpHQ1FRVCPWtK/aB1y0tVi1TTLW/dRjzVYxM316jP0Aq1N+0TqTL/o+gWsZ9ZJ2f+QFeN0VPKh3Z3vin4v8AiDxXo02l3kFjb2sxBbyI3DHBzjJY/wAq4KiinawBRRRTEFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAH/2Q==";
            let footer = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAkACQAAD/4RDiRXhpZgAATU0AKgAAAAgABAE7AAIAAAAIAAAISodpAAQAAAABAAAIUpydAAEAAAAQAAAQyuocAAcAAAgMAAAAPgAAAAAc6gAAAAgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHNvcyBnc20AAAWQAwACAAAAFAAAEKCQBAACAAAAFAAAELSSkQACAAAAAzE0AACSkgACAAAAAzE0AADqHAAHAAAIDAAACJQAAAAAHOoAAAAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAyMDIwOjAyOjIwIDEwOjU4OjUzADIwMjA6MDI6MjAgMTA6NTg6NTMAAABzAG8AcwAgAGcAcwBtAAAA/+ELGmh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSfvu78nIGlkPSdXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQnPz4NCjx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iPjxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+PHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9InV1aWQ6ZmFmNWJkZDUtYmEzZC0xMWRhLWFkMzEtZDMzZDc1MTgyZjFiIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iLz48cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0idXVpZDpmYWY1YmRkNS1iYTNkLTExZGEtYWQzMS1kMzNkNzUxODJmMWIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyI+PHhtcDpDcmVhdGVEYXRlPjIwMjAtMDItMjBUMTA6NTg6NTMuMTM1PC94bXA6Q3JlYXRlRGF0ZT48L3JkZjpEZXNjcmlwdGlvbj48cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0idXVpZDpmYWY1YmRkNS1iYTNkLTExZGEtYWQzMS1kMzNkNzUxODJmMWIiIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyI+PGRjOmNyZWF0b3I+PHJkZjpTZXEgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj48cmRmOmxpPnNvcyBnc208L3JkZjpsaT48L3JkZjpTZXE+DQoJCQk8L2RjOmNyZWF0b3I+PC9yZGY6RGVzY3JpcHRpb24+PC9yZGY6UkRGPjwveDp4bXBtZXRhPg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3hwYWNrZXQgZW5kPSd3Jz8+/9sAQwAHBQUGBQQH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";

            let statsCallsPrealableChart = document.getElementById('statsCallsPrealableChart');
            let callsStatesAgenciesChart = document.getElementById('callsStatesAgenciesChart');
            let callsStatesWeeksChart = document.getElementById('callsStatesWeeksChart');
            let statsCallsPosChart = document.getElementById('statsCallsPosChart');
            let statscallsNegChart = document.getElementById('statscallsNegChart');
            let CallResultPrealableChart = document.getElementById('CallResultPrealableChart');

            //creates image
            let statsRegionsChartImg = statsCallsPrealableChart.toDataURL("image/png", 1.0);
            let callsStatesAgenciesChartImg = callsStatesAgenciesChart.toDataURL("image1/png", 1.0);
            let callsStatesWeeksChartImg = callsStatesWeeksChart.toDataURL("image2/png", 1.0);
            let statsCallsPosChartImg = statsCallsPosChart.toDataURL("image5/png", 1.0);
            let statscallsNegChartImg = statscallsNegChart.toDataURL("image6/png", 1.0);
            let CallResultPrealableChartImg = statscallsNegChart.toDataURL("image7/png", 1.0);
            //creates PDF from img
            let doc = new jsPDF('p', 'pt', [842, 842]);
            doc.addImage(logo, 'jpeg', 371, 10, 100,30);
            doc.text(10 , 40, 'Résultats Appels');
            doc.autoTable({html: '#statsCallsPrealable', margin: {left: 0 , top: 50}, pageBreak: 'auto', tableWidth: 520 ,styles: {fontSize: 7, cellPadding: {right : 0} } });
            doc.addImage(statsRegionsChartImg, 'JPEG',  532 , 30 , 350 , 300);
            doc.addPage();
            doc.text(10, 20, 'Résultats Appels Préalables par agence');
            doc.autoTable({html: '#callsStatesAgencies', margin: {left: 0 , top: 30}, pageBreak: 'auto', tableWidth: 520 ,styles: {cellPadding: {right : 0} ,fontSize: 7} });
            doc.addImage(callsStatesAgenciesChartImg, 'JPEG',  532 , 30 , 350 , 300);
            doc.text(10, 390, 'Résultats Appels Préalables par semaine');
            doc.autoTable({html: '#callsStatesWeeks', pageBreak: 'auto', tableWidth: 520, startY: 400, margin: {left: 0} ,styles: {fontSize: 7}});
            doc.addImage(callsStatesWeeksChartImg, 'JPEG',532, 400  , 350 , 350);
            doc.addPage();
            doc.text(10, 20, 'Code Interventions liés aux RDV Confirmés (Clients Joignables)');
            doc.autoTable({
                html: '#statsCallsPos',
                margin: {left: 0, top: 30},
                pageBreak: 'auto',
                tableWidth: 842,
                styles: {fontSize: 7}
            });
            doc.addImage(statsCallsPosChartImg, 'JPEG', 150, ($('#statsCallsPos').height() / 1.328147) + 50, 500, 300);
            doc.addPage();
            doc.text(10, 20, 'Code Interventions liés aux RDV Non Confirmés (Clients Injoignables)');
            doc.autoTable({
                html: '#statsCallsNeg',
                margin: {left: 0, top: 30},
                pageBreak: 'auto',
                tableWidth: 842,
                styles: {fontSize: 7}
            });
            doc.addImage(statscallsNegChartImg, 'JPEG', 150, ($('#statsCallsNeg').height() / 1.328147) + 50, 500, 250);
            doc.addPage();
            doc.text(10, 20, 'Global Résultat Appels Préalables');
            doc.autoTable({
                html: '#CallResultPrealable',
                margin: {left: 0, top: 30},
                pageBreak: 'auto',
                tableWidth: 842,
                styles: {fontSize: 7}
            });
            doc.addImage(CallResultPrealableChartImg, 'JPEG', 150, ($('#CallResultPrealable').height() / 1.328147) + 50, 500, 250);
            doc .addImage(footer, 'jpeg', 371, 810, 100,30);
            doc.save('Appels Préalables.pdf');

            toggleLoader($('body'), true);
        }, 100);
    })

});
