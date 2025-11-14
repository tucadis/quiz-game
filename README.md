# 🎮 Plataforma de Minijuegos Multijugador

Plataforma web colaborativa con 9 minijuegos interactivos diseñados para trabajar en equipo en tiempo real, desarrollada con **PHP, MySQL y HTML/CSS/JavaScript**.

## 🎯 Características

- **Sistema de sesiones**: Crea o únete a sesiones con códigos de 6 caracteres
- **Multijugador en tiempo real**: Sincronización mediante polling (AJAX)
- **Base de datos MySQL**: Persistencia de datos
- **9 minijuegos colaborativos**:
  1. ☁️ **Nube de Palabras**: Crea nubes de palabras colaborativas
  2. 📊 **Encuestas Interactivas**: Crea y responde encuestas en tiempo real
  3. 💡 **Pizarra de Ideas**: Lluvia de ideas con post-its virtuales
  4. 🏆 **Votación y Ranking**: Vota y rankea opciones en equipo
  5. 🎯 **Trivia Competitiva**: Responde preguntas contra el reloj
  6. 🎨 **Pictionary**: Dibuja y adivina palabras
  7. 🃏 **Planning Poker**: Estimación ágil de tareas
  8. 😊 **Muro de Reacciones**: Expresa emociones con emojis
  9. 🎡 **Ruleta de Decisiones**: Selector aleatorio de opciones

## 📋 Requisitos Previos

- **Servidor Web**: Apache o Nginx
- **PHP**: versión 7.4 o superior
- **MySQL**: versión 5.7 o superior
- **Extensiones PHP requeridas**:
  - PDO
  - PDO_MySQL
  - JSON
  - Session

## 🚀 Instalación

### 1. Clonar/Descargar el Proyecto

```bash
git clone <url-del-repositorio>
cd quiz-game
```

### 2. Configurar la Base de Datos

1. Crear la base de datos:
```bash
mysql -u root -p < database.sql
```

O manualmente:
- Accede a phpMyAdmin o tu gestor de MySQL
- Crea una nueva base de datos llamada `minijuegos_db`
- Importa el archivo `database.sql`

2. Configurar credenciales:

Edita el archivo `config/database.php` y ajusta las credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'minijuegos_db');
define('DB_USER', 'tu_usuario');     // Cambia esto
define('DB_PASS', 'tu_contraseña');  // Cambia esto
```

### 3. Configurar el Servidor Web

#### Opción A: Usando XAMPP/WAMP/MAMP

1. Copia la carpeta del proyecto a:
   - XAMPP: `C:\xampp\htdocs\quiz-game`
   - WAMP: `C:\wamp64\www\quiz-game`
   - MAMP: `/Applications/MAMP/htdocs/quiz-game`

2. Inicia Apache y MySQL desde el panel de control

3. Accede a: `http://localhost/quiz-game`

#### Opción B: Usando PHP Built-in Server (solo desarrollo)

```bash
php -S localhost:8000
```

Accede a: `http://localhost:8000`

#### Opción C: Servidor de Producción

1. Sube todos los archivos al directorio público de tu hosting
2. Asegúrate que el dominio apunte a la raíz del proyecto
3. Configura las credenciales de MySQL en `config/database.php`

### 4. Configurar Permisos (Linux/Mac)

```bash
chmod -R 755 .
chmod -R 777 config/
```

## 📖 Cómo Usar

### Para el Instructor

1. **Crear Sesión**:
   - Ve a la página principal
   - Haz clic en "Crear Sesión"
   - Ingresa tu nombre
   - Recibirás un código de 6 caracteres para compartir

2. **Compartir Código**:
   - Comparte el código con los participantes

3. **Seleccionar Minijuego**:
   - Desde el lobby, haz clic en el minijuego deseado
   - Solo el instructor puede cambiar de juego

4. **Controlar el Juego**:
   - Cada minijuego tiene controles específicos para el instructor
   - Configura preguntas, tareas o parámetros según el juego

### Para Participantes

1. **Unirse a Sesión**:
   - Haz clic en "Unirse a Sesión"
   - Ingresa el código de 6 caracteres
   - Escribe tu nickname

2. **Esperar en el Lobby**:
   - Verás a otros participantes conectarse
   - El instructor seleccionará el minijuego

3. **Jugar**:
   - Participa activamente en el minijuego seleccionado
   - Las actualizaciones se sincronizan automáticamente

## 🎮 Guía de Minijuegos

### 1. Nube de Palabras ☁️
- Todos pueden agregar palabras o frases
- Las palabras más repetidas aparecen más grandes
- El instructor puede limpiar la nube

**Estado**: ✅ Implementado

### 2. Encuestas Interactivas 📊
- El instructor crea preguntas con múltiples opciones
- Los participantes votan
- Los resultados se muestran en tiempo real con porcentajes

**Estado**: ✅ API implementada (falta interfaz completa)

### 3-9. Otros Minijuegos
Los demás minijuegos siguen la misma estructura:
- API en `/api/[nombre-juego].php`
- Vista en `/games/[nombre-juego].php`
- JavaScript en `/js/[nombre-juego].js`

**Estado**: 🔨 Estructura lista para implementación

## 📁 Estructura del Proyecto

```
quiz-game/
├── index.php              # Página de inicio
├── lobby.php              # Lobby de sesión
├── database.sql           # Script SQL de base de datos
├── config/
│   ├── database.php       # Configuración de BD
│   └── functions.php      # Funciones auxiliares
├── api/
│   ├── sesiones.php       # API de sesiones
│   ├── wordcloud.php      # API nube de palabras
│   └── poll.php           # API encuestas
├── games/
│   ├── wordcloud.php      # Vista nube de palabras
│   └── [otros-juegos].php # Otros minijuegos
├── js/
│   ├── main.js            # JavaScript página inicio
│   ├── lobby.js           # JavaScript lobby
│   ├── wordcloud.js       # JavaScript nube de palabras
│   └── [otros].js         # Otros scripts
└── css/
    └── styles.css         # Estilos globales
```

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **AJAX**: Fetch API para comunicación asíncrona
- **Arquitectura**: MVC simplificado

## 🔒 Seguridad

- Validación de entrada en todas las APIs
- Protección contra SQL Injection usando PDO con prepared statements
- Sanitización de HTML con `htmlspecialchars()`
- Control de acceso basado en roles (Instructor/Participante)
- Sesiones PHP para autenticación

## ⚙️ Configuración Avanzada

### Cambiar Tiempo de Sesión PHP

Edita `config/functions.php`:

```php
// Cambiar 2 horas a otro valor
WHERE ultima_actividad > DATE_SUB(NOW(), INTERVAL 2 HOUR)
```

### Ajustar Frecuencia de Polling

En los archivos JS, cambia los intervalos:

```javascript
// Cada 2 segundos (2000ms)
setInterval(cargarDatos, 2000);
```

## 🐛 Solución de Problemas

### Error de Conexión a la Base de Datos

- Verifica las credenciales en `config/database.php`
- Asegúrate que MySQL está corriendo
- Verifica que el usuario tiene permisos

### Las sesiones no funcionan

- Verifica que `session_start()` se llama en todas las páginas
- Comprueba permisos de la carpeta de sesiones PHP

### Los datos no se actualizan

- Verifica la consola del navegador para errores JavaScript
- Comprueba que las APIs responden correctamente
- Asegúrate que el polling está activo

## 📝 Expandir el Proyecto

Para agregar más minijuegos:

1. Crear tabla en `database.sql` si es necesario
2. Crear API en `api/nuevo-juego.php`
3. Crear vista en `games/nuevo-juego.php`
4. Crear JavaScript en `js/nuevo-juego.js`
5. Agregar card en `lobby.php`

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Para contribuir:

1. Haz fork del proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📄 Licencia

MIT License

## 👨‍💻 Autor

Desarrollado para facilitar la colaboración en equipo de forma interactiva.

---

## 📞 Soporte

Para reportar bugs o solicitar features, abre un issue en el repositorio.

¡Disfruta jugando en equipo! 🎉
