from flask import Blueprint, jsonify, request
from models.orden import Orden
from models.detalle_orden import DetalleOrden
from models.producto import Producto
from models.usuario import Usuario
from models.estado_orden_compra import EstadoOrdenCompra
from extensions import db
from middlewares import token_requerido  # 🛡 Importamos el middleware

# 🧩 Blueprint para agrupar rutas del historial (admin)
admin_historial_bp = Blueprint('admin_historial', __name__)

# 📋 Obtener todas las órdenes del sistema (opcionalmente filtradas por estado)
@admin_historial_bp.route('/api/admin/ordenes', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])  # 🔐 Solo administradores
def obtener_todas_las_ordenes():
    estado_nombre = request.args.get('estado')  # Obtenemos parámetro ?estado=

    # Si se proporciona un estado, filtramos por él
    if estado_nombre:
        estado = EstadoOrdenCompra.query.filter_by(estado=estado_nombre).first()
        if not estado:
            return jsonify({"error": "Estado no válido"}), 400
        ordenes = Orden.query.filter_by(id_estado=estado.id).order_by(Orden.fecha.desc()).all()
    else:
        # Si no, traemos todas las órdenes
        ordenes = Orden.query.order_by(Orden.fecha.desc()).all()

    resultado = []
    for orden in ordenes:
        usuario = Usuario.query.get(orden.id_usuario)
        resultado.append({
            "id": orden.id,
            "usuario": usuario.nombre,
            "correo": usuario.correo,
            "fecha": orden.fecha.strftime("%Y-%m-%d %H:%M:%S"),
            "total": float(orden.total),
            "estado": orden.estado.estado
        })

    return jsonify(resultado), 200

# 📘 Obtener todos los estados posibles de una orden (para dropdown en frontend)
@admin_historial_bp.route('/api/admin/estados', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])  # 🔐 Solo administradores
def obtener_estados_orden():
    estados = EstadoOrdenCompra.query.all()
    resultado = [{"id": e.id, "estado": e.estado} for e in estados]
    return jsonify(resultado), 200

# 🔄 Actualizar el estado de una orden existente
@admin_historial_bp.route('/api/admin/ordenes/<int:id_orden>/estado', methods=['PUT'])
@token_requerido(roles_permitidos=['admin'])  # 🔐 Solo administradores
def actualizar_estado_orden(id_orden):
    orden = Orden.query.get(id_orden)
    if not orden:
        return jsonify({"error": "Orden no encontrada"}), 404

    data = request.get_json()
    nuevo_estado_nombre = data.get('estado')

    if not nuevo_estado_nombre:
        return jsonify({"error": "Debe proporcionar un estado válido"}), 400

    nuevo_estado = EstadoOrdenCompra.query.filter_by(estado=nuevo_estado_nombre).first()
    if not nuevo_estado:
        return jsonify({"error": "Estado no válido"}), 400

    orden.id_estado = nuevo_estado.id
    db.session.commit()

    return jsonify({"mensaje": f"Estado de la orden {id_orden} actualizado a '{nuevo_estado_nombre}'"}), 200

# 🔎 Ver el detalle completo de una orden específica
@admin_historial_bp.route('/api/admin/ordenes/<int:id_orden>', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])  # 🔐 Solo administradores
def ver_orden_detalle_admin(id_orden):
    orden = Orden.query.get(id_orden)
    if not orden:
        return jsonify({"error": "Orden no encontrada"}), 404

    usuario = Usuario.query.get(orden.id_usuario)
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
        "orden_id": orden.id,
        "usuario": usuario.nombre,
        "correo": usuario.correo,
        "fecha": orden.fecha.strftime("%Y-%m-%d %H:%M:%S"),
        "estado": orden.estado.estado,
        "total": float(orden.total),
        "nombre_destinatario": orden.nombre_destinatario,
        "telefono": orden.telefono,
        "telefono_alterno": orden.telefono_alterno,
        "direccion_envio": orden.direccion_envio,
        "ciudad_envio": orden.ciudad_envio,
        "items": items
    }

    return jsonify(resultado), 200
