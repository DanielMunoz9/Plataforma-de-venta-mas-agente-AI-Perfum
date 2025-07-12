# middlewares.py

from functools import wraps
from flask import request, jsonify
from models.usuario import Usuario
from models.rol import Rol
import jwt
import os

# 🔐 Clave secreta para decodificar JWT
CLAVE_SECRETA = os.getenv('JWT_SECRET', 'clave_super_secreta')

# ✅ Middleware con validación de token + verificación de roles opcional
def token_requerido(roles_permitidos=None):
    def decorador(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            token = None

            # 🔎 Obtenemos el token del encabezado Authorization: Bearer <token>
            if 'Authorization' in request.headers:
                auth_header = request.headers['Authorization']
                if auth_header.startswith('Bearer '):
                    token = auth_header.split(' ')[1]

            if not token:
                return jsonify({'error': 'Token no proporcionado'}), 401

            try:
                # ✅ Decodificamos el token
                data = jwt.decode(token, CLAVE_SECRETA, algorithms=['HS256'])
                usuario = Usuario.query.get(data['id'])
                if not usuario:
                    return jsonify({'error': 'Usuario inválido'}), 401

                # 🔐 Verificamos si su rol está autorizado (si se indicó)
                if roles_permitidos:
                    rol = Rol.query.get(usuario.id_rol)
                    if not rol or rol.nombre.lower() not in [r.lower() for r in roles_permitidos]:
                        return jsonify({'error': f'Acceso denegado para rol {rol.nombre}'}), 403

                # 📌 Guardamos el usuario actual en la petición si se necesita luego
                request.usuario_actual = usuario

            except jwt.ExpiredSignatureError:
                return jsonify({'error': 'Token expirado'}), 401
            except jwt.InvalidTokenError:
                return jsonify({'error': 'Token inválido'}), 401

            return func(*args, **kwargs)
        return wrapper
    return decorador
