document.addEventListener('DOMContentLoaded', () => {
  // Pausar cada carrusel tras un hover
  document.querySelectorAll('.carousel').forEach(car => {
    car.addEventListener('mouseover', () => bootstrap.Carousel.getInstance(car).pause());
    car.addEventListener('mouseout', () => bootstrap.Carousel.getInstance(car).cycle());
  });
});
let carrito = [];

function agregarAlCarrito(id, nombre, precio) {
  carrito.push({ id, nombre, precio });
  actualizarCarrito();
}

function eliminarDelCarrito(index) {
  carrito.splice(index, 1);
  actualizarCarrito();
}

function actualizarCarrito() {
  const contenedor = document.getElementById("carrito-contenido");
  const totalSpan = document.getElementById("carrito-total");
  const contador = document.getElementById("contadorCarrito");

  contenedor.innerHTML = "";

  if (carrito.length === 0) {
    contenedor.innerHTML = "<p class='text-muted'>Tu carrito está vacío.</p>";
    totalSpan.textContent = "0";
    contador.textContent = "0";
    return;
  }

  carrito.forEach((item, index) => {
    const div = document.createElement("div");
    div.className = "d-flex justify-content-between align-items-center mb-2";
    div.innerHTML = `
      <span>${item.nombre}</span>
      <div>
        <span>$${item.precio.toLocaleString()}</span>
        <button class="btn btn-sm btn-danger ms-2" onclick="eliminarDelCarrito(${index})">✕</button>
      </div>
    `;
    contenedor.appendChild(div);
  });

  const total = carrito.reduce((sum, item) => sum + item.precio, 0);
  totalSpan.textContent = total.toLocaleString();
  contador.textContent = carrito.length;
}


  const toggle = document.getElementById('darkToggle');
  toggle.addEventListener('change', function () {
    if (this.checked) {
      document.body.classList.add('bg-dark', 'text-white');
    } else {
      document.body.classList.remove('bg-dark', 'text-white');
    }
  });


