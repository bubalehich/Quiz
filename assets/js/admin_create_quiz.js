import '../css/admin_create_quiz.css';

var $collectionHolder;

var $addTagButton = $('<button type="button" class="add_question_link btn btn-info">Add question</button>');
var $newLinkLi = $('<li></li>').append($addTagButton);

$(document).ready(function () {

    $collectionHolder = $('ul.questions');

    $collectionHolder.append($newLinkLi);
    $collectionHolder.data('index', $collectionHolder.find('input').length);

    $('.add_question_link').on('click', function (e) {
        addTagForm($collectionHolder, $newLinkLi);
    });

});

function addTagForm($collectionHolder, $newLinkLi) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    var $newFormLi = $('<li class="question_element"></li>').append(newForm);
    $newLinkLi.before($newFormLi);
    addTagFormDeleteLink($newFormLi);
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger">Delete question</button>');
    $tagFormLi.append($removeFormButton);

    $removeFormButton.on('click', function (e) {
        $tagFormLi.remove();
    });
}
