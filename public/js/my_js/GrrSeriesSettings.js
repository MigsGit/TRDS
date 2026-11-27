$(document).ready(function () {
    const $table = $('#tblGRRSeries');
    if ($table.length === 0) return;

    const $form = $('#formGRRSeries');
    const $modal = $('#modalAddGRRSeries');
    const $section = $('#seriesSection');
    const $btnSave = $('#btnSaveGRRSeries');

    const dtSeries = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: 'view_grr_series' },
        fixedHeader: true,
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'section' },
            { data: 'series_name' },
            { data: 'status_label', orderable: false, searchable: false }
        ]
    });

    $('#grrSeries-tab').on('shown.bs.tab', function () {
        dtSeries.columns.adjust();
    });

    function resetForm() {
        $form[0].reset();
        $('#txtGRRSeriesId').val('');
        $section.val('').trigger('change');
    }

    function errorMessage(xhr, fallback) {
        if (xhr.status === 401) return 'Your session has expired. Please log in again.';
        return (xhr.responseJSON && xhr.responseJSON.message) || fallback;
    }

    $('#btnShowAddGRRSeriesModal').on('click', function () {
        resetForm();
        $modal.modal('show');
    });

    $form.on('submit', function (e) {
        e.preventDefault();

        if (!$section.val() || $.trim($('#seriesName').val()) === '') {
            showError('Please select a section and enter a series name.');
            return;
        }

        $btnSave.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: 'save_grr_series',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: $form.serialize(),
            dataType: 'json'
        }).done(function (response) {
            dtSeries.draw(false);
            $modal.modal('hide');
            resetForm();
            showSuccess(response.message || 'Successfully saved!');
        }).fail(function (xhr) {
            showError(errorMessage(xhr, 'Failed to save data.'));
        }).always(function () {
            $btnSave.prop('disabled', false);
        });
    });

    $table.on('click', '.btnEditSeries', function () {
        $.ajax({
            type: 'GET',
            url: 'get_grr_series_by_id',
            data: { id: $(this).data('id') },
            dataType: 'json'
        }).done(function (response) {
            $('#txtGRRSeriesId').val(response.id);
            $section.val(response.section).trigger('change');
            $('#seriesName').val(response.series_name);
            $modal.modal('show');
        }).fail(function (xhr) {
            showError(errorMessage(xhr, 'Failed to fetch data.'));
        });
    });

    function toggleStatus(id) {
        $.ajax({
            type: 'POST',
            url: 'update_grr_series_status',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { id: id },
            dataType: 'json'
        }).done(function () {
            showSuccess('Status updated successfully.');
            dtSeries.draw(false);
        }).fail(function (xhr) {
            showError(errorMessage(xhr, 'Failed to update status.'));
        });
    }

    $table.on('click', '.btnDisableSeries', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to disable this series?', function () { toggleStatus(id); });
    });

    $table.on('click', '.btnEnableSeries', function () {
        const id = $(this).data('id');
        confirmAction('Are you sure you want to enable this series?', function () { toggleStatus(id); });
    });
});
