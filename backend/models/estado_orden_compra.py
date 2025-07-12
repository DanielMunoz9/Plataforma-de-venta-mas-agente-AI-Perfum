# Importamos la instancia de SQLAlchemy desde extensions.py
from extensions import db

# Definimos el modelo EstadoOrdenCompra
# Representa los posibles estados por los que puede pasar una orden
class EstadoOrdenCompra(db.Model):
    # Nombre de la tabla en la base de datos
    __tablename__ = 'estado_orden_compra'

    # Clave primaria única para cada estado
    id = db.Column(db.Integer, primary_key=True)

    # Nombre del estado, como "pendiente", "pagado", "enviado", "cancelado"
    # Debe ser único y obligatorio
    estado = db.Column(db.String(50), unique=True, nullable=False)

    # Relación uno-a-muchos: Un estado puede estar asignado a muchas órdenes
    # backref='estado' permite acceder al estado desde una instancia de Orden como orden.estado
    # lazy=True significa que se cargarán las órdenes solo cuando se acceda a ellas
    ordenes = db.relationship('Orden', backref='estado', lazy=True)
