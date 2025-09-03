# VF Extras Plugin

Plugin de funcionalidades adicionales para Vane France - Incluye reportes avanzados, gestión de ofertas, control de stock y programa de regalos automático.

## Características Principales

### 🎯 Custom Post Type "Ofertas"
- Gestión completa de ofertas promocionales
- Tipos de descuento: porcentual, fijo, 2x1, envío gratis
- Control de fechas de validez y límites de uso
- Shortcode `[vf_offers limit="6"]` para mostrar ofertas

### 📊 Sistema de Reportes Avanzados
- Gráficos de ingresos y órdenes de los últimos 30 días
- Top 10 productos por vistas (tracking automático)
- Top 10 productos más vendidos
- Estadísticas de clientes y distribuidores
- Análisis de conversión y rendimiento

### 👥 Gestión de Roles de Usuario
- Rol personalizado "Distribuidor" con capacidades específicas
- Promoción automática de clientes a distribuidores
- Tracking de actividad y estadísticas por rol
- Dashboard específico para distribuidores

### 📦 Gestión Rápida de Stock
- Actualización masiva de inventarios
- Alertas automáticas de stock bajo
- Búsqueda rápida de productos por nombre/SKU
- Historial de cambios de stock
- Estadísticas de inventario

### 🎁 Programa de Regalos Automático
- Agregado automático de productos regalo al carrito
- Configuración de monto mínimo y restricciones por rol
- Tracking de regalos otorgados y valor total
- Prevención de adición manual de productos regalo

### 💬 Integración WhatsApp
- Endpoint "Soporte Técnico" en Mi Cuenta
- Enlaces directos por categorías de consulta
- Configuración centralizada del número de contacto

### 🎛️ Panel de Administración Integrado
- Menú "Vane France" en WordPress Admin
- Dashboard con métricas clave y alertas
- Páginas de configuración unificadas
- Acciones rápidas para gestión diaria

## Instalación

### Requisitos
- WordPress 5.0+
- WooCommerce 3.0+
- PHP 7.4+

### Pasos de Instalación

1. **Subir el plugin**:
   - Ve a `Plugins > Agregar nuevo` en WordPress Admin
   - Haz clic en "Subir plugin"
   - Selecciona el archivo `vf-extras.zip`
   - Haz clic en "Instalar ahora"

2. **Activar el plugin**:
   - Haz clic en "Activar plugin"
   - El plugin creará automáticamente el rol "distribuidor"
   - Se establecerán las configuraciones por defecto

3. **Configuración inicial**:
   - Ve a `Vane France > Ajustes`
   - Configura número de WhatsApp y redes sociales
   - Establece parámetros del programa de regalos

## Configuración

### Programa de Regalos

1. **Configurar producto regalo**:
   - Ve a `Vane France > Ajustes`
   - Selecciona el producto que se otorgará como regalo
   - Establece el monto mínimo de compra
   - Opcionalmente, restringe por rol de usuario

2. **Funcionamiento automático**:
   - Cuando el subtotal del carrito alcance el monto mínimo
   - El producto regalo se agregará automáticamente con precio $0
   - Se mostrará una notificación al cliente
   - Si el subtotal baja, el regalo se removerá

### Gestión de Ofertas

1. **Crear nueva oferta**:
   - Ve a `Vane France > Nueva Oferta`
   - Completa la información básica (título, descripción, imagen)
   - Configura los detalles de la oferta:
     - Tipo de descuento
     - Valor del descuento
     - Fechas de validez
     - Código promocional (opcional)
     - Límites de uso

2. **Mostrar ofertas en el sitio**:
   ```
   [vf_offers limit="6" columns="3"]
   ```

### Gestión de Stock

1. **Acceso rápido**:
   - Ve a `Vane France > Stock Rápido`
   - Busca productos por nombre o SKU
   - Actualiza cantidades individualmente o en masa

2. **Alertas automáticas**:
   - El dashboard mostrará productos con stock bajo
   - Configuración de umbral en WooCommerce > Configuración

### Reportes y Analytics

1. **Ver reportes**:
   - Ve a `Vane France > Reportes`
   - Revisa gráficos de ingresos y órdenes
   - Analiza productos más vistos y vendidos

2. **Tracking automático**:
   - Las vistas de productos se registran automáticamente
   - Los datos se actualizan en tiempo real
   - Histórico disponible para análisis de tendencias

## Shortcodes Disponibles

### Ofertas
```
[vf_offers limit="6" columns="3" show_expired="false"]
```

**Parámetros**:
- `limit`: Número de ofertas a mostrar (default: 6)
- `columns`: Número de columnas (default: 3)
- `show_expired`: Mostrar ofertas vencidas (default: false)

## Roles y Permisos

### Rol Distribuidor
- Capacidades base de cliente
- Acceso a precios especiales
- Endpoint de soporte técnico en Mi Cuenta
- Tracking especial de actividad

### Promoción a Distribuidor
- Los administradores pueden promover clientes desde Usuarios
- Se mantiene historial de promociones
- Estadísticas específicas por distribuidor

## Funcionalidades Técnicas

### Tracking de Vistas
- Incremento automático en visualización de productos
- Meta key: `vf_views`
- Usado para reportes de productos populares

### Logs de Stock
- Historial de cambios con usuario, fecha e IP
- Máximo 1000 entradas (rotación automática)
- Accesible desde panel de administración

### Notificaciones
- Sistema de alertas para stock bajo
- Notificaciones por email (configurables)
- Alertas en tiempo real en el dashboard

### Hooks y Filtros Disponibles

```php
// Modificar configuración de regalos
add_filter('vf_gift_qualification_check', 'custom_gift_logic');

// Personalizar tracking de vistas
add_action('vf_product_view_tracked', 'custom_view_handler');

// Modificar alertas de stock
add_filter('vf_low_stock_threshold', 'custom_stock_threshold');
```

## Estructura de Datos

### Ofertas (vf_offer)
- Meta fields para configuración de descuentos
- Tracking automático de uso
- Validación de fechas y límites

### Configuraciones (wp_options)
- `vf_whatsapp_number`: Número de WhatsApp
- `vf_gift_min_amount`: Monto mínimo para regalo
- `vf_gift_product_id`: ID del producto regalo
- `vf_gift_usage_stats`: Estadísticas de uso

### User Meta
- `vf_last_login`: Última conexión
- `vf_login_count`: Contador de conexiones
- `vf_distribuidor_activity`: Log de actividad

## Troubleshooting

### Problemas Comunes

**Los regalos no se agregan automáticamente**:
- Verifica que el producto regalo esté configurado
- Asegúrate de que el monto mínimo sea correcto
- Revisa que el usuario tenga el rol requerido (si aplica)

**Las ofertas no aparecen**:
- Verifica que las fechas de validez sean correctas
- Asegúrate de que no se hayan excedido los límites de uso
- Revisa que el shortcode tenga los parámetros correctos

**Los reportes no cargan**:
- Verifica que WooCommerce tenga órdenes con datos
- Asegúrate de que Chart.js se cargue correctamente
- Revisa la consola del navegador por errores JavaScript

**Problemas de permisos**:
- Solo usuarios con capacidad `manage_woocommerce` pueden acceder a reportes y stock
- Solo usuarios con capacidad `manage_options` pueden modificar configuraciones

### Logs de Debug
- Activar WP_DEBUG para ver logs detallados
- Los errores se registran en el log de WordPress
- Información adicional disponible en navegador (F12)

## Compatibilidad

### Plugins Compatibles
- WooCommerce (requerido)
- Wompi for WooCommerce
- Vane France Theme
- VF Amélie Plugin

### Plugins Probados
- Yoast SEO
- Contact Form 7
- MailChimp for WordPress
- WooCommerce Subscriptions

## Desarrollo y Extensiones

### Estructura del Plugin
```
vf-extras/
├── vf-extras.php (archivo principal)
├── includes/
│   ├── class-vf-offers.php
│   ├── class-vf-reports.php
│   ├── class-vf-gifts.php
│   ├── class-vf-roles.php
│   └── class-vf-stock.php
├── admin/
│   └── class-vf-admin.php
└── assets/
    ├── js/
    └── css/
```

### Hooks Personalizados
- `vf_offer_used`: Cuando se usa una oferta
- `vf_gift_added`: Cuando se agrega un regalo
- `vf_stock_updated`: Cuando se actualiza stock
- `vf_distribuidor_promoted`: Cuando se promueve a distribuidor

## Soporte y Documentación

### Recursos Adicionales
- [Documentación de WooCommerce](https://docs.woocommerce.com/)
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)

### Información de Contacto
- Desarrollado específicamente para Vane France
- Integración completa con el ecosistema Vane France

---

**Versión**: 1.0.0  
**Última actualización**: 2024  
**Compatibilidad**: WordPress 5.0+ | WooCommerce 3.0+