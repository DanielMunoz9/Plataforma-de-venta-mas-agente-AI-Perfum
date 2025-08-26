# controllers/carrito_controller/carrito_controller.py

from flask import Blueprint, render_template, redirect, url_for, session, flash, request
from flask_login import login_required
from models.producto import Producto

# Definimos un blueprint específico para carrito con prefijo '/carrito'
carrito_bp = Blueprint('carrito_bp', __name__, url_prefix='/carrito')

@carrito_bp.route('/')
@login_required
def ver_carrito():
    carrito = session.get('carrito', {})
    productos = []
    total = 0

    for id_producto, cantidad in carrito.items():
        producto = Producto.query.get(int(id_producto))
        if producto:
            subtotal = producto.precio * cantidad
            total += subtotal
            productos.append({
                'producto': producto,
                'cantidad': cantidad,
                'subtotal': subtotal
            })
            print(productos)
            print('🧾 Sesión del carrito:', session.get('carrito', {}))
    
    if not productos:
        flash('🛒 Tu carrito está vacío.', 'info')
        return redirect(url_for('html_views.catalogo'))

    return render_template('carrito.html', productos=productos, total=total)

@carrito_bp.route('/agregar/<int:id>')
@login_required
def agregar_carrito(id):
    producto = Producto.query.get_or_404(id)
    carrito = session.get('carrito', {})
    carrito[str(id)] = carrito.get(str(id), 0) + 1
    session['carrito'] = carrito
    flash(f'🛒 {producto.nombre} agregado al carrito.', 'success')
    # Redirige al referer o al catálogo si no hay referer
    return redirect(request.referrer or url_for('html_views.catalogo'))

@carrito_bp.route('/vaciar')
@login_required
def vaciar_carrito():
    session['carrito'] = {}
    flash('🧺 Carrito vaciado con éxito.', 'info')
    return redirect(url_for('carrito_bp.ver_carrito'))

@carrito_bp.route('/eliminar/<int:id>', methods=['POST'])
@login_required
def eliminar_producto_carrito(id):
    carrito = session.get('carrito', {})
    if str(id) in carrito:
        del carrito[str(id)]
        session['carrito'] = carrito
        flash('🧺 Producto eliminado del carrito.', 'warning')
    return redirect(url_for('carrito_bp.ver_carrito'))
