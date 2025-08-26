# controllers/admin/admin_historial_controller.py

from flask import Blueprint, jsonify, request
from models.orden import Orden
from models.detalle_orden import DetalleOrden
from models.producto import Producto
from models.usuario import Usuario
from models.estado_orden_compra import EstadoOrdenCompra
from extensions import db
from middlewares import token_requerido  # 🛡 Middleware para validar token y rol

# 🧩 Creamos un Blueprint para agrupar las rutas relacionadas con el historial de órdenes
admin_historial_bp = Blueprint('admin_historial', __name__)

# 📋 Ruta para obtener todas las órdenes (con posibilidad de filtrarlas por estado)
@admin_historial_bp.route('/api/admin/ordenes', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])  # 🔐 Solo accesible para administradores
def obtener_todas_las_ordenes():
    estado_nombre = request.args.get('estado')  # Parámetro opcional: ?estado=Enviado

    # 🔍 Si se solicita filtrar por estado
    if estado_nombre:
        estado = EstadoOrdenCompra.query.filter_by(estado=estado_nombre).first()
        if not estado:
            return jsonify({"error": "Estado no válido"}), 400
        # 🧮 Ordenamos las órdenes más recientes primero
        ordenes = Orden.query.filter_by(id_estado=estado.id).order_by(Orden.fecha.desc()).all()
    else:
        # 🧾 Si no hay filtro, retornamos todas las órdenes
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

# 📘 Ruta para obtener todos los estados posibles de una orden
@admin_historial_bp.route('/api/admin/estados', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])  # 🔐 Solo accesible para administradores
def obtener_estados_orden():
    estados = EstadoOrdenCompra.query.all()
    resultado = [{"id": e.id, "estado": e.estado} for e in estados]
    return jsonify(resultado), 200

# 🔄 Ruta para actualizar el estado de una orden existente
@admin_historial_bp.route('/api/admin/ordenes/<int:id_orden>/estado', methods=['PUT'])
@token_requerido(roles_permitidos=['admin'])  # 🔐 Solo accesible para administradores
def actualizar_estado_orden(id_orden):
    orden = Orden.query.get(id_orden)
    if not orden:
        return jsonify({"error": "Orden no encontrada"}), 404

    data = request.get_json()
    nuevo_estado_nombre = data.get('estado')

    # ❗ Validación: ¿el estado fue proporcionado?
    if not nuevo_estado_nombre:
        return jsonify({"error": "Debe proporcionar un estado válido"}), 400

    nuevo_estado = EstadoOrdenCompra.query.filter_by(estado=nuevo_estado_nombre).first()
    if not nuevo_estado:
        return jsonify({"error": "Estado no válido"}), 400

    # ✅ Actualización en base de datos
    orden.id_estado = nuevo_estado.id
    db.session.commit()

    return jsonify({
        "mensaje": f"Estado de la orden {id_orden} actualizado a '{nuevo_estado_nombre}'"
    }), 200

# 🔍 Ruta para ver el detalle completo de una orden específica
@admin_historial_bp.route('/api/admin/ordenes/<int:id_orden>', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])  # 🔐 Solo accesible para administradores
def ver_orden_detalle_admin(id_orden):
    orden = Orden.query.get(id_orden)
    if not orden:
        return jsonify({"error": "Orden no encontrada"}), 404

    usuario = Usuario.query.get(orden.id_usuario)
    detalles = DetalleOrden.query.filter_by(id_orden=id_orden).all()

    # 🧾 Construimos el listado de productos de la orden
    items = []
    for detalle in detalles:
        producto = Producto.query.get(detalle.id_producto)
        items.append({
            "nombre_producto": producto.nombre,
            "cantidad": detalle.cantidad,
            "precio_unitario": float(detalle.precio_unitario),
            "subtotal": float(detalle.precio_unitario) * detalle.cantidad
        })

    # 📦 Resumen completo de la orden
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
