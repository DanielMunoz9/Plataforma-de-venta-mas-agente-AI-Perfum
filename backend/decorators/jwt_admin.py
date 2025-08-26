# decorators/jwt_admin.py
from functools import wraps
from flask import session, redirect, url_for, flash

def jwt_admin_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if session.get('rol') != 2:
            flash('❌ Acceso denegado', 'danger')
            return redirect(url_for('html_views.index'))
        return f(*args, **kwargs)
    return decorated_function
