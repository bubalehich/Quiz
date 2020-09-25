import $ from 'jquery';

var $collectionHolder;

var $addTagButton = $('<button type="button" class="add_answer_link btn btn-success">Add answer</button>');
var $newLinkLi = $('<li></li>').append($addTagButton);

$(document).ready(function () {
    $('input[type="checkbox"]').on('click', function () {
        checkboxEvent($(this));
    });

    $collectionHolder = $('ul.answers');

    $collectionHolder.append($newLinkLi);
    $collectionHolder.data('index', $collectionHolder.find('input').length);

    $('.add_answer_link').on('click', function (e) {
        addTagForm($collectionHolder, $newLinkLi);
        $('input[type="checkbox"]').on('click', function () {
            checkboxEvent($(this));
        });

        if ($collectionHolder.find('input').length === 2) {
            $($('input[type="checkbox"]')['0']).prop('checked', true);
        }
    });

});

function addTagForm($collectionHolder, $newLinkLi) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    var $newFormLi = $('<li class="answer_element"></li>').append(newForm);
    $newLinkLi.before($newFormLi);
    addTagFormDeleteLink($newFormLi);
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger">Delete answer</button>');
    $tagFormLi.append($removeFormButton);

    $removeFormButton.on('click', function (e) {
        $tagFormLi.remove();
    });
}

function checkboxEvent(e) {
    var checkboxes = $('input[type="checkbox"]');
    $(checkboxes).each(function () {
        if (!$(this).is(e)) {
            $(this).prop('checked', false);
        }
    })
    var required = true;
    $(checkboxes).each(function () {
        if ($(this).prop('checked') === true) required = false;
    });
    if (required) $($(checkboxes)['0']).prop('checked', true);
}


