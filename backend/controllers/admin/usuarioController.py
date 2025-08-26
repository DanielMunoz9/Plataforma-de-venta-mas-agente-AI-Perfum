# Importamos los módulos necesarios de Flask
from flask import Blueprint, request, jsonify
# Importamos la extensión de base de datos SQLAlchemy
from extensions import db
# Importamos los modelos de Usuario y Rol
from models.usuario import Usuario
from models.rol import Rol
# Importamos el decorador de permisos para proteger las rutas
from decorators.jwt_admin import admin_required

# Creamos el Blueprint para agrupar las rutas de usuario
usuario_bp = Blueprint('usuario', __name__)

# ---------------------- RUTA: Obtener todos los usuarios ----------------------
@usuario_bp.route('/api/usuarios', methods=['GET'])
@admin_required
def obtener_usuarios():
    try:
        usuarios = Usuario.query.all()
        resultado = []
        for usuario in usuarios:
            resultado.append({
                'id': usuario.id,
                'nombre': usuario.nombre,
                'correo': usuario.correo,
                'rol': usuario.rol.nombre,
                'tipo_cliente': usuario.tipo_cliente  # Incluimos el tipo de cliente
            })
        return jsonify(resultado), 200
    except Exception as e:
        return jsonify({'error': 'Error al obtener usuarios', 'detalle': str(e)}), 500

# ---------------------- RUTA: Crear un nuevo usuario ----------------------
@usuario_bp.route('/api/usuarios', methods=['POST'])
@admin_required
def crear_usuario():
    try:
        data = request.get_json()

        # Verificamos campos obligatorios
        campos_requeridos = ('nombre', 'correo', 'password', 'id_rol', 'tipo_cliente')
        if not all(k in data for k in campos_requeridos):
            return jsonify({'error': 'Faltan campos obligatorios'}), 400

        # Validamos tipo_cliente
        if data['tipo_cliente'] not in ['mayorista', 'minorista']:
            return jsonify({'error': 'Tipo de cliente inválido'}), 400

        if Usuario.query.filter_by(correo=data['correo']).first():
            return jsonify({'error': 'El correo ya está registrado'}), 400

        nuevo_usuario = Usuario(
            nombre=data['nombre'],
            correo=data['correo'],
            id_rol=data['id_rol'],
            tipo_cliente=data['tipo_cliente']
        )
        nuevo_usuario.set_password(data['password'])

        db.session.add(nuevo_usuario)
        db.session.commit()

        return jsonify({'mensaje': 'Usuario creado exitosamente', 'id': nuevo_usuario.id}), 201

    except Exception as e:
        db.session.rollback()
        return jsonify({'error': 'Error al crear el usuario', 'detalle': str(e)}), 500

# ---------------------- RUTA: Actualizar un usuario existente ----------------------
@usuario_bp.route('/api/usuarios/<int:id>', methods=['PUT'])
@admin_required
def actualizar_usuario(id):
    try:
        usuario = Usuario.query.get(id)
        if not usuario:
            return jsonify({'error': 'Usuario no encontrado'}), 404

        data = request.get_json()

        if 'correo' in data and data['correo'] != usuario.correo:
            if Usuario.query.filter_by(correo=data['correo']).first():
                return jsonify({'error': 'El nuevo correo ya está en uso'}), 400

        if 'nombre' in data:
            usuario.nombre = data['nombre']
        if 'correo' in data:
            usuario.correo = data['correo']
        if 'id_rol' in data:
            usuario.id_rol = data['id_rol']
        if 'password' in data:
            usuario.set_password(data['password'])
        if 'tipo_cliente' in data:
            if data['tipo_cliente'] in ['mayorista', 'minorista']:
                usuario.tipo_cliente = data['tipo_cliente']
            else:
                return jsonify({'error': 'Tipo de cliente inválido'}), 400

        db.session.commit()
        return jsonify({'mensaje': 'Usuario actualizado correctamente'}), 200

    except Exception as e:
        db.session.rollback()
        return jsonify({'error': 'Error al actualizar usuario', 'detalle': str(e)}), 500

# ---------------------- RUTA: Eliminar un usuario ----------------------
@usuario_bp.route('/api/usuarios/<int:id>', methods=['DELETE'])
@admin_required
def eliminar_usuario(id):
    try:
        usuario = Usuario.query.get(id)
        if not usuario:
            return jsonify({'error': 'Usuario no encontrado'}), 404

        db.session.delete(usuario)
        db.session.commit()
        return jsonify({'mensaje': 'Usuario eliminado correctamente'}), 200

    except Exception as e:
        db.session.rollback()
        return jsonify({'error': 'Error al eliminar usuario', 'detalle': str(e)}), 500
