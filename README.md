# Amélie Backend (Blueprint para Render)

Backend mínimo en Flask para el widget de chat “Amélie”, con despliegue en 1 clic en Render y conexión a WordPress.

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/DanielMunoz9/Plataforma-de-venta-mas-agente-AI-Perfum)

## Endpoints
- GET /api/ai/status → health
- GET /api/ai/personality → datos estáticos del agente
- POST /api/ai/chat → respuesta simple determinística (usa header `X-Amelie-Secret` si AMELIE_SECRET está definido)
- GET /api/ai/test → página HTML de prueba

## Variables de entorno
- ALLOWED_ORIGINS: dominios permitidos para CORS (coma-separado). Ej.: `https://lightslategrey-gorilla-972270.hostingersite.com`
- AMELIE_NAME, AMELIE_GREETING: personalización del agente.
- AMELIE_SECRET: si existe, el backend exige el header `X-Amelie-Secret` para `/api/ai/chat`.

## Despliegue rápido (Render)
1. Haz clic en “Deploy to Render” (arriba) y sigue el asistente (plan Free).
2. Espera a que el servicio quede “Live” y prueba:
   - https://TU_APP.onrender.com/api/ai/status
3. Copia la URL base (p. ej. `https://TU_APP.onrender.com`).

## Conexión con WordPress
1. WordPress → “Vane France > Amélie AI”
2. API Base URL: pega `https://TU_APP.onrender.com`
3. API Secret: pega el valor de `AMELIE_SECRET` (si Render lo generó)
4. Guarda y purga caché (LiteSpeed > Toolbox > Purge All)

Nota: Si ves “API no configurada” en el widget, asegúrate de haber pegado la Base URL y (si aplica) el Secret.

## Troubleshooting
- 401 Unauthorized en `/api/ai/chat`: el header `X-Amelie-Secret` no coincide con `AMELIE_SECRET`.
- Error CORS (bloqueado):
  - Verifica que el dominio exacto de tu sitio (con https) esté en `ALLOWED_ORIGINS`.
  - Si estás probando local, puedes dejar ALLOWED_ORIGINS vacío (permite todos en dev).
- Lento al despertar: Render Free “duerme”; el primer request puede tardar unos segundos.

## Pruebas rápidas
```bash
# Estado
curl -s https://TU_APP.onrender.com/api/ai/status | jq

# Chat con secreto (si está configurado)
curl -s -X POST \
  -H 'Content-Type: application/json' \
  -H 'X-Amelie-Secret: TU_SECRETO' \
  -d '{"message":"hola"}' \
  https://TU_APP.onrender.com/api/ai/chat | jq
```

## Ejecución local
```bash
pip install -r requirements.txt
python test_amelie.py
# Abre: http://127.0.0.1:5000/api/ai/status
```

## Archivos de despliegue (raíz del repo)
- render.yaml
- Procfile
- requirements.txt
- test_amelie.py

Este backend es determinista a propósito (rápido y económico). Puedes reemplazar la lógica de `/api/ai/chat` por un LLM cuando lo necesites.
