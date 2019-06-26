var lang = {
    sProcessing: 'Procesando...',
    sLengthMenu: 'Mostrar _MENU_ registros',
    sZeroRecords: 'No se encontraron resultados',
    sEmptyTable: 'Ningún dato disponible en esta tabla',
    sInfo: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
    sInfoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
    sInfoFiltered: '(filtrado de un total de _MAX_ registros)',
    sInfoPostFix: '',
    sSearch: 'Buscar:',
    sUrl: '',
    sInfoThousands: ',',
    sLoadingRecords: 'Cargando...',
    oPaginate: {
        sFirst: 'Primero',
        sLast: 'Último',
        sNext: 'Siguiente',
        sPrevious: 'Anterior'
    },
    oAria: {
        sSortAscending: ': Activar para ordenar la columna de manera ascendente',
        sSortDescending: ': Activar para ordenar la columna de manera descendente'
    },
    buttons: {
        print: 'Imprimir',
        copy: "Copiar",
        copyTitle: 'Añadido al portapapeles',
        copyKeys: 'Presione <i>ctrl</i> o <i>\u2318</i> + <i>C</i> para copiar los datos de la tabla al portapapeles. <br><br>Para cancelar, haga clic en este mensaje o presione Esc.',
        copySuccess: {
            _: '%d lineas copiadas',
            1: '1 linea copiada'
        }
    },
    select: {
        rows: {
            _: '%d filas seleccionadas',
            1: '1 fila seleccionada'
        }
    }
};

$.fn.selectpicker.defaults = {
    noneSelectedText: 'Seleccione...',
    noneResultsText: 'No hay resultados {0}',
    countSelectedText: '{0} items seleccionados',
    maxOptionsText: ['Límite alcanzado ({n} {var} max)', 'Límite del grupo alcanzado({n} {var} max)', ['elementos', 'element']],
    multipleSeparator: ', ',
    selectAllText: 'Seleccionar Todos',
    deselectAllText: 'Desmarcar Todos'
};

toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: false,
    progressBar: false,
    positionClass: "toast-bottom-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: 300,
    hideDuration: 1000,
    timeOut: 5000,
    extendedTimeOut: 1000,
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut"
};

$(document).ready(function () {

    var url = $('#nav-active').data('url');
    $('a[href="' + url + '"]').parents('li').addClass('active');
    $('.treeview[class="active"]').addClass('menu-open');
    $('.active > .treeview-menu').show();


    $(document).on('mousedown', 'select[readonly]', function (e) {
        e.preventDefault();
    });

    $(document).on('mousedown', 'disabled', function (e) {
        e.preventDefault();
    });

    $('.selectpicker').selectpicker({
        liveSearch: true,
        selectedTextFormat: 'count'
    });

    $(document).on('focus', 'form:not([readonly]) .datepicker', function () {
        $(this).datepicker({
            format: 'yyyy/mm/dd',
            language: 'es',
            autoclose: true,
            todayHighlight: true
        });
    });

    $(document).on('focus', '.phone', function () {
        $(this).mask('(0000) 000-0000');
    });

    $(document).on('focus', '.money', function () {
        $(this).maskMoney({
            allowZero: true
        });
    });

    $(document).on('focus', '.rif', function () {
        $(this).mask('A-00000000-0', {
            translation: {
                'A': {pattern: /[vepgjc]/i},
                '0': {pattern: /[0-9]/}
            }
            , onKeyPress: function (value, event) {
                event.currentTarget.value = value.toUpperCase();
            }
        });
    });

    $(document).on('click', '.modal-trigger', function (event) {
        event.preventDefault();

        $('#modal-default').remove();
        $('body').append('<div id="modal-default" class="modal fade" role="dialog"></div>');

        $.ajax({
            url: $(this).data('action'),
            type: $(this).data('method'),
            success: function (data) {
                $('#modal-default').empty().append(data).modal('show');
                initializeInputs()
            }
        });
    });

    $('#modal-default').on('hidden.bs.modal', function () {
        $(this).remove();
    });


    $(document).on('click', '.ajax-submit', function (e) {
        e.preventDefault();

        $('#ajax-submit').prop('disabled', true);
        $('.modal-container').hide();
        $('.loader').show();

        $('.modal-container select[readonly]').attr('disabled', false);

        var form = $(this).parents('form');

        if ($(form).find('input[type="file"]').val()) {
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                type: form.attr('method'),
                url: form.attr('action'),
                data: new FormData($(form)[0]),
                success: function (data) {
                    if ('success' === data) {
                        if ($.fn.DataTable.isDataTable('.datatable')) {
                            table.ajax.reload();
                        }

                        if ($.fn.DataTable.isDataTable('.datatable-filter')) {
                            table_filter.ajax.reload();
                        }

                        toastr.success('Exito!. Operación realizada satisfactoriamente.');

                        $('#modal-default').modal('toggle');

                    } else if ('success-reload' === data) {
                        location.reload();
                    } else if ('error' === data) {
                        toastr.error('Oops!. La operación no pudo ser realizada. Compruebe que el registro no posea dependencias.');
                    } else if (!data.includes('input') && data.includes('/manage/credit/')) {
                        window.location.replace(data);
                    } else {
                        $('#modal-default').empty().append(data);
                        initializeInputs();
                    }
                }
            });
        } else {
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function (data) {
                    if ('success' === data) {
                        if ($.fn.DataTable.isDataTable('.datatable')) {
                            table.ajax.reload();
                        }

                        if ($.fn.DataTable.isDataTable('.datatable-filter')) {
                            table_filter.ajax.reload();
                        }

                        toastr.success('Exito!. Operación realizada satisfactoriamente.');

                        $('#modal-default').modal('toggle');

                    } else if ('success-reload' === data) {
                        location.reload();
                    } else if ('error' === data) {
                        toastr.error('Oops!. La operación no pudo ser realizada. Compruebe que el registro no posea dependencias.');
                    } else if (!data.includes('input') && data.includes('/manage/credit/')) {
                        window.location.replace(data);
                    } else {
                        $('#modal-default').empty().append(data);
                        initializeInputs();
                    }
                }
            });
        }
    });

    $('.btn-new').appendTo('.add-btn-container');

    var selector1 = $('.datatable');
    var table = $('.datatable').DataTable({
        order: [0, 'DESC'],
        ajax: selector1.data('src'),
        language: lang,
        responsive: true,
        dom: "<'row'<'col-sm-8 float-right-content'B><'col-sm-4'<'btn-add-container'>><'col-sm-12 date-filter-container'><'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
        select: {
            style: 'os',
            selector: 'td:not(:last-child)',
            blurable: true
        },
        buttons: [
            {
                extend: 'copy',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });

    var selector2 = $('.datatable-filter');
    var table_filter = $('.datatable-filter').DataTable({
        order: [0, 'DESC'],
        ajax: selector2.data('src'),
        language: lang,
        responsive: true,
        dom: "<'row'<'col-sm-8 float-right-content'B><'col-sm-4'<'btn-add-container'>><'col-sm-12 date-filter-container'><'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
        initComplete: function (settings, json) {
            dateFilter(settings, json, 0);
        },
        select: {
            style: 'os',
            selector: 'td:not(:last-child)',
            blurable: true
        },
        buttons: [
            {
                extend: 'copy',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });
});

$(document).on('change', '#appbundle_taxreturn_taxpayer', function () {

    var form = $(this).closest('form');
    var data = {};

    data[$(this).attr('name')] = $(this).val();

    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: data,
        success: function (data) {
            $(document).find('#taxReturn-data').replaceWith($(data).find('#taxReturn-data'));
        }
    });
});

$(document).on('keyup', '.declared-amount', function (event) {
    var aliquot = (($(this).find('.form-control').val().replace(/,/g, '') * $(this).data('aliquot')) / 100);
    var minimumTaxable = $(this).data('minimum-taxable');

    if (minimumTaxable > aliquot) {
        $(document).find($(this).data('id')).empty().html(Number(minimumTaxable).toLocaleString('en', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    } else {
        $(document).find($(this).data('id')).empty().html(Number(aliquot).toLocaleString('en', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    }

    var amountToPay = 0;

    $(document).find('.amount-to-pay').each(function () {
        amountToPay += Number($(this).text().replace(/,/g, ''));
    });

    $(document).find($('#total-amount-to-pay')).empty().html(Number(amountToPay).toLocaleString('en', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));
});

//================================================================================
//================================================================================
// functions
//================================================================================

function dateFilter(settings, json, dateColumn) {

    var table = settings.oInstance.api();

    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            if ($('#startDate').val() === '' && $('#endDate').val() === '') {
                return true;
            }

            if ($('#startDate').val() !== '' || $('#endDate').val() !== '') {
                var iMin_temp = $('#startDate').val();
                if (iMin_temp === '') {
                    iMin_temp = '0/0/0';
                }

                var iMax_temp = $('#endDate').val();
                if (iMax_temp === '') {
                    iMax_temp = '9999/99/99';
                }

                var arr_min = iMin_temp.split('/');
                var arr_max = iMax_temp.split('/');
                var arr_date = data[dateColumn].split('/');
                var aux = arr_date[2].split(' ');
                arr_date[2] = aux[0];

                var iMin = new Date(arr_min[0], arr_min[1], arr_min[2], 0, 0, 0, 0);
                var iMax = new Date(arr_max[0], arr_max[1], arr_max[2], 0, 0, 0, 0);
                var iDate = new Date(arr_date[0], arr_date[1], arr_date[2], 0, 0, 0, 0);

                if (iMin === '' && iMax === '') {
                    return true;
                }
                else if (iMin === '' && iDate < iMax) {
                    return true;
                }
                else if (iMin <= iDate && '' === iMax) {
                    return true;
                }
                else if (iMin <= iDate && iDate <= iMax) {
                    return true;
                }

                return false;
            }
        }
    );

    $('#startDate').datepicker({
        format: 'yyyy/mm/dd',
        language: 'es',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#endDate').datepicker('setStartDate', minDate);
    });

    $('#endDate').datepicker({
        format: 'yyyy/mm/dd',
        language: 'es',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (selected) {
        var maxDate = new Date(selected.date.valueOf());
        $('#startDate').datepicker('setEndDate', maxDate);
    });

    $('#startDate, #endDate').on('changeDate', function () {
        table.draw();
    });
}

function initializeInputs() {
    $('.modal-container select[readonly]').attr('disabled', true);

    $('.selectpicker').selectpicker({
        liveSearch: true,
        selectedTextFormat: 'count'
    });

    $('.datatable-modal').DataTable({
        info: false,
        language: lang,
        lengthChange: false,
        paging: false,
        responsive: true,
        searching: false
    });

    $("#taxpayer-economicActivity-table").appendTo($('#appbundle_taxpayer_economicActivity').parents('.form-group'));
}