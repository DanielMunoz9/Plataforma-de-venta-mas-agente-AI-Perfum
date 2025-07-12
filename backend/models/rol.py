# Importamos la instancia de SQLAlchemy desde extensions.py
from extensions import db

# Modelo que representa los diferentes roles de usuario en el sistema
class Rol(db.Model):
    # Nombre de la tabla en la base de datos
    __tablename__ = 'roles'

    # Clave primaria: ID único para cada rol (por ejemplo: 1 = Admin, 2 = Cliente)
    id = db.Column(db.Integer, primary_key=True)

    # Nombre del rol, por ejemplo: "admin", "cliente"
    # Debe ser único y obligatorio
    nombre = db.Column(db.String(50), unique=True, nullable=False)

    # Relación uno-a-muchos con Usuario
    # Un rol puede estar asignado a muchos usuarios
    usuarios = db.relationship('Usuario', backref='rol', lazy=True)
