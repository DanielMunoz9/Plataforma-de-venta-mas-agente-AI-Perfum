from flask import Blueprint, request, jsonify
from models.producto import Producto
from extensions import db
from middlewares import token_requerido  # 🛡 Importamos middleware para proteger rutas

# 🧩 Creamos un Blueprint específico para productos del administrador
admin_producto_bp = Blueprint('admin_producto', __name__)

# 📋 Obtener todos los productos (ordenados por nombre ascendente)
@admin_producto_bp.route('/api/admin/productos', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])  # Solo accesible por admin
def obtener_productos_admin():
    productos = Producto.query.order_by(Producto.nombre.asc()).all()
    resultado = [{
        "id": p.id,
        "nombre": p.nombre,
        "descripcion": p.descripcion,
        "precio": float(p.precio),
        "stock": p.stock,
        "imagen_url": p.imagen_url
    } for p in productos]

    return jsonify(resultado), 200

# 🔍 Buscar productos por nombre (búsqueda parcial, insensible a mayúsculas)
@admin_producto_bp.route('/api/admin/productos/buscar', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])
def buscar_producto_admin():
    nombre = request.args.get('nombre', '').strip()

    if not nombre:
        return jsonify({"error": "Parámetro 'nombre' requerido"}), 400

    productos = Producto.query.filter(Producto.nombre.ilike(f"%{nombre}%")).all()

    resultado = [{
        "id": p.id,
        "nombre": p.nombre,
        "descripcion": p.descripcion,
        "precio": float(p.precio),
        "stock": p.stock,
        "imagen_url": p.imagen_url
    } for p in productos]

    return jsonify(resultado), 200

# 🆕 Crear un nuevo producto
@admin_producto_bp.route('/api/admin/productos', methods=['POST'])
@token_requerido(roles_permitidos=['admin'])
def crear_producto_admin():
    data = request.get_json()

    nuevo = Producto(
        nombre=data.get('nombre'),
        descripcion=data.get('descripcion'),
        precio=data.get('precio'),
        stock=data.get('stock', 0),  # Si no se especifica stock, se asume 0
        imagen_url=data.get('imagen_url')
    )

    db.session.add(nuevo)
    db.session.commit()

    return jsonify({"mensaje": "Producto creado exitosamente", "id": nuevo.id}), 201

# ✏️ Actualizar producto existente
@admin_producto_bp.route('/api/admin/productos/<int:id>', methods=['PUT'])
@token_requerido(roles_permitidos=['admin'])
def actualizar_producto_admin(id):
    producto = Producto.query.get(id)
    if not producto:
        return jsonify({"error": "Producto no encontrado"}), 404

    data = request.get_json()

    producto.nombre = data.get('nombre', producto.nombre)
    producto.descripcion = data.get('descripcion', producto.descripcion)
    producto.precio = data.get('precio', producto.precio)
    producto.stock = data.get('stock', producto.stock)
    producto.imagen_url = data.get('imagen_url', producto.imagen_url)

    db.session.commit()

    return jsonify({"mensaje": "Producto actualizado exitosamente"}), 200

# ❌ Eliminar producto
@admin_producto_bp.route('/api/admin/productos/<int:id>', methods=['DELETE'])
@token_requerido(roles_permitidos=['admin'])
def eliminar_producto_admin(id):
    producto = Producto.query.get(id)
    if not producto:
        return jsonify({"error": "Producto no encontrado"}), 404

    db.session.delete(producto)
    db.session.commit()

    return jsonify({"mensaje": "Producto eliminado exitosamente"}), 200
