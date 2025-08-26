# controllers/usuarios/cliente/testimonio_controller.py

from flask import Blueprint, request, redirect, url_for, flash, render_template
from models.testimonio import Testimonio
from extensions import db

cliente_testimonio_bp = Blueprint('cliente_testimonio_bp', __name__)

@cliente_testimonio_bp.route('/testimonios')
def ver_testimonios():
    testimonios = Testimonio.query.all()
    return render_template('cliente/testimonios.html', testimonios=testimonios)

@cliente_testimonio_bp.route('/guardar_testimonio', methods=['POST'])
def guardar_testimonio():
    nombre = request.form.get('nombre')
    comentario = request.form.get('comentario')

    if not nombre or not comentario:
        flash('❌ Todos los campos son obligatorios', 'danger')
        return redirect(url_for('html_views.index'))  # O la página donde esté el formulario

    nuevo_testimonio = Testimonio(nombre=nombre, comentario=comentario)
    db.session.add(nuevo_testimonio)
    db.session.commit()

    flash('✅ ¡Gracias por tu testimonio!', 'success')
    return redirect(url_for('html_views.index'))  # Redirige al home o donde lo necesites
