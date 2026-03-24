<?php
/**
 * CalcAPI - Expression Evaluator
 *
 * Accepts a base64-encoded math expression via POST, decodes it,
 * and evaluates it with eval(). Intended for expressions like:
 *   (12 + 8) * 3 / 2
 *
 * VULNERABILITY: eval() executes arbitrary PHP, not just math.
 * An attacker can base64-encode any PHP code and it will run.
 */

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

// Evaluate the expression
ob_start();
try {
    $result = eval('return ' . $expr . ';');
} catch (Throwable $e) {
    $result = null;
    $error = $e->getMessage();
}
$output = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CalcAPI - Result</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; color: #333; }
        .header { background: #2c3e50; color: white; padding: 1rem 2rem; }
        .header h1 { font-size: 1.4rem; }
        .header small { color: #95a5a6; }
        .container { max-width: 700px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 1rem; font-size: 1.1rem; color: #2c3e50; }
        .result { padding: 1rem; background: #fafafa; border: 1px solid #eee; border-radius: 4px; font-family: monospace; font-size: 1.1rem; white-space: pre-wrap; }
        .expr { color: #888; font-family: monospace; margin-bottom: 0.75rem; }
        .error { color: #c0392b; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CalcAPI</h1>
        <small>Server-side Expression Evaluator</small>
    </div>
    <div class="container">
        <div class="card">
            <h2>Result</h2>
            <div class="expr">Expression: <?= htmlspecialchars($expr) ?></div>
            <div class="expr">Base64: <?= htmlspecialchars($encoded) ?></div>
            <div class="result"><?php
                if (isset($error)) {
                    echo '<span class="error">Error: ' . htmlspecialchars($error) . '</span>';
                } elseif ($output) {
                    echo htmlspecialchars($output);
                } else {
                    echo htmlspecialchars((string)$result);
                }
            ?></div>
        </div>
        <p><a href="./">&larr; Back</a></p>
    </div>
</body>
</html>
