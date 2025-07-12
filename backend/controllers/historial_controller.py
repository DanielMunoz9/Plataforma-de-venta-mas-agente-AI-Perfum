from flask import Blueprint, jsonify
from models.orden import Orden
from models.detalle_orden import DetalleOrden
from models.producto import Producto

# 🧩 Creamos el blueprint para historial de órdenes
historial_bp = Blueprint('historial', __name__)

# 📋 Obtener todas las órdenes realizadas por un usuario
@historial_bp.route('/api/historial/<int:id_usuario>', methods=['GET'])
def obtener_historial(id_usuario):
    # 🔍 Buscar todas las órdenes de ese usuario
    ordenes = Orden.query.filter_by(id_usuario=id_usuario).order_by(Orden.fecha.desc()).all()

    resultado = []
    for orden in ordenes:
        resultado.append({
            "id": orden.id,
            "fecha": orden.fecha.strftime("%Y-%m-%d %H:%M:%S"),
            "total": float(orden.total),
            "estado": orden.estado.estado,  # Relación con EstadoOrdenCompra
        })

    return jsonify(resultado), 200

# 🔎 Ver detalles de una orden específica
@historial_bp.route('/api/historial/orden/<int:id_orden>', methods=['GET'])
def obtener_detalle_orden(id_orden):
    # 🔍 Buscar la orden
    orden = Orden.query.get(id_orden)
    if not orden:
        return jsonify({"error": "Orden no encontrada"}), 404

    # 🛍️ Traer todos los ítems de esa orden
    detalles = DetalleOrden.query.filter_by(id_orden=id_orden).all()

    items = []
    for detalle in detalles:
        producto = Producto.query.get(detalle.id_producto)
        items.append({
            "nombre_producto": producto.nombre,
            "cantidad": detalle.cantidad,
            "precio_unitario": float(detalle.precio_unitario),
            "subtotal": float(detalle.precio_unitario) * detalle.cantidad
        })

    resultado = {
        "id": orden.id,
        "fecha": orden.fecha.strftime("%Y-%m-%d %H:%M:%S"),
        "total": float(orden.total),
        "estado": orden.estado.estado,
        "nombre_destinatario": orden.nombre_destinatario,
        "telefono": orden.telefono,
        "telefono_alterno": orden.telefono_alterno,
        "direccion_envio": orden.direccion_envio,
        "ciudad_envio": orden.ciudad_envio,
        "items": items
    }

    return jsonify(resultado), 200
