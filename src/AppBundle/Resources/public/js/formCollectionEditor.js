/*
 * FormCollectionEditor
 */
var FCE = {
    init: function(name,ulClass,liClass){

        var oFCE = this;

        this.name = name;
        this.ulClass = ulClass;
        this.liClass = liClass;

        // setup an "add a tag" link
        var $addLink = $('<a href="#" class="add_'+name+'_link btn btn-success btn-sm"><i class="fa fa-plus" ></i></a>');
        var $newLinkLi = $('<li></li>').append($addLink);

        // Get the ul that holds the collection of tags
        var $collectionHolder = $('ul.'+this.name);



        // add a delete link to all of the existing tag form li elements
        $collectionHolder.find('li').each(function() {
            oFCE.addTagFormDeleteLink($(this));
        });

        // add the "add a tag" anchor and li to the tags ul
        $collectionHolder.append($newLinkLi);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $addLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see code block below)
            oFCE.addTagForm($collectionHolder, $newLinkLi);
        });


    },


    addTagForm: function ($collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        // Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $('<li class="'+this.liClass+'"></li>').append(newForm);


        // also add a remove button, just for this example
        $newFormLi.append('<a href="#" class="remove-tag btn btn-warning btn-sm"><i class="fa fa-minus" ></i></a>');

        $newLinkLi.before($newFormLi);

        // handle the removal, just for this example
        $('.remove-tag').click(function(e) {
            e.preventDefault();

            $(this).parent().remove();

            return false;
        });

    },

    addTagFormDeleteLink:function($formLi) {

        var $removeFormA = $('<a href="#" class="remove-tag btn btn-warning btn-sm"><i class="fa fa-minus" ></i></a>');
        $formLi.append($removeFormA);

        $removeFormA.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // remove the li for the tag form
            $formLi.remove();
        });
    },

    ulClass:  'list-inline',
    liClass:  'list-inline-item',
    name:null
};