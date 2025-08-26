# 📦 Importamos la instancia de SQLAlchemy desde el archivo extensions.py
from extensions import db

# 🧱 Modelo que representa un producto en la tienda
class Producto(db.Model):
    # 🏷️ Nombre real de la tabla en la base de datos
    __tablename__ = 'productos'

    # 🆔 ID único del producto (clave primaria)
    id = db.Column(db.Integer, primary_key=True)

    # 📝 Nombre del producto (obligatorio)
    nombre = db.Column(db.String(100), nullable=False)

    # 📄 Descripción del producto (opcional, tipo texto largo)
    descripcion = db.Column(db.Text)

    # 💰 Precio del producto con dos decimales (obligatorio)
    precio = db.Column(db.Numeric(10, 2), nullable=False)

    # 📦 Stock disponible (obligatorio, por defecto es 0)
    stock = db.Column(db.Integer, default=0, nullable=False)

    # 🖼️ Imagen en formato binario guardada directamente en la base de datos (puede ser nula)
    imagen_blob = db.Column(db.LargeBinary)

    # 🔗 Clave foránea que relaciona el producto con su categoría
    id_categoria = db.Column(db.Integer, db.ForeignKey('categorias.id'), nullable=False)

    # 🔄 Relación uno-a-muchos con los productos que están en carritos de compra
    carrito_productos = db.relationship(
        'CarritoProducto',     # Modelo relacionado
        backref='producto',    # Cómo se accede desde el otro modelo
        lazy=True              # Carga perezosa (solo cuando se necesita)
    )

    # 📦 Relación uno-a-muchos con los detalles de órdenes donde aparece el producto
    detalle_ordenes = db.relationship(
        'DetalleOrden',
        backref='producto',
        lazy=True
    )

    # 🏷️ Relación con posibles ofertas aplicadas al producto
    ofertas = db.relationship(
        'OfertaProducto',          # Modelo relacionado
        back_populates='producto', # Relación bidireccional
        cascade='all, delete-orphan' # Si se borra el producto, se borran sus ofertas
    )
