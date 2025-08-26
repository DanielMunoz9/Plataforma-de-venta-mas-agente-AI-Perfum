# backend/controllers/admin/admin_oferta_controller.py

from flask import Blueprint, render_template, request, redirect, url_for, flash
from models.oferta import Oferta  # Modelo de ofertas
from models.oferta_producto import OfertaProducto  # Modelo intermedio Oferta-Producto
from models.producto import Producto  # Modelo de productos
from extensions import db  # Instancia de SQLAlchemy

# Creamos un Blueprint para agrupar las rutas del panel de administración de ofertas
admin_oferta_bp = Blueprint('admin_oferta_bp', __name__)

@admin_oferta_bp.route('/admin/ofertas')
def ver_ofertas():
    """
    Ruta GET: Muestra todas las ofertas existentes.
    """
    # Obtenemos todas las ofertas desde la base de datos
    ofertas = Oferta.query.order_by(Oferta.fecha_inicio.desc()).all()
    # Renderizamos la vista con la lista de ofertas
    return render_template('admin_ofertas.html', ofertas=ofertas)

@admin_oferta_bp.route('/admin/ofertas/crear', methods=['GET', 'POST'])
def crear_oferta():
    """
    GET: muestra el formulario para crear una oferta.
    POST: procesa el formulario, crea la oferta y enlaza productos.
    """
    if request.method == 'POST':
        # Recogemos los datos del formulario
        titulo = request.form.get('titulo')
        descripcion = request.form.get('descripcion')
        descuento = float(request.form.get('descuento', 0))
        fecha_inicio = request.form.get('fecha_inicio')
        fecha_fin = request.form.get('fecha_fin')
        productos_seleccionados = request.form.getlist('productos')

        # Validaciones básicas (podrías ampliar esto)
        if not titulo or descuento <= 0:
            flash('Título y descuento son obligatorios y deben ser válidos.', 'danger')
            return redirect(url_for('admin_oferta_bp.crear_oferta'))

        # Creamos la oferta y guardamos para obtener el ID
        nueva_oferta = Oferta(
            titulo=titulo,
            descripcion=descripcion,
            descuento_porcentaje=descuento,
            fecha_inicio=fecha_inicio,
            fecha_fin=fecha_fin
        )
        db.session.add(nueva_oferta)
        db.session.commit()  # Commit para generar nueva_oferta.id

        # Relacionamos cada producto seleccionado con la nueva oferta
        for prod_id in productos_seleccionados:
            relacion = OfertaProducto(
                id_oferta=nueva_oferta.id,  # FK a Oferta.id
                id_producto=int(prod_id)     # FK a Producto.id
            )
            db.session.add(relacion)
        db.session.commit()  # Guardamos todas las relaciones

        # Confirmamos al usuario y redirigimos
        flash('Oferta creada correctamente.', 'success')
        return redirect(url_for('admin_oferta_bp.ver_ofertas'))

    # Si es GET, obtenemos todos los productos para el select múltiple
    productos = Producto.query.order_by(Producto.nombre).all()
    # Renderizamos el formulario de creación
    return render_template('crear_oferta.html', productos=productos)

@admin_oferta_bp.route('/admin/ofertas/<int:id>/editar', methods=['GET', 'POST'])
def editar_oferta(id):
    """
    GET: Muestra el formulario para editar una oferta existente.
    POST: Guarda los cambios realizados a la oferta.
    """
    oferta = Oferta.query.get_or_404(id)
    productos = Producto.query.order_by(Producto.nombre).all()

    if request.method == 'POST':
        oferta.titulo = request.form.get('titulo')
        oferta.descripcion = request.form.get('descripcion')
        oferta.descuento_porcentaje = float(request.form.get('descuento', 0))
        oferta.fecha_inicio = request.form.get('fecha_inicio')
        oferta.fecha_fin = request.form.get('fecha_fin')

        # Actualizar relaciones
        db.session.query(OfertaProducto).filter_by(id_oferta=oferta.id).delete()
        productos_seleccionados = request.form.getlist('productos')
        for prod_id in productos_seleccionados:
            relacion = OfertaProducto(
                id_oferta=oferta.id,
                id_producto=int(prod_id)
            )
            db.session.add(relacion)

        db.session.commit()
        flash('Oferta actualizada correctamente.', 'success')
        return redirect(url_for('admin_oferta_bp.ver_ofertas'))

    return render_template('editar_oferta.html', oferta=oferta, productos=productos)

@admin_oferta_bp.route('/admin/ofertas/<int:id>/eliminar', methods=['POST'])
def eliminar_oferta(id):
    """
    Elimina una oferta y sus relaciones con productos.
    """
    oferta = Oferta.query.get_or_404(id)

    # Eliminar las relaciones con productos
    OfertaProducto.query.filter_by(id_oferta=oferta.id).delete()

    # Eliminar la oferta
    db.session.delete(oferta)
    db.session.commit()

    flash('Oferta eliminada correctamente.', 'success')
    return redirect(url_for('admin_oferta_bp.ver_ofertas'))

