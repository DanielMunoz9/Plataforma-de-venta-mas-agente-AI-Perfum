# Importamos SQLAlchemy desde extensions
from extensions import db

# Importamos funciones para generar y verificar contraseñas encriptadas
from werkzeug.security import generate_password_hash, check_password_hash

# Modelo que representa a los usuarios registrados en la tienda (clientes o administradores)
class Usuario(db.Model):
    # Nombre de la tabla en la base de datos
    __tablename__ = 'usuarios'

    # Clave primaria: identificador único del usuario
    id = db.Column(db.Integer, primary_key=True)

    # Nombre completo del usuario
    nombre = db.Column(db.String(100), nullable=False)

    # Correo electrónico del usuario, debe ser único
    correo = db.Column(db.String(100), unique=True, nullable=False)

    # Contraseña encriptada del usuario (no se guarda texto plano)
    contraseña_hash = db.Column(db.String(200), nullable=False)

    # Rol del usuario (cliente, admin, etc.)
    id_rol = db.Column(db.Integer, db.ForeignKey('roles.id'), nullable=False)

    # Relación uno-a-muchos con carritos: un usuario puede tener varios carritos
    carritos = db.relationship('Carrito', backref='usuario', lazy=True)

    # Relación uno-a-muchos con órdenes: un usuario puede tener varias órdenes
    ordenes = db.relationship('Orden', backref='usuario', lazy=True)

    # Método para encriptar y guardar la contraseña
    def set_password(self, password):
        self.contraseña_hash = generate_password_hash(password)

    # Método para verificar si una contraseña ingresada es correcta
    def check_password(self, password):
        return check_password_hash(self.contraseña_hash, password)
