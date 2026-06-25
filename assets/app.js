// assets/app.js - Full Clean Slate Architecture

// 1. Initialize the Chess.js game engine
const game = new Chess();
const coachText = document.getElementById('coach-text') || document.querySelector('.coach-panel-text');

// 2. Function to pass the board position to your working PHP engine
function getCoachAnalysis() {
    if (!coachText) return;
    
    coachText.innerHTML = "Coach is thinking...";

    // We use the direct relative path to hit your working API folder
    fetch('api/llm_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ fen: game.fen() })
    })
    .then(async response => {
        // Grab the raw response text before attempting a JSON parse
        const rawText = await response.text();
        try {
            // Attempt to parse it as clean JSON
            const data = JSON.parse(rawText);
            coachText.innerHTML = data.reply;
        } catch (e) {
            // If the JSON parsing breaks, dump the exact raw text to the console to see what PHP sent
            console.error("JSON Parse Failed. Raw server text:", rawText);
            coachText.innerHTML = "<span style='color: #ff5252;'>System Error: Check F12 Console.</span>";
        }
    })
    .catch(error => {
        console.error("Fetch request failed completely:", error);
        coachText.innerHTML = "<span style='color: #ff5252;'>Network Error: Check F12 Console.</span>";
    });
}

// 3. Handle piece movement mechanics
function onDragStart(source, piece, position, orientation) {
    // Prevent moving pieces if the game is over or it's not White's turn
    if (game.game_over() || piece.search(/^b/) !== -1) return false;
}

function onDrop(source, target) {
    // Attempt the move inside the engine
    const move = game.move({
        from: source,
        to: target,
        promotion: 'q' // Auto-promote to queen for simplicity
    });

    // If illegal move, snap piece back
    if (move === null) return 'snapback';

    // Move is valid! Trigger the API communication right away
    getCoachAnalysis();
    
    // Simulate automatic response for Black if training specific openings
    window.setTimeout(makeBlackMove, 250);
}

function onSnapEnd() {
    board.position(game.fen());
}

// 4. Basic AI response generation for Black
function makeBlackMove() {
    const possibleMoves = game.moves();
    if (possibleMoves.length === 0) return;

    // Look for standard Queen's Gambit responses or play a random move
    const randomIdx = Math.floor(Math.random() * possibleMoves.length);
    game.move(possibleMoves[randomIdx]);
    board.position(game.fen());
}

// 5. Initialize the visual Chessboard configuration
const config = {
    draggable: true,
    position: 'start',
    onDragStart: onDragStart,
    onDrop: onDrop,
    onSnapEnd: onSnapEnd,
    pieceTheme: 'https://chessboardjs.com/img/chesspieces/wikipedia/{piece}.png'
};

const board = Chessboard('board', config);