<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chess Coach</title>
    
    <link rel="stylesheet" href="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.css">
    
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>AI Chess Coach - Queen's Gambit Trainer</h2>
    
    <div class="app-container">
        <div id="board"></div>

        <div class="coach-panel">
            <div class="coach-header">System Coach: Online</div>
            <div id="coach-text" class="coach-message">
                Welcome. We are learning the Queen's Gambit today. <br><br>
                Your goal is to dominate the center. <strong>Play d4.</strong>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.3/chess.min.js"></script>
    <script src="assets/app.js?v=5"></script>
</body>
</html>