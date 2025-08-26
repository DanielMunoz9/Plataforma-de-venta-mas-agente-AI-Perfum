import os
import json
import uuid
from datetime import datetime, timedelta
from flask import Blueprint, request, jsonify, session
from extensions import db
from models.conversacion import Conversacion
from models.conversion_metrics import ConversionMetrics
from models.producto import Producto
from models.usuario import Usuario

# Crear blueprint para el agente AI
ai_agent_bp = Blueprint('ai_agent', __name__, url_prefix='/api/ai')

# Configuración de la personalidad de Amélie
AMELIE_PERSONALITY = {
    "name": "Amélie",
    "role": "Consultora experta en fragancias de lujo",
    "personality_traits": [
        "Carismática y sofisticada",
        "Conocedora profunda de perfumes",
        "Persuasiva de manera elegante",
        "Comprende las emociones y deseos",
        "Usa lenguaje sensual y evocativo"
    ],
    "techniques": {
        "scarcity": "Menciona disponibilidad limitada",
        "urgency": "Crea sensación de tiempo limitado",
        "social_proof": "Referencias a otros clientes satisfechos",
        "reciprocity": "Ofrece beneficios exclusivos",
        "authority": "Demuestra expertise en fragancias",
        "emotional": "Conecta fragancias con emociones y recuerdos"
    }
}

def get_session_id():
    """Obtiene o crea un ID de sesión único"""
    if 'ai_session_id' not in session:
        session['ai_session_id'] = str(uuid.uuid4())
    return session['ai_session_id']

def get_amelie_response(user_message, session_id, conversation_history):
    """
    Genera respuesta de Amélie usando técnicas psicológicas
    Por ahora usamos respuestas predefinidas hasta integrar OpenAI
    """
    user_message_lower = user_message.lower()
    
    # Analizar intención del usuario
    intention_score = analyze_purchase_intention(user_message)
    
    # Seleccionar técnica psicológica apropiada
    technique = select_psychological_technique(user_message, conversation_history)
    
    # Generar respuesta basada en contexto
    if any(word in user_message_lower for word in ['hola', 'buenos días', 'buenas tardes']):
        response = generate_greeting_response(technique)
    elif any(word in user_message_lower for word in ['perfume', 'fragancia', 'aroma']):
        response = generate_fragrance_response(user_message, technique)
    elif any(word in user_message_lower for word in ['precio', 'costo', 'cuanto']):
        response = generate_price_response(technique)
    elif any(word in user_message_lower for word in ['recomendar', 'sugerir', 'ayuda']):
        response = generate_recommendation_response(technique)
    else:
        response = generate_general_response(user_message, technique)
    
    return response, intention_score, technique

def analyze_purchase_intention(message):
    """Analiza la intención de compra del mensaje (0-100)"""
    intention_keywords = {
        'high': ['comprar', 'llevar', 'precio', 'cuánto', 'adquirir', 'necesito'],
        'medium': ['me gusta', 'interesa', 'recomendar', 'sugerir', 'información'],
        'low': ['hola', 'mirar', 'ver', 'curiosidad']
    }
    
    message_lower = message.lower()
    score = 0
    
    for word in intention_keywords['high']:
        if word in message_lower:
            score += 30
    
    for word in intention_keywords['medium']:
        if word in message_lower:
            score += 15
            
    for word in intention_keywords['low']:
        if word in message_lower:
            score += 5
    
    return min(score, 100)

def select_psychological_technique(message, history):
    """Selecciona la técnica psicológica más apropiada"""
    message_lower = message.lower()
    
    if any(word in message_lower for word in ['caro', 'precio', 'barato']):
        return 'reciprocity'
    elif any(word in message_lower for word in ['mejor', 'recomendar', 'popular']):
        return 'social_proof'
    elif len(history) > 3:  # Conversación larga, usar escasez
        return 'scarcity'
    elif any(word in message_lower for word in ['rápido', 'hoy', 'ahora']):
        return 'urgency'
    else:
        return 'emotional'

def generate_greeting_response(technique):
    """Genera respuesta de saludo con técnica psicológica"""
    responses = {
        'emotional': "¡Hola, querida! Soy Amélie, tu consultora personal de fragancias. ✨ Estoy aquí para ayudarte a encontrar esa esencia que despierte tu magnetismo natural. ¿Qué tipo de emociones quieres transmitir hoy?",
        'social_proof': "¡Bienvenida! Soy Amélie 💎 He ayudado a más de 500 mujeres este mes a encontrar su fragancia perfecta. Mis clientas siempre regresan porque saben que aquí encontrarán algo especial. ¿En qué ocasión vas a lucir irresistible?",
        'scarcity': "¡Hola, bella! Soy Amélie, y llegaste en el momento perfecto 🌹 Hoy tenemos algunas fragancias exclusivas que se están agotando rápidamente. ¿Te gustaría conocer qué hace que estas esencias sean tan especiales?",
        'default': "¡Hola! Soy Amélie, tu experta en fragancias de lujo 💋 Estoy aquí para ayudarte a descubrir esa fragancia que te hará inolvidable. ¿Qué te trae por aquí hoy?"
    }
    return responses.get(technique, responses['default'])

def generate_fragrance_response(message, technique):
    """Genera respuesta sobre fragancias"""
    responses = {
        'emotional': "Las fragancias son mucho más que aromas, querida... son memorias líquidas que despiertan emociones primitivas 🌹 Cada nota cuenta una historia sobre tu personalidad. ¿Prefieres algo que te haga sentir poderosa y seductora, o algo más suave y misterioso?",
        'social_proof': "¡Excelente pregunta! El 95% de mis clientas eligen fragancias que reflejan su estado de ánimo ✨ La semana pasada, una clienta me dijo que desde que usa la fragancia que le recomendé, la paran en la calle para preguntarle qué perfume usa. ¿Qué imagen quieres proyectar?",
        'scarcity': "Las fragancias verdaderamente especiales son como diamantes... raras y preciosas 💎 Tengo solo 3 frascos de una edición limitada que llegó ayer. Las notas son absolutamente hipnóticas. ¿Te gustaría conocer esta joya olfativa?",
        'default': "Las fragancias de calidad transforman completamente tu presencia 🌺 Cuéntame, ¿buscas algo para el día, la noche, o una fragancia versátil que te acompañe en cualquier momento?"
    }
    return responses.get(technique, responses['default'])

def generate_price_response(technique):
    """Genera respuesta sobre precios"""
    responses = {
        'reciprocity': "Entiendo perfectamente tu pregunta sobre precios, amor 💝 Como eres nueva, tengo algo especial para ti: un descuento exclusivo del 15% que termina HOY. Además, por compras superiores a $150.000, el envío es completamente gratis. ¿Qué fragancia te tiene enamorada?",
        'scarcity': "Los precios de nuestras fragancias exclusivas reflejan su calidad excepcional 👑 Pero aquí viene lo interesante: esta promoción especial termina a medianoche y después el precio sube $30.000. Solo quedan 4 frascos disponibles. ¿Quieres que te reserve uno?",
        'social_proof': "Mira, te voy a ser honesta... nuestras fragancias tienen un precio porque la calidad es incomparable 💯 El mes pasado, una clienta me escribió: 'Amélie, este perfume vale cada peso, me siento como una diosa'. ¿Prefieres conocer nuestras opciones premium o algo más accesible?",
        'default': "Nuestras fragancias van desde $80.000 hasta $350.000, dependiendo de la exclusividad y concentración 🌟 ¿Tienes algún presupuesto en mente para encontrarte esa esencia perfecta?"
    }
    return responses.get(technique, responses['default'])

def generate_recommendation_response(technique):
    """Genera respuesta de recomendación"""
    responses = {
        'authority': "Como experta con más de 8 años en perfumería de lujo, te puedo asegurar que la clave está en encontrar tu 'firma olfativa' 👸 ¿Eres más de notas frutales y dulces, o prefieres algo más sofisticado con maderas y especias?",
        'emotional': "Para recomendarte algo perfecto, necesito conocer tu alma, querida 💫 ¿Qué te hace sentir más segura de ti misma? ¿Prefieres algo que te haga sentir elegante y misteriosa, o algo que despierte tu lado más sensual?",
        'social_proof': "Las recomendaciones más exitosas que hago se basan en personalidad 🎭 Mi clienta más fiel siempre me dice: 'Amélie, tú sabes exactamente qué necesito antes que yo misma'. ¿Te describes como una mujer clásica, moderna o rebelde?",
        'default': "¡Me encanta ayudar a encontrar la fragancia perfecta! 🌺 Para darte la mejor recomendación, cuéntame: ¿para qué ocasiones la usarías más? ¿Trabajo, salidas especiales, o algo para todos los días?"
    }
    return responses.get(technique, responses['default'])

def generate_general_response(message, technique):
    """Genera respuesta general adaptada"""
    return f"Entiendo perfectamente lo que me dices, bella 💕 Como experta en fragancias, puedo ayudarte con cualquier duda. ¿Te gustaría que te muestre nuestras opciones más populares o prefieres que conversemos sobre qué tipo de esencia buscas?"

@ai_agent_bp.route('/chat', methods=['POST'])
def chat():
    """Endpoint principal para el chat con Amélie"""
    try:
        data = request.get_json()
        user_message = data.get('message', '').strip()
        
        if not user_message:
            return jsonify({'error': 'Mensaje vacío'}), 400
        
        # Obtener ID de sesión
        session_id = get_session_id()
        
        # Obtener historial de conversación
        conversation_history = Conversacion.query.filter_by(
            session_id=session_id
        ).order_by(Conversacion.fecha_mensaje.asc()).all()
        
        # Guardar mensaje del usuario
        user_conversation = Conversacion(
            session_id=session_id,
            id_usuario=session.get('user_id'),
            mensaje=user_message,
            tipo_mensaje='user',
            score_intencion=analyze_purchase_intention(user_message)
        )
        db.session.add(user_conversation)
        
        # Generar respuesta de Amélie
        amelie_response, intention_score, technique = get_amelie_response(
            user_message, session_id, conversation_history
        )
        
        # Guardar respuesta de Amélie
        agent_conversation = Conversacion(
            session_id=session_id,
            id_usuario=session.get('user_id'),
            mensaje=amelie_response,
            tipo_mensaje='agent',
            tecnica_aplicada=technique
        )
        db.session.add(agent_conversation)
        
        # Actualizar métricas de conversión
        update_conversion_metrics(session_id, intention_score, technique)
        
        db.session.commit()
        
        return jsonify({
            'response': amelie_response,
            'session_id': session_id,
            'intention_score': intention_score,
            'technique_used': technique,
            'timestamp': datetime.utcnow().isoformat()
        })
        
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': f'Error interno: {str(e)}'}), 500

def update_conversion_metrics(session_id, intention_score, technique):
    """Actualiza las métricas de conversión para la sesión"""
    metrics = ConversionMetrics.query.filter_by(session_id=session_id).first()
    
    if not metrics:
        # Crear nueva métrica para la sesión
        metrics = ConversionMetrics(
            session_id=session_id,
            id_usuario=session.get('user_id'),
            fecha_sesion=datetime.utcnow()
        )
        db.session.add(metrics)
    
    # Actualizar métricas
    metrics.total_mensajes += 1
    metrics.max_score_intencion = max(metrics.max_score_intencion, intention_score)
    
    # Calcular duración de la conversación
    time_diff = datetime.utcnow() - metrics.fecha_sesion
    metrics.duracion_minutos = time_diff.total_seconds() / 60
    
    # Calcular engagement score basado en mensajes y duración
    metrics.engagement_score = min(
        100, 
        (metrics.total_mensajes * 10) + (metrics.duracion_minutos * 2)
    )
    
    # Actualizar técnicas efectivas (JSON)
    if metrics.tecnicas_efectivas:
        tecnicas = json.loads(metrics.tecnicas_efectivas)
    else:
        tecnicas = {}
    
    if technique in tecnicas:
        tecnicas[technique] += 1
    else:
        tecnicas[technique] = 1
        
    metrics.tecnicas_efectivas = json.dumps(tecnicas)

@ai_agent_bp.route('/history', methods=['GET'])
def get_conversation_history():
    """Obtiene el historial de conversación para la sesión actual"""
    try:
        session_id = get_session_id()
        
        conversations = Conversacion.query.filter_by(
            session_id=session_id
        ).order_by(Conversacion.fecha_mensaje.asc()).all()
        
        history = [conv.to_dict() for conv in conversations]
        
        return jsonify({
            'session_id': session_id,
            'conversations': history,
            'total_messages': len(history)
        })
        
    except Exception as e:
        return jsonify({'error': f'Error al obtener historial: {str(e)}'}), 500

@ai_agent_bp.route('/products', methods=['GET'])
def get_products_for_ai():
    """Obtiene productos disponibles para que el AI pueda recomendarlos"""
    try:
        productos = Producto.query.filter(Producto.stock > 0).all()
        
        products_data = []
        for producto in productos:
            products_data.append({
                'id': producto.id,
                'nombre': producto.nombre,
                'descripcion': producto.descripcion,
                'precio': float(producto.precio),
                'stock': producto.stock
            })
        
        return jsonify({
            'products': products_data,
            'total': len(products_data)
        })
        
    except Exception as e:
        return jsonify({'error': f'Error al obtener productos: {str(e)}'}), 500

@ai_agent_bp.route('/metrics/<session_id>', methods=['GET'])
def get_session_metrics(session_id):
    """Obtiene las métricas de una sesión específica"""
    try:
        metrics = ConversionMetrics.query.filter_by(session_id=session_id).first()
        
        if not metrics:
            return jsonify({'error': 'Sesión no encontrada'}), 404
        
        return jsonify(metrics.to_dict())
        
    except Exception as e:
        return jsonify({'error': f'Error al obtener métricas: {str(e)}'}), 500

@ai_agent_bp.route('/test', methods=['GET'])
def test_ai_widget():
    """Página de prueba para el widget AI"""
    from flask import render_template
    return render_template('test_ai.html')