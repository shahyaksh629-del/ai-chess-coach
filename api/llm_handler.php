<?php
// 1. Include your hidden API key
require_once '../config.php';

// 2. Accept the incoming request from your JavaScript
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$fen = $data['fen'] ?? '';

if (!$fen) {
    echo json_encode(['reply' => 'Error: No board state received.']);
    exit;
}

// 3. The System Prompt 
$prompt = "You are an expert, ruthless chess coach. The current board state in FEN notation is: " . $fen . " " .
          "Analyze the position for White. Tell the user exactly what piece to move next and why. " .
          "Keep your response to 2 or 3 short, punchy sentences. Be direct. Do not use complex markdown, just use basic text.";

// 4. The API Endpoint for Gemini 3.5 Flash
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=" . GEMINI_API_KEY;

// 5. Structure the JSON payload
$post_data = [
    "contents" => [
        ["parts" => [["text" => $prompt]]]
    ]
];

// 6. Initialize cURL to send the POST request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));

// Bypass local XAMPP SSL verification issues
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

// 7. Execute the call
$response = curl_exec($ch);

// If the server connection completely fails
if(curl_errno($ch)){
    echo json_encode(['reply' => 'cURL System Error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

// 8. Decode the response into an associative array
$response_data = json_decode($response, true);

// 9. Bulletproof check to extract the text block
if (json_last_error() === JSON_ERROR_NONE && isset($response_data['candidates']['content']['parts']['text'])) {
    
    $ai_text = $response_data['candidates']['content']['parts']['text'];
    
    // Clean up any stray markdown characters if the AI accidentally sent them
    $ai_text = str_replace(['```json', '```'], '', $ai_text);
    
    echo json_encode(['reply' => trim($ai_text)]);
    
}  else {
    // Fallback parser in case standard nested arrays hiccup
    if (preg_match('/"text"\s*:\s*"(.*?)"/s', $response, $matches)) {
        // Fix: Use $matches explicitly since it is the extracted string, not the array
        $clean_text = stripslashes($matches);
        $clean_text = str_replace('\n', '<br>', $clean_text);
        echo json_encode(['reply' => $clean_text]);
    } else {
        echo json_encode([
            'reply' => 'Parser Error: Could not extract message. JSON Error: ' . json_last_error_msg() . '<br><br>Raw text: ' . htmlspecialchars($response)
        ]);
    }
}

?>