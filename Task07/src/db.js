import {
    writeAllGame
} from "./View.js";

let idbSupported = false;
let db;

let dbName = "guess-number";
let dbVersion = 1;

let game,
    idLastGame;

export function startDB() {
    if ("indexedDB" in window) {
        idbSupported = true;
    }

    if (idbSupported) {
        const openRequest = indexedDB.open(dbName, dbVersion);

        openRequest.onupgradeneeded = function (e) {
            console.log("DataBase is updating, please wait...");
            const thisDB = e.target.result;

            if (!thisDB.objectStoreNames.contains("gamesinfo")) {
                thisDB.createObjectStore("gamesinfo", {autoIncrement: true}).createIndex('idxOutcome', 'outcome');
            }

            if (!thisDB.objectStoreNames.contains("attempts")) {
                thisDB.createObjectStore("attempts", {autoIncrement: true}).createIndex('idx', 'idGame');
            }
        }

        openRequest.onsuccess = function (e) {
            console.log("Successeful update!");
            db = e.target.result;
        }

        openRequest.onerror = function (e) {
            console.log("Error");
            console.dir(e);
        }
    }
}

export function addNewGame(name, maxNum, numAttempt, hiddenNumber) {
    const transaction = db.transaction(["gamesinfo"], "readwrite");
    const store = transaction.objectStore("gamesinfo");

    game = {
        date: new Date().toLocaleDateString(),
        userName: name,
        maxNumber: maxNum,
        numAttempt: numAttempt,
        hiddenNumber: hiddenNumber,
        outcome: null
    }

    let request = store.add(game);

    request.onerror = function (e) {
        console.log("Error", e.target.error.name, ":", e.target.error);
    }

    request.onsuccess = function (e) {
        console.log("New game entry added!");
    }

    getLastIdGame()
}

export function getLastIdGame() {
    const transaction = db.transaction(["gamesinfo"], "readonly");
    const store = transaction.objectStore("gamesinfo");

    let request = store.openCursor(null, "prev");

    request.onsuccess = function (event) {
        let cursor = request.result;

        idLastGame = cursor.key;
    }
}

export function editGame(outcome) {
    const transaction = db.transaction(["gamesinfo"], "readwrite");
    const store = transaction.objectStore("gamesinfo");

    game.outcome = outcome;

    let request = store.put(game, idLastGame);

    request.onerror = function (e) {
        console.log("Error", e.target.error.name, ":", e.target.error);
    }

    request.onsuccess = function (e) {
        console.log("Game " + idLastGame + " is modyfied!");
    }

}

export function addAttempt(numAttempt, number, ansComp) {
    const transaction = db.transaction(["attempts"], "readwrite");
    const store = transaction.objectStore("attempts");

    let attempt = {
        idGame: idLastGame,
        numAttempt: numAttempt,
        getNumber: number,
        ansComp: ansComp
    };

    let request = store.add(attempt);

    request.onerror = function (e) {
        console.log("Error", e.target.error.name, ":", e.target.error);
    }

    request.onsuccess = function (e) {
        console.log("Attempt entry added!");
    }

}

export function getAllGame() {
    getGame("all");
}

export function getWinGame() {
    getGame("win");
}

export function getLossGame() {
    getGame("lost");
}

async function getGame(out) {

    const gamesinfo = db.transaction(["gamesinfo"], "readonly");
    const store_gamesinfo = gamesinfo.objectStore("gamesinfo");

    const index = store_gamesinfo.index("idxOutcome");

    let cursor_gamesinfo;

    if (out === "all") {
        cursor_gamesinfo = await store_gamesinfo.openCursor();
    } else {
        cursor_gamesinfo = await index.openCursor(out);
    }

    let result = "<table><tr>" +
        "<th>NickName</th>" +
        "<th>Max num</th>" +
        "<th>Num of tries</th>" +
        "<th>Guess number</th>" +
        "<th>Outcome</th>" +
        "<th>Date</th>" +
        "<th></th></tr>";

    cursor_gamesinfo.onsuccess = async function (e) {
        let res = e.target.result;

        if (res) {

            result += "<tr>" +
                "<td>" + res.value.userName + "</td>" +
                "<td>" + res.value.maxNumber + "</td>" +
                "<td>" + res.value.numAttempt + "</td>" +
                "<td>" + res.value.hiddenNumber + "</td>" +
                "<td>" + res.value.outcome + "</td>" +
                "<td>" + res.value.date + "</td>" +
                "<td><button>Run</button></td>" +
                "</tr>";

            result += await getAttempts(res.key);

            res.continue();
        } else {
            result += "</table>";
            writeAllGame(result);
        }
    }
}

let resGetAttempts;

function getAttempts(id) {
    const attempts = db.transaction(["attempts"], "readonly");
    const store_attempts = attempts.objectStore("attempts");
    const gameIndex = store_attempts.index("idx");

    let request = gameIndex.getAll(id);

    request.onsuccess = function () {
        if (request.result !== undefined) {
            resGetAttempts = "<tr>" +
                "<th>&raquo;</th>" +
                "<th colspan=\"2\" style='background-color: #8a2be2;'>№</th>" +
                "<th colspan=\"2\" style='background-color: #8a2be2;'>Number</th>" +
                "<th colspan=\"2\" style='background-color: #8a2be2;'>Answer</th>" +
                "</tr>";
            request.result.forEach(function (entry) {
                resGetAttempts += "<tr>" +
                    "<td>&raquo;</td>" +
                    "<td colspan=\"2\" style='background-color: #8a2be2;'>" + entry.numAttempt + "</td>" +
                    "<td colspan=\"2\" style='background-color: #8a2be2;'>" + entry.getNumber + "</td>" +
                    "<td colspan=\"2\" style='background-color: #8a2be2;'>" + entry.ansComp + "</td>" +
                    "</tr>";
            });
        } else {
            console.log("No attempts!");
        }
    };

    return resGetAttempts;
}