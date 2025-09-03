# AI Chat Controller for Amélie integration
from flask import Blueprint, request, jsonify
import os
import logging

# Configure logger
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Create Blueprint for AI routes
ai_bp = Blueprint('ai_bp', __name__)

@ai_bp.route('/api/ai/chat', methods=['POST'])
def chat():
    """
    Simple AI chat endpoint for Amélie integration
    Expected payload: {"message": "user message"}
    Returns: {"reply": "AI response"}
    """
    try:
        # Get the request data
        data = request.get_json()
        if not data or 'message' not in data:
            return jsonify({"error": "Message is required"}), 400
        
        user_message = data['message'].strip()
        if not user_message:
            return jsonify({"error": "Message cannot be empty"}), 400
        
        # Check for API key if configured
        api_key = request.headers.get('X-API-KEY')
        expected_key = os.environ.get('AMELIE_API_KEY')
        
        if expected_key and api_key != expected_key:
            return jsonify({"error": "Invalid API key"}), 401
        
        logger.info(f"Received chat message: {user_message[:100]}...")
        
        # Simple AI response logic (can be enhanced with actual AI integration)
        ai_response = generate_ai_response(user_message)
        
        return jsonify({"reply": ai_response})
        
    except Exception as e:
        logger.error(f"Error in chat endpoint: {str(e)}")
        return jsonify({"error": "Internal server error"}), 500

def generate_ai_response(message):
    """
    Generate AI response - placeholder implementation
    In production, this would integrate with actual AI service
    """
    message_lower = message.lower()
    
    # Simple response logic for Vane France perfume store
    if any(word in message_lower for word in ['hola', 'hello', 'hi', 'buenos días', 'buenas tardes']):
        return "¡Hola! Soy Amélie, tu asistente virtual de Vane France. ¿En qué puedo ayudarte hoy? Puedo ayudarte con información sobre nuestros perfumes, horarios de atención o cualquier consulta que tengas."
    
    elif any(word in message_lower for word in ['perfume', 'fragancia', 'producto']):
        return "En Vane France tenemos una amplia selección de perfumes franceses de alta calidad. Contamos con fragancias para emprendedores y clientes finales. ¿Te interesa algún tipo específico de fragancia?"
    
    elif any(word in message_lower for word in ['precio', 'costo', 'cuanto']):
        return "Los precios varían según el producto y tu plan (Emprendedor o Cliente). Te recomiendo visitar nuestro catálogo o contactarnos por WhatsApp al 319 3605666 para información específica de precios."
    
    elif any(word in message_lower for word in ['horario', 'hora', 'abierto', 'cerrado']):
        return "Nuestros horarios son: Lunes a Sábado de 9:00 AM a 7:00 PM. Domingos cerrados. Tenemos dos ubicaciones en Bogotá: Cl. 12 #13-99 a 13, 1 y Cl. 12 #13-69 Local 102."
    
    elif any(word in message_lower for word in ['ubicación', 'dirección', 'donde', 'localización']):
        return "Tenemos dos tiendas en Bogotá: 📍 Cl. 12 #13-99 a 13, 1, Bogotá y 📍 Cl. 12 #13-69 Local 102, Bogotá. ¿Te gustaría que te ayude con indicaciones para llegar?"
    
    elif any(word in message_lower for word in ['whatsapp', 'contacto', 'teléfono']):
        return "Puedes contactarnos por WhatsApp al 319 3605666. Nuestro equipo estará encantado de ayudarte con cualquier consulta sobre nuestros productos."
    
    elif any(word in message_lower for word in ['emprendedor', 'negocio', 'distribuidor']):
        return "¡Perfecto! Nuestro Plan Emprendedor está diseñado para personas que quieren iniciar su propio negocio de perfumes. Ofrecemos precios especiales y productos exclusivos. ¿Te gustaría conocer más detalles?"
    
    elif any(word in message_lower for word in ['cliente', 'comprar', 'personal']):
        return "Como cliente individual, puedes acceder a nuestra amplia gama de perfumes franceses de alta calidad. Tenemos opciones para todos los gustos y ocasiones. ¿Hay algún tipo de fragancia en particular que te interese?"
    
    elif any(word in message_lower for word in ['gracias', 'thank', 'adiós', 'chao', 'bye']):
        return "¡De nada! Ha sido un placer ayudarte. Si tienes más preguntas, no dudes en contactarnos. ¡Que tengas un excelente día! 🌸"
    
    else:
        return "Gracias por tu mensaje. Soy Amélie, tu asistente de Vane France. Puedo ayudarte con información sobre nuestros perfumes, horarios, ubicaciones y planes. ¿Podrías contarme más específicamente en qué te puedo ayudar?"