
import $ from 'jquery';
import _ from 'lodash';

import '../css/store.scss';

var fa = require("fontawesome");
require('font-awesome/css/font-awesome.css');


require('angular');
require('angular-sanitize');
require('./populator');


require('popper.js');
require('bootstrap');
require('bootstrap-select');
require('bootstrap-select/dist/js/i18n/defaults-fr_FR.js');
require('bootstrap-select/dist/css/bootstrap-select.css');
require('ajax-bootstrap-select');
require('ajax-bootstrap-select/dist/css/ajax-bootstrap-select.css');




require('ekko-lightbox');
require('ekko-lightbox/dist/ekko-lightbox.css');
var Translator = require('bazinga-translator');

(function (t) {
    t.fallback = 'fr';
    t.defaultDomain = 'messages';
})(Translator);

 (function (t) {
 // fr
 t.add("lilworks.filter.open", "ouvrir le filtre", "messages", "fr");
 t.add("lilworks.filter.close", "fermer le filtre", "messages", "fr");
 t.add("js.alert.unsubscribe.title", "Lettre d'information", "messages", "fr");
 t.add("js.alert.unsubscribe.text", "Etes vous sûr de vouloir vous desabonner de la lettre d'information", "messages", "fr");
 t.add("js.alert.subscribe.title", "Lettre d'information", "messages", "fr");
 t.add("js.alert.subscribe.text", "Etes vous sûr de vouloir recevoir la lettre d'information", "messages", "fr");
 t.add("js.alert.empty.title", "Vider", "messages", "fr");
 t.add("js.alert.empty.text", "Etes-vous sûr de vouloir supprimer vider entrée?", "messages", "fr");
 t.add("js.alert.delete.title", "Supprimer", "messages", "fr");
 t.add("js.alert.delete.text", "Etes-vous sûr de vouloir supprimer cette entrée?", "messages", "fr");
 t.add("js.alert.yes", "oui", "messages", "fr");
 t.add("js.alert.no", "non", "messages", "fr");
 })(Translator);


require("jquery-confirm");
require('jquery-confirm/dist/jquery-confirm.min.css');

require("bootstrap-datepicker/dist/js/bootstrap-datepicker.js");
require("bootstrap-datepicker/dist/css/bootstrap-datepicker.css");

var FF = require('./formFilter.js');
var RE = require('./rowsEditor.js');
var FCE = require('./formCollectionEditor.js');
var SLE = require('./simpleLiveEditor.js');
require('../css/simpleLiveEditor.scss');



require('./alert.js');

var tinymce = require('tinymce');
var inittinymce = require('./init-tinymce');
require('tinymce/themes/modern/theme.min.js');


$(document).ready(function() {
    inittinymce.text('.editor-text');
    inittinymce.css('.editor-css');

    RE.init('product_index');


    FCE.init('returns','returns list-inline','return list-inline-item');
    FCE.init('shippingmethodsCountries','shippingmethodsCountries list-inline','shippingmethodsCountry list-inline-item');
    FCE.init('depositSalesPaymentMethods','depositSalesPaymentMethods list-inline','depositSalePaymentMethod list-inline-item');
    FCE.init('products','products list-inline','product list-inline-item');
    FCE.init('realShippingMethods','realShippingMethods list-inline','realShippingMethod list-inline-item');
    FCE.init('ordersOrderSteps','ordersOrderSteps list-inline','orderOrderStep list-inline-item');
    FCE.init('ordersPaymentMethods','ordersPaymentMethods list-inline','orderPaymentMethod list-inline-item');
    FCE.init('addresses','addresses list-inline','addresse list-inline-item');
    FCE.init('phonenumbers','phonenumbers list-inline','phonenumber list-inline-item');
    FCE.init('pictures','pictures list-inline','picture list-inline-item');
    FCE.init('triggers','triggers list-inline','trigger list-inline-item');
    FCE.init('categories','categories list-inline','category list-inline-item');


    $('.datepicker').datepicker({
        language:'fr',
        format: "dd/mm/yyyy"
    });






    $('#ajax_customer').submit(function(e) {

        var form = $("#ajax_customer");
        var spinnerHtml = '<i id="customer-spinner" class="fa fa-cog fa-spin fa-3x fa-fw"></i>';


        var btn = $('#ajax-customer-btn-create');

        ($(spinnerHtml)).insertAfter(btn);
        btn.attr('disabled','disabled');
        e.preventDefault();
        var url = Routing.generate('lilworks_store_ajax_customer');
        var formSerialize = $(this).serialize();

        $.post(url, formSerialize, function(response) {

            $("#customer-spinner").remove();

            var data = response.data;

            if(response.success === true){
                (!data.companyName)?data.companyName='':null;
                (!data.firstName)?data.firstName='':null;
                (!data.lastName)?data.lastName='':null;
                console.log
                $("#lilworks_storebundle_order_customer").append('<option value="'+data.id+'">'+data.firstName+' '+data.lastName+' '+data.companyName+'</option>')
                $("#lilworks_storebundle_order_customer").val(data.id);
                $("#lilworks_storebundle_order_customer").selectpicker();
                $("#lilworks_storebundle_order_customer").selectpicker("refresh");

            }else{
                alert("error");
            }

            btn.attr('disabled',false);
            $("#customerModal").modal('hide');
        }, 'JSON');
    });




    $( ".sle-col" ).each(function( index ) {
        var data =  $( this ).data('sle-col') ;

        $("#sle_colValue_"+data.colName+"_"+data.rowId).click(function(){
            SLE.formColVisibility($(this));
        });

        $("#sle_form_"+data.colName+"_"+data.rowId).on('submit',function(e){
            SLE.submitCol($(this),e,$(this).attr('formType'),$(this).attr('entityName'),$(this).attr('colName'),$(this).attr('rowId'));
        });

        $("#sle_form_"+data.colName+"_"+data.rowId).find(".sle-cancel").on('click',function(){
            SLE.formColCancel($(this).closest('form').parent());
        });

    });


});

$(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});

$( window ).on( "load", FF.init() );