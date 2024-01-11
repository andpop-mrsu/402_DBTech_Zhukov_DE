import {
    messageOutput
} from "./View.js";

import {
    addAttempt,
    editGame
} from "./db.js";

export const
    blockGreeting = document.getElementById("greeting"),
    blockInformation = document.getElementById("information"),
    blockShowGame = document.getElementById("show-game"),
    blockMessage = document.getElementById("message"),
    blockResult = document.getElementById("result"),
    blockResultText = document.getElementById("resultText"),
    blockGameMenu = document.getElementById("GameMenu"),
    blockList = document.getElementById("list"),
    blockTextList = document.getElementById("textList");

export const
    btnGoMenu = document.getElementById("goMenu"),
    btnNextGameMenu = document.getElementById("nextInfo"),
    btnNextShowGame = document.getElementById("nextShowGame"),
    btnCheckNumber = document.getElementById("checkNumber"),
    btnReplayGame = document.getElementById("replayGame"),
    btnNewGame = document.getElementById("newGame"),
    btnListAllGame = document.getElementById("listAllGame"),
    btnListWinGame = document.getElementById("listWinGame"),
    btnListLostGame = document.getElementById("listLostGame");

export const
    fieldName = document.getElementById("name"),
    field_getNumber = document.getElementById("getNumber");

export const
    textInfo = document.getElementById("info");

export const
    numAttempt = 10,
    maxNum = 100;

export let
    var_numAttempt = numAttempt,
    hiddenNumber;

let getNumber;

export function checkNumber() {
    if (field_getNumber.value === "") {
        alert("Enter the number");
    } else {
        getNumber = Number(field_getNumber.value);
        if (var_numAttempt !== 1) {
            if (getNumber < hiddenNumber) {
                var_numAttempt--;
                messageOutput("less");
                addAttempt((numAttempt - var_numAttempt), getNumber, "less");
            }
            if (getNumber > hiddenNumber) {
                var_numAttempt--;
                messageOutput("more");
                addAttempt((numAttempt - var_numAttempt), getNumber, "more");
            }
            if (getNumber === hiddenNumber) {
                messageOutput("win");
                addAttempt((numAttempt - var_numAttempt), getNumber, "win");
                editGame("win");
            }
        } else {
            messageOutput("lost");
            editGame("lost");
            addAttempt((numAttempt - var_numAttempt + 1), getNumber, "loss");
        }
    }
}

export function makeNumber() {
    hiddenNumber = Math.floor(Math.random() * (maxNum - 1)) + 1;
    var_numAttempt = numAttempt;
}