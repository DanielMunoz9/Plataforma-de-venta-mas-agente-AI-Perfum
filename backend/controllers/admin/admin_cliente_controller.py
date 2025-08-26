# controllers/admin/admin_cliente_controller.py
from flask import Blueprint, render_template, session, flash, redirect, url_for, request
from models.usuario import Usuario
from extensions import db
from models.rol import Rol

admin_cliente_bp = Blueprint('admin_cliente', __name__)

@admin_cliente_bp.route('/admin/clientes', methods=['GET', 'POST'])
def ver_clientes():
    if session.get('rol') != 2:
        flash('❌ Acceso denegado', 'danger')
        return redirect(url_for('html_views.index'))

    # Cambiar rol de cliente
    if request.method == 'POST' and 'cambiar_rol' in request.form:
        user_id = request.form.get('usuario_id')
        nuevo_rol = request.form.get('nuevo_rol')
        usuario = Usuario.query.get(user_id)
        if usuario:
            usuario.id_rol = nuevo_rol
            db.session.commit()
            flash(f"✅ Rol actualizado para {usuario.nombre}", 'success')
        return redirect(url_for('admin_cliente.ver_clientes'))

    # Agregar nuevo empleado
    if request.method == 'POST' and 'agregar_empleado' in request.form:
        nombre = request.form.get('nombre')
        correo = request.form.get('correo')
        password = request.form.get('password')  # Deberías encriptarla
        nuevo_empleado = Usuario(nombre=nombre, correo=correo, password=password, id_rol=2, activo=True)
        db.session.add(nuevo_empleado)
        db.session.commit()
        flash('✅ Nuevo empleado agregado', 'success')
        return redirect(url_for('admin_cliente.ver_clientes'))

    # Desactivar empleado
    if request.method == 'POST' and 'desactivar_empleado' in request.form:
        empleado_id = request.form.get('empleado_id')
        empleado = Usuario.query.get(empleado_id)
        if empleado:
            empleado.activo = False
            db.session.commit()
            flash(f'Empleado {empleado.nombre} desactivado', 'warning')
        return redirect(url_for('admin_cliente.ver_clientes'))

    # Listar usuarios
    clientes = Usuario.query.filter(Usuario.id_rol != 2).all()
    empleados = Usuario.query.filter(Usuario.id_rol == 2).all()
    roles = Rol.query.all()

    return render_template('clientes.html', clientes=clientes, empleados=empleados, roles=roles)

@admin_cliente_bp.route('/admin/clientes/actualizar_rol/<int:id>', methods=['POST'])
def actualizar_rol_cliente(id):
    if session.get('rol') != 2:
        flash('❌ Acceso denegado', 'danger')
        return redirect(url_for('html_views.index'))

    cliente = Usuario.query.get_or_404(id)
    nuevo_rol = request.form.get('id_rol')

    try:
        cliente.id_rol = int(nuevo_rol)
        db.session.commit()
        flash('✅ Rol actualizado correctamente.', 'success')
    except Exception as e:
        db.session.rollback()
        flash(f'❌ Error al actualizar el rol: {str(e)}', 'danger')

    return redirect(url_for('admin_cliente.ver_clientes'))
