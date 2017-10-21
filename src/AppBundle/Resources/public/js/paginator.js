
var Paginator = {
    init:function (url) {

        var selector = '<div><select  class="custom-select" name="maxItemPerPage" id="maxItemPerPage">'+
            '<option selected="true" style="display:none;">Number Per Page</option>'+
            '<option id="1">1</option>'+
            '<option id="5">5</option>'+
            '<option id="10">10</option>'+
            '<option id="25">25</option>'+
            '<option id="50">50</option>'+
            '<option id="100">100</option>'+
            '<option id="250">250</option>'+
            '<option id="500">500</option>'+
            '<option id="1000">1000</option>'+


            '</select></div>';


        $( selector ).insertAfter( ".paginator" );

        $('#maxItemPerPage').change(function(){

            var item = $('#maxItemPerPage').find(":selected").text();
            jQuery(location).attr('href', url.replace('_itemNum',item ));

        });
    }
}