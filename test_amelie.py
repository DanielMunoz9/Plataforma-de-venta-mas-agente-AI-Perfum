#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Amélie AI Agent - Flask API
Agente de ventas inteligente para Plataforma de venta
"""

import os
import secrets
from flask import Flask, jsonify, request
from flask_cors import CORS

app = Flask(__name__)

# Configuración CORS segura
allowed_origins = os.getenv('ALLOWED_ORIGINS', 'https://lightslategrey-gorilla-972270.hostingersite.com').split(',')
CORS(app, origins=[origin.strip() for origin in allowed_origins])

# Variables de entorno
AMELIE_NAME = os.getenv('AMELIE_NAME', 'Amélie')
AMELIE_GREETING = os.getenv('AMELIE_GREETING', '¡Hola! Soy Amélie, tu asistente de ventas. ¿En qué puedo ayudarte hoy?')
AMELIE_SECRET = os.getenv('AMELIE_SECRET', secrets.token_urlsafe(32))

def check_auth():
    """Verificar autenticación opcional mediante header X-Amelie-Secret"""
    if 'X-Amelie-Secret' in request.headers:
        return request.headers.get('X-Amelie-Secret') == AMELIE_SECRET
    return True  # Sin header = acceso permitido

@app.route('/api/ai/status', methods=['GET'])
def status():
    """Estado del agente AI"""
    if not check_auth():
        return jsonify({'error': 'Acceso no autorizado'}), 401
    
    return jsonify({
        'status': 'activo',
        'agente': AMELIE_NAME,
        'version': '1.0.0',
        'endpoints': [
            '/api/ai/status',
            '/api/ai/personality', 
            '/api/ai/chat',
            '/api/ai/test'
        ]
    })

@app.route('/api/ai/personality', methods=['GET'])
def personality():
    """Personalidad del agente AI"""
    if not check_auth():
        return jsonify({'error': 'Acceso no autorizado'}), 401
    
    return jsonify({
        'nombre': AMELIE_NAME,
        'saludo': AMELIE_GREETING,
        'personalidad': 'Amigable, profesional y experta en productos de belleza',
        'especialidad': 'Asesoramiento en productos de peluquería y belleza',
        'idioma': 'español'
    })

@app.route('/api/ai/chat', methods=['POST'])
def chat():
    """Endpoint principal de chat con el agente"""
    if not check_auth():
        return jsonify({'error': 'Acceso no autorizado'}), 401
    
    data = request.get_json()
    if not data or 'mensaje' not in data:
        return jsonify({'error': 'Se requiere un mensaje'}), 400
    
    user_message = data['mensaje']
    
    # Respuesta básica de demostración
    if 'hola' in user_message.lower():
        response = AMELIE_GREETING
    elif 'producto' in user_message.lower() or 'ayuda' in user_message.lower():
        response = f"Como {AMELIE_NAME}, puedo ayudarte a encontrar los mejores productos de belleza. ¿Buscas algo específico como secadores, planchas, o productos para spa?"
    elif 'precio' in user_message.lower():
        response = "Te puedo ayudar con información sobre precios. ¿Qué producto te interesa?"
    else:
        response = f"Entiendo tu consulta. Como {AMELIE_NAME}, estoy aquí para ayudarte con cualquier pregunta sobre nuestros productos de belleza y peluquería."
    
    return jsonify({
        'agente': AMELIE_NAME,
        'respuesta': response,
        'timestamp': '2024-01-01T12:00:00Z'
    })

@app.route('/api/ai/test', methods=['GET'])
def test():
    """Endpoint de prueba"""
    return jsonify({
        'mensaje': f'✅ {AMELIE_NAME} está funcionando correctamente',
        'cors_origins': allowed_origins,
        'auth_required': 'X-Amelie-Secret' in request.headers
    })

@app.route('/', methods=['GET'])
def home():
    """Página principal de la API"""
    return jsonify({
        'proyecto': 'Plataforma de venta + Agente AI Amélie',
        'descripcion': 'API para agente de ventas inteligente',
        'documentacion': {
            'status': 'GET /api/ai/status',
            'personalidad': 'GET /api/ai/personality', 
            'chat': 'POST /api/ai/chat',
            'test': 'GET /api/ai/test'
        },
        'version': '1.0.0'
    })

if __name__ == '__main__':
    port = int(os.getenv('PORT', 5000))
    debug = os.getenv('FLASK_ENV') == 'development'
    app.run(host='0.0.0.0', port=port, debug=debug)