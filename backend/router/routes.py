# 📦 Importamos Blueprint desde Flask
from flask import Blueprint

# 🔐 Importamos el blueprint para autenticación (login, registro)
from controllers.auth_controller import auth_bp

# 📦 Importamos los blueprints para órdenes y productos
from controllers.orden_controller import orden_bp
from controllers.producto_controller import producto_bp
from controllers.carrito_controller import carrito_bp
from controllers.historial_controller import historial_bp
from controllers.admin_historial_controller import admin_historial_bp
from controllers.admin_producto_controller import admin_producto_bp


# 🧩 Creamos un blueprint principal llamado 'routes'
routes = Blueprint('routes', __name__)

# ✅ Registramos todos los blueprints aquí.
# Esto organiza las rutas y facilita el mantenimiento del proyecto.

routes.register_blueprint(carrito_bp)

routes.register_blueprint(historial_bp)

routes.register_blueprint(admin_historial_bp)

routes.register_blueprint(admin_producto_bp)

# 🟢 Rutas para productos (listar, buscar, crear, actualizar, eliminar)
routes.register_blueprint(producto_bp)

# 🔵 Rutas para órdenes (crear orden, listar, etc.)
routes.register_blueprint(orden_bp)

# 🔐 Rutas para autenticación de usuarios (registro, login)
routes.register_blueprint(auth_bp)
