window.onAnswer = function (answer, locale) {
    showIsRightAnswer(answer, locale);
    showButton();
}
let content;

function showIsRightAnswer(answer, locale) {
    const rbs = document.querySelectorAll('input[type="radio"]');
    let selectedRadio;
    for (const rb of rbs) {
        if (rb.checked) {
            selectedRadio = rb;
        } else rb.disabled = true;
    }
    let msg;
    if (selectedRadio.value == answer) {
        msg = locale === "ru" ? "Правильно!" : "Right!";
        content = "<span style=\'color: #23bf5d\'>" + msg + "</span>";
    } else {
        msg = locale === "ru" ? "Неправильно!" : "Wrong!";
        content = "<span style=\'color:darkred\'>" + msg + "</span>";
    }
    document.getElementById("isCorrect").innerHTML = content;
}

function showButton() {
    document.getElementById("sub").innerHTML = "<button type=\"submit\">Next question</button>";
}