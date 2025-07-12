# Importamos la instancia de SQLAlchemy desde extensions.py
from extensions import db

# Definimos el modelo DetalleOrden
# Este modelo representa cada ítem individual (producto) dentro de una orden de compra
class DetalleOrden(db.Model):
    # Nombre de la tabla en la base de datos
    __tablename__ = 'detalle_orden'

    # Clave primaria única para cada detalle o ítem de orden
    id = db.Column(db.Integer, primary_key=True)

    # Clave foránea que enlaza este detalle con una orden específica
    id_orden = db.Column(db.Integer, db.ForeignKey('ordenes.id'), nullable=False)

    # Clave foránea que enlaza con el producto que fue comprado
    id_producto = db.Column(db.Integer, db.ForeignKey('productos.id'), nullable=False)

    # Cantidad de unidades de este producto compradas en esta orden
    cantidad = db.Column(db.Integer, nullable=False)

    # Precio unitario del producto en el momento de la compra
    # Se guarda así para mantener histórico, incluso si el precio del producto cambia después
    precio_unitario = db.Column(db.Numeric(10, 2), nullable=False)
