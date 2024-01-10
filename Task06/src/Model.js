let Model = {
    max_num: 100,
    attempt_num: 10,
    rand_num: 0,
    current_attempt: 0,

    rand_gen: function () {
        this.rand_num = Math.floor(Math.random() * this.max_num) + 1;
    },

    reset_game: function () {
        this.current_attempt = 0;
        this.rand_num = 0;
    },

    guess_num: function (user_input) {
        if (this.current_attempt >= this.attempt_num) {
            View.Message("Вы проиграли! Попыток больше нет. Число было " + this.rand_num);
            return;
        }
        if (user_input === this.rand_num) {
            View.Message("Поздравляем! Вы угадали число!");
            return;
        } else {
            if (user_input > this.rand_num) {
                View.Message("Загаданное число меньше!");
            } else if (user_input < this.rand_num) {
                View.Message("Загаданное число больше!");
            }
            this.current_attempt += 1;
        }
    }
};
    