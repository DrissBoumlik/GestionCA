$(function () {
    var skillsTable = $("#skills-data").DataTable({
        responsive: true,
        info: false,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: 'getSkills?dt',
        columns: [
            {data: 'name', name: 'name'},
            {
                data: 'id', name: 'id',
                render: function (data, type, full, meta) {
                    return "<a href='#' class='align-center blue pointer edit-skill  mr-3' data-id='" + data + "' data-name='" + full.name + "'" +
                        " data-target='#skillForm' data-toggle='modal'>" +
                        "<i class='far fa-edit'></i>" +
                        "</a>" +
                        "<a data-skill='" + data + "' class='delete-skill blue pointer'>" +
                        "<i class='fas fa-trash-alt'></i></a>";
                }
            },
        ]
    });

    $('#skills-data').on('click', '.edit-skill', function () {
        createMode = false;
        $('#header-form').text('Edit Skill');
        $('#header-btn-send').text('Update');
        $('#header-btn-send + i').toggleClass('fa-edit fa-plus-square');
        // Assign value to the model
        var skillInput = $('#skill');
        skillInput.val($(this).attr('data-name'));
        skillInput.attr('data-id', $(this).attr('data-id'));
    });

    var createMode = false;
    $('.add-skill').on('click', function () {
        createMode = true;
        $('#header-form').text('Add Skill');
        $('#header-btn-send').text('Create');
        $('#header-btn-send + i').toggleClass('fa-edit fa-plus-square');
        var skillInput = $('#skill');
        skillInput.val('');
        skillInput.attr('data-id', '');
    });

    $('.update-skill').on('click', function () {
        $('#skillForm').modal('hide');
        var baseUrl = window.location.origin;
        var skillInput = $('#skill');
        $.ajax({
            method: createMode ? 'POST' : 'PUT',
            url: baseUrl + '/skills/' + (createMode ? '' : skillInput.attr('data-id')),
            data: {name: skillInput.val()},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (response) {
                skillsTable.ajax.url('/getSkills?dt').load();
                feedBack(response.message, 'success');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 401 || jqXHR.status === 422) {
                    feedBack(jqXHR.responseJSON.message, 'error');
                }
            }
        });
    });

    $('#skills-data').on('click', '.delete-skill', function () {
        var confirmed = confirm('Are you sure ?');
        if (confirmed) {
            skill_id = $(this).attr('data-skill');
            element = this;
            var baseUrl = window.location.origin;
            $.ajax({
                method: 'DELETE',
                url: baseUrl + '/skills/' + skill_id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    feedBack(response.message, 'success');
                    $('#skills-data #skill-' + skill_id).addClass('danger');
                    setTimeout(() => {
                        var index = element.parentNode.parentNode.rowIndex;
                        // document.getElementById("users-data").deleteRow(index);
                        skillsTable.row($(this).parents('tr'))
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


    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }

});
