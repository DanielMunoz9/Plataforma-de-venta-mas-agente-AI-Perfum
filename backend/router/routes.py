# 📦 Importamos Blueprint desde Flask
from flask import Blueprint

# 🔐 Importamos el blueprint para autenticación (login, registro)
from backend.controllers.login_register_logout.auth_controller import auth_bp

# 📦 Importamos los blueprints para órdenes y productos
from backend.controllers.ordenes.orden_controller import orden_bp
from backend.controllers.html_controller.producto_controller import producto_bp
from backend.controllers.html_controller.carrito_controller import carrito_bp
from backend.controllers.cliente_controller.historial_controller import historial_bp
from backend.controllers.admin.admin_historial_controller import admin_historial_bp
from backend.controllers.admin.admin_producto_controller import admin_producto_bp
from backend.controllers.html_controller.main_controller import main_bp  # 👈 Importación correcta
from controllers.payu_controller import payu_bp  # ✅ Importa el blueprint
from backend.controllers.admin.admin_dashboard_controller import admin_dashboard_bp




# 🧩 Creamos un blueprint principal llamado 'routes'
routes = Blueprint('routes', __name__)

# ✅ Registramos todos los blueprints aquí.
routes.register_blueprint(carrito_bp)
routes.register_blueprint(historial_bp)
routes.register_blueprint(admin_historial_bp)
routes.register_blueprint(admin_producto_bp)
routes.register_blueprint(producto_bp)
routes.register_blueprint(orden_bp)
routes.register_blueprint(auth_bp)
routes.register_blueprint(main_bp)  # 👈 Registro correcto
routes.register_blueprint(payu_bp)  # ✅ Registro del blueprint de PayU
routes.register_blueprint(admin_dashboard_bp)


