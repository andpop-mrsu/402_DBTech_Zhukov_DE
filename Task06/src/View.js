let View = {
    Message: function (msg) {
        let msg_elem = document.getElementById('result');
        let attempt_elem = document.getElementById('attempt_num');


        msg_elem.innerText = msg;
        attempt_elem.innerText = "Попыток осталось: " + (Model.attempt_num - Model.current_attempt);

    }
}