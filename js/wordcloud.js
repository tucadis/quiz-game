// Cargar palabras cada 2 segundos
setInterval(cargarPalabras, 2000);

// Cargar al inicio
cargarPalabras();

function volverLobby() {
    window.location.href = '../lobby.php';
}

async function agregarPalabra() {
    const input = document.getElementById('palabraInput');
    const palabra = input.value.trim();

    if (!palabra) {
        alert('Por favor escribe una palabra');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('accion', 'agregar');
        formData.append('palabra', palabra);

        const response = await fetch('../api/wordcloud.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.palabras) {
            mostrarPalabras(data.palabras);
            input.value = '';
        } else if (data.error) {
            alert(data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function limpiarNube() {
    if (!confirm('¿Estás seguro de limpiar la nube de palabras?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('accion', 'limpiar');

        const response = await fetch('../api/wordcloud.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            mostrarPalabras({});
        } else if (data.error) {
            alert(data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function cargarPalabras() {
    try {
        const response = await fetch('../api/wordcloud.php?accion=obtener');
        const data = await response.json();

        if (data.palabras) {
            mostrarPalabras(data.palabras);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function mostrarPalabras(palabras) {
    const cloudDiv = document.getElementById('wordCloud');

    if (Object.keys(palabras).length === 0) {
        cloudDiv.innerHTML = '<p style="color: #999;">Las palabras aparecerán aquí...</p>';
        return;
    }

    // Calcular tamaño basado en frecuencia
    const maxCount = Math.max(...Object.values(palabras));

    cloudDiv.innerHTML = Object.entries(palabras)
        .map(([palabra, contador]) => {
            const tamano = 1 + (contador / maxCount) * 2.5; // Entre 1 y 3.5
            return `<span class="word-item" style="font-size: ${tamano}em;">${palabra} (${contador})</span>`;
        })
        .join('');
}

// Enter para agregar palabra
document.getElementById('palabraInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        agregarPalabra();
    }
});
