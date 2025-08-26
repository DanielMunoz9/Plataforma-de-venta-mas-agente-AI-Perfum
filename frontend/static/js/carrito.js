// carrito.js

document.addEventListener('DOMContentLoaded', () => {
  // Confirma antes de vaciar todo el carrito
  const vaciarBtn = document.querySelector('a[href$="/vaciar"]');
  if (vaciarBtn) {
    vaciarBtn.addEventListener('click', ev => {
      if (!confirm('¿Estás seguro que deseas vaciar todo el carrito?')) {
        ev.preventDefault();
      }
    });
  }

  // Intercepta los formularios de eliminar ítem para confirmar
  document.querySelectorAll('form[action*="/eliminar/"]').forEach(form => {
    form.addEventListener('submit', ev => {
      if (!confirm('¿Eliminar este producto del carrito?')) {
        ev.preventDefault();
      }
    });
  });

  // Opcional: actualización dinámica del badge del carrito
  const badge = document.querySelector('.navbar .badge');
  if (badge) {
    // Si quisieras actualizarlo via AJAX, podrías hacerlo aquí...
    console.log('Carrito badge cargado con valor:', badge.textContent.trim());
  }
});
