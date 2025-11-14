// Actualizar usuarios cada 2 segundos
setInterval(cargarUsuarios, 2000);

// Verificar cambio de juego cada segundo
setInterval(verificarJuego, 1000);

// Cargar usuarios al inicio
cargarUsuarios();

async function cargarUsuarios() {
    try {
        const response = await fetch('api/sesiones.php?accion=usuarios');
        const data = await response.json();

        if (data.usuarios) {
            mostrarUsuarios(data.usuarios);
        }
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
    }
}

function mostrarUsuarios(usuarios) {
    const usersList = document.getElementById('usersList');
    const userCount = document.getElementById('userCount');

    userCount.textContent = usuarios.length;

    usersList.innerHTML = usuarios.map(user => `
        <div class="user-item ${user.es_instructor ? 'instructor' : ''}">
            <span>${user.es_instructor ? '👑' : '👤'} ${user.nickname}</span>
            ${user.es_instructor ? '<span class="user-badge">INSTRUCTOR</span>' : ''}
        </div>
    `).join('');
}

async function seleccionarJuego(juego) {
    if (!esInstructor) {
        alert('Solo el instructor puede seleccionar juegos');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('accion', 'cambiar_juego');
        formData.append('juego', juego);

        const response = await fetch('api/sesiones.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = `games/${juego}.php`;
        } else {
            alert(data.error || 'Error al cambiar de juego');
        }
    } catch (error) {
        alert('Error de conexión');
        console.error('Error:', error);
    }
}

let juegoActual = null;

async function verificarJuego() {
    try {
        const response = await fetch('api/sesiones.php?accion=verificar');
        const data = await response.json();

        if (data.juego_actual && data.juego_actual !== juegoActual) {
            juegoActual = data.juego_actual;
            if (!esInstructor) {
                // Solo participantes son redirigidos automáticamente
                window.location.href = `games/${juegoActual}.php`;
            }
        }
    } catch (error) {
        console.error('Error al verificar juego:', error);
    }
}
