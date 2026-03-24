<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CalcAPI - Expression Evaluator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 140%; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; color: #333; }
        .header { background: #2c3e50; color: white; padding: 1rem 2rem; }
        .header h1 { font-size: 1.4rem; }
        .header small { color: #95a5a6; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 1rem; font-size: 1.1rem; color: #2c3e50; }
        input[type=text] { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; font-size: 1rem; }
        .btn { padding: 0.6rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem; margin-top: 0.75rem; margin-right: 0.5rem; }
        .btn-primary { background: #3498db; color: white; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn:hover { opacity: 0.9; }
        .info { background: #eaf4fd; border-left: 3px solid #3498db; padding: 0.75rem; margin-bottom: 1rem; font-size: 0.9rem; }
        .result { padding: 1rem; background: #fafafa; border: 1px solid #eee; border-radius: 4px; font-family: monospace; font-size: 1.1rem; min-height: 44px; white-space: pre-wrap; }
        .b64 { margin-top: 0.5rem; color: #888; font-size: 0.8rem; font-family: monospace; word-break: break-all; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CalcAPI</h1>
        <small>Server-side Expression Evaluator</small>
    </div>

    <div class="container">
        <div class="card">
            <h2>About</h2>
            <div class="info">
                Expressions are <strong>base64-encoded</strong> before sending to avoid issues
                with special characters (<code>+</code>, <code>*</code>, <code>/</code>,
                <code>&lt;</code>, <code>&gt;</code>) in URL and form encoding.
                The server decodes and evaluates the expression.
            </div>
        </div>

        <div class="card" style="background:#fff8f0; border-left: 3px solid #e74c3c;">
            <h2 style="color:#c0392b;">&#9888; What happens on the server</h2>
            <p style="font-size:0.9rem; line-height:1.6;">
                The server receives the base64-encoded string, decodes it, and passes the result
                directly into PHP's <code>eval()</code>:
            </p>
            <pre style="background:#2c3e50; color:#ecf0f1; padding:0.75rem; border-radius:4px; margin:0.75rem 0; font-size:0.85rem; overflow-x:auto;">$expr = base64_decode($_POST['expr']);
$result = eval('return ' . $expr . ';');</pre>
            <p style="font-size:0.9rem; line-height:1.6;">
                This is intended for math like <code>2 + 2</code>, but since <code>eval()</code>
                executes <strong>any</strong> PHP code, an attacker can send something like
                <code>system('ls /')</code> and the server will run it as a shell command.
                The base64 encoding hides the malicious payload from simple text-based filters.
            </p>
        </div>

        <div class="card">
            <h2>Enter Expression</h2>
            <form method="POST" action="eval.php">
                <input type="text" id="expr" placeholder="e.g. 2 + 2" value="2 + 2">
                <input type="hidden" name="expr" id="expr_b64">
                <div class="b64">Encoded: <span id="b64preview"></span></div>
                <button type="submit" class="btn btn-primary">Evaluate</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('expr').value='system(\'cd ../ && ls \')';updatePreview();" style="background:#e74c3c; color:white;">🏴‍☠️ PURELY FOR DEMO</button>
            </form>
        </div>
    </div>

    <script>
        function updatePreview() {
            const expr = document.getElementById('expr').value;
            const encoded = btoa(expr);
            document.getElementById('b64preview').textContent = encoded;
            document.getElementById('expr_b64').value = encoded;
        }

        document.getElementById('expr').addEventListener('input', updatePreview);
        document.querySelector('form').addEventListener('submit', function() {
            updatePreview();
        });

        updatePreview();
    </script>
</body>
</html>
