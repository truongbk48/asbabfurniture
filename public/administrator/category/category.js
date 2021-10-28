(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('#categories-table').length) {
        let urlAjax = $('#categories-table').data('url');
        let columns;
        if (urlAjax.split('data/')[1] == 0) {
            columns = [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            }];
        } else {
            columns = [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }];
        }

        $('#categories-table').DataTable({
            processing: true,
            responsive: true,
            dom: '<"flex-between"lf>t<"flex-between"ip>',
            language: {
                processing: "<div id='loader'>Dang load nghe bay !</div>",
                paginate: {
                    previous: '← Prev',
                    next: 'Next →'
                },
                lengthMenu: '_MENU_ results per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ results'
            },
            serverSide: true,
            order: [0, 'desc'],
            ajax: urlAjax,
            columns: columns
        });
        $(document).on('click', '.action-delete', actionDelete);
    }

    $('#editCategory').on('show.bs.modal', function (e) {
        let editUrl = $(e.relatedTarget).data('href');
        let that = $(this);
        that.find('[name="parent_id"]').find('option:not(:first-child)').remove()
        $.ajax({
            type: 'GET',
            url: editUrl,
            dataType: 'json',
            success: function (data) {
                that.find('.modal-title').text('Edit: ' + data.category.name);
                that.find('[name="url"]').val(data.editUrl);
                that.find('[name="name"]').val(data.category.name);
                that.find('[name="parent_id"]').append(data.htmlOptions);
            }
        });
    });

    $('#createCategory').on('show.bs.modal', function (e) {
        $(this).find('form [name]').each(function (i, el) {
            $(el).val('');
        });
        let creatUrl = $(e.relatedTarget).data('url');
        let optionEls = $(this).find('select');
        optionEls.find('option:not(:first-child)').remove()
        $.ajax({
            type: 'GET',
            url: creatUrl,
            dataType: 'json',
            success: function (data) {
                optionEls.append(data);
            }
        });
    });

    $('#add-btn-category').on('click', function (e) {
        e.preventDefault();
        let storeUrl = $(this).data('url');
        $.ajax({
            type: 'POST',
            url: storeUrl,
            dataType: 'JSON',
            data: $('#createCategory .category-body').serialize(),
            success: function (data) {
                $('#createCategory form .error').remove();
                $('#createCategory form .alert-danger').removeClass('alert-danger');
                $('#createCategory').modal('hide');
                Swal.fire(
                    'Added!',
                    'Your categry has been added.',
                    'success'
                )
                $('#categories-table').DataTable().ajax.reload();
            },
            error: function (data) {
                if (data.status === 422) {
                    let errors = data.responseJSON.errors;
                    $('#createCategory form [name]').each(function (ind, elem) {
                        if (errors[elem.name]) {
                            $(elem).parents('.form-group').find('.error').remove();
                            $(elem).addClass('alert-danger').parents('.form-group').append('<div class="error">' + errors[elem.name] + '</div>');
                        }
                    });
                } else {
                    $('#createCategory form .error').remove();
                    $('#createCategory form .alert-danger').removeClass('alert-danger');
                }
            }
        });
    })

    $('#edit-btn-category').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: $('#editCategory form [name="url"]').val(),
            dataType: 'JSON',
            data: $('#editCategory .category-body').serialize(),
            success: function (data) {
                $('#editCategory form .error').remove();
                $('#editCategory form .alert-danger').removeClass('alert-danger');
                $('#editCategory').modal('hide');
                Swal.fire(
                    'Edited!',
                    'Your categry has been edited.',
                    'success'
                )
                $('#categories-table').DataTable().ajax.reload();
            },
            error: function (data) {
                if (data.status === 422) {
                    let errors = data.responseJSON.errors;
                    $('#editCategory form [name]').each(function (ind, elem) {
                        if (errors[elem.name]) {
                            $(elem).parents('.form-group').find('.error').remove();
                            $(elem).addClass('alert-danger').parents('.form-group').append('<div class="error">' + errors[elem.name] + '</div>');
                        }
                    });
                } else {
                    $('#editCategory form .error').remove();
                    $('#editCategory form .alert-danger').removeClass('alert-danger');
                }
            }
        });
    })

    function actionDelete(e) {
        e.preventDefault();
        let hrefData = $(this).data('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'GET',
                    url: hrefData,
                    dataType: 'json',
                    success: function (data) {
                        Swal.fire(
                            'Deleted!',
                            'Your category has been deleted.',
                            'success'
                        )
                        $('#categories-table').DataTable().ajax.reload();
                    }
                });
            }
        })
    }

})(jQuery)
