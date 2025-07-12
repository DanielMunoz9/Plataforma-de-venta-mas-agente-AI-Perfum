from flask import Flask, jsonify
from extensions import db, migrate, cors
from config import Config
from router.routes import routes
from database import init_db
from database.seed import seed_data

from controllers.payu_controller import payu_bp   # ✅ Importa el blueprint

def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)

    init_db(app)
    cors.init_app(app)

    app.register_blueprint(routes)
    app.register_blueprint(payu_bp)  # ✅ Registra el blueprint de PayU

    @app.route('/')
    def index():
        return jsonify({"mensaje": "Bienvenido a la tienda web 🛒"})

    return app

if __name__ == '__main__':
    app = create_app()
    seed_data(app)
    app.run(debug=True)
