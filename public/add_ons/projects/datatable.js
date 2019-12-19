$(function () {

    const _techs = $('#techs');
    const _collaborators = $('#collaborators');

    function format(data) {
        const techs = !data.techs.length ? 'No Technologies' :
            'Technologies : ' + data.techs.map(function (tech) {
                return tech.name;
            }).join(', ');
        const users = !data.users.length ? 'No Collaborators' :
            'Collaborators : ' + data.users.map(function (user) {
                return '<a href="/users/' + user.id + '">' + user.firstname + ' ' + user.lastname + '</a>';
            }).join(', ');
        return techs + '<br>' + users;
    }

    const projectsTable = $("#projects-data").DataTable({
        responsive: true,
        info: false,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: 'getProjects?dt',
        columns: [
            {
                class: "details-control pointer",
                data: null,
                name: 'id',
                defaultContent: ""
            },
            // {
            //     data: 'id', name: 'id',
            //     render: function (data, type, full, meta) {
            //         return "<a href='/projects/" + data + "' class='align-center blue d-block'><i class='far fa-eye big-icon-fz'></i></a>";
            //     }
            // },
            {data: 'name', name: 'name'},
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'},
            {
                data: 'id', name: 'id',
                render: function (data, type, full, meta) {
                    return "<a href='#' class='align-center blue pointer edit-project mr-3' data-id='" + data + "' data-name='" + full.name + "'" +
                        " data-target='#projectForm' data-toggle='modal'>" +
                        "<i class='far fa-edit'></i>" +
                        "</a>" +
                        "<a data-project='" + data + "' class='delete-project blue pointer'>" +
                        "<i class='fas fa-trash-alt'></i></a>";
                }
            },
        ]
    });

    let createMode = false;

    projectsTable.on('click', '.edit-project', function () {
        createMode = false;
        $('#header-form').text('Edit Project');
        $('#header-btn-send').text('Update');
        $('#header-btn-send + i').toggleClass('fa-edit fa-plus-square');
        // Assign value to the model
        const projectInput = $('#project');
        const project = $(this);
        projectInput.val(project.attr('data-name'));
        projectInput.attr('data-id', project.attr('data-id'));

        if (_techs.data('select2')) {
            _techs.select2("destroy");
        }
        TechsInitLoad(project.attr('data-id'));
        _techs.select2(TechsInitSelect2());

        if (_collaborators.data('select2')) {
            _collaborators.select2("destroy");
        }
        CollaboratorsInitLoad(project.attr('data-id'));
        _collaborators.select2(CollaboratorsInitSelect2());
    });

    $('.add-project').on('click', function () {
        createMode = true;
        $('#header-form').text('Add Project');
        $('#header-btn-send').text('Create');
        $('#header-btn-send + i').toggleClass('fa-edit fa-plus-square');
        const projectInput = $('#project');
        projectInput.val('');
        projectInput.attr('data-id', '');

        if (_techs.data('select2')) {
            _techs.select2("destroy");
        }
        _techs.find('option').remove().end();
        _techs.select2(TechsInitSelect2());


        if (_collaborators.data('select2')) {
            _collaborators.select2("destroy");
        }
        _collaborators.find('option').remove().end();
        _collaborators.select2(CollaboratorsInitSelect2());
    });

    function prepareRequestData(options) {
        const data = [];
        Object.values(options).forEach(function (item, index) {
            if (index < options.length) {
                data.push($(item).val())
            }
        });
        return data;
    }
    const baseUrl = window.location.origin;
    $('.update-project').on('click', function () {
        $('#projectForm').modal('hide');
        const projectInput = $('#project');

        const techs = prepareRequestData($('#techs option:selected'));
        const collaborators = prepareRequestData($('#collaborators option:selected'));
        console.log(techs, collaborators);

        $.ajax({
            method: createMode ? 'POST' : 'PUT',
            url: baseUrl + '/projects/' + (createMode ? '' : projectInput.attr('data-id')),
            data: {name: projectInput.val(), techs, collaborators, json: true},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (response) {
                projectsTable.ajax.url('/getProjects?dt').load();
                feedBack(response.message, 'success');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 401 || jqXHR.status === 422) {
                    feedBack(jqXHR.responseJSON.message, 'error');
                }
            }
        });
    });

    projectsTable.on('click', '.delete-project', function () {
        const confirmed = confirm('Are you sure ?');
        if (confirmed) {
            const project_id = $(this).attr('data-project');
            const element = this;
            $.ajax({
                method: 'DELETE',
                url: baseUrl + '/projects/' + project_id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    feedBack(response.message, 'success');
                    $('#projects-data #project-' + project_id).addClass('danger');
                    setTimeout(() => {
                        const index = element.parentNode.parentNode.rowIndex;
                        // document.getElementById("users-data").deleteRow(index);
                        projectsTable.row($(this).parents('tr'))
                            .remove()
                            .draw();
                    }, 200);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status === 401 || jqXHR.status === 422) {
                        feedBack(jqXHR.responseJSON.message, 'error');
                    }
                }
            });
        }

    });


    // Array to track the ids of the details displayed rows
    var detailRows = [];

    projectsTable.on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = projectsTable.row(tr);
        var idx = $.inArray(tr.attr('id'), detailRows);

        if (row.child.isShown()) {
            tr.removeClass('details');
            row.child.hide();

            // Remove from the 'open' array
            detailRows.splice(idx, 1);
        } else {
            tr.addClass('details');
            row.child(format(row.data())).show();

            // Add to the 'open' array
            if (idx === -1) {
                detailRows.push(tr.attr('id'));
            }
        }
    });

    projectsTable.on('draw', function () {
        $.each(detailRows, function (i, id) {
            $('#' + id + ' td.details-control').trigger('click');
        });
    });

    // ==========================

    function TechsInitLoad(project_id) {
        _techs.find('option').remove().end();
        $.ajax({
            type: 'GET',
            url: '/getTechs/' + project_id
        }).then(function (data) {
            // create the option and append to Select2
            data.techs.forEach((tech, index) => {
                var option = new Option(tech.name, tech.id, false, tech.assigned);
                _techs.append(option).trigger('change');
            });

            // manually trigger the `select2:select` event
            // _skills.trigger({
            //     type: 'select2:select',
            //     params: {
            //         data: data.skills
            //     }
            // });
        });
    }

    function TechsInitSelect2(maxSelectionLength = null) {
        return {
            multiple: true,
            maximumSelectionLength: maxSelectionLength,
            allowClear: true,
            width: '100%',
            // theme: 'classic',
            placeholder: 'Select a Tech',
            // placeholder: {
            //     id: '0', // the value of the option
            //     text: 'Select an option'
            // },
            ajax: {
                url: '/getTechs',
                type: 'GET',
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    params.term = params.term === '*' ? '' : params.term;
                    return {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        searchTerm: $.trim(params.term)
                    };
                },
                processResults: function (response) {
                    results = response.techs.map(function (tech) {
                        tech.text = tech.name;
                        return tech;
                    });
                    return {
                        results: results
                    };
                },
                cache: true
            },
            templateResult: function (data) {
                if (data.loading) {
                    return data.text;
                }
                return data.name;
            }
        };
    }

    function CollaboratorsInitLoad(project_id) {
        _collaborators.find('option').remove().end();
        $.ajax({
            type: 'GET',
            url: '/getCollaborators/' + project_id
        }).then(function (data) {
            // create the option and append to Select2
            data.collaborators.forEach((user, index) => {
                var option = new Option(user.firstname + ' ' + user.lastname, user.id, false, user.assigned);
                _collaborators.append(option).trigger('change');
            });

            // manually trigger the `select2:select` event
            // _skills.trigger({
            //     type: 'select2:select',
            //     params: {
            //         data: data.skills
            //     }
            // });
        });
    }

    function CollaboratorsInitSelect2(maxSelectionLength = null) {
        return {
            multiple: true,
            maximumSelectionLength: maxSelectionLength,
            allowClear: true,
            width: '100%',
            // theme: 'classic',
            placeholder: 'Select a Collaborator',
            // placeholder: {
            //     id: '0', // the value of the option
            //     text: 'Select an option'
            // },
            ajax: {
                url: '/getCollaborators',
                type: 'GET',
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    params.term = params.term === '*' ? '' : params.term;
                    return {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        searchTerm: $.trim(params.term)
                    };
                },
                processResults: function (response) {
                    results = response.collaborators.map(function (user) {
                        user.text = user.firstname + ' ' + user.lastname;
                        return user;
                    });
                    return {
                        results: results
                    };
                },
                cache: true
            },
            templateResult: function (data) {
                if (data.loading) {
                    return data.text;
                }
                return data.firstname + ' ' + data.lastname; //data.name;
            }
        };
    }

// ===================

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }

});
