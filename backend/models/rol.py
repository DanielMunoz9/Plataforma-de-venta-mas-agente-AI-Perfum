# models/rol.py

from extensions import db

# 📦 Definición del modelo Rol
class Rol(db.Model):
    __tablename__ = 'roles'

    # 🆔 ID del rol (clave primaria)
    id = db.Column(db.Integer, primary_key=True)

    # 📝 Nombre del rol (único y obligatorio)
    nombre = db.Column(db.String(50), unique=True, nullable=False)

    # 🔗 Relación con la tabla Usuario (relación inversa)
    # Esto permite acceder a todos los usuarios que tienen este rol
    usuarios = db.relationship('Usuario', back_populates='rol', lazy=True)
