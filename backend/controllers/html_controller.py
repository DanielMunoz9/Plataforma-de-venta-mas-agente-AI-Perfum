from flask import Blueprint, render_template, request, redirect, url_for, flash, session

html_bp = Blueprint('html_views', __name__)

# Mock de productos (puedes mover a BD luego)
PRODUCTOS = [
    {"id": 1, "nombre": "Secadora KF 8948", "descripcion": "Secador con carcasa antichoque y difusor", "precio": 299000, "imagen": "productos/secadora.png"},
    {"id": 2, "nombre": "Plancha MD 83", "descripcion": "Placas flotantes, cable giratorio, cerámica", "precio": 189000, "imagen": "productos/plancha.png"},
    {"id": 3, "nombre": "Máquina EXT-001", "descripcion": "Alta potencia, motor silencioso, luz led", "precio": 249000, "imagen": "productos/maquina.png"}
]

@html_bp.route('/')
def index():
    return render_template('index.html')

@html_bp.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        nombre = request.form['nombre']
        email = request.form['email']
        password = request.form['password']
        confirmar = request.form['confirmar']
        if password != confirmar:
            flash('Las contraseñas no coinciden', 'danger')
            return redirect(url_for('html_views.register'))
        flash('Usuario registrado correctamente', 'success')
        return redirect(url_for('html_views.login'))
    return render_template('register.html')

@html_bp.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        session['user_id'] = 1  # Simulación
        flash('Has iniciado sesión correctamente', 'success')
        return redirect(url_for('html_views.index'))
    return render_template('login.html')

@html_bp.route('/logout')
def logout():
    session.clear()
    flash('Sesión cerrada', 'info')
    return redirect(url_for('html_views.login'))

@html_bp.route('/catalogo')
def catalogo():
    return render_template('catalogo.html', productos=PRODUCTOS)

@html_bp.route('/carrito')
def carrito():
    carrito = session.get('carrito', [])
    total = sum(item['precio'] for item in carrito)
    return render_template('carrito.html', carrito=carrito, total=total)

@html_bp.route('/agregar/<int:id>', methods=['POST'])
def agregar_carrito(id):
    producto = next((p for p in PRODUCTOS if p["id"] == id), None)
    if producto:
        carrito = session.get('carrito', [])
        carrito.append(producto)
        session['carrito'] = carrito
        flash(f'{producto["nombre"]} agregado al carrito', 'success')
    else:
        flash('Producto no encontrado', 'danger')
    return redirect(url_for('html_views.catalogo'))

@html_bp.route('/carrito/vaciar')
def vaciar_carrito():
    session['carrito'] = []
    flash('Carrito vaciado correctamente', 'info')
    return redirect(url_for('html_views.carrito'))

# Categorías individuales con sus propias plantillas
@html_bp.route('/categoria/secadoras')
def secadoras():
    return render_template('secadoras.html')

@html_bp.route('/categoria/planchas')
def planchas():
    return render_template('planchas.html')

@html_bp.route('/categoria/pinzas')
def pinzas():
    return render_template('pinzas.html')

@html_bp.route('/categoria/patilleras')
def patilleras():
    return render_template('patilleras.html')

@html_bp.route('/categoria/cepillos')
def cepillos():
    return render_template('cepillos.html')

@html_bp.route('/categoria/tijeras')
def tijeras():
    return render_template('tijeras.html')

@html_bp.route('/categoria/barbera')
def barbera():
    return render_template('barbera.html')

@html_bp.route('/categoria/spa')
def spa():
    return render_template('spa.html')

@html_bp.route('/categoria/otros')
def otros():
    return render_template('otros.html')

# Ruta genérica opcional (para casos no contemplados arriba)
@html_bp.route('/categoria/<nombre>')
def categoria(nombre):
    productos_mock = {
        "secadores": [{"nombre": "Secadora Pro", "precio": 299000}],
        "planchas": [{"nombre": "Plancha Ionic", "precio": 199000}],
        "pinzas": [{"nombre": "Pinza Curl 360", "precio": 159000}],
        "patilleras": [{"nombre": "Patillera Pro", "precio": 149000}],
        "cepillos": [{"nombre": "Cepillo Térmico", "precio": 79000}],
        "tijeras": [{"nombre": "Tijera Profesional", "precio": 89000}],
        "barbera": [{"nombre": "Kit Barbería", "precio": 399000}],
        "spa": [{"nombre": "Vaporizador Facial", "precio": 139000}],
        "otros": [{"nombre": "Bolso LIZO", "precio": 69000}]
    }
    productos = productos_mock.get(nombre.lower(), [])
    return render_template('categoria.html', categoria=nombre.capitalize(), productos=productos)
