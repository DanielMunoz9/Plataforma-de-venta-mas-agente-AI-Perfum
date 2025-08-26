import os
import uuid
import requests
from flask import Blueprint, request, jsonify
from dotenv import load_dotenv
import re

# Cargar variables de entorno desde .env
load_dotenv()

# Mostrar credenciales para depuración
print("CLIENT_ID:", os.getenv("PAYU_API_LOGIN"))
print("CLIENT_SECRET:", os.getenv("PAYU_API_KEY"))
print("MERCHANT_ID:", os.getenv("PAYU_MERCHANT_ID"))
print("ACCOUNT_ID:", os.getenv("PAYU_ACCOUNT_ID"))

payu_bp = Blueprint('payu', __name__)

# URLs de PayU
PAYU_PAYMENTS_URL = "https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi"

# Credenciales
CLIENT_ID = os.getenv("PAYU_API_LOGIN")
CLIENT_SECRET = os.getenv("PAYU_API_KEY")
MERCHANT_ID = os.getenv("PAYU_MERCHANT_ID")
ACCOUNT_ID = os.getenv("PAYU_ACCOUNT_ID")

MIN_AMOUNT_COP = 12047  # Monto mínimo

# -----------------------------------------------
# Validar datos del formulario
# -----------------------------------------------
def validar_datos(data):
    errores = []
    try:
        valor = float(data.get('valor', 0))
        if valor < MIN_AMOUNT_COP:
            errores.append(f"Monto mínimo permitido es {MIN_AMOUNT_COP} COP.")
    except:
        errores.append("Monto inválido.")

    descripcion = data.get('descripcion', '').strip()
    if not descripcion:
        errores.append("La descripción es obligatoria.")

    payment_method = data.get("paymentMethod")
    if not payment_method:
        errores.append("El método de pago es obligatorio.")

    if payment_method in ['VISA', 'MASTERCARD', 'AMEX']:
        if not re.fullmatch(r'\d{3,4}', data.get('cvv', '')):
            errores.append("CVV inválido.")
        if not re.fullmatch(r'\d{13,19}', data.get('numero', '')):
            errores.append("Número de tarjeta inválido.")
        if not re.fullmatch(r'\d{4}[/\-]\d{2}', data.get('fecha', '')):
            errores.append("Fecha inválida.")
        if not data.get('nombre'):
            errores.append("Nombre obligatorio.")
        if not re.fullmatch(r"[^@]+@[^@]+\.[^@]+", data.get('email', '')):
            errores.append("Email inválido.")
    return errores

# -----------------------------------------------
# Ruta para crear el pago
# -----------------------------------------------
@payu_bp.route('/api/pagos/crear_pago', methods=['POST'])
def crear_pago():
    data = request.form.to_dict()
    errores = validar_datos(data)
    if errores:
        return jsonify({"error": "Validación fallida", "detalles": errores}), 400

    referencia = f"ORDER-{uuid.uuid4().hex[:8]}"
    valor_float = float(data.get('valor', '0'))
    payment_method = data.get("paymentMethod")
    descripcion = data.get("descripcion", "Compra en tienda")

    transaction = {
        "order": {
            "accountId": ACCOUNT_ID,
            "referenceCode": referencia,
            "description": descripcion,
            "language": "es",
            "additionalValues": {
                "TX_VALUE": {
                    "value": valor_float,
                    "currency": "COP"
                }
            },
            "buyer": {
                "fullName": data.get('nombre', ''),
                "emailAddress": data.get('email', ''),
                "dniNumber": data.get('dni', '00000000'),
                "shippingAddress": {
                    "street1": data.get('direccion', 'No especificada'),
                    "city": data.get('ciudad', 'No especificada'),
                    "state": "CO",
                    "country": "CO",
                    "postalCode": "000000",
                    "phone": data.get('telefono', '0000000000')
                }
            }
        },
        "payer": {
            "fullName": data.get('nombre', ''),
            "emailAddress": data.get('email', ''),
            "contactPhone": data.get('telefono', '0000000000'),
            "dniNumber": data.get("dni", "00000000")
        },
        "extraParameters": {},
        "type": "AUTHORIZATION_AND_CAPTURE",
        "paymentCountry": "CO",
        "installmentsNumber": 1,
        "paymentMethod": payment_method
    }

    if payment_method in ['VISA', 'MASTERCARD', 'AMEX']:
        transaction["creditCard"] = {
            "number": data['numero'],
            "securityCode": data['cvv'],
            "expirationDate": data['fecha'],
            "name": data['nombre']
        }

    # Payload final
    payload = {
        "language": "es",
        "command": "SUBMIT_TRANSACTION",
        "merchant": {
            "apiKey": CLIENT_SECRET,
            "apiLogin": CLIENT_ID
        },
        "transaction": transaction,
        "test": True  # True para sandbox
    }

    headers = {
        "Content-Type": "application/json"
    }

    resp = requests.post(PAYU_PAYMENTS_URL, json=payload, headers=headers)

    try:
        resp_json = resp.json()
    except:
        return jsonify({"error": "Respuesta no válida de PayU", "raw": resp.text}), 500

    if resp.status_code == 200 and resp_json.get("code") == "SUCCESS":
        return jsonify({
            "mensaje": "Pago procesado correctamente",
            "respuesta_payu": resp_json
        }), 200
    else:
        return jsonify({
            "error": "Error en el pago",
            "detalle": resp_json
        }), resp.status_code
