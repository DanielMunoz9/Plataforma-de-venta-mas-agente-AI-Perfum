# Amélie Backend (Blueprint for Render)

Despliega el backend mínimo de Amélie (Flask) en Render y conéctalo a WordPress.

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/DanielMunoz9/Plataforma-de-venta-mas-agente-AI-Perfum)

## Endpoints
- `GET /api/ai/status` → health
- `GET /api/ai/personality` → datos estáticos del agente
- `POST /api/ai/chat` → respuesta simple determinística (con header `X-Amelie-Secret` si configuraste AMELIE_SECRET)
- `GET /api/ai/test` → página de prueba

## Variables de entorno
- `ALLOWED_ORIGINS`: dominios permitidos para CORS (coma-separado). Ej.: `https://lightslategrey-gorilla-972270.hostingersite.com`
- `AMELIE_NAME`, `AMELIE_GREETING`: personalización.
- `AMELIE_SECRET`: si la defines, el backend exige el header `X-Amelie-Secret`.

## Pasos de despliegue rápido
1. Haz clic en "Deploy to Render" y sigue el asistente (plan Free).
2. Espera a que quede "Live" y prueba `https://TU_APP.onrender.com/api/ai/status`.
3. Copia la URL base (por ejemplo `https://TU_APP.onrender.com`).
4. En WordPress ve a: `Vane France > Amélie AI` y:
   - API Base URL: pega la URL
   - API Secret: pega el valor de `AMELIE_SECRET` de Render (si se generó)
   - Guarda y purga caché (LiteSpeed > Toolbox > Purge All)

## Pruebas rápidas
```bash
curl -s https://TU_APP.onrender.com/api/ai/status | jq

# Si usas secreto
curl -s -X POST \
  -H 'Content-Type: application/json' \
  -H 'X-Amelie-Secret: TU_SECRETO' \
  -d '{"message":"hola"}' \
  https://TU_APP.onrender.com/api/ai/chat | jq
```

## Nota
Este backend es estático y determinista a propósito (rápido y económico). Puedes reemplazar la lógica del endpoint `/api/ai/chat` por una integración con OpenAI u otro LLM cuando lo necesites.