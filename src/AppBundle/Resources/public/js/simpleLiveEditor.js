var SLE = {

    formColCancel:function(o){
        o.parent().find('.sle_formCol').find('form').removeClass('sle-visible');
        o.parent().find('.sle_formCol').find('form').addClass('sle-hidden');
    },


    formColVisibility:function(o){
        o.parent().find('.sle_formCol').find('form').addClass('sle-visible');
        o.parent().find('.sle_formCol').find('form').removeClass('sle-hidden');
    },

    submitCol:function(form,e,formType,entityName,colName,rowId){

        //form.find('input').attr("disabled","disabled");
        form.find('button').attr("disabled","disabled");
        form.parent().parent().append('<i class="fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span>');


        e.preventDefault();

        url = Routing.generate('app_simpleLiveEditor_submitCol',{
            'formType': formType,
            'entityName': entityName,
            'colName': colName,
            'rowId': rowId
        });

        $.ajax({
            method: 'post',
            url: url,
            data: form.serialize()
        })
            .done(function (response) {
                form.parent().parent().html(response);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                alert("SLE AJAX ERROR");
            });
    }

};
