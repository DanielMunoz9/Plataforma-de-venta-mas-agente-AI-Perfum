import os
import base64
from flask import Flask, jsonify
from extensions import db, migrate, cors, login_manager
from config import Config

# from database.seed import seed_data  # Commented out for now
from models.usuario import Usuario

# Blueprints principales
from controllers.html_controller.html_controller import html_bp
from controllers.html_controller.main_controller import main_bp
from controllers.login_register_logout.auth_routes import auth_bp
from controllers.payu_controller        import payu_bp
from controllers.cliente_controller.dashboard_cliente_controller import dashboard_bp

# Administración
from controllers.admin.admin_dashboard_controller import admin_dashboard_bp
from controllers.admin.admin_producto_controller  import admin_producto_bp
from controllers.admin.admin_cliente_controller   import admin_cliente_bp
from controllers.admin.admin_orden_controller     import admin_orden_bp
from controllers.admin.admin_historial_controller import admin_historial_bp
from controllers.admin.admin_oferta_controller    import admin_oferta_bp
from controllers.admin.admin_categoria_controller import admin_categoria_bp
from controllers.cliente_controller.orden_cliente_controller import orden_bp


# Testimonios
from controllers.cliente_controller.testimonio_controller import cliente_testimonio_bp

# ¡Importa aquí carrito_bp!
from controllers.carrito_controller.carrito_controller import carrito_bp

# AI Agent Blueprint
from controllers.ai_agent_controller import ai_agent_bp

# Plantillas y estáticos en frontend/
template_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../frontend/templates'))
static_dir   = os.path.abspath(os.path.join(os.path.dirname(__file__), '../frontend/static'))

def b64encode_filter(data):
    if not data: return ''
    return base64.b64encode(data).decode('utf-8')

def create_app():
    app = Flask(__name__, template_folder=template_dir, static_folder=static_dir)
    app.config.from_object(Config)

    # Extensiones
    db.init_app(app)
    migrate.init_app(app, db)
    cors.init_app(app)
    login_manager.init_app(app)

    # después:
    login_manager.login_view = 'auth.login'
    @login_manager.user_loader
    def load_user(user_id):
        return Usuario.query.get(int(user_id))

    app.add_template_filter(b64encode_filter, 'b64encode')

    # 👇 Registra todos los blueprints
    app.register_blueprint(main_bp)
    app.register_blueprint(html_bp)
    app.register_blueprint(auth_bp)
    app.register_blueprint(payu_bp)
    app.register_blueprint(admin_dashboard_bp)
    app.register_blueprint(admin_producto_bp)
    app.register_blueprint(admin_cliente_bp)
    app.register_blueprint(admin_orden_bp)
    app.register_blueprint(admin_historial_bp)
    app.register_blueprint(admin_oferta_bp)
    app.register_blueprint(admin_categoria_bp)
    app.register_blueprint(cliente_testimonio_bp)
    app.register_blueprint(carrito_bp)   # <— Aquí
    app.register_blueprint(ai_agent_bp)  # <— AI Agent
    app.register_blueprint(orden_bp)
    app.register_blueprint(dashboard_bp)


    @app.route('/api')
    def api_index():
        return jsonify({"mensaje": "Bienvenido a la tienda web 🛒"})

    @app.route('/_rutas')
    def _ver_rutas():
        rutas = [f"{r.endpoint} -> {r.rule}" for r in app.url_map.iter_rules()]
        return "<br>".join(sorted(rutas))

    return app

if __name__ == '__main__':
    app = create_app()
    # seed_data(app)  # Commented out for now
    app.run(debug=True)
