# Importamos la instancia de SQLAlchemy desde extensions.py
from extensions import db

# Modelo que representa un producto disponible en la tienda
class Producto(db.Model):
    # Nombre de la tabla en la base de datos
    __tablename__ = 'productos'

    # Clave primaria: ID único para cada producto
    id = db.Column(db.Integer, primary_key=True)

    # Nombre del producto, obligatorio
    nombre = db.Column(db.String(100), nullable=False)

    # Descripción del producto (texto largo), puede ser opcional
    descripcion = db.Column(db.Text)

    # Precio actual del producto
    precio = db.Column(db.Numeric(10, 2), nullable=False)

    # Cantidad disponible en inventario, por defecto 0
    stock = db.Column(db.Integer, default=0, nullable=False)

    # URL de la imagen del producto (puede ser vacía)
    imagen_url = db.Column(db.String(255))

    # Relación uno-a-muchos con CarritoProducto
    # Permite saber en qué carritos ha sido agregado este producto
    carrito_productos = db.relationship('CarritoProducto', backref='producto', lazy=True)

    # Relación uno-a-muchos con DetalleOrden
    # Permite saber en qué órdenes ha sido incluido este producto
    detalle_ordenes = db.relationship('DetalleOrden', backref='producto', lazy=True)
