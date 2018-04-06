
import $ from 'jquery';

import '../css/app.scss';


var fa = require("fontawesome");
require('font-awesome/css/font-awesome.css');

require('popper.js');



require('bootstrap');
require('bootstrap-select');
require('bootstrap-select/dist/js/i18n/defaults-fr_FR.js');
require('bootstrap-select/dist/css/bootstrap-select.css');

require('ekko-lightbox');
require('ekko-lightbox/dist/ekko-lightbox.css');
var Translator = require('bazinga-translator');

require("bootstrap-slider");
require("bootstrap-slider/dist/css/bootstrap-slider.min.css");

require("jquery-confirm");
require('jquery-confirm/dist/jquery-confirm.min.css');

require("slick-carousel");
require('slick-carousel/slick/slick.css');
require('slick-carousel/slick/slick-theme.css');

require("jssocials");
require("jssocials/dist/jssocials.css");
require("jssocials/dist/jssocials-theme-minima.css");

var FCE = require('./formCollectionEditor.js');


$(document).ready(function() {
    $('.carousel-home').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 1
    });
});

$(document).ready(function() {
    $("#share").jsSocials({
        shares: ["twitter", "facebook", "googleplus", "pinterest"]
    });
});



$( document ).ready(function() {

    $(".selectpicker").selectpicker({
        iconBase: 'fa',
        tickIcon: 'fa-check'
    });
    $(".selectpicker").selectpicker("refresh");
    $(".price-range").slider({
        formatter: function(value) {
            return  value + "€";
        }
    })

    $('.tooltip').css({ 'margin-top': '18px' });
    $('.tooltip').css({ opacity: 1 });
    $('.tooltip').css({ 'z-index': 2 });
    $('.tooltip').css({ 'font-size': '12px' });



    FCE.init('addresses','addresses list-inline','addresse list-inline-item');
    FCE.init('phonenumbers','phonenumbers list-inline','phonenumber list-inline-item');
});

$(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});