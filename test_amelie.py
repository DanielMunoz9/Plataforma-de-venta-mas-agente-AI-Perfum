import os
from flask import Flask, request, jsonify
from flask_cors import CORS
from datetime import datetime

app = Flask(__name__)

# CORS domains from env var ALLOWED_ORIGINS (comma-separated). If empty, allow all (dev).
origins_env = os.getenv("ALLOWED_ORIGINS", "")
allowed_origins = [o.strip() for o in origins_env.split(",") if o.strip()] or ["*"]
CORS(app, resources={r"/api/*": {"origins": allowed_origins}}, supports_credentials=True)

AMELIE_NAME = os.getenv("AMELIE_NAME", "Amélie")
AMELIE_GREETING = os.getenv(
    "AMELIE_GREETING",
    f"¡Hola! Soy {AMELIE_NAME}, tu asistente de Vane France. ¿En qué puedo ayudarte hoy?",
)
API_SECRET = os.getenv("AMELIE_SECRET", "")


def check_auth(req):
    if not API_SECRET:
        return True
    return req.headers.get("X-Amelie-Secret") == API_SECRET


@app.get("/api/ai/status")
def status():
    return jsonify(ok=True, name=AMELIE_NAME, time=datetime.utcnow().isoformat() + "Z")


@app.get("/api/ai/personality")
def personality():
    return jsonify(
        {
            "name": AMELIE_NAME,
            "greeting": AMELIE_GREETING,
            "tone": "lujo, cálido, consultivo",
            "quick_actions": [
                {"text": "Recomiéndame un perfume", "payload": "recomendacion"},
                {"text": "Rango de precios", "payload": "precios"},
                {"text": "Fragancias populares", "payload": "populares"},
            ],
        }
    )


@app.post("/api/ai/chat")
def chat():
    if not check_auth(request):
        return jsonify(error=True, message="No autorizado"), 401

    data = request.get_json(force=True, silent=True) or {}
    user = (data.get("message") or "").lower().strip()

    if not user:
        return jsonify(error=True, message="Mensaje vacío"), 400

    if any(k in user for k in ["hola", "buenas", "hey"]):
        text = f"{AMELIE_GREETING}"
    elif "precio" in user or "vale" in user or "$" in user:
        text = (
            "Manejamos opciones desde $80.000 hasta $350.000, según concentración y exclusividad. "
            "¿Qué presupuesto tienes en mente?"
        )
    elif "recom" in user or "suger" in user:
        text = "Con gusto. ¿Prefieres notas cítricas y frescas, florales elegantes o amaderadas intensas?"
    else:
        text = (
            "Puedo ayudarte con recomendaciones, precios y disponibilidad. "
            "Cuéntame qué buscas y te guío."
        )

    return jsonify(error=False, reply=text)


@app.get("/api/ai/test")
def test_page():
    return """
    <html><body>
    <h1>Amélie Test</h1>
    <p>POST /api/ai/chat {"message":"Hola"}</p>
    </body></html>
    """


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=int(os.getenv("PORT", 5000)))