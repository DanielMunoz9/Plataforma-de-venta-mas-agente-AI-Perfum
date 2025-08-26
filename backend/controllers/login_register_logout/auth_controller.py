from flask import Blueprint, request, jsonify
from extensions import db
from models.usuario import Usuario
from models.rol import Rol
import jwt
import datetime
import os

# Definir un blueprint llamado 'auth' para las rutas de autenticación
auth_bp = Blueprint('auth', __name__)

# Usar una clave secreta tomada del entorno o una por defecto
JWT_SECRET = os.environ.get('JWT_SECRET', 'clave_por_defecto_segura')

# ----------------- REGISTRO DE USUARIOS -----------------
@auth_bp.route('/api/register', methods=['POST'])
def registrar_usuario():
    contenido = request.get_json()

    nombre = contenido.get('nombre')
    correo = contenido.get('correo')
    clave = contenido.get('contraseña')
    rol_id = contenido.get('id_rol', 2)  # Valor predeterminado: cliente

    # Verificar si ya existe un usuario con ese correo
    if Usuario.query.filter_by(correo=correo).first():
        return jsonify({"error": "El correo ya está en uso"}), 400

    # Crear instancia del nuevo usuario
    usuario = Usuario(nombre=nombre, correo=correo, id_rol=rol_id)
    usuario.set_password(clave)

    db.session.add(usuario)
    db.session.commit()

    return jsonify({"mensaje": "Registro exitoso"}), 201

# ----------------- INICIO DE SESIÓN -----------------
@auth_bp.route('/api/login', methods=['POST'])
def iniciar_sesion():
    datos = request.get_json()
    correo = datos.get('correo')
    clave = datos.get('contraseña')

    usuario = Usuario.query.filter_by(correo=correo).first()

    if usuario and usuario.check_password(clave):
        token = jwt.encode({
            'id': usuario.id,
            'exp': datetime.datetime.utcnow() + datetime.timedelta(hours=3)
        }, JWT_SECRET, algorithm='HS256')

        return jsonify({
            "mensaje": "Acceso concedido",
            "token": token,
            "usuario": {
                "id": usuario.id,
                "nombre": usuario.nombre,
                "correo": usuario.correo,
                "rol": usuario.usuario_rol.nombre  # Se asume que existe la relación con Rol
            }
        }), 200

    return jsonify({"error": "Usuario o contraseña incorrectos"}), 401
