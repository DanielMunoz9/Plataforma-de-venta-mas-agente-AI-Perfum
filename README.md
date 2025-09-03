# Plataforma de Venta + Agente AI "Amélie"

Una plataforma completa de e-commerce especializada en productos de belleza y peluquería, equipada con un agente de ventas inteligente llamado **Amélie**.

## 🚀 Despliegue Rápido en Render

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/DanielMunoz9/Plataforma-de-venta-mas-agente-AI-Perfum)

## 📋 Descripción del Proyecto

Este repositorio contiene:
- **Plataforma de Venta**: Sistema completo de e-commerce con gestión de productos, carritos, órdenes y usuarios
- **Agente AI "Amélie"**: Asistente inteligente de ventas que puede integrarse con WordPress y otros sitios web

## 🤖 API de Amélie - Endpoints Disponibles

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `/api/ai/status` | GET | Estado del agente AI |
| `/api/ai/personality` | GET | Información de personalidad de Amélie |
| `/api/ai/chat` | POST | Conversación con el agente |
| `/api/ai/test` | GET | Endpoint de prueba |

### Ejemplo de uso:

```bash
# Verificar estado
curl https://tu-app.onrender.com/api/ai/status

# Chatear con Amélie
curl -X POST https://tu-app.onrender.com/api/ai/chat \
  -H "Content-Type: application/json" \
  -d '{"mensaje": "Hola, necesito ayuda con productos"}'
```

## 🔧 Variables de Entorno

| Variable | Descripción | Valor por Defecto |
|----------|-------------|-------------------|
| `ALLOWED_ORIGINS` | Dominios permitidos para CORS (separados por comas) | `https://lightslategrey-gorilla-972270.hostingersite.com` |
| `AMELIE_NAME` | Nombre del agente AI | `Amélie` |
| `AMELIE_GREETING` | Mensaje de saludo | `¡Hola! Soy Amélie, tu asistente de ventas...` |
| `AMELIE_SECRET` | Clave secreta para autenticación (opcional) | *Generada automáticamente* |

## 🚀 Pasos para Desplegar en Render

### 1. Despliegue Automático
1. Haz clic en el botón **"Deploy to Render"** arriba
2. Conecta tu cuenta de GitHub si es necesario
3. Render configurará automáticamente el servicio
4. Espera a que termine el despliegue (5-10 minutos)

### 2. Configuración en WordPress (Vane France > Amélie AI)

1. **Acceder al Customizer de WordPress:**
   - Ve a `Apariencia > Personalizar`
   - Busca la sección "Vane France > Amélie AI"

2. **Configurar la URL de la API:**
   - En el campo "URL de API", ingresa: `https://tu-app.onrender.com`
   - Reemplaza `tu-app` con el nombre real de tu aplicación en Render

3. **Configurar Autenticación (Opcional):**
   - Si quieres usar autenticación, copia el valor de `AMELIE_SECRET` desde Render
   - Pégalo en el campo correspondiente en WordPress

4. **Guardar Cambios:**
   - Haz clic en "Publicar" para guardar la configuración

## 🔧 Ejecutar Localmente

Para probar Amélie en tu máquina local:

```bash
# Clonar el repositorio
git clone https://github.com/DanielMunoz9/Plataforma-de-venta-mas-agente-AI-Perfum.git
cd Plataforma-de-venta-mas-agente-AI-Perfum

# Instalar dependencias
pip install -r requirements.txt

# Ejecutar el agente AI
python test_amelie.py
```

La API estará disponible en `http://localhost:5000`

## 🐛 Solución de Problemas

### Error CORS 403/Bloqueado
```bash
# Verificar dominios permitidos
curl https://tu-app.onrender.com/api/ai/test
```
**Solución:** Agrega tu dominio a la variable `ALLOWED_ORIGINS` en Render.

### Error 401 - Secret Mismatch
```bash
# Probar sin autenticación
curl https://tu-app.onrender.com/api/ai/status

# Probar con secret
curl -H "X-Amelie-Secret: tu-secret-aqui" \
     https://tu-app.onrender.com/api/ai/status
```
**Solución:** Verifica que el `AMELIE_SECRET` en WordPress coincida con el de Render.

### El widget muestra "API no configurada"
1. Verifica que la URL de la API en WordPress sea correcta
2. Asegúrate de que el servicio en Render esté funcionando
3. Revisa los logs en Render para errores

## 📱 Ejemplo de Integración

```javascript
// Ejemplo de JavaScript para integrar con Amélie
async function chatWithAmelie(mensaje) {
  const response = await fetch('https://tu-app.onrender.com/api/ai/chat', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      // 'X-Amelie-Secret': 'tu-secret' // Opcional
    },
    body: JSON.stringify({ mensaje: mensaje })
  });
  
  const data = await response.json();
  return data.respuesta;
}

// Usar la función
chatWithAmelie("Hola, ¿qué productos recomiendan?")
  .then(respuesta => console.log(respuesta));
```

## 📂 Estructura del Proyecto

```
├── README.md              # Este archivo
├── render.yaml            # Configuración de Render
├── Procfile               # Comando de inicio para Render
├── requirements.txt       # Dependencias de Python
├── test_amelie.py         # API del agente AI Amélie
├── backend/               # Aplicación principal Flask
│   ├── app.py
│   ├── requirements.txt
│   └── ...
└── frontend/              # Templates y archivos estáticos
    ├── templates/
    └── static/
```

## 🆘 Soporte

Si tienes problemas:
1. Revisa los logs en el Dashboard de Render
2. Verifica que todas las variables de entorno estén configuradas
3. Prueba los endpoints directamente con curl
4. Asegúrate de que CORS esté configurado correctamente

---

**Desarrollado con ❤️ para Vane France**