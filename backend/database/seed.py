# database/seed.py

import os
from extensions import db
from models.rol import Rol
from models.estado_orden_compra import EstadoOrdenCompra
from models.categoria import Categoria

def seed_data(app):
    with app.app_context():
        # Insertar Roles
        if not Rol.query.first():
            print("🔁 Insertando roles...")
            roles = ['cliente', 'administrador']
            for nombre in roles:
                db.session.add(Rol(nombre=nombre))

        # Insertar Estados de Orden
        if not EstadoOrdenCompra.query.first():
            print("🔁 Insertando estados de orden de compra...")
            estados = ['pendiente', 'pagado', 'enviado', 'cancelado']
            for estado in estados:
                db.session.add(EstadoOrdenCompra(estado=estado))

        # Insertar Categorías
        if not Categoria.query.first():
            print("🔁 Insertando categorías...")
            categorias = [
                "Secadores", "Planchas", "Pinzas", "Patilleras",
                "Cepillos", "Tijeras", "Barberia", "Spa", "Otros"
            ]
            for nombre in categorias:
                db.session.add(Categoria(nombre=nombre))

        db.session.commit()

        # Ruta absoluta a las imágenes
        BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
        RUTA_PORTADAS = os.path.join(BASE_DIR, 'frontend', 'static', 'img', 'imagenes de portada')


        print("📂 Buscando portadas en:", RUTA_PORTADAS)

        # Pares de (nombre_categoria, archivo_imagen)
        portada_por_categoria = {
            "Secadores": "secadores.jpg",
            "Planchas": "planchas.jpg",
            "Pinzas": "pinzas.jpg",
            "Patilleras": "patilleras.jpg",
            "Cepillos": "cepillos.jpg",
            "Tijeras": "tijeras.jpg",
            "Barberia": "barberia.jpg",
            "Spa": "spa.jpg",
            "Otros": "otros.jpg"
            # O puedes usar un genérico, como:
            # "Secadores": "imagen de la tienda.jpg"
        }

        for nombre_categoria, nombre_archivo in portada_por_categoria.items():
            cat = Categoria.query.filter_by(nombre=nombre_categoria).first()
            if not cat:
                print(f"⚠️  Categoría no encontrada: {nombre_categoria}")
                continue

            ruta_imagen = os.path.join(RUTA_PORTADAS, nombre_archivo)
            if not os.path.isfile(ruta_imagen):
                print(f"❌ Imagen no encontrada: {ruta_imagen}")
                continue

            with open(ruta_imagen, "rb") as f:
                cat.imagen_portada = f.read()
                print(f"✅ Imagen asignada a categoría: {nombre_categoria}")

            db.session.add(cat)

        db.session.commit()
        print("✅ Datos iniciales y portadas cargadas con éxito.")
