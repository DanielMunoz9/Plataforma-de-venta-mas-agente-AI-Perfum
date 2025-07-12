# Importamos la instancia de SQLAlchemy desde el archivo extensions.py
from extensions import db

# Definimos el modelo Carrito, que representa un carrito de compras de un usuario
class Carrito(db.Model):
    # Nombre de la tabla que se creará en la base de datos
    __tablename__ = 'carritos'

    # Clave primaria: ID único para cada carrito
    id = db.Column(db.Integer, primary_key=True)

    # Clave foránea que indica a qué usuario pertenece este carrito
    id_usuario = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)

    # Fecha y hora en que se creó el carrito (por defecto: ahora mismo)
    fecha_creacion = db.Column(db.DateTime, server_default=db.func.now())

    # Relación uno-a-muchos con CarritoProducto (productos en este carrito)
    # backref='carrito' permite acceder desde CarritoProducto al carrito asociado
    # lazy=True hace que se carguen los datos solo cuando se acceden
    # cascade="all, delete-orphan" asegura que si se elimina el carrito, se eliminan los productos relacionados
    productos = db.relationship(
        'CarritoProducto',
        backref='carrito',
        lazy=True,
        cascade="all, delete-orphan"
    )
