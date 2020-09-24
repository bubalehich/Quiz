import '../css/admin.css';
import p from "bootstrap/js/src/alert";
import $ from 'jquery';
$('document').ready(function() {
    $('.link_block span').on('click', function () {
        if ($('.links').css('display') === "none") {
            $('.links').css('display', 'flex');
            $('.links').css('flex-direction', 'column');
        } else {
            $('.links').css('display', 'none');
        }
    });
});