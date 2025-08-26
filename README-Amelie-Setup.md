# Amélie Setup

## 1) Variables
- Copia `.env.example` a `.env` y completa `OPENAI_API_KEY` (no lo subas).
- Ajusta `ALLOW_ORIGINS` a tu dominio en producción.

## 2) Backend local
```bash
python -m venv .venv
# Windows: .venv\Scripts\activate
source .venv/bin/activate
pip install -r server/requirements.txt
export OPENAI_API_KEY="TU_KEY"  # PowerShell: setx OPENAI_API_KEY "TU_KEY" y abre nueva terminal
uvicorn server.main:app --reload
```
- Health: http://127.0.0.1:8000/api/health
- Prueba rápida:
```bash
curl -X POST http://127.0.0.1:8000/api/amelie \
  -H "Content-Type: application/json" \
  -d '{"text":"Busco un perfume dulce para cita de noche"}'
```

## 3) Frontend
- Inserta `public/snippets/amelie_widget.html` en tu `index.html` (o página donde lo quieras mostrar).
- En local, antes del snippet agrega:
```html
<script>window.AMELIE_BACKEND_URL = "http://127.0.0.1:8000";</script>
```

## 4) Despliegue (Render ejemplo)
1. Create New → Web Service → conecta este repo.
2. Build Command: `pip install -r server/requirements.txt`
3. Start Command: `uvicorn server.main:app --host 0.0.0.0 --port $PORT`
4. Env Vars: `OPENAI_API_KEY` = tu key; `ALLOW_ORIGINS` = `https://<tu-dominio>,https://<tu-usuario>.github.io`
5. Deploy y copia la URL (ej: `https://tu-api.onrender.com`).

## 5) Frontend en producción
- En tu HTML define:
```html
<script>window.AMELIE_BACKEND_URL = "https://tu-api.onrender.com";</script>
```

## 6) Seguridad y control
- OpenAI Billing → Usage limits: Soft $4, Hard $5.
- No compartas tu key; rota si se expone.
- Monitorea Usage → Overview semanalmente.