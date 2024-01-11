import {
    blockGreeting,
    blockInformation,
    blockShowGame,
    blockMessage,
    blockResult,
    blockResultText,
    blockGameMenu,
    fieldName,
    textInfo,
    numAttempt,
    maxNum,
    makeNumber,
    hiddenNumber,
    var_numAttempt,
	blockList,
	blockTextList
} from './Model.js';

import {
    addNewGame
} from "./db.js";

export let userName;

export function startGame() {
    hideAll();

    blockGreeting.style.display = 'flex';
}

export function informationOutput() {
    if (fieldName.value === "") {
        alert("Enter your name")
    } else {
        userName = fieldName.value;

        hideAll();

        blockInformation.style.display = 'flex';

        textInfo.innerHTML = "Hello, <b>" + userName + "</b>! Let's play the game \"Guess Number\"." +
            "  I guess the number<b> from 1 to " + maxNum + "." +
            "</b> Try to guess this number for <b>" + numAttempt + "</b> attempts.";
    }
}

export function GameMenu(){
    hideAll();

    blockGameMenu.style.display = 'flex';
}

export function showGameOutput() {
    hideAll();

    makeNumber();

    addNewGame(userName, maxNum, numAttempt, hiddenNumber);

    blockShowGame.style.display = 'flex';
}

export function messageOutput(type_message) {
    blockResultText.style.display = "flex";

    if (type_message === "less") {
        blockMessage.style.display = 'flex';
        blockMessage.innerHTML = "Your number is less than the right one. Attempts left: " + var_numAttempt;
    }
    if (type_message === "more") {
        blockMessage.style.display = 'flex';
        blockMessage.innerHTML = "Your number is higher than the right one. Attempts left: " + var_numAttempt;
    }
    if (type_message === "win") {
        blockMessage.style.display = 'none';
        blockShowGame.style.display = 'none';
        blockResult.style.display = 'flex';
        blockResultText.innerHTML = "Victory! You guessed the number " + hiddenNumber +
            " for " + (numAttempt - var_numAttempt + 1) + "th attempt";
    }
    if (type_message === "lost") {
        blockMessage.style.display = 'none';
        blockShowGame.style.display = 'none';
        blockResult.style.display = 'flex';
        blockResultText.innerHTML = "Lose! You didn't guess the number " + hiddenNumber;
    }
}

export function writeAllGame(result){
    hideAll();

    blockList.style.display = 'flex';
    blockTextList.innerHTML = result;
}

function hideAll(){
    blockGreeting.style.display = 'none';
    blockInformation.style.display = 'none';
    blockShowGame.style.display = 'none';
    blockMessage.style.display = 'none';
    blockResult.style.display = 'none';
    blockGameMenu.style.display = 'none';
    blockList.style.display = 'none';
}