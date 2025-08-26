# database/__init__.py

from extensions import db

# Esta función puede usarse solo si quieres crear tablas directamente sin migraciones
def init_db(app):
    with app.app_context():
        db.create_all()  # Solo para pruebas, no se usa si trabajas con flask-migrate
