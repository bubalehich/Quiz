window.onAnswer = function (answer) {
    showIsRightAnswer(answer);
    showButton();
}
let content;

function showIsRightAnswer(answer) {
    const rbs = document.querySelectorAll('input[type="radio"]');
    let selectedRadio;
    for (const rb of rbs) {
        if (rb.checked) {
            selectedRadio = rb;
        } else rb.disabled = true;
    }
    if (selectedRadio.value == answer) {
        content = "<span style=\'color: #23bf5d\'>Right</span>";
    } else {
        content = "<span style=\'color:darkred\'>Wrong</span>";
    }
    document.getElementById("isCorrect").innerHTML = content;
}

function showButton() {
    document.getElementById("sub").innerHTML = "<button type=\"submit\">Next question</button>";
}