import os
from flask import Flask, jsonify
from extensions import db, migrate, cors
from config import Config
from router.routes import routes
from database import init_db
from database.seed import seed_data
from controllers.payu_controller import payu_bp   # ✅ Blueprint PayU
from controllers.html_controller import html_bp   # ✅ Blueprint HTML

# Establece rutas personalizadas para templates y static
template_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../frontend/templates'))
static_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '../frontend/static'))

def create_app():
    # Usa las rutas personalizadas
    app = Flask(__name__, template_folder=template_dir, static_folder=static_dir)
    app.config.from_object(Config)

    init_db(app)
    cors.init_app(app)

    app.register_blueprint(routes)
    app.register_blueprint(payu_bp)
    app.register_blueprint(html_bp)

    # Ruta de prueba API
    @app.route('/api')
    def api_index():
        return jsonify({"mensaje": "Bienvenido a la tienda web 🛒"})

    return app

if __name__ == '__main__':
    app = create_app()
    seed_data(app)
    app.run(debug=True)
