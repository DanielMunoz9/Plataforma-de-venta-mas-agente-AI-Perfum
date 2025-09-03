# Vane France WordPress Theme

Tema personalizado para Vane France - Perfumería Francesa de alta gama con integración completa de WooCommerce.

## Características

### Diseño y Estilo
- **Paleta de colores francesa**: Navy blue (#002395), White (#fff), Red (#ed2939)
- **Tipografía elegante**: Playfair Display como fuente principal
- **Diseño responsive**: Totalmente adaptable a todos los dispositivos
- **Animaciones suaves**: Efectos de entrada y transiciones elegantes

### Funcionalidades Principales
- **Sección Hero**: Con dos CTAs principales (Plan Emprendedor y Cliente)
- **Layout de blog**: Con sidebar derecha similar al demo de referencia
- **Integración WooCommerce**: Estilos personalizados y funcionalidades específicas
- **Badge "Especial"**: Para productos etiquetados como "emprendedor"
- **Botón WhatsApp flotante**: En páginas de productos individuales
- **Footer informativo**: Con información de contacto y horarios completos

### Catálogos Duales
- **Plan Emprendedor**: Productos exclusivos con precios especiales
- **Cliente**: Catálogo general para clientes finales
- **Gestión de precios**: Oculta precios especiales a usuarios no logueados
- **Shortcodes incluidos**: `[vf_catalog plan="emprendedor" per_page="12"]`

### Integración WooCommerce
- **Templates personalizados**: Estilos únicos para productos
- **Badge especial**: Para productos de emprendedores
- **Tracking de vistas**: Para reportes de productos populares
- **Botón WhatsApp**: En páginas de productos con mensaje personalizado

## Instalación

### Requisitos Previos
1. **WordPress 5.0+**
2. **WooCommerce 3.0+** (para funcionalidades de tienda)
3. **Wompi for WooCommerce** (para pagos - opcional)

### Pasos de Instalación

1. **Subir el tema**:
   - Ve a `Apariencia > Temas` en tu panel de WordPress
   - Haz clic en "Agregar nuevo" > "Subir tema"
   - Selecciona el archivo `vane-france-theme.zip`
   - Haz clic en "Instalar ahora"

2. **Activar el tema**:
   - Una vez instalado, haz clic en "Activar"
   - El tema creará automáticamente las páginas necesarias

3. **Configuración inicial**:
   - Ve a `Apariencia > Personalizar` para configurar colores, contacto y redes sociales
   - Configura el logo personalizado en `Apariencia > Personalizar > Identidad del sitio`
   - Ajusta los menús en `Apariencia > Menús`

## Configuración

### Información de Contacto
Configura la información de contacto en `Apariencia > Personalizar > Vane France Settings`:

- **Número de WhatsApp**: Para el botón flotante
- **Teléfono principal**: 319 3605666
- **Direcciones de tiendas**:
  - Cl. 12 #13-99 a 13, 1, Bogotá
  - Cl. 12 #13-69 Local 102, Bogotá

### Horarios de Atención
Los horarios están predefinidos en el tema:
- **Lunes a Sábado**: 9:00 AM - 7:00 PM
- **Domingo**: Cerrado

### Configuración de WooCommerce

1. **Crear etiquetas de producto**:
   - Ve a `Productos > Etiquetas`
   - Crea las etiquetas: `emprendedor` y `cliente`

2. **Configurar productos**:
   - Asigna la etiqueta `emprendedor` a productos exclusivos
   - Asigna la etiqueta `cliente` a productos del catálogo general

3. **Configurar atributos** (opcional):
   - Marca: Para filtrar por marcas
   - Género: Para filtrar por tipo de fragancia

### Páginas Automáticas
El tema crea automáticamente:
- **Catálogo Emprendedor**: Con shortcode `[vf_catalog plan="emprendedor"]`
- **Catálogo Cliente**: Con shortcode `[vf_catalog plan="cliente"]`

## Shortcodes Disponibles

### Catálogo de Productos
```
[vf_catalog plan="emprendedor" per_page="12" columns="3"]
[vf_catalog plan="cliente" per_page="12" columns="3"]
```

**Parámetros**:
- `plan`: "emprendedor" o "cliente"
- `per_page`: Número de productos por página (defecto: 12)
- `columns`: Número de columnas (defecto: 3)

### Ofertas (requiere plugin vf-extras)
```
[vf_offers limit="6" columns="3"]
```

### Chat de Amélie (requiere plugin vf-amelie)
```
[amelie_chat]
```

## Personalización

### Colores del Tema
Personaliza los colores en `Apariencia > Personalizar > Vane France Settings > Colores del Tema`:
- **Color Primario**: #002395 (Navy Blue)
- **Color Secundario**: #ed2939 (Red)

### CSS Personalizado
Agrega CSS personalizado en `Apariencia > Personalizar > Vane France Settings > Configuración Avanzada`.

### Widgets
El tema incluye varias áreas de widgets:
- **Sidebar Principal**: Para blog y páginas
- **Footer 1, 2, 3**: Para contenido del footer

## Menús

### Menú Principal
Configura el menú principal en `Apariencia > Menús`:
- Asigna el menú a la ubicación "Menú Principal"
- Incluye enlaces a catálogos y páginas importantes

### Estructura Recomendada
```
- Inicio
- Plan Emprendedor (enlace a página de catálogo emprendedor)
- Cliente (enlace a página de catálogo cliente)
- Blog
- Contacto
```

## Compatibilidad con Plugins

### Plugins Recomendados
- **WooCommerce**: Para funcionalidad de tienda
- **Wompi for WooCommerce**: Para procesar pagos
- **vf-extras**: Plugin personalizado para funcionalidades adicionales
- **vf-amelie**: Plugin para chat con IA

### Plugins Compatibles
- **Yoast SEO**: Para optimización SEO
- **Contact Form 7**: Para formularios de contacto
- **MailChimp**: Para newsletter (con personalización)

## Actualizaciones

### Mantener Personalizaciones
- Utiliza un tema hijo para personalizaciones CSS
- Las configuraciones del personalizador se mantienen entre actualizaciones
- Exporta/importa configuraciones si es necesario

### Respaldo
Antes de actualizar:
1. Haz respaldo de tu sitio
2. Exporta configuraciones del personalizador
3. Documenta personalizaciones realizadas

## Solución de Problemas

### Problemas Comunes

**Los precios no se ocultan para emprendedores**:
- Verifica que los productos tengan la etiqueta "emprendedor"
- Asegúrate de que WooCommerce esté activo

**El botón WhatsApp no aparece**:
- Verifica que estés en una página de producto individual
- Configura el número de WhatsApp en el personalizador

**Las páginas de catálogo están vacías**:
- Asegúrate de que hay productos con las etiquetas correspondientes
- Verifica que WooCommerce esté configurado correctamente

### Logs de Error
Para debugging, revisa:
- `wp-content/debug.log` (si WP_DEBUG está activado)
- Consola del navegador para errores JavaScript
- Panel de herramientas para desarrolladores

## Soporte

### Documentación Adicional
- [Documentación de WooCommerce](https://docs.woocommerce.com/)
- [Codex de WordPress](https://codex.wordpress.org/)

### Recursos de Desarrollo
- **Archivos del tema**: `wp-content/themes/vane-france-theme/`
- **Funciones principales**: `functions.php`
- **Estilos**: `style.css` y `assets/css/custom.css`
- **Scripts**: `assets/js/custom.js`

## Créditos

- **Desarrollado para**: Vane France
- **Basado en**: Bootstrap 5.3.3
- **Fuentes**: Google Fonts (Playfair Display)
- **Iconos**: Font Awesome 6.4.0

## Licencia

Este tema está desarrollado específicamente para Vane France y no debe ser redistribuido sin autorización.

---

**Versión**: 1.0  
**Última actualización**: 2024  
**Compatibilidad**: WordPress 5.0+ | WooCommerce 3.0+