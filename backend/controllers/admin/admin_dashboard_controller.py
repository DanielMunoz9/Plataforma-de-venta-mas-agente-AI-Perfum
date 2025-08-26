from flask import Blueprint, render_template, session, flash, redirect, url_for
from extensions import db
from models.orden import Orden
from models.producto import Producto
from sqlalchemy import func
from datetime import datetime

# Blueprint para rutas del panel administrativo
admin_dashboard_bp = Blueprint('admin_dashboard', __name__)

@admin_dashboard_bp.route('/dashboard/admin')
def dashboard_admin():
    # Verifica que el usuario sea un administrador (rol 2)
    if session.get('rol') != 2:
        flash('❌ Acceso denegado', 'danger')
        return redirect(url_for('html_views.index'))

    # Total de ventas realizadas (todas las órdenes distintas a "pendiente" = estado 1)
    ventas_realizadas = db.session.query(
        func.count(Orden.id)
    ).filter(Orden.id_estado != 1).scalar() or 0

    # Fecha actual
    fecha_actual = datetime.now()
    mes_actual = fecha_actual.month
    anio_actual = fecha_actual.year

    # Suma de ingresos del mes actual (excluyendo órdenes pendientes)
    ingresos_mes = db.session.query(
        func.sum(Orden.total)
    ).filter(
        func.extract('month', Orden.fecha) == mes_actual,
        func.extract('year', Orden.fecha) == anio_actual,
        Orden.id_estado != 1
    ).scalar() or 0

    # Total de clientes únicos que han hecho pedidos
    clientes_activos = db.session.query(
        func.count(func.distinct(Orden.id_usuario))
    ).scalar() or 0

    # Cantidad de pedidos pendientes (estado = 1)
    pedidos_pendientes = db.session.query(
        func.count(Orden.id)
    ).filter(Orden.id_estado == 1).scalar() or 0

    # Productos con stock igual o menor a 10 unidades
    productos_bajo_stock = Producto.query.filter(Producto.stock <= 10).all()

    # Renderiza el dashboard con los datos calculados
    return render_template(
        "dashboard_admin.html",
        ventas_realizadas=ventas_realizadas,
        ingresos_mes=ingresos_mes,
        clientes_activos=clientes_activos,
        pedidos_pendientes=pedidos_pendientes,
        productos_bajo_stock=productos_bajo_stock
    )
