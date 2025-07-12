# Importamos la instancia de SQLAlchemy desde extensions.py
from extensions import db

# Modelo que representa una orden de compra realizada por un usuario
class Orden(db.Model):
    # Nombre de la tabla en la base de datos
    __tablename__ = 'ordenes'

    # Clave primaria de la orden
    id = db.Column(db.Integer, primary_key=True)

    # Clave foránea que indica qué usuario realizó esta orden
    id_usuario = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)

    # Fecha y hora en que se realizó la orden, con valor predeterminado actual
    fecha = db.Column(db.DateTime, server_default=db.func.now())

    # Total de la orden (suma de los productos)
    total = db.Column(db.Numeric(10, 2), nullable=False)

    # Clave foránea que indica el estado actual de la orden (pendiente, pagado, etc.)
    id_estado = db.Column(db.Integer, db.ForeignKey('estado_orden_compra.id'), nullable=False)

    # 🆕 Datos de entrega del pedido:

    # Nombre del destinatario que va a recibir el pedido
    nombre_destinatario = db.Column(db.String(100), nullable=False)

    # Teléfono principal para contacto
    telefono = db.Column(db.String(20), nullable=False)

    # Teléfono alterno (opcional)
    telefono_alterno = db.Column(db.String(20))

    # Dirección exacta donde se debe entregar el pedido
    direccion_envio = db.Column(db.String(150), nullable=False)

    # Ciudad donde se debe entregar el pedido
    ciudad_envio = db.Column(db.String(100), nullable=False)

    # Relación uno-a-muchos con los detalles de la orden (los productos comprados)
    detalles = db.relationship(
        'DetalleOrden',
        backref='orden',
        lazy=True,
        cascade="all, delete-orphan"  # Si se borra la orden, también se borran los detalles
    )
