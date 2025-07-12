# 📦 Importamos los módulos necesarios de Flask y SQLAlchemy
from flask import Blueprint, request, jsonify
from extensions import db
from models.carrito import Carrito
from models.carrito_producto import CarritoProducto
from models.producto import Producto

# 🧩 Creamos un Blueprint llamado 'carrito'
carrito_bp = Blueprint('carrito', __name__)

# 🟢 Obtener el carrito de un usuario específico
@carrito_bp.route('/api/carrito/<int:id_usuario>', methods=['GET'])
def obtener_carrito(id_usuario):
    # 🔎 Buscar el carrito del usuario
    carrito = Carrito.query.filter_by(id_usuario=id_usuario).first()

    # 🧾 Si no hay carrito aún, devolver lista vacía
    if not carrito:
        return jsonify({"productos": []}), 200

    productos = []
    # 🔄 Recorremos los productos del carrito
    for item in carrito.productos:
        productos.append({
            "id_producto": item.producto.id,
            "nombre": item.producto.nombre,
            "precio": float(item.producto.precio),
            "cantidad": item.cantidad,
            "imagen_url": item.producto.imagen_url
        })

    # ✅ Retornamos el contenido del carrito
    return jsonify({"id_carrito": carrito.id, "productos": productos}), 200

# 🟠 Agregar un producto al carrito
@carrito_bp.route('/api/carrito/<int:id_usuario>', methods=['POST'])
def agregar_producto(id_usuario):
    # 📥 Obtenemos los datos del producto desde el cuerpo JSON
    data = request.get_json()
    id_producto = data.get('id_producto')
    cantidad = data.get('cantidad', 1)

    # 🔍 Buscar el carrito del usuario o crearlo si no existe
    carrito = Carrito.query.filter_by(id_usuario=id_usuario).first()
    if not carrito:
        carrito = Carrito(id_usuario=id_usuario)
        db.session.add(carrito)
        db.session.commit()

    # 🔁 Verificamos si ya existe ese producto en el carrito
    item = CarritoProducto.query.filter_by(id_carrito=carrito.id, id_producto=id_producto).first()
    if item:
        # ➕ Si ya existe, solo aumentamos la cantidad
        item.cantidad += cantidad
    else:
        # 🆕 Si no existe, lo agregamos como nuevo
        nuevo_item = CarritoProducto(id_carrito=carrito.id, id_producto=id_producto, cantidad=cantidad)
        db.session.add(nuevo_item)

    db.session.commit()
    return jsonify({"mensaje": "Producto agregado al carrito"}), 201

# 🔵 Actualizar la cantidad de un producto en el carrito
@carrito_bp.route('/api/carrito/<int:id_usuario>', methods=['PUT'])
def actualizar_cantidad(id_usuario):
    # 📥 Recibimos el ID del producto y la nueva cantidad
    data = request.get_json()
    id_producto = data.get('id_producto')
    cantidad = data.get('cantidad')

    # 🔍 Buscar el carrito del usuario
    carrito = Carrito.query.filter_by(id_usuario=id_usuario).first()
    if not carrito:
        return jsonify({"error": "Carrito no encontrado"}), 404

    # 🔍 Buscar el producto en el carrito
    item = CarritoProducto.query.filter_by(id_carrito=carrito.id, id_producto=id_producto).first()
    if not item:
        return jsonify({"error": "Producto no encontrado en el carrito"}), 404

    # 🔁 Actualizar la cantidad
    item.cantidad = cantidad
    db.session.commit()
    return jsonify({"mensaje": "Cantidad actualizada"}), 200

# 🔴 Eliminar un producto del carrito
@carrito_bp.route('/api/carrito/<int:id_usuario>/<int:id_producto>', methods=['DELETE'])
def eliminar_producto(id_usuario, id_producto):
    # 🔍 Buscar el carrito del usuario
    carrito = Carrito.query.filter_by(id_usuario=id_usuario).first()
    if not carrito:
        return jsonify({"error": "Carrito no encontrado"}), 404

    # 🔍 Buscar el producto en el carrito
    item = CarritoProducto.query.filter_by(id_carrito=carrito.id, id_producto=id_producto).first()
    if not item:
        return jsonify({"error": "Producto no encontrado en el carrito"}), 404

    # 🗑️ Eliminar el producto
    db.session.delete(item)
    db.session.commit()
    return jsonify({"mensaje": "Producto eliminado del carrito"}), 200

# 🧹 Vaciar todo el carrito del usuario
@carrito_bp.route('/api/carrito/<int:id_usuario>/vaciar', methods=['DELETE'])
def vaciar_carrito(id_usuario):
    # 🔍 Buscar el carrito del usuario
    carrito = Carrito.query.filter_by(id_usuario=id_usuario).first()
    if not carrito:
        return jsonify({"error": "Carrito no encontrado"}), 404

    # 🗑️ Eliminar todos los productos del carrito
    CarritoProducto.query.filter_by(id_carrito=carrito.id).delete()
    db.session.commit()
    return jsonify({"mensaje": "Carrito vaciado"}), 200
