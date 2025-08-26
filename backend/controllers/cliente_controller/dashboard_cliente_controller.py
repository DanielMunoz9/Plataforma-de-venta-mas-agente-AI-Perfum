# controllers/cliente_controller/dashboard_cliente_controller.py

from flask import Blueprint, render_template, session, redirect, url_for, flash
from models.orden import Orden
from models.usuario import Usuario

dashboard_bp = Blueprint('dashboard_bp', __name__)

@dashboard_bp.route('/dashboard')
def dashboard_cliente():
    # Verifica si hay usuario en sesión
    id_usuario = session.get('usuario_id')
    if not id_usuario:
        flash("Debes iniciar sesión para ver tu panel", "warning")
        return redirect(url_for('auth.login'))

    # Consulta al usuario por ID
    usuario = Usuario.query.get(id_usuario)
    if not usuario:
        flash("Usuario no encontrado", "danger")
        return redirect(url_for('auth.login'))

    # ✅ Ahora que usuario existe, puedes imprimirlo
    print(f"📥 Cliente accedió al dashboard: ID={usuario.id}")

    # Consultar órdenes
    ordenes = Orden.query.filter_by(id_usuario=id_usuario).order_by(Orden.fecha.desc()).all()

    # Calcular estadísticas
    total = 0
    total_productos = 0
    pendientes = 0

    for orden in ordenes:
        if orden.id_estado == 1:
            pendientes += 1
        for detalle in orden.detalles:
            total += detalle.precio_unitario * detalle.cantidad
            total_productos += detalle.cantidad

    # Renderizar vista
    return render_template(
        'dashboard_cliente.html',
        usuario=usuario,
        nombre_usuario = usuario.nombre,
        ordenes=ordenes,
        total_comprado=total,
        productos_comprados=total_productos,
        pedidos_pendientes=pendientes
    )
