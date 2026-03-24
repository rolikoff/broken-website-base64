<?php
/**
 * QuickReport Template Renderer
 *
 * VULNERABILITY: This file uses eval() to process base64-decoded user input.
 * This is intentionally vulnerable for security testing purposes.
 *
 * The "legitimate" use case: base64 encoding allows templates containing
 * HTML, special characters, and PHP expressions to be safely transported
 * via HTTP POST without encoding issues. The server decodes and evaluates
 * the template to render dynamic content.
 *
 * The vulnerability: an attacker can base64-encode arbitrary PHP code
 * and the server will execute it via eval().
 */

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed. Use POST.";
    exit;
}

$encodedTemplate = $_POST['template'] ?? '';

if (empty($encodedTemplate)) {
    http_response_code(400);
    echo "No template provided.";
    exit;
}

// Decode the base64 template
$template = base64_decode($encodedTemplate, true);

if ($template === false) {
    http_response_code(400);
    echo "Invalid base64 encoding.";
    exit;
}

// Set up template variables that users can reference
$author = $_POST['author'] ?? 'Anonymous';
$department = $_POST['department'] ?? 'General';
$company = 'Acme Corp';
$year = date('Y');

// --- NO GUARD — eval() on raw decoded input ---

ob_start();

try {
    eval('?>' . $template);
} catch (Throwable $e) {
    ob_end_clean();
    echo "<div style='color:#c0392b;'>Template rendering error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

$rendered = ob_get_clean();
echo $rendered;
