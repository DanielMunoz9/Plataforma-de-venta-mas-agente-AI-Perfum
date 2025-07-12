# database/seed.py

from extensions import db
from models.rol import Rol
from models.estado_orden_compra import EstadoOrdenCompra

# Función para insertar datos iniciales (seeds)
def seed_data(app):
    with app.app_context():
        # Verificamos si ya existen roles
        if not Rol.query.first():
            print("🔁 Insertando roles...")
            roles = ['cliente', 'administrador']
            for nombre in roles:
                db.session.add(Rol(nombre=nombre))
        
        # Verificamos si ya existen estados de orden
        if not EstadoOrdenCompra.query.first():
            print("🔁 Insertando estados de orden de compra...")
            estados = ['pendiente', 'pagado', 'enviado', 'cancelado']
            for estado in estados:
                db.session.add(EstadoOrdenCompra(estado=estado))

        db.session.commit()
        print("✅ Datos iniciales insertados con éxito.")
