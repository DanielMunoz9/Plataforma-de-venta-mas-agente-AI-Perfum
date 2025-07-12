from flask import Blueprint, request, jsonify
from extensions import db
from models.usuario import Usuario
from models.rol import Rol
import jwt
import datetime
import os

auth_bp = Blueprint('auth', __name__)

# 🔐 Clave secreta desde entorno o valor por defecto
CLAVE_SECRETA = os.getenv('JWT_SECRET', 'clave_super_secreta')

# 📝 Ruta para registrar un nuevo usuario
@auth_bp.route('/api/register', methods=['POST'])
def register():
    data = request.get_json()
    nombre = data.get('nombre')
    correo = data.get('correo')
    contraseña = data.get('contraseña')
    id_rol = data.get('id_rol', 2)  # rol por defecto: cliente

    if Usuario.query.filter_by(correo=correo).first():
        return jsonify({"error": "El correo ya está registrado"}), 400

    nuevo_usuario = Usuario(nombre=nombre, correo=correo, id_rol=id_rol)
    nuevo_usuario.set_password(contraseña)
    db.session.add(nuevo_usuario)
    db.session.commit()

    return jsonify({"mensaje": "Usuario registrado exitosamente"}), 201

# 🔐 Ruta para iniciar sesión y obtener token JWT
@auth_bp.route('/api/login', methods=['POST'])
def login():
    data = request.get_json()
    correo = data.get('correo')
    contraseña = data.get('contraseña')

    usuario = Usuario.query.filter_by(correo=correo).first()

    if usuario and usuario.check_password(contraseña):
        # Generar el token
        token = jwt.encode({
            'id': usuario.id,
            'exp': datetime.datetime.utcnow() + datetime.timedelta(hours=3)  # Token válido por 3 horas
        }, CLAVE_SECRETA, algorithm='HS256')

        return jsonify({
            "mensaje": "Inicio de sesión exitoso",
            "token": token,
            "usuario": {
                "id": usuario.id,
                "nombre": usuario.nombre,
                "correo": usuario.correo,
                "rol": usuario.usuario_rol.nombre
            }
        }), 200

    return jsonify({"error": "Credenciales inválidas"}), 401
