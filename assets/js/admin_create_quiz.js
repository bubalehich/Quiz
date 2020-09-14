import $ from 'jquery';

var selected = $('.selected_questions');
$('document').ready(function () {
    $('.question').on('click', function () {
        if ($(this)['0'].parentElement.className === "available_questions") {
            $(selected).html($(selected).html()+$(this)['0'].innerHTML);
        }
        $(this).css('display', 'none');
    });

    $('#clear').on('click',function (){
        $('.available_questions div').each(function(){
            $(this).css('display','block');
        });
        $('.selected_questions').html('');
    });

});
