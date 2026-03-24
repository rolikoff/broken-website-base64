<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CalcAPI - Expression Evaluator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; color: #333; }
        .header { background: #2c3e50; color: white; padding: 1rem 2rem; }
        .header h1 { font-size: 1.4rem; }
        .header small { color: #95a5a6; }
        .container { max-width: 700px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 1rem; font-size: 1.1rem; color: #2c3e50; }
        input[type=text] { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; font-size: 1rem; }
        .btn { padding: 0.6rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem; margin-top: 0.75rem; margin-right: 0.5rem; }
        .btn-primary { background: #3498db; color: white; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn:hover { opacity: 0.9; }
        .info { background: #eaf4fd; border-left: 3px solid #3498db; padding: 0.75rem; margin-bottom: 1rem; font-size: 0.9rem; }
        .result { padding: 1rem; background: #fafafa; border: 1px solid #eee; border-radius: 4px; font-family: monospace; font-size: 1.1rem; min-height: 44px; }
        .b64 { margin-top: 0.5rem; color: #888; font-size: 0.8rem; font-family: monospace; word-break: break-all; }
        .examples code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; cursor: pointer; }
        .examples code:hover { background: #e0e0e0; }
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

        <div class="card">
            <h2>Enter Expression</h2>
            <input type="text" id="expr" placeholder="e.g. (12 + 8) * 3 / 2" value="(12 + 8) * 3 / 2">
            <div class="b64">Encoded: <span id="b64preview"></span></div>
            <button class="btn btn-primary" onclick="evaluate()">Evaluate</button>
            <button class="btn btn-secondary" onclick="setExpr('sqrt(144) + pow(2, 10)')">sqrt + pow</button>
            <button class="btn btn-secondary" onclick="setExpr('round(M_PI, 4)')">pi</button>
            <button class="btn btn-secondary" onclick="setExpr('max(10, 20, 30) - min(1, 2, 3)')">max/min</button>
        </div>

        <div class="card">
            <h2>Result</h2>
            <div class="result" id="result">—</div>
        </div>
    </div>

    <script>
        function setExpr(val) {
            document.getElementById('expr').value = val;
            updatePreview();
        }

        function updatePreview() {
            const expr = document.getElementById('expr').value;
            document.getElementById('b64preview').textContent = btoa(expr);
        }

        async function evaluate() {
            const expr = document.getElementById('expr').value;
            const encoded = btoa(expr);
            updatePreview();

            const response = await fetch('eval.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'expr=' + encodeURIComponent(encoded)
            });

            document.getElementById('result').textContent = await response.text();
        }

        updatePreview();
    </script>
</body>
</html>
