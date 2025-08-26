from flask import Blueprint, render_template, request, redirect, url_for, flash, session
from flask_login import login_user, logout_user, login_required
from werkzeug.security import check_password_hash
from models.usuario import Usuario
from models.rol import Rol
from extensions import db

# Creamos un blueprint llamado 'auth' para manejar rutas relacionadas a la autenticación
auth_bp = Blueprint('auth', __name__)

# Ruta para registrar nuevos usuarios
@auth_bp.route('/register', methods=['GET', 'POST'])
def register():
    # Si el método es POST, se está enviando el formulario
    if request.method == 'POST':
        nombre = request.form['nombre']  # Capturamos el nombre desde el formulario
        correo = request.form['correo']  # Capturamos el correo desde el formulario
        contrasena = request.form['contrasena']  # Capturamos la contraseña

        # Verificamos si ya existe un usuario con ese correo
        if Usuario.query.filter_by(correo=correo).first():
            flash('El correo ya está registrado.', 'danger')  # Mostramos mensaje de error
            return redirect(url_for('auth.register'))  # Redirigimos al formulario de registro

        # Creamos un nuevo usuario
        nuevo_usuario = Usuario(nombre=nombre, correo=correo)
        nuevo_usuario.set_password(contrasena)  # Encriptamos la contraseña

        # Asignamos el rol por defecto (cliente) si existe
        rol_cliente = Rol.query.filter_by(nombre='cliente').first()
        if rol_cliente:
            nuevo_usuario.roles.append(rol_cliente)

        # Agregamos el nuevo usuario a la base de datos
        db.session.add(nuevo_usuario)
        db.session.commit()

        flash('Registro exitoso. Ahora puedes iniciar sesión.', 'success')
        return redirect(url_for('auth.login'))  # Redirigimos al login tras registro exitoso

    # Si el método es GET, simplemente mostramos el formulario de registro
    return render_template('auth/register.html')

# Ruta para iniciar sesión
@auth_bp.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        correo = request.form['correo']  # Obtenemos el correo ingresado
        contrasena = request.form['contrasena']  # Obtenemos la contraseña ingresada

        # Buscamos el usuario por correo
        usuario = Usuario.query.filter_by(correo=correo).first()

        # Verificamos si el usuario existe y si la contraseña es correcta
        if usuario and check_password_hash(usuario.contrasena, contrasena):
            login_user(usuario)  # Iniciamos sesión con Flask-Login
            flash('Inicio de sesión exitoso.', 'success')
            return redirect(url_for('html.index'))  # Redirigimos a la página principal
        else:
            flash('Correo o contraseña incorrectos.', 'danger')  # Mensaje de error

    return render_template('auth/login.html')  # Mostramos formulario de login

# Ruta para cerrar sesión
@auth_bp.route('/logout')
@login_required  # Solo puede acceder un usuario logueado
def logout():
    logout_user()  # Cerramos la sesión del usuario
    flash('Has cerrado sesión correctamente.', 'success')
    return redirect(url_for('auth.login'))  # Redirigimos al login
