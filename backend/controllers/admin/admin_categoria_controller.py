# 📦 Importamos los módulos necesarios
from flask import Blueprint, render_template, request, redirect, url_for, flash
from models.categoria import Categoria  # Modelo de categoría desde la carpeta models
from decorators.jwt_admin import jwt_admin_required  # Decorador personalizado para proteger rutas de admin
from extensions import db  # Objeto de conexión a la base de datos SQLAlchemy
import logging

# 🛠️ Configuramos el logger para mostrar errores detallados en consola
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# 🟦 Creamos un Blueprint para las rutas de categoría dentro del admin
admin_categoria_bp = Blueprint('admin_categoria_bp', __name__)

# -------------------- VER CATEGORÍAS --------------------
@admin_categoria_bp.route('/admin/categorias')
@jwt_admin_required
def ver_categorias():
    try:
        logger.info("🔍 Cargando categorías desde la base de datos...")
        categorias = Categoria.query.all()  # Consulta todas las categorías
        logger.info(f"✅ Categorías encontradas: {len(categorias)}")
        return render_template('admin/categorias/ver_categorias.html', categorias=categorias)
    except Exception as e:
        logger.exception("❌ Error al cargar las categorías")
        flash(f'❌ Error al cargar las categorías: {str(e)}', 'danger')
        return redirect(url_for('html_views.index'))  # Redirige al inicio en caso de error

# -------------------- CREAR CATEGORÍA --------------------
@admin_categoria_bp.route('/admin/categorias/nueva', methods=['GET', 'POST'])
@jwt_admin_required
def crear_categoria():
    if request.method == 'POST':
        try:
            logger.info("📝 Creando nueva categoría...")
            nombre = request.form['nombre']
            imagen = request.files.get('imagen')  # Imagen miniatura
            portada = request.files.get('portada')  # Imagen de portada

            if not nombre:
                raise ValueError("El nombre de la categoría es obligatorio.")

            nueva_categoria = Categoria(nombre=nombre)

            # Convertimos la imagen miniatura a binario si viene
            if imagen and imagen.filename:
                nueva_categoria.imagen_blob = imagen.read()

            # Convertimos la imagen de portada a binario si viene
            if portada and portada.filename:
                nueva_categoria.imagen_portada = portada.read()

            db.session.add(nueva_categoria)
            db.session.commit()

            logger.info(f"✅ Categoría '{nombre}' creada con éxito.")
            flash('✅ Categoría creada correctamente', 'success')
            return redirect(url_for('admin_categoria_bp.ver_categorias'))

        except Exception as e:
            db.session.rollback()  # Revierte en caso de error
            logger.exception("❌ Error al crear la categoría")
            flash(f'❌ Error al crear la categoría: {str(e)}', 'danger')

    return render_template('admin/categorias/crear_categoria.html')  # Muestra formulario

# -------------------- EDITAR CATEGORÍA --------------------
@admin_categoria_bp.route('/admin/categorias/editar/<int:id>', methods=['GET', 'POST'])
@jwt_admin_required
def editar_categoria(id):
    logger.info(f"🔄 Cargando categoría con ID: {id}")
    categoria = Categoria.query.get_or_404(id)  # 404 si no existe

    if request.method == 'POST':
        try:
            logger.info(f"✏️ Editando categoría con ID: {id}")
            nombre = request.form['nombre']
            nueva_imagen = request.files.get('imagen')  # Imagen miniatura nueva
            nueva_portada = request.files.get('portada')  # Imagen de portada nueva

            if not nombre:
                raise ValueError("El nombre no puede estar vacío.")

            categoria.nombre = nombre

            # Si hay nueva imagen, la sobrescribimos
            if nueva_imagen and nueva_imagen.filename:
                categoria.imagen_blob = nueva_imagen.read()

            if nueva_portada and nueva_portada.filename:
                categoria.imagen_portada = nueva_portada.read()

            db.session.commit()
            logger.info(f"✅ Categoría actualizada correctamente: {nombre}")
            flash('✅ Categoría actualizada correctamente', 'success')
            return redirect(url_for('admin_categoria_bp.ver_categorias'))

        except Exception as e:
            db.session.rollback()
            logger.exception("❌ Error al actualizar la categoría")
            flash(f'❌ Error al actualizar la categoría: {str(e)}', 'danger')

    return render_template('admin/categorias/editar_categoria.html', categoria=categoria)

# -------------------- ELIMINAR CATEGORÍA --------------------
@admin_categoria_bp.route('/admin/categorias/eliminar/<int:id>', methods=['POST'])
@jwt_admin_required
def eliminar_categoria(id):
    logger.info(f"🗑️ Intentando eliminar categoría con ID: {id}")
    categoria = Categoria.query.get_or_404(id)

    try:
        db.session.delete(categoria)
        db.session.commit()
        logger.info(f"✅ Categoría eliminada: {categoria.nombre}")
        flash('🗑️ Categoría eliminada correctamente', 'success')
    except Exception as e:
        db.session.rollback()
        logger.exception("❌ Error al eliminar la categoría")
        flash(f'❌ Error al eliminar la categoría: {str(e)}', 'danger')

    return redirect(url_for('admin_categoria_bp.ver_categorias'))
