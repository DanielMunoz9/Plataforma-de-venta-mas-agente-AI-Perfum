# Importamos la extensión db que contiene la instancia de SQLAlchemy
from extensions import db

# Definimos el modelo CarritoProducto, que representa los productos añadidos a un carrito
class CarritoProducto(db.Model):
    # Nombre de la tabla en la base de datos
    __tablename__ = 'carrito_producto'

    # Clave primaria única para identificar cada fila
    id = db.Column(db.Integer, primary_key=True)

    # Clave foránea que referencia al carrito donde se encuentra este producto
    id_carrito = db.Column(db.Integer, db.ForeignKey('carritos.id'), nullable=False)

    # Clave foránea que referencia al producto que se agregó al carrito
    id_producto = db.Column(db.Integer, db.ForeignKey('productos.id'), nullable=False)

    # Cantidad de unidades del producto dentro del carrito
    cantidad = db.Column(db.Integer, nullable=False, default=1)

    # Relaciones reversas (opcional, pero útiles si necesitas navegar desde producto o carrito)
    # Estas relaciones ya están definidas en los modelos de Producto y Carrito con backref
