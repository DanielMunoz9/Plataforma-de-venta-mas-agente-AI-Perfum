# 📦 Importaciones necesarias
from flask import Blueprint, request, jsonify
from extensions import db
from models.usuario import Usuario
from models.rol import Rol
from middlewares import token_requerido  # 🛡 Importamos el middleware de autenticación

# 🔹 Creamos un blueprint llamado 'usuario' para agrupar las rutas relacionadas con usuarios
usuario_bp = Blueprint('usuario', __name__)

# 🟢 Obtener todos los usuarios (solo admin)
@usuario_bp.route('/api/usuarios', methods=['GET'])
@token_requerido(roles_permitidos=['admin'])
def listar_usuarios():
    # Obtenemos todos los usuarios desde la base de datos
    usuarios = Usuario.query.all()
    resultado = []

    # Convertimos cada objeto Usuario a un diccionario para responder en formato JSON
    for usuario in usuarios:
        resultado.append({
            "id": usuario.id,
            "nombre": usuario.nombre,
            "correo": usuario.correo,
            "rol": usuario.usuario_rol.nombre  # Obtenemos el nombre del rol usando relación con tabla Rol
        })

    return jsonify(resultado), 200

# 🟠 Crear un nuevo usuario (solo admin)
@usuario_bp.route('/api/usuarios', methods=['POST'])
@token_requerido(roles_permitidos=['admin'])
def crear_usuario():
    data = request.get_json()

    # Validaciones mínimas para asegurar datos necesarios
    if not all([data.get('nombre'), data.get('correo'), data.get('contraseña'), data.get('id_rol')]):
        return jsonify({"error": "Faltan campos obligatorios"}), 400

    # Verificamos que no exista otro usuario con el mismo correo
    if Usuario.query.filter_by(correo=data['correo']).first():
        return jsonify({"error": "El correo ya está registrado"}), 400

    # Creamos una nueva instancia de Usuario
    nuevo_usuario = Usuario(
        nombre=data['nombre'],
        correo=data['correo'],
        id_rol=data['id_rol']
    )

    # Encriptamos la contraseña usando el método de la clase Usuario
    nuevo_usuario.set_password(data['contraseña'])

    # Guardamos en la base de datos
    db.session.add(nuevo_usuario)
    db.session.commit()

    return jsonify({"mensaje": "Usuario creado exitosamente", "id": nuevo_usuario.id}), 201

# 🔵 Actualizar un usuario existente (solo admin)
@usuario_bp.route('/api/usuarios/<int:id>', methods=['PUT'])
@token_requerido(roles_permitidos=['admin'])
def actualizar_usuario(id):
    usuario = Usuario.query.get(id)
    if not usuario:
        return jsonify({"error": "Usuario no encontrado"}), 404

    data = request.get_json()

    # Actualizamos los campos si vienen en la petición
    usuario.nombre = data.get('nombre', usuario.nombre)
    usuario.correo = data.get('correo', usuario.correo)
    usuario.id_rol = data.get('id_rol', usuario.id_rol)

    # Si se incluye contraseña, se actualiza y se encripta
    if 'contraseña' in data:
        usuario.set_password(data['contraseña'])

    # Guardamos cambios
    db.session.commit()

    return jsonify({"mensaje": "Usuario actualizado exitosamente"}), 200

# 🔴 Eliminar un usuario por ID (solo admin)
@usuario_bp.route('/api/usuarios/<int:id>', methods=['DELETE'])
@token_requerido(roles_permitidos=['admin'])
def eliminar_usuario(id):
    usuario = Usuario.query.get(id)
    if not usuario:
        return jsonify({"error": "Usuario no encontrado"}), 404

    # Eliminamos de la base de datos
    db.session.delete(usuario)
    db.session.commit()

    return jsonify({"mensaje": "Usuario eliminado exitosamente"}), 200
