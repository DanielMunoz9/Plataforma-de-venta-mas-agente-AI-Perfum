# auth_routes.py

from flask import Blueprint, render_template, request, redirect, url_for, flash, session
from flask_login import login_user, logout_user, login_required
from werkzeug.security import check_password_hash
from models.usuario import Usuario
from models.rol import Rol
from extensions import db

# ✅ Creamos un Blueprint llamado 'auth' para rutas de autenticación (registro, login, logout)
auth_bp = Blueprint('auth', __name__)

# 🟢 Ruta para registrar nuevos usuarios
@auth_bp.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        nombre = request.form['nombre']
        correo = request.form['correo']
        contrasena = request.form['contrasena']

        # Validar si el correo ya está registrado
        if Usuario.query.filter_by(correo=correo).first():
            flash('❌ El correo ya está registrado.', 'danger')
            return redirect(url_for('auth.register'))

        # Crear nuevo usuario y asignar rol de cliente
        nuevo_usuario = Usuario(nombre=nombre, correo=correo)
        nuevo_usuario.set_password(contrasena)

        rol_cliente = Rol.query.filter_by(nombre='cliente').first()
        if rol_cliente:
            nuevo_usuario.roles.append(rol_cliente)

        db.session.add(nuevo_usuario)
        db.session.commit()

        flash('✅ Registro exitoso. Ahora puedes iniciar sesión.', 'success')
        return redirect(url_for('auth.login'))

    return render_template('register.html')


# 🔐 Ruta para iniciar sesión
@auth_bp.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        correo = request.form.get('correo')
        contrasena = request.form.get('contrasena')

        usuario = Usuario.query.filter_by(correo=correo).first()

        if not usuario or not check_password_hash(usuario.contrasena_hash, contrasena):
            flash("❌ Correo o contraseña incorrectos", "danger")
            return redirect(url_for('auth.login'))

        # Guardar datos de sesión
        # Guardar datos de sesión (usa 'rol' para que coincida con la validación)
        session['usuario_id'] = usuario.id
        session['usuario_nombre'] = usuario.nombre
        session['rol'] = usuario.id_rol   # <-- Aquí cambias 'usuario_rol' por 'rol'


        # Redirección según rol
        if usuario.id_rol == 2:
            print("🔴 Usuario es administrador, redirigiendo al dashboard de admin")
            return redirect(url_for('admin_dashboard.dashboard_admin'))

        elif usuario.id_rol in [1, 3, 4]:  # Cliente, Mayorista, Minorista
            return redirect(url_for('dashboard_bp.dashboard_cliente'))
        else:
            flash("⚠️ Rol no reconocido", "warning")
            return redirect(url_for('auth.login'))

    return render_template('login.html')


# 🔴 Ruta para cerrar sesión
@auth_bp.route('/logout')
@login_required
def logout():
    logout_user()
    flash('🚪 Has cerrado sesión correctamente.', 'success')
    return redirect(url_for('auth.login'))  # ✅ Correcto
  # ✅ Aquí es el nombre del endpoint

