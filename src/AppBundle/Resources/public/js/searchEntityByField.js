var SEF = {


    init: function(target,inputName,multiple,entity,field){

        var self = this;
        this.multiple = multiple;
        this.inputName = inputName;
        this.target = target;
        this.entity = entity;
        this.field = field;

        this.checked = [];

        $("#"+this.target+" :input[type=checkbox]:checked").each(function(){
            self.checked.push($(this).attr('id'))
        });
        //$("#"+this.target).hide();

        //return this;
        $( self.getSearchField(self.target+'Search') ).insertBefore( $( "#"+target ) );

        if(self.setInitialData()){
            self.start();
        }
    },
    setInitialData: function(){
        var self = this;

        if(self.multiple == 1){
            $("#"+self.target + " :checkbox").each(function(){
                self.addMultiple($(this).attr('value'),$(this).parent().text());
            });
        }else{
            $("#"+self.target+" option:selected").each(function(){
                self.addSingle($(this).attr('value'),$(this).text());
            });
        }



        return true;
    },
    getMatching: function(searchValue){
        var self = this;
        var url = Routing.generate('searchEntityByFieldAction',{
            'entity': self.entity,
            'field':  self.field,
            'searchValue': searchValue,
            'exclude':self.checked
        });

        $.ajax({
            method: 'post',
            url: url,
            data: null
        })
            .done(function (response) {
                $("#"+self.id+"_results").empty();
                self.showResults(response);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                alert("SEF AJAX ERROR");
            });
    },
    showResults: function(results){
        var self = this;

        $.each(results, function(k, v) {
            if(v.length === 0){
                var nothingFound = Translator.trans('lilworks.sef.nothingfound');
                var html = '<span style="color: red;">'+nothingFound+'</span>' ;
                $("#"+self.id+"_results").append(html);
            }
            $.each(v, function(k2, v2) {
                var html = '<button id="'+self.id+'_searchResult'+k2+'" targetId="'+v2.id+'" targetName="'+v2.name+'" type="button" class="btn btn-sm btn-outline-secondary"><i class="fa fa-plus" aria-hidden="true"></i> '+v2.name+'</button>';
                $("#"+self.id+"_results").append(html);

                $('#'+self.id+'_searchResult'+k2).click(function() {
                    $(this).remove();
                    self.add( $(this).attr( "targetId" ) , $(this).attr( "targetName" ));
                });

            });
        });
    },

    addMultiple: function(id,name){
        var self = this;
        if(  $('#'+self.id+'_selectedResult'+id).length === 0 ){
            var html = '<button id="'+self.id+'_selectedResult'+id+'" targetId="'+id+'" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-minus" aria-hidden="true"></i> '+name+'</button>';
            $("#"+this.id+"_selected").append(html);
            self.checked.push(id);
            if($("#"+self.target+"_"+id).length === 0){
                $("#"+self.target).append('<input name="'+self.inputName+'[]" type="checkbox" id="'+self.target+'_'+id+'" value="'+id+'">');
            }
            $("#"+self.target+" :checkbox[value="+id+"]").prop("checked","true");
            $('#'+self.id+'_selectedResult'+id).click(function() {
                $(this).remove();

                $("#"+self.target+" :checkbox[value="+id+"]").prop("checked",false);

                self.checked = jQuery.grep(self.checked, function(value) {
                    return value != id;
                });
            });
        }
    },
    addSingle: function(id,name){
        var self = this;

        $("#"+this.id+"_selected").empty();
        var html = '<button id="'+self.id+'_selectedResult'+id+'" targetId="'+id+'" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-minus" aria-hidden="true"></i> '+name+'</button>';

        $("#"+this.id+"_selected").append(html);

        self.checked.push(id);

        $("#"+self.target+" option:selected").prop("selected", false);


        $("#"+self.target).append('<option value="'+id+'">'+name+'</option>');



            $("#"+self.target+" option[value="+id+"]").prop('selected', 'selected');

            $('#'+self.id+'_selectedResult'+id).click(function() {
                $(this).remove();

                $("#"+self.target+" :checkbox[value="+id+"]").prop("checked",false);

                self.checked = jQuery.grep(self.checked, function(value) {
                    return value != id;
                });
            });

    },
    add:function(id,name){
        var self = this;

        if(self.multiple == 1){
            self.addMultiple(id,name);
        }else{
            self.addSingle(id,name);
        }
    },
    getSearchField: function(id){

        var search = Translator.trans('lilworks.sef.search');

        this.id = id;
        var htmlInput = '<label class="sr-only" for="'+id+'">'+search+'</label>'+
            '<div class="input-group mb-2 mr-sm-2 mb-sm-0">'+
            '<div class="input-group-addon">?</div>'+
            '<input type="text" class="form-control" id="'+id+'" placeholder="'+search+'">'+
            '</div>';

        htmlInput = htmlInput + '<div id="'+id+'_results"></div>';
        htmlInput = htmlInput + '<div id="'+id+'_selected"></div>';
        return htmlInput;
    },
    start: function(){


        var self = this;


        var delay = (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();


        $("#"+this.id).keyup(function() {
            var val = $(this).val();
            delay(function(){
                if(val.length === 0){
                    $("#"+self.id+"_results").empty();
                }else{
                    self.getMatching(val);
                }
            }, 800 );


        });
    }


}
