import {
    startGame,
    informationOutput,
    showGameOutput,
    GameMenu
} from './View.js';

import {
    btnNextGameMenu,
    btnNextShowGame,
    btnCheckNumber,
    btnReplayGame,
    btnNewGame,
    btnGoMenu,
    checkNumber,
    btnListAllGame,
    btnListWinGame,
    btnListLostGame
} from "./Model.js";

import {getAllGame, getLossGame, getWinGame, startDB} from "./db.js";

startGame();

document.addEventListener("DOMContentLoaded", startDB);
btnNextGameMenu.addEventListener("click", GameMenu);
btnNewGame.addEventListener("click", informationOutput);
btnNextShowGame.addEventListener("click", showGameOutput);
btnCheckNumber.addEventListener("click", checkNumber);
btnReplayGame.addEventListener("click", showGameOutput);
btnGoMenu.addEventListener("click", GameMenu);
btnListAllGame.addEventListener("click", getAllGame);
btnListWinGame.addEventListener("click", getWinGame);
btnListLostGame.addEventListener("click", getLossGame);