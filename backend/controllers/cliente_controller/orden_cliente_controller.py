# backend/controller/cliente_controller/orden_cliente_controller.py
# Importamos los módulos necesarios de Flask
from flask import Blueprint, render_template, request, redirect, url_for, session, flash
# Importamos los modelos de Producto y Orden
from models.producto import Producto
from models.orden import Orden
# Importamos la instancia de base de datos desde extensions
from extensions import db

# Creamos un Blueprint para las rutas relacionadas con las órdenes del cliente
orden_bp = Blueprint('orden_bp', __name__)

# ------------------- RUTA: /checkout -------------------
# Página de resumen de compra antes de confirmar el pago
@orden_bp.route('/checkout')
def checkout():
    # Validamos que el usuario haya iniciado sesión
    if 'usuario_id' not in session:
        flash("Debes iniciar sesión para continuar con la compra", "warning")
        return redirect(url_for('login_bp.login'))

    # Obtenemos el carrito de la sesión (diccionario con ID de producto y cantidad)
    carrito = session.get('carrito', {})
    productos = []  # Lista para almacenar información detallada de los productos
    total = 0  # Variable para calcular el total de la compra

    # Recorremos los productos del carrito
    for id_producto, cantidad in carrito.items():
        producto = Producto.query.get(id_producto)  # Consultamos el producto por su ID
        if producto:
            subtotal = producto.precio * cantidad  # Calculamos subtotal por producto
            total += subtotal  # Sumamos al total general
            # Agregamos la información detallada del producto a la lista
            productos.append({
                'producto': producto,
                'cantidad': cantidad,
                'subtotal': subtotal
            })

    # Renderizamos la plantilla del checkout con los datos del carrito
    return render_template('cliente/checkout.html', productos=productos, total=total)

# ------------------- RUTA: /confirmar-pago -------------------
# Ruta para confirmar el pago y registrar la orden
@orden_bp.route('/confirmar-pago', methods=['POST'])
def confirmar_pago():
    # Validamos que el usuario haya iniciado sesión
    if 'usuario_id' not in session:
        flash("Debes iniciar sesión para confirmar el pedido", "warning")
        return redirect(url_for('login_bp.login'))

    # Obtenemos el carrito de la sesión
    carrito = session.get('carrito', {})
    if not carrito:
        flash("Tu carrito está vacío", "danger")
        return redirect(url_for('html_views.index'))

    # Creamos una nueva orden con estado inicial "Pendiente"
    nueva_orden = Orden(
        usuario_id=session['usuario_id'],
        estado='Pendiente',
        total=0  # El total se calculará después
    )
    db.session.add(nueva_orden)  # Agregamos la orden a la sesión de BD
    db.session.flush()  # Hacemos flush para obtener el ID de la orden antes del commit

    total = 0  # Reiniciamos total
    # Recorremos el carrito para registrar los detalles de la orden
    for id_producto, cantidad in carrito.items():
        producto = Producto.query.get(id_producto)  # Consultamos el producto
        if producto:
            subtotal = producto.precio * cantidad  # Subtotal por producto
            total += subtotal  # Sumamos al total de la orden
            # Registramos el detalle de la orden con ID de producto, cantidad y precio
            nueva_orden.agregar_detalle(
                producto_id=producto.id,
                cantidad=cantidad,
                precio_unitario=producto.precio
            )

    # Actualizamos el total de la orden y guardamos en la base de datos
    nueva_orden.total = total
    db.session.commit()  # Guardamos todos los cambios en la base de datos

    # Limpiamos el carrito de la sesión
    session.pop('carrito', None)

    # Notificamos al usuario que su pedido ha sido registrado
    flash("¡Tu pedido ha sido registrado con éxito!", "success")
    # Redirigimos al dashboard del cliente
    return redirect(url_for('dashboard_bp.dashboard_cliente'))
