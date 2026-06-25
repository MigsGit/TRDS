const getDivDeptSec = (params) => {
// params.comboId
    let data = {

    };

    call_ajax(data,'get_div_dept_sec',function(response){
        console.log(response);
     
        let paramsGetSelect2Value = {
            comboId : params.comboId,
            dataValue : response['section']
        }
        fnGetSelect2Value(paramsGetSelect2Value)
    });

}


function fnGetSelect2Value(params){
    // $('#formEditSa select[name="select_checked_by_qc[]"]').select2({

    params.comboId.select2({
        // data : response['iqc_qc_checkedby']
        data : params.dataValue,
        theme: 'bootstrap-5',
    });
    var arrValue = [];
    $.each(params.dataValue, function(key, value){
        arrValue.push(value)
    });
    console.log('arrValue',arrValue);

    params.comboId.val(arrValue).trigger('change');
}

