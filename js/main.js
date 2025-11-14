function mostrarCrearSesion() {
    document.getElementById('crearForm').classList.remove('hidden');
    document.getElementById('unirseForm').classList.add('hidden');
    document.getElementById('mensaje').textContent = '';
}

function mostrarUnirseSesion() {
    document.getElementById('unirseForm').classList.remove('hidden');
    document.getElementById('crearForm').classList.add('hidden');
    document.getElementById('mensaje').textContent = '';
}

function mostrarMensaje(texto, tipo) {
    const mensajeDiv = document.getElementById('mensaje');
    mensajeDiv.textContent = texto;
    mensajeDiv.className = `message ${tipo}`;
}

async function crearSesion() {
    const nombre = document.getElementById('nombreInstructor').value.trim();

    if (!nombre) {
        mostrarMensaje('Por favor ingresa tu nombre', 'error');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('accion', 'crear');
        formData.append('nombre', nombre);

        const response = await fetch('api/sesiones.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = 'lobby.php';
        } else {
            mostrarMensaje(data.error || 'Error al crear la sesión', 'error');
        }
    } catch (error) {
        mostrarMensaje('Error de conexión', 'error');
        console.error('Error:', error);
    }
}

async function unirseSesion() {
    const codigo = document.getElementById('codigoSesion').value.trim().toUpperCase();
    const nombre = document.getElementById('nombreParticipante').value.trim();

    if (!codigo || codigo.length !== 6) {
        mostrarMensaje('El código debe tener 6 caracteres', 'error');
        return;
    }

    if (!nombre) {
        mostrarMensaje('Por favor ingresa tu nickname', 'error');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('accion', 'unirse');
        formData.append('codigo', codigo);
        formData.append('nombre', nombre);

        const response = await fetch('api/sesiones.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = 'lobby.php';
        } else {
            mostrarMensaje(data.error || 'Error al unirse a la sesión', 'error');
        }
    } catch (error) {
        mostrarMensaje('Error de conexión', 'error');
        console.error('Error:', error);
    }
}

// Auto-uppercase para código de sesión
document.addEventListener('DOMContentLoaded', () => {
    const codigoInput = document.getElementById('codigoSesion');
    if (codigoInput) {
        codigoInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.toUpperCase();
        });
    }
});
