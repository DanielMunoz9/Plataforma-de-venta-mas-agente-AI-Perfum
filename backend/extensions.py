# extensions.py

# 📦 Importamos las extensiones necesarias de Flask y librerías asociadas
from flask_sqlalchemy import SQLAlchemy       # ORM para interactuar con la base de datos
from flask_migrate import Migrate             # Para manejar migraciones de la base de datos
from flask_cors import CORS                   # Para permitir solicitudes desde el frontend (CORS)

# 🧠 Instanciamos los objetos de extensión para usarlos luego en app.py
db = SQLAlchemy()   # Base de datos
migrate = Migrate() # Migraciones
cors = CORS()       # Configuración de CORS
