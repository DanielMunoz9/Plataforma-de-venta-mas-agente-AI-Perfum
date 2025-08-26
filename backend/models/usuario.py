from extensions import db                    # Extensión de SQLAlchemy para el ORM
from flask_login import UserMixin            # Para manejar sesiones de usuario (login)
from werkzeug.security import generate_password_hash, check_password_hash  # Hashing seguro

# ✅ Clase del modelo Usuario
class Usuario(db.Model, UserMixin):
    """
    Modelo de la tabla 'usuarios'. Representa un usuario del sistema,
    con sus datos personales, rol asignado y relaciones con pedidos y carritos.
    """
    __tablename__ = 'usuarios'               # Nombre de la tabla en la base de datos

    # 🔑 Columnas principales
    id = db.Column(db.Integer, primary_key=True)                      # ID único del usuario
    nombre = db.Column(db.String(100), nullable=False)                # Nombre del usuario
    correo = db.Column(db.String(100), unique=True, nullable=False)   # Correo electrónico único
    contrasena_hash = db.Column(db.String(200), nullable=False)       # Contraseña en formato hash
    id_rol = db.Column(db.Integer, db.ForeignKey('roles.id'), nullable=False)  # FK al rol
    celular = db.Column(db.String(20), nullable=True)                 # Número celular (opcional)

    # 🔗 Relaciones con otras tablas
    carritos = db.relationship('Carrito', backref='usuario', lazy=True)  # Relación con Carrito
    ordenes = db.relationship('Orden', backref='usuario', lazy=True)     # Relación con Orden

    # 🔁 Relación con la tabla Rol (desde el lado del usuario)
    rol = db.relationship('Rol', back_populates='usuarios')         # Bidireccional con Rol

    # 🔒 Asignar una contraseña segura (encriptada)
    def set_password(self, password):
        """
    Genera un hash de la contraseña proporcionada y lo asigna al atributo 'contrasena_hash'.
    
    Args:
        password (str): Contraseña en texto plano a ser hasheada.
    """
        self.contrasena_hash = generate_password_hash(password)     # Aplica hash a la contraseña

    # 🔑 Verificar una contraseña ingresada con la almacenada
    def check_password(self, password):
        """
    Verifica si la contraseña proporcionada coincide con el hash almacenado.

    Args:
        password (str): Contraseña en texto plano a verificar.

    Returns:
        bool: True si la contraseña es correcta, False en caso contrario.
    """
        return check_password_hash(self.contrasena_hash, password)  # Compara con el hash
