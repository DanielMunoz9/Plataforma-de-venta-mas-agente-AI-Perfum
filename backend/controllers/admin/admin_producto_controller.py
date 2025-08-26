from flask import Blueprint, render_template, request, redirect, url_for, flash,send_file
from models.producto import Producto
from models.categoria import Categoria
from decorators.jwt_admin import jwt_admin_required
from extensions import db
from io import BytesIO

admin_producto_bp = Blueprint('admin_producto_bp', __name__)

@admin_producto_bp.route('/admin/productos/nuevo', methods=['GET', 'POST'])
@jwt_admin_required
def crear_producto():
    """
    - GET: muestra el formulario para crear un producto.
    - POST: guarda producto + opcionalmente actualiza la portada de su categoría.
    """
    if request.method == 'POST':
        # 1️⃣ Recogemos campos básicos
        nombre      = request.form.get('nombre', '').strip()
        descripcion = request.form.get('descripcion', '').strip()
        precio_str  = request.form.get('precio', '0')
        id_cat_str  = request.form.get('id_categoria', '')
        imagen_prod = request.files.get('imagen')             # Imagen del producto
        portada_cat = request.files.get('portada_categoria')  # Nueva portada para la categoría

        # 2️⃣ Validaciones
        if not all([nombre, descripcion, precio_str, id_cat_str]):
            flash('Todos los campos son obligatorios.', 'warning')
            return redirect(request.url)
        try:
            precio       = float(precio_str)
            id_categoria = int(id_cat_str)
        except ValueError:
            flash('Precio o categoría inválidos.', 'danger')
            return redirect(request.url)

        # 3️⃣ Creamos y guardamos el producto
        nuevo = Producto(
            nombre       = nombre,
            descripcion  = descripcion,
            precio       = precio,
            id_categoria = id_categoria
        )
        if imagen_prod and imagen_prod.filename:
            nuevo.imagen_blob = imagen_prod.read()

        db.session.add(nuevo)

        # 4️⃣ Si subieron portada de categoría, la actualizamos
        if portada_cat and portada_cat.filename:
            categoria = Categoria.query.get(id_categoria)
            if categoria:
                categoria.imagen_portada = portada_cat.read()
            else:
                flash('⚠️ Categoría no encontrada; no se actualizó la portada.', 'warning')

        # 5️⃣ Commit final
        try:
            db.session.commit()
            flash('✅ Producto (y portada de categoría) guardados correctamente.', 'success')
            return redirect(url_for('admin_producto_bp.ver_productos'))
        except Exception as e:
            db.session.rollback()
            flash(f'❌ Error al guardar en BD: {e}', 'danger')
            return redirect(request.url)

    # GET → traemos categorías para el select
    categorias = Categoria.query.order_by(Categoria.nombre).all()
    return render_template('agregar_producto.html', categorias=categorias)


@admin_producto_bp.route('/admin/productos/editar/<int:id>', methods=['GET', 'POST'])
@jwt_admin_required
def editar_producto(id):
    """
    - GET: muestra el formulario de edición.
    - POST: actualiza producto + opcionalmente su categoría.
    """
    producto = Producto.query.get_or_404(id)

    if request.method == 'POST':
        # Actualizamos campos
        producto.nombre       = request.form.get('nombre', producto.nombre).strip()
        producto.descripcion  = request.form.get('descripcion', producto.descripcion).strip()
        try:
            producto.precio       = float(request.form.get('precio', producto.precio))
            producto.id_categoria = int(request.form.get('id_categoria', producto.id_categoria))
            producto.stock        = int(request.form.get('stock', producto.stock))
        except ValueError:
            flash('Precio, stock o categoría inválidos.', 'danger')
            return redirect(request.url)

        # Nueva imagen de producto
        nueva_img = request.files.get('imagen')
        if nueva_img and nueva_img.filename:
            producto.imagen_blob = nueva_img.read()

        # Nueva portada de categoría
        portada_cat = request.files.get('portada_categoria')
        if portada_cat and portada_cat.filename:
            categoria = Categoria.query.get(producto.id_categoria)
            if categoria:
                categoria.imagen_portada = portada_cat.read()
            else:
                flash('⚠️ Categoría no encontrada; no se actualizó la portada.', 'warning')

        # Guardamos todo
        try:
            db.session.commit()
            flash('✅ Producto y portada de categoría actualizados.', 'success')
            return redirect(url_for('admin_producto_bp.ver_productos'))
        except Exception as e:
            db.session.rollback()
            flash(f'❌ Error al actualizar en BD: {e}', 'danger')
            return redirect(request.url)

    categorias = Categoria.query.order_by(Categoria.nombre).all()
    return render_template('editar_producto.html',
                           producto=producto,
                           categorias=categorias)


@admin_producto_bp.route('/admin/productos')
@jwt_admin_required
def ver_productos():
    productos = Producto.query.order_by(Producto.nombre).all()
    return render_template('productos.html', productos=productos)


@admin_producto_bp.route('/admin/productos/eliminar/<int:id>', methods=['POST'])
@jwt_admin_required
def eliminar_producto(id):
    producto = Producto.query.get_or_404(id)
    db.session.delete(producto)
    db.session.commit()
    flash('Producto eliminado correctamente.', 'success')
    return redirect(url_for('admin_producto_bp.ver_productos'))

@admin_producto_bp.route('/admin/productos/imagen/<int:id>')
def obtener_imagen_producto(id):
    producto = Producto.query.get_or_404(id)

    if producto.imagen_blob:
        return send_file(
            BytesIO(producto.imagen_blob),
            mimetype='image/jpeg'  # o 'image/png' según el tipo que estés usando
        )
    else:
        # Imagen por defecto si no hay en la BD
        return redirect(url_for('static', filename='img/no_image.png'))
