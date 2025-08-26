# Importamos todos los modelos definidos en esta carpeta "models"
# Esto permite que se puedan usar desde una sola fuente común

from .rol import Rol  # Modelo para los roles de usuario (admin, cliente, etc.)
from .usuario import Usuario  # Modelo para los datos de los usuarios registrados
from .producto import Producto  # Modelo para productos disponibles en la tienda
from .carrito import Carrito  # Carritos de compra activos o en progreso
from .carrito_producto import CarritoProducto  # Productos dentro de un carrito
from .estado_orden_compra import EstadoOrdenCompra  # Posibles estados de una orden (pendiente, pagado, etc.)
from .orden import Orden  # Modelo que representa una orden/compra realizada
from .detalle_orden import DetalleOrden  # Detalle de cada producto incluido en una orden
from .categoria import Categoria  # Categorías de productos
from .oferta import Oferta  # Ofertas y promociones
from .oferta_producto import OfertaProducto  # Relación ofertas-productos
from .testimonio import Testimonio  # Testimonios de clientes
from .conversacion import Conversacion  # Conversaciones con el agente AI
from .conversion_metrics import ConversionMetrics  # Métricas de conversión del AI

# Lista de todos los modelos exportables. Es opcional pero útil para importar desde fuera.
__all__ = [
    'Rol',
    'Usuario',
    'Producto',
    'Carrito',
    'CarritoProducto',
    'EstadoOrdenCompra',
    'Orden',
    'DetalleOrden',
    'Categoria',
    'Oferta',
    'OfertaProducto',
    'Testimonio',
    'Conversacion',
    'ConversionMetrics'
]
