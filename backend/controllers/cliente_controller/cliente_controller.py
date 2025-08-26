# cliente_controller.py

from flask import Blueprint, render_template, session, redirect, url_for, flash
from flask_login import login_required
from extensions import db
from models.orden import Orden

# Blueprint para las vistas HTML
html_bp = Blueprint('html_views', __name__)

# Ruta del dashboard del cliente
@html_bp.route('/dashboard/cliente')
@login_required
def dashboard_cliente():
    # Verificamos si el usuario tiene rol de cliente (rol = 1)
    if session.get('rol') != 1:
        flash('❌ Acceso denegado. Esta sección es solo para clientes.', 'danger')
        return redirect(url_for('html_views.index'))

    user_id = session.get('user_id')

    # Validación extra por si el ID del usuario no está en la sesión
    if not user_id:
        flash('⚠️ No se pudo obtener la información del usuario.', 'warning')
        return redirect(url_for('html_views.index'))

    # Obtener las órdenes del usuario ordenadas por fecha descendente
    ordenes = Orden.query.filter_by(id_usuario=user_id).order_by(Orden.fecha.desc()).all()

    # Calcular estadísticas del dashboard
    total_comprado = sum(orden.total for orden in ordenes)
    productos_comprados = sum(len(orden.detalles) for orden in ordenes if orden.detalles)
    pedidos_pendientes = sum(1 for orden in ordenes if orden.id_estado == 1)

    # Renderizar la plantilla del dashboard del cliente
    return render_template(
        'dashboard_cliente.html',
        nombre_usuario=session.get('nombre'),
        total_comprado=total_comprado,
        productos_comprados=productos_comprados,
        pedidos_pendientes=pedidos_pendientes,
        ordenes=ordenes
    )
