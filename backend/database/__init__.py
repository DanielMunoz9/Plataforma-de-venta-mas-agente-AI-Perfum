# database/__init__.py

from flask_migrate import Migrate
from extensions import db  # db viene desde extensions.py (ya configurado con SQLAlchemy)

# Función para inicializar la base de datos y las migraciones
def init_db(app):
    db.init_app(app)       # Vincula SQLAlchemy con la app
    Migrate(app, db)       # Habilita Flask-Migrate para manejar migraciones con Alembic
