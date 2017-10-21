$( function() {
    var hash = window.location.hash.substr(1);
    if(hash != ''){
        var hashname = "#"+hash;
        $('div a[href="'+hashname+'"]').tab('show');
    }
} );