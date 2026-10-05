# Restaurant Automation

سامانه ERP/POS رستوران بر مبنای مستندات `Docs/` و معماری Modular Monolith.

**QAVNS: 001.1.0.0** — نخستین نسل قابل ارائه هسته سامانه.

## Stack
- PHP 8.3+
- MySQL 8.0/8.4
- PDO
- Vanilla HTML/CSS/JS
- REST-ready `/api/v1`

## اجرا
```bash
cp .env.example .env
docker compose up -d --build
```
سپس `http://localhost:8080` را باز کنید.

Seed: `admin@example.com` / `password`

قواعد QAVNS در `Docs/19-versioning.md` آمده است.
