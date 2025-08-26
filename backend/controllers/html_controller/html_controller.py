# controllers/html_controller.py

import logging
from flask import Blueprint, render_template, flash, redirect, url_for, request
from models.producto import Producto
from models.categoria import Categoria

# Configurar logger para imprimir errores en consola
logger = logging.getLogger(__name__)
logging.basicConfig(level=logging.INFO)

# Blueprint para las rutas públicas (landing, catálogo, categorías, detalle)
html_bp = Blueprint('html_views', __name__)

@html_bp.route('/')
def index():
    """
    Página de inicio:
      - Muestra los 6 primeros productos.
      - Muestra las 3 primeras categorías.
    """
    try:
        productos = Producto.query.limit(6).all()
        categorias = Categoria.query.limit(3).all()
    except Exception as e:
        # Registrar error en consola y mostrar mensaje genérico al usuario
        logger.exception("Error al cargar productos o categorías en index")
        flash("❌ Error al cargar la página principal. Inténtalo más tarde.", "danger")
        return render_template('index.html', productos=[], categorias=[])
    return render_template('index.html', productos=productos, categorias=categorias)


@html_bp.route('/catalogo')
def catalogo():
    """
    Catálogo completo de productos.
    """
    try:
        productos = Producto.query.all()
    except Exception as e:
        logger.exception("Error al cargar el catálogo de productos")
        flash("❌ No se pudo cargar el catálogo. Inténtalo más tarde.", "danger")
        productos = []
    return render_template('catalogo.html', productos=productos)


@html_bp.route('/categoria/<nombre>')
def categoria(nombre):
    """
    Filtra el catálogo por nombre de categoría.
    """
    try:
        categoria_obj = Categoria.query.filter(Categoria.nombre.ilike(nombre)).first()
        if not categoria_obj:
            flash(f"❌ La categoría «{nombre}» no existe.", "warning")
            return redirect(url_for('html_views.catalogo'))

        productos = Producto.query.filter_by(id_categoria=categoria_obj.id).all()
    except Exception as e:
        logger.exception(f"Error al filtrar por categoría: {nombre}")
        flash("❌ Error al filtrar categorías. Inténtalo más tarde.", "danger")
        return redirect(url_for('html_views.catalogo'))

    return render_template(
        'categoria.html',
        categoria=categoria_obj,
        productos=productos
    )


@html_bp.route('/producto/<int:id>')
def detalle_producto(id):
    """
    Muestra la página de detalle de un producto dado su ID.
    """
    try:
        producto = Producto.query.get_or_404(id)
    except Exception as e:
        # get_or_404 ya lanza 404 si no existe; cualquier otro error se registra
        logger.exception(f"Error al obtener producto con ID {id}")
        flash("❌ No se pudo cargar el detalle del producto.", "danger")
        return redirect(url_for('html_views.catalogo'))

    return render_template('producto_detalle.html', producto=producto)



@html_bp.route('/ofertas')
def ofertas():
    # Traemos todas las categorías para el menú
    categorias = Categoria.query.all()
    # Filtramos sólo los productos que estén marcados como oferta
    productos = Producto.query.filter_by(oferta=True).all()
    # Renderizamos la plantilla ofertas.html
    return render_template(
        'ofertas.html',
        categorias=categorias,
        productos=productos
    )

@html_bp.route('/categoria/<int:id>')
def productos_por_categoria(id):
    """
    Muestra productos filtrados por ID de categoría.
    """
    productos = Producto.query.filter_by(id_categoria=id).all()
    categorias = Categoria.query.all()
    categoria_actual = Categoria.query.get_or_404(id)
    return render_template('public/productos_por_categoria.html', productos=productos, categorias=categorias, categoria_actual=categoria_actual)

