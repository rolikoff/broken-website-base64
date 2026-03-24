<?php
/**
 * CalcAPI - Expression Evaluator
 *
 * Accepts a base64-encoded math expression via POST, decodes it,
 * and evaluates it with eval(). Intended for expressions like:
 *   (12 + 8) * 3 / 2
 *   sqrt(144) + pow(2, 10)
 *
 * VULNERABILITY: eval() executes arbitrary PHP, not just math.
 * An attacker can base64-encode any PHP code and it will run.
 */

header('Content-Type: text/plain; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "POST only.";
    exit;
}

$encoded = $_POST['expr'] ?? '';

if (empty($encoded)) {
    http_response_code(400);
    echo "No expression provided.";
    exit;
}

$expr = base64_decode($encoded, true);

if ($expr === false) {
    http_response_code(400);
    echo "Invalid base64.";
    exit;
}

// Evaluate the expression and return the result
try {
    $result = eval('return ' . $expr . ';');
    echo $result;
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
