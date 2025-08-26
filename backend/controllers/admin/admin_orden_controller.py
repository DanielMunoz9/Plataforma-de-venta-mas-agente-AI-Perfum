# controllers/admin/admin_orden_controller.py

from flask import Blueprint, render_template, session, flash, redirect, url_for, request
from extensions import db
from models.orden import Orden
from models.estado_orden_compra import EstadoOrdenCompra

# Blueprint del módulo de gestión de órdenes del administrador
admin_orden_bp = Blueprint('admin_orden', __name__)

# Ruta para visualizar todas las órdenes
@admin_orden_bp.route('/admin/ordenes')
def ver_ordenes():
    if session.get('rol') != 2:
        flash('❌ Acceso denegado. Solo administradores pueden acceder.', 'danger')
        return redirect(url_for('html_views.index'))

    ordenes = Orden.query.all()
    estados = EstadoOrdenCompra.query.all()

    return render_template('ordenes.html', ordenes=ordenes, estados=estados)

# Ruta para actualizar el estado de una orden específica
@admin_orden_bp.route('/admin/ordenes/actualizar', methods=['POST'])
def actualizar_estado_orden():
    if session.get('rol') != 2:
        flash('❌ Acceso denegado. Solo administradores pueden modificar órdenes.', 'danger')
        return redirect(url_for('html_views.index'))

    orden_id = request.form.get('orden_id')
    nuevo_estado = request.form.get('nuevo_estado')

    if not orden_id or not nuevo_estado:
        flash('⚠️ Datos incompletos para actualizar la orden.', 'warning')
        return redirect(url_for('admin_orden.ver_ordenes'))

    try:
        orden = Orden.query.get(int(orden_id))
        if orden:
            if orden.id_estado != int(nuevo_estado):
                orden.id_estado = int(nuevo_estado)
                db.session.commit()
                flash('✅ Estado de la orden actualizado con éxito.', 'success')
            else:
                flash('ℹ️ El estado de la orden ya estaba establecido.', 'info')
        else:
            flash('❌ Orden no encontrada.', 'danger')
    except Exception as e:
        db.session.rollback()
        flash(f'❌ Error al actualizar la orden: {str(e)}', 'danger')

    return redirect(url_for('admin_orden.ver_ordenes'))
