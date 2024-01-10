document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('start_game').addEventListener('click', function () {
        document.getElementById('start_page').style.display = 'none';
        document.getElementById('game_page').style.display = 'block';
        document.getElementById('rules').style.display = 'none';
        document.getElementById('input_num').value = '';

        Model.reset_game();
        Model.rand_gen();
    });

    document.getElementById('check_answer').addEventListener('click', function () {
        let user_input = parseInt(document.getElementById('input_num').value);
        let num = Model.guess_num(user_input);
    });

    document.getElementById('leave_game').addEventListener('click', function () {
        document.getElementById('start_page').style.display = 'block';
        document.getElementById('game_page').style.display = 'none';
        document.getElementById('rules').style.display = 'none';
        document.getElementById('input_num').value = '';

        Model.reset_game();
    });

    document.getElementById('display_rules').addEventListener('click', function () {
        document.getElementById('rules').style.display = 'block';
    });
});