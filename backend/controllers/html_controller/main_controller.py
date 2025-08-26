# controllers/html_controller/main_controller.py

import logging
import base64
from datetime import datetime
from flask import Blueprint, render_template, flash
from models.producto import Producto
from models.categoria import Categoria
from models.testimonio import Testimonio  # ✅ Importar el modelo de testimonios

# Configurar logger
logger = logging.getLogger(__name__)
logging.basicConfig(level=logging.INFO)

main_bp = Blueprint('main_html', __name__)

@main_bp.route('/')
def index():
    try:
        productos = Producto.query.limit(6).all()
        categorias = Categoria.query.limit(3).all()
        testimonios = Testimonio.query.all()  # ✅ Obtener todos los testimonios

        for producto in productos:
            if producto.imagen_blob:
                producto.imagen_base64 = base64.b64encode(producto.imagen_blob).decode('utf-8')
            else:
                producto.imagen_base64 = ""

    except Exception as e:
        logger.exception("Error cargando productos, categorías o testimonios")
        flash("❌ Error al cargar la página principal. Inténtalo de nuevo más tarde.", "danger")
        productos = []
        categorias = []
        testimonios = []  # ✅ Asegurar que no haya error si falla

    # ✅ Ahora también se pasa `testimonios` a la plantilla
    return render_template(
        'index.html',
        productos=productos,
        categorias=categorias,
        testimonios=testimonios,
        now=datetime.now()
    )
