$(document).ready(function () {
    $('#btnListCertPersonnel').on('click', function(){
        $('#modalListOfCertPersonnel').modal('show');
    });

    $('#btnListCertPersonnel').on('click', function(){
        $.ajax({
            url: 'get_dropdown_select_certpersonnel',
            type: 'GET',
            beforeSend: function(){
                $('#selCertPersonnelSection').html('');
                $('#selCertPersonnelSeries').html('');
                $('#selCertPersonnelProductLine').html('');
            },
            success: function(response) {
                let section = response.section;
                let prodLine = response.product_line;
                let series = response.series;

                let sectionOption = "";
                let prodLineOption = "";
                let seriesOption = "";

                sectionOption += `<option value="" selected disabled>Select Section</option>`;
                prodLineOption += `<option value="" selected disabled>Select Product Line</option>`;
                seriesOption += `<option value="" selected disabled>Select Series</option>`;
                section.forEach(function(item) {
                    sectionOption += `<option value="${item}">${item}</option>`;
                });
                $('#selCertPersonnelSection').append(sectionOption);


                prodLine.forEach(function(item) {
                    prodLineOption += `<option value="${item.id}">${item.dropdown_masters_details}</option>`;
                });

                $('#selCertPersonnelProductLine').append(prodLineOption);

                series.forEach(function(item) {
                    seriesOption += `<option value="${item}">${item}</option>`;
                })
                $('#selCertPersonnelSeries').append(seriesOption);
            },
            error: function(xhr, status, error) {
                toastr.error('Error fetching list of certified personnel. Please try again later.');
                console.error('Error fetching list of certified personnel:', error);
            }
        });
    });

    $("#btnExportListCertPersonnel").on('click', function() {
        let section = $('#selCertPersonnelSection').val();
        // let series = $('#selCertPersonnelSeries').val();
        let productLine = $('#selCertPersonnelProductLine').val();

        if (!section || !productLine) {
            toastr.warning('Please select both Section and Product Line before exporting.');
            return;
        }

        window.open(`export_list_cert_personnel?section=${section}&product_line=${productLine}`, '_blank');
    });
});