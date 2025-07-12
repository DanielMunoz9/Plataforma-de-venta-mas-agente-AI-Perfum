# 📦 Importaciones necesarias de Flask y módulos internos
from flask import Blueprint, request, jsonify
from extensions import db
from models.producto import Producto

# 🔹 Creamos un Blueprint llamado 'producto' para agrupar las rutas relacionadas con productos
producto_bp = Blueprint('producto', __name__)

# 🟢 Ruta para obtener la lista de todos los productos disponibles
@producto_bp.route('/api/productos', methods=['GET'])
def listar_productos():
    # Obtener todos los productos de la base de datos
    productos = Producto.query.all()
    resultado = []

    # Convertimos los objetos Producto a diccionarios para enviarlos como JSON
    for producto in productos:
        resultado.append({
            "id": producto.id,
            "nombre": producto.nombre,
            "descripcion": producto.descripcion,
            "precio": float(producto.precio),
            "stock": producto.stock,
            "imagen_url": producto.imagen_url
        })

    # Devolvemos la lista de productos con código 200 (OK)
    return jsonify(resultado), 200

# 🔍 Ruta para buscar productos por nombre (filtro parcial, insensible a mayúsculas)
@producto_bp.route('/api/productos/buscar', methods=['GET'])
def buscar_producto_por_nombre():
    # Obtenemos el valor del parámetro "nombre" de la URL
    nombre = request.args.get('nombre', '').strip()

    # Si el parámetro no se envía, devolvemos error 400 (Bad Request)
    if not nombre:
        return jsonify({"error": "Parámetro 'nombre' requerido"}), 400

    # Buscamos productos cuyo nombre contenga la cadena enviada (usando ilike para no distinguir mayúsculas)
    productos = Producto.query.filter(Producto.nombre.ilike(f"%{nombre}%")).all()

    # Si no hay coincidencias, devolvemos una lista vacía
    if not productos:
        return jsonify([]), 200

    resultado = []
    for producto in productos:
        resultado.append({
            "id": producto.id,
            "nombre": producto.nombre,
            "descripcion": producto.descripcion,
            "precio": float(producto.precio),
            "stock": producto.stock,
            "imagen_url": producto.imagen_url
        })

    # Devolvemos los productos encontrados con código 200
    return jsonify(resultado), 200

# 🟠 Ruta para crear un nuevo producto
@producto_bp.route('/api/productos', methods=['POST'])
def crear_producto():
    # Obtenemos los datos enviados por el cliente en formato JSON
    data = request.get_json()

    # Creamos una nueva instancia de Producto
    nuevo = Producto(
        nombre=data.get('nombre'),
        descripcion=data.get('descripcion'),
        precio=data.get('precio'),
        stock=data.get('stock', 0),  # Si no se especifica el stock, se usa 0
        imagen_url=data.get('imagen_url')
    )

    # Añadimos y confirmamos en la base de datos
    db.session.add(nuevo)
    db.session.commit()

    # Respondemos con un mensaje de éxito y el ID del nuevo producto
    return jsonify({"mensaje": "Producto creado exitosamente", "id": nuevo.id}), 201

# 🔵 Ruta para actualizar un producto existente mediante su ID
@producto_bp.route('/api/productos/<int:id>', methods=['PUT'])
def actualizar_producto(id):
    # Buscamos el producto por su ID
    producto = Producto.query.get(id)
    if not producto:
        return jsonify({"error": "Producto no encontrado"}), 404

    # Obtenemos los nuevos datos del producto
    data = request.get_json()

    # Actualizamos los campos solo si fueron enviados
    producto.nombre = data.get('nombre', producto.nombre)
    producto.descripcion = data.get('descripcion', producto.descripcion)
    producto.precio = data.get('precio', producto.precio)
    producto.stock = data.get('stock', producto.stock)
    producto.imagen_url = data.get('imagen_url', producto.imagen_url)

    # Guardamos los cambios
    db.session.commit()

    return jsonify({"mensaje": "Producto actualizado exitosamente"}), 200

# 🔴 Ruta para eliminar un producto existente por ID
@producto_bp.route('/api/productos/<int:id>', methods=['DELETE'])
def eliminar_producto(id):
    # Buscamos el producto por ID
    producto = Producto.query.get(id)
    if not producto:
        return jsonify({"error": "Producto no encontrado"}), 404

    # Eliminamos el producto y guardamos cambios
    db.session.delete(producto)
    db.session.commit()

    return jsonify({"mensaje": "Producto eliminado exitosamente"}), 200
