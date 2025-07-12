import os
import uuid
import requests
from flask import Blueprint, request, jsonify
from dotenv import load_dotenv
import re

# Cargar variables del entorno
load_dotenv()

payu_bp = Blueprint('payu', __name__)

# URLs de producción (no sandbox)
PAYU_OAUTH_URL = "https://api.payulatam.com/oauth/token"
PAYU_PAYMENTS_URL = "https://api.payulatam.com/api/v4/transactions"

# Credenciales de producción desde tu entorno .env
CLIENT_ID = os.getenv("PAYU_API_LOGIN")     # apiLogin
CLIENT_SECRET = os.getenv("PAYU_API_KEY")   # apiKey
MERCHANT_ID = os.getenv("PAYU_MERCHANT_ID")
ACCOUNT_ID = os.getenv("PAYU_ACCOUNT_ID")

MIN_AMOUNT_COP = 12047  # monto mínimo permitido

def validar_datos(data):
    errores = []

    try:
        valor = float(data.get('valor', 0))
        if valor < MIN_AMOUNT_COP:
            errores.append(f"Monto mínimo permitido es {MIN_AMOUNT_COP} COP.")
    except:
        errores.append("Monto inválido.")

    cvv = data.get('cvv', '')
    if not re.fullmatch(r'\d{3,4}', cvv):
        errores.append("CVV inválido. Debe tener 3 o 4 dígitos numéricos.")

    numero = data.get('numero', '')
    if not re.fullmatch(r'\d{13,19}', numero):
        errores.append("Número de tarjeta inválido. Debe tener entre 13 y 19 dígitos.")

    email = data.get('email', '')
    if not re.fullmatch(r"[^@]+@[^@]+\.[^@]+", email):
        errores.append("Email inválido.")

    fecha = data.get('fecha', '')
    if not re.fullmatch(r'\d{4}[/\-]\d{2}', fecha):
        errores.append("Fecha de expiración inválida. Formato esperado: YYYY/MM o YYYY-MM.")

    nombre = data.get('nombre', '')
    if not nombre.strip():
        errores.append("El nombre es obligatorio.")

    descripcion = data.get('descripcion', '')
    if not descripcion.strip():
        errores.append("La descripción es obligatoria.")

    return errores

def obtener_token():
    headers = {
        "Content-Type": "application/x-www-form-urlencoded"
    }
    data = {
        "grant_type": "client_credentials",
        "client_id": CLIENT_ID,
        "client_secret": CLIENT_SECRET
    }
    r = requests.post(PAYU_OAUTH_URL, headers=headers, data=data)

    if r.status_code == 200:
        return r.json().get("access_token")
    else:
        raise Exception(f"Error obteniendo token: {r.text}")

@payu_bp.route('/api/pagos/crear_pago', methods=['POST'])
def crear_pago():
    data = request.json

    errores = validar_datos(data)
    if errores:
        return jsonify({"error": "Validación fallida", "detalles": errores}), 400

    try:
        token = obtener_token()
    except Exception as e:
        return jsonify({"error": "No se pudo obtener token", "mensaje": str(e)}), 500

    referencia = f"ORDER-{uuid.uuid4().hex[:8]}"

    payload = {
        "language": "es",
        "command": "SUBMIT_TRANSACTION",
        "merchant": {
            "apiKey": CLIENT_SECRET,
            "apiLogin": CLIENT_ID
        },
        "transaction": {
            "order": {
                "accountId": ACCOUNT_ID,
                "referenceCode": referencia,
                "description": data['descripcion'],
                "language": "es",
                "additionalValues": {
                    "TX_VALUE": {
                        "value": float(data['valor']),
                        "currency": "COP"
                    }
                },
                "buyer": {
                    "fullName": data['nombre'],
                    "emailAddress": data['email'],
                    "dniNumber": data.get('dni', '00000000'),
                    "shippingAddress": {
                        "street1": "Calle Falsa 123",
                        "city": "Bogotá",
                        "state": "Cundinamarca",
                        "country": "CO",
                        "postalCode": "110111",
                        "phone": "3001234567"
                    }
                }
            },
            "payer": {
                "fullName": data['nombre'],
                "emailAddress": data['email'],
                "contactPhone": "3001234567",
                "dniNumber": data.get("dni", "00000000")
            },
            "creditCard": {
                "number": data['numero'],
                "securityCode": data['cvv'],
                "expirationDate": data['fecha'],
                "name": data['nombre']
            },
            "extraParameters": {},
            "type": "AUTHORIZATION_AND_CAPTURE",
            "paymentMethod": data.get("paymentMethod", "VISA"),
            "paymentCountry": "CO",
            "installmentsNumber": 1
        },
        "test": False
    }

    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {token}"
    }

    resp = requests.post(PAYU_PAYMENTS_URL, json=payload, headers=headers)

    try:
        resp_json = resp.json()
    except Exception:
        return jsonify({"error": "Respuesta no válida de PayU", "raw": resp.text}), 500

    if resp.status_code == 200:
        return jsonify({
            "mensaje": "Pago procesado correctamente",
            "respuesta_payu": resp_json
        }), 200
    else:
        return jsonify({
            "error": "Error en el pago",
            "detalle": resp_json
        }), resp.status_code
