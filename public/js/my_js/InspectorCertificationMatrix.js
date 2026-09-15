$(document).ready(function () {
    $('#btnInsCertMatrix').on('click', function(){
        $('#modalInspectorCertMatrix').modal('show');
        loadDropdownSelInspCertMatrix();
    });   

    $('#btnExportInspectorCertMatrix').on('click', function(){
        let productLine = $('#selInsCertMatrixProductLine').val();
        let section = $('#selInsCertMatrixSection').val();
        window.open('export-inspector-cert-matrix?product_line=' + productLine + '&section=' + section, '_blank');
    });
});

const loadDropdownSelInspCertMatrix = () => {
    $.ajax({
        type: "GET",
        url: "get_dropdown_select_certpersonnel",
        data: {},
        dataType: "json",
        beforeSend: function(){
        },
        success: function (response) {
            // let productLineOptions = '<option value="">Select Product Line</option>';
            let productLineOptions = '';
            let sectionOptions = '<option value="">Select Section</option>';
            let sections = response.section;
            let productLines = response.product_line;


            productLines.forEach(function(productLine){
                productLineOptions += '<option value="' + productLine.id + '">' + productLine.dropdown_masters_details + '</option>';
            });
            sections.forEach(function(section){
                sectionOptions += '<option value="' + section + '">' + section + '</option>';
            });
            
            $('#selInsCertMatrixProductLine').append(productLineOptions);
            $('#selInsCertMatrixSection').append(sectionOptions);
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}