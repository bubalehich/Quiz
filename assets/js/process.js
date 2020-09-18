import '../css/quiz.css';

window.onAnswer = function (answer, locale) {
    if (showIsRightAnswer(answer, locale))
        showButton();
}
let content;

function showIsRightAnswer(answer, locale) {
    const rbs = document.querySelectorAll('input[type="radio"]');
    let flag = false;
    let selectedRadio;
    let msg;

    for (const rb of rbs) {
        if (rb.checked) {
            selectedRadio = rb;
            flag = true;
        } else rb.disabled = true;
    }
    if (!flag) {
        for (const rb of rbs)
            rb.disabled = false;
        msg = locale === "ru" ? "Выберите ответ!" : "Choose an answer!";
        content = "<span style=\'color: #ffbf00\'>" + msg + "</span>";
        document.getElementById("isCorrect").innerHTML = content;
        return false;
    }
    if (selectedRadio.value == answer) {
        msg = locale === "ru" ? "Правильно!" : "Right!";
        content = "<span style=\'color: #23bf5d\'>" + msg + "</span>";
    } else {
        msg = locale === "ru" ? "Неправильно!" : "Wrong!";
        content = "<span style=\'color:#b03c3c\'>" + msg + "</span>";
    }
    document.getElementById("isCorrect").innerHTML = content;
    return true;
}

function showButton() {
    document.getElementById("sub").innerHTML = "<button type=\"submit\">Next question</button>";
    document.getElementById("answer").innerHTML = "";
}