from flask import Blueprint, request, jsonify
from extensions import db
from models.orden import Orden
from models.detalle_orden import DetalleOrden
from models.producto import Producto
from models.estado_orden_compra import EstadoOrdenCompra

# Creamos un Blueprint llamado orden_bp
orden_bp = Blueprint('orden', __name__)

# Ruta para crear una nueva orden (compra)
@orden_bp.route('/api/ordenes', methods=['POST'])
def crear_orden():
    data = request.get_json()

    try:
        # 📦 Datos básicos
        id_usuario = data.get('id_usuario')
        productos = data.get('productos', [])  # Lista de productos: [{id_producto, cantidad}]
        nombre_destinatario = data.get('nombre_destinatario')
        telefono = data.get('telefono')
        telefono_alterno = data.get('telefono_alterno')
        direccion_envio = data.get('direccion_envio')
        ciudad_envio = data.get('ciudad_envio')

        # ⚠️ Validación básica
        if not productos:
            return jsonify({"error": "No hay productos en la orden"}), 400

        # 🧮 Calcular el total
        total = 0
        for item in productos:
            producto = Producto.query.get(item['id_producto'])
            if not producto:
                return jsonify({"error": f"Producto con ID {item['id_producto']} no encontrado"}), 404
            if producto.stock < item['cantidad']:
                return jsonify({"error": f"No hay suficiente stock para {producto.nombre}"}), 400
            total += producto.precio * item['cantidad']

        # 🛒 Crear la orden
        nueva_orden = Orden(
            id_usuario=id_usuario,
            total=total,
            id_estado=1,  # estado "pendiente" (ID=1)
            nombre_destinatario=nombre_destinatario,
            telefono=telefono,
            telefono_alterno=telefono_alterno,
            direccion_envio=direccion_envio,
            ciudad_envio=ciudad_envio
        )
        db.session.add(nueva_orden)
        db.session.flush()  # Obtener ID antes de hacer commit

        # 🧾 Crear detalle por cada producto
        for item in productos:
            detalle = DetalleOrden(
                id_orden=nueva_orden.id,
                id_producto=item['id_producto'],
                cantidad=item['cantidad'],
                precio_unitario=Producto.query.get(item['id_producto']).precio
            )
            db.session.add(detalle)

            # 🧯 Descontar stock del producto
            producto = Producto.query.get(item['id_producto'])
            producto.stock -= item['cantidad']

        db.session.commit()

        return jsonify({"mensaje": "Orden creada exitosamente", "id_orden": nueva_orden.id}), 201

    except Exception as e:
        db.session.rollback()
        return jsonify({"error": str(e)}), 500
