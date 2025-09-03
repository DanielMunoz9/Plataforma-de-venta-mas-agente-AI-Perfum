# Vane France WordPress Complete Deliverable

Entrega completa de WordPress para Vane France - Incluye tema personalizado, plugins de funcionalidades y sistema de chat con IA Amélie.

## Contenido del Paquete

### 📦 Archivos de Instalación
- `dist/vane-france-theme.zip` - Tema personalizado de WordPress
- `dist/vf-extras.zip` - Plugin de funcionalidades adicionales  
- `dist/vf-amelie.zip` - Plugin de chat con IA Amélie

### 🎨 Tema: Vane France
**Archivo**: `vane-france-theme.zip`

Tema personalizado que replica el diseño de referencia con colores de la bandera francesa (azul marino dominante).

#### Características:
- ✅ Sección hero con CTAs animados: "Plan Emprendedor" y "Cliente"
- ✅ Layout de blog con sidebar derecha (similar al demo de referencia)
- ✅ Integración completa con WooCommerce
- ✅ Badge "Especial" para productos etiquetados como "emprendedor"
- ✅ Botón WhatsApp flotante en páginas de productos
- ✅ Footer con información completa del negocio
- ✅ Diseño 100% responsive con animaciones
- ✅ Shortcodes para catálogos duales: `[vf_catalog plan="emprendedor/cliente"]`

### 🛠️ Plugin: VF Extras
**Archivo**: `vf-extras.zip`

Plugin de lógica de negocio con funcionalidades avanzadas para la gestión de la tienda.

#### Características:
- ✅ **Custom Post Type "Ofertas"** con gestión completa
- ✅ **Sistema de Reportes** con Chart.js (ingresos, órdenes, productos populares)
- ✅ **Gestión de Roles** - Rol "distribuidor" personalizado
- ✅ **Stock Rápido** - Actualización masiva de inventarios
- ✅ **Programa de Regalos** - Agregado automático basado en monto mínimo
- ✅ **Endpoint WhatsApp** en Mi Cuenta para "Soporte Técnico"
- ✅ **Panel Administrativo** unificado "Vane France"

### 🤖 Plugin: VF Amélie 
**Archivo**: `vf-amelie.zip`

Plugin de chat con IA que conecta con el backend Flask existente.

#### Características:
- ✅ **Widget flotante** con diseño elegante tipo diamante
- ✅ **Shortcode** `[amelie_chat]` para insertar en páginas
- ✅ **Integración con Flask** - Conecta al endpoint `/api/ai/chat`
- ✅ **Configuración segura** - Campos para API URL y Secret Key
- ✅ **Analytics básicos** de interacciones

## Instalación

### Requisitos Previos
1. **WordPress 5.0+**
2. **WooCommerce 3.0+** (para funcionalidades de tienda)
3. **Wompi for WooCommerce** (para pagos - recomendado)

### Paso 1: Instalar WooCommerce
1. Ve a `Plugins > Agregar nuevo`
2. Busca "WooCommerce"
3. Instala y activa WooCommerce
4. Completa el asistente de configuración básica

### Paso 2: Instalar Tema Vane France
1. Ve a `Apariencia > Temas`
2. Haz clic en "Agregar nuevo" > "Subir tema"
3. Selecciona `vane-france-theme.zip`
4. Haz clic en "Instalar ahora" > "Activar"

### Paso 3: Instalar Plugins
1. Ve a `Plugins > Agregar nuevo` > "Subir plugin"
2. Instala `vf-extras.zip` y actívalo
3. Instala `vf-amelie.zip` y actívalo

### Paso 4: Configuración Inicial

#### Configurar Información de Contacto
1. Ve a `Vane France > Ajustes`
2. Configura:
   - **Número de WhatsApp**: `3193605666`
   - **Redes sociales**: URLs de Instagram, Facebook, TikTok
   - **Email de notificaciones**: Tu email administrativo

#### Configurar Programa de Regalos
1. En `Vane France > Ajustes`
2. Establece:
   - **Monto mínimo**: `100000` (ejemplo)
   - **Producto regalo**: Selecciona un producto existente
   - **Rol requerido**: Opcional (distribuidor/cliente/todos)

#### Configurar Chat Amélie
1. Ve a `Vane France > Amélie AI` (será agregado automáticamente al menú)
2. Configura:
   - **API Base URL**: `https://tu-dominio.com` (URL de tu Flask backend)
   - **API Secret**: Tu clave secreta (campo opcional para seguridad)
   - **Mostrar widget flotante**: Activado

### Paso 5: Configurar WooCommerce

#### Crear Etiquetas de Producto
1. Ve a `Productos > Etiquetas`
2. Crea las etiquetas:
   - `emprendedor` (para productos exclusivos)
   - `cliente` (para catálogo general)

#### Configurar Atributos (Recomendado)
1. Ve a `Productos > Atributos`
2. Crea atributos:
   - **Marca**: Para filtrar por marcas de perfumes
   - **Género**: Masculino, Femenino, Unisex

#### Configurar Widgets
1. Ve a `Apariencia > Widgets`
2. Configura las áreas:
   - **Sidebar Principal**: Agregar búsqueda, categorías, productos recientes
   - **Footer 1, 2, 3**: Información adicional, enlaces, formularios

## Configuración Avanzada

### Personalizar Colores del Tema
1. Ve a `Apariencia > Personalizar > Vane France Settings > Colores del Tema`
2. Ajusta:
   - **Color Primario**: `#002395` (azul marino)
   - **Color Secundario**: `#ed2939` (rojo)

### Configurar Menús
1. Ve a `Apariencia > Menús`
2. Crea estructura recomendada:
   ```
   - Inicio
   - Plan Emprendedor (enlace a página de catálogo emprendedor)
   - Cliente (enlace a página de catálogo cliente)  
   - Blog
   - Contacto
   ```

### Configurar Páginas Automáticas
El tema crea automáticamente:
- **Catálogo Emprendedor**: Con shortcode `[vf_catalog plan="emprendedor"]`
- **Catálogo Cliente**: Con shortcode `[vf_catalog plan="cliente"]`

## Uso de Funcionalidades

### Gestión de Ofertas
1. Ve a `Vane France > Nueva Oferta`
2. Crea ofertas con:
   - Tipo de descuento (porcentual, fijo, 2x1, envío gratis)
   - Fechas de validez
   - Límites de uso
   - Código promocional opcional

3. Mostrar en el sitio:
   ```php
   [vf_offers limit="6" columns="3"]
   ```

### Gestión de Stock
1. Ve a `Vane France > Stock Rápido`
2. Busca productos por nombre/SKU
3. Actualiza cantidades individual o masivamente
4. Revisa alertas de stock bajo en el dashboard

### Ver Reportes
1. Ve a `Vane France > Reportes`
2. Analiza:
   - Gráficos de ingresos y órdenes (30 días)
   - Top 10 productos por vistas
   - Top 10 productos más vendidos
   - Estadísticas de clientes y distribuidores

### Chat Amélie
- **Widget flotante**: Aparece automáticamente en todas las páginas
- **Shortcode**: Usa `[amelie_chat]` para insertar en páginas específicas
- **Configuración**: Personaliza colores, mensajes y posición

## Información del Negocio

### Datos Preconfigurados
**Direcciones**:
- Cl. 12 #13-99 a 13, 1, Bogotá
- Cl. 12 #13-69 Local 102, Bogotá

**Teléfono**: 319 3605666

**Horarios**:
- Lunes a Sábado: 9:00 AM - 7:00 PM
- Domingo: Cerrado

## Shortcodes Disponibles

### Catálogos
```php
[vf_catalog plan="emprendedor" per_page="12" columns="3"]
[vf_catalog plan="cliente" per_page="12" columns="3"]
```

### Ofertas
```php
[vf_offers limit="6" columns="3"]
```

### Chat Amélie
```php
[amelie_chat width="100%" height="400px"]
```

## Seguridad

### ⚠️ Importante: No hay claves hardcodeadas
- Todos los secretos se configuran a través de campos en el admin
- El usuario debe configurar manualmente la API URL y secret key
- No se incluyen credenciales en el código fuente

### Configuración Recomendada
1. Usa HTTPS para la API de Amélie
2. Configura clave secreta para la API del chat
3. Actualiza WordPress y plugins regularmente
4. Usa contraseñas fuertes para usuarios administradores

## Troubleshooting

### Problemas Comunes

**Los catálogos están vacíos**:
- Verifica que WooCommerce esté configurado
- Asegúrate de que los productos tengan las etiquetas `emprendedor` o `cliente`

**El chat de Amélie no responde**:
- Verifica que la API URL esté configurada correctamente
- Asegúrate de que el backend Flask esté ejecutándose
- Revisa la consola del navegador por errores

**Los precios no se ocultan para emprendedores**:
- Verifica que los productos tengan la etiqueta `emprendedor`
- Asegúrate de que el usuario no esté logueado

**Las ofertas no aparecen**:
- Verifica las fechas de validez de las ofertas
- Asegúrate de que no se hayan excedido los límites de uso

## Soporte

Para soporte técnico o consultas:
- Revisa la documentación incluida en cada componente
- Verifica los logs de WordPress (`WP_DEBUG = true`)
- Consulta la consola del navegador para errores JavaScript

---

**Versión**: 1.0.0  
**Entrega**: Diciembre 2024  
**Compatibilidad**: WordPress 5.0+ | WooCommerce 3.0+ | PHP 7.4+

## Archivos Incluidos

```
dist/
├── vane-france-theme.zip     # Tema principal de WordPress
├── vf-extras.zip            # Plugin de funcionalidades de negocio  
├── vf-amelie.zip           # Plugin de chat con IA
└── README.md               # Este archivo de instrucciones
```

¡Tu plataforma Vane France está lista para usar! 🎉