import uuid
from datetime import datetime
from flask import Blueprint, request, jsonify, session, render_template

# Crear blueprint para el agente AI
ai_agent_bp = Blueprint('ai_agent', __name__, url_prefix='/api/ai')

# Configuración de la personalidad de Amélie
AMELIE_PERSONALITY = {
    "name": "Amélie",
    "role": "Consultora experta en fragancias de lujo",
    "greeting": "¡Hola, querida! Soy Amélie, tu consultora personal de fragancias 💎",
    "style": "Elegante, sofisticada y persuasiva",
    "techniques": [
        "Conecta fragancias con emociones",
        "Usa lenguaje sensual y evocativo", 
        "Demuestra expertise en perfumería",
        "Crea conexión personal con el cliente"
    ]
}

def get_session_id():
    """Obtiene o crea un ID de sesión único"""
    if 'ai_session_id' not in session:
        session['ai_session_id'] = str(uuid.uuid4())
    return session['ai_session_id']

def get_amelie_response(user_message):
    """
    Genera respuesta de Amélie con su personalidad distintiva
    Versión simplificada sin base de datos
    """
    user_message_lower = user_message.lower()
    
    # Respuestas de saludo
    if any(word in user_message_lower for word in ['hola', 'buenos días', 'buenas tardes', 'hi']):
        return "¡Hola, bella! Soy Amélie 💎 Tu consultora personal de fragancias de lujo. Estoy aquí para ayudarte a encontrar esa esencia perfecta que despierte tu magnetismo natural. ¿Qué tipo de fragancia tienes en mente?"
    
    # Respuestas sobre perfumes/fragancias
    elif any(word in user_message_lower for word in ['perfume', 'fragancia', 'aroma', 'olor']):
        return "¡Qué emocionante! Las fragancias son como memorias líquidas que despiertan emociones 🌹 Cada una cuenta una historia única sobre tu personalidad. ¿Prefieres algo fresco y floral para el día, o algo más intenso y seductor para las noches especiales?"
    
    # Respuestas sobre precios
    elif any(word in user_message_lower for word in ['precio', 'costo', 'cuanto', 'vale']):
        return "Entiendo perfectamente tu pregunta, amor 💫 Nuestras fragancias van desde $80.000 hasta $350.000, dependiendo de la exclusividad y concentración. Como eres especial, tengo algunas opciones increíbles que se ajustan a diferentes presupuestos. ¿Qué rango te interesa más?"
    
    # Respuestas de recomendación
    elif any(word in user_message_lower for word in ['recomendar', 'sugerir', 'ayuda', 'consejo']):
        return "¡Me encanta ayudar a encontrar la fragancia perfecta! ✨ Para darte la mejor recomendación, cuéntame: ¿es para uso diario o ocasiones especiales? ¿Prefieres algo dulce y femenino, o algo más sofisticado y misterioso? Tu personalidad es única y mereces una fragancia igual de especial."
    
    # Respuestas sobre ocasiones
    elif any(word in user_message_lower for word in ['ocasión', 'evento', 'fiesta', 'trabajo', 'cita']):
        return "¡Perfecto! Cada ocasión merece su fragancia ideal 👑 Para el trabajo, algo elegante pero sutil. Para una cita romántica, algo seductor que deje huella. Para fiestas, algo memorable que te haga única. ¿Cuál de estas ocasiones te interesa más?"
    
    # Respuesta general/conversacional
    else:
        return f"Entiendo perfectamente, querida 💕 Como experta en fragancias de lujo, estoy aquí para cualquier duda que tengas. Las fragancias son mi pasión y me encanta compartir ese conocimiento contigo. ¿Hay algo específico sobre perfumes que te gustaría saber?"

@ai_agent_bp.route('/chat', methods=['POST'])
def chat():
    """Endpoint principal para el chat con Amélie - versión simplificada"""
    try:
        data = request.get_json()
        user_message = data.get('message', '').strip()
        
        if not user_message:
            return jsonify({'error': 'Mensaje vacío'}), 400
        
        # Obtener ID de sesión
        session_id = get_session_id()
        
        # Generar respuesta de Amélie
        amelie_response = get_amelie_response(user_message)
        
        return jsonify({
            'response': amelie_response,
            'session_id': session_id,
            'agent_name': AMELIE_PERSONALITY['name'],
            'timestamp': datetime.utcnow().isoformat()
        })
        
    except Exception as e:
        return jsonify({'error': f'Error interno: {str(e)}'}), 500

@ai_agent_bp.route('/personality', methods=['GET'])
def get_personality():
    """Obtiene información sobre la personalidad de Amélie"""
    return jsonify(AMELIE_PERSONALITY)

@ai_agent_bp.route('/test', methods=['GET'])
def test_ai_widget():
    """Página de prueba para el widget AI"""
    return render_template('test_ai.html')

@ai_agent_bp.route('/status', methods=['GET'])
def status():
    """Endpoint para verificar que el agente AI está funcionando"""
    return jsonify({
        'status': 'active',
        'agent': AMELIE_PERSONALITY['name'],
        'message': 'Amélie está lista para ayudarte 💎'
    })