var FF = {
    init: function(){

        var openFilter = Translator.trans('lilworks.filter.open');
        var closeFilter = Translator.trans('lilworks.filter.close');

        FF.$openFilterButton = $('<a href="#" role="button" class="btn btn-sm btn-info"><i class="fa fa-filter" aria-hidden="true"></i> '+openFilter+'</a>');
        FF.$closeFilterButton = $('<a href="#" role="button" class="btn btn-sm  btn-info"><i class="fa fa-window-close" aria-hidden="true"></i> '+closeFilter+'</a>');
        FF.$formFilter = $(".formFilter");



        FF.formFilterHtml = $(".formFilter").html();

        FF.$formFilter.html(this.$openFilterButton);
        FF.setClick();


    },

    setClick: function(){
        FF.$openFilterButton.click(function(){
            FF.$formFilter.html(FF.formFilterHtml);
            FF.$formFilter.prepend(FF.$closeFilterButton);
            FF.$closeFilterButton.click(function(){
                $(this).remove();
                FF.init();
            });
        });
    }

};