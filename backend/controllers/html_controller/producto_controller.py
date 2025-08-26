# back/controller/html_controller/producto_controller.py

import logging
from flask import Blueprint, request, jsonify, url_for
from sqlalchemy.exc import SQLAlchemyError
from extensions import db
from models.producto import Producto

# Configuración básica del logger
logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)
handler = logging.StreamHandler()  # imprime en consola
formatter = logging.Formatter('[%(asctime)s] %(levelname)s in %(module)s: %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)

producto_bp = Blueprint('producto', __name__)

@producto_bp.route('/api/productos', methods=['GET'])
def listar_productos():
    try:
        productos = Producto.query.all()
        resultado = [{
            "id": p.id,
            "nombre": p.nombre,
            "descripcion": p.descripcion,
            "precio": float(p.precio),
            "stock": p.stock,
            "imagen_url": url_for('html_views.producto_imagen', id=p.id, _external=True)
        } for p in productos]
        return jsonify(resultado), 200

    except SQLAlchemyError as e:
        logger.exception("Error al listar productos")
        return jsonify({"error": "Error interno al listar productos"}), 500

@producto_bp.route('/api/productos/buscar', methods=['GET'])
def buscar_producto_por_nombre():
    nombre = request.args.get('nombre', '').strip()
    if not nombre:
        logger.warning("Parámetro 'nombre' faltante en búsqueda")
        return jsonify({"error": "Parámetro 'nombre' requerido"}), 400

    try:
        productos = Producto.query.filter(Producto.nombre.ilike(f"%{nombre}%")).all()
        resultado = [{
            "id": p.id,
            "nombre": p.nombre,
            "descripcion": p.descripcion,
            "precio": float(p.precio),
            "stock": p.stock,
            "imagen_url": url_for('html_views.producto_imagen', id=p.id, _external=True)
        } for p in productos]
        return jsonify(resultado), 200

    except SQLAlchemyError as e:
        logger.exception(f"Error al buscar productos con nombre '{nombre}'")
        return jsonify({"error": "Error interno al buscar productos"}), 500

@producto_bp.route('/api/productos', methods=['POST'])
def crear_producto():
    data = request.get_json() or {}
    required = ['nombre', 'descripcion', 'precio']
    missing = [f for f in required if not data.get(f)]
    if missing:
        logger.warning(f"Datos faltantes al crear producto: {missing}")
        return jsonify({"error": f"Faltan campos: {', '.join(missing)}"}), 400

    try:
        nuevo = Producto(
            nombre=data['nombre'],
            descripcion=data['descripcion'],
            precio=data['precio'],
            stock=data.get('stock', 0),
            imagen_blob=None
        )
        db.session.add(nuevo)
        db.session.commit()
        logger.info(f"Producto creado con ID {nuevo.id}")
        return jsonify({"mensaje": "Producto creado exitosamente", "id": nuevo.id}), 201

    except SQLAlchemyError as e:
        db.session.rollback()
        logger.exception("Error al crear producto")
        return jsonify({"error": "Error interno al crear producto"}), 500

@producto_bp.route('/api/productos/<int:id>', methods=['PUT'])
def actualizar_producto(id):
    try:
        producto = Producto.query.get(id)
        if not producto:
            logger.warning(f"Producto con ID {id} no encontrado para actualizar")
            return jsonify({"error": "Producto no encontrado"}), 404

        data = request.get_json() or {}
        producto.nombre = data.get('nombre', producto.nombre)
        producto.descripcion = data.get('descripcion', producto.descripcion)
        producto.precio = data.get('precio', producto.precio)
        producto.stock = data.get('stock', producto.stock)

        db.session.commit()
        logger.info(f"Producto con ID {id} actualizado")
        return jsonify({"mensaje": "Producto actualizado exitosamente"}), 200

    except SQLAlchemyError as e:
        db.session.rollback()
        logger.exception(f"Error al actualizar producto con ID {id}")
        return jsonify({"error": "Error interno al actualizar producto"}), 500

@producto_bp.route('/api/productos/<int:id>', methods=['DELETE'])
def eliminar_producto(id):
    try:
        producto = Producto.query.get(id)
        if not producto:
            logger.warning(f"Producto con ID {id} no encontrado para eliminar")
            return jsonify({"error": "Producto no encontrado"}), 404

        db.session.delete(producto)
        db.session.commit()
        logger.info(f"Producto con ID {id} eliminado")
        return jsonify({"mensaje": "Producto eliminado exitosamente"}), 200

    except SQLAlchemyError as e:
        db.session.rollback()
        logger.exception(f"Error al eliminar producto con ID {id}")
        return jsonify({"error": "Error interno al eliminar producto"}), 500
