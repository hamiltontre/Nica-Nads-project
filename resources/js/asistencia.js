let modoEdicion = false;
let cambiosPendientes = false;

const iniciarBtn = document.getElementById('iniciarBtn');
const guardarBtn = document.getElementById('guardarBtn');

// Evento para iniciar asistencia
iniciarBtn?.addEventListener('click', function (e) {
    e.preventDefault();
    modoEdicion = !modoEdicion;
    actualizarEstadoUI();

    if (modoEdicion) {
        Swal.fire({
            title: 'Modo edición activado',
            text: 'Puedes comenzar a registrar asistencias',
            icon: 'success'
        });
    }
});

// Evento para guardar
guardarBtn?.addEventListener('click', guardarAsistencias);

// Delegación de eventos para las celdas (solo una vez)
document.addEventListener('click', function (e) {
    if (!modoEdicion) return;

    const celda = e.target.closest('.asistencia-td:not(.bg-light)');
    if (!celda) return;

    e.preventDefault();
    e.stopPropagation();

    const currentEstado = celda.dataset.estado || 'libre';
    let nuevoEstado;

    if (e.detail === 2) { // Doble clic
        nuevoEstado = 'libre';
    } else { // Clic simple
        nuevoEstado = currentEstado === 'presente' ? 'ausente' :
                      currentEstado === 'ausente' ? 'libre' :
                      'presente';
    }

    celda.dataset.estado = nuevoEstado;
    actualizarIconoAsistencia(celda, nuevoEstado);
    cambiosPendientes = true;
    actualizarEstadoUI();
});

function actualizarIconoAsistencia(celda, estado) {
    let icono = celda.querySelector('.bi');
    if (!icono) {
        icono = document.createElement('i');
        icono.className = 'bi fs-5';
        const cell = celda.querySelector('.asistencia-cell');
        if (cell) {
            cell.innerHTML = '';
            cell.appendChild(icono);
        }
    }

    icono.className = 'bi fs-5';

    if (estado === 'presente') {
        icono.classList.add('bi-check-circle-fill', 'text-success');
    } else if (estado === 'ausente') {
        icono.classList.add('bi-x-circle-fill', 'text-danger');
    } else {
        icono.classList.add('bi-circle', 'text-muted');
    }
}

async function guardarAsistencias() {
    if (!cambiosPendientes) {
        Swal.fire('Info', 'No hay cambios pendientes para guardar', 'info');
        return;
    }

    const asistencias = [];
    document.querySelectorAll('.asistencia-td:not(.bg-light)').forEach(celda => {
        asistencias.push({
            atleta_id: celda.dataset.atletaId,
            fecha: celda.dataset.fecha,
            turno: celda.dataset.turno,
            estado: celda.dataset.estado || 'libre'
        });
    });

    try {
        const response = await fetch(window.asistenciaConfig.storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.asistenciaConfig.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ asistencias: asistencias })
        });

        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

        const data = await response.json();

        if (data.success) {
            await Swal.fire('Éxito', data.message, 'success');
            cambiosPendientes = false;
            actualizarEstadoUI();
        } else {
            throw new Error(data.message || 'Error al guardar');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', error.message, 'error');
    }
}

function actualizarEstadoUI() {
    if (!iniciarBtn || !guardarBtn) return;

    iniciarBtn.innerHTML = modoEdicion
        ? '<i class="bi bi-x-circle"></i> Cancelar'
        : '<i class="bi bi-pencil"></i> Iniciar Asistencia';

    iniciarBtn.className = modoEdicion
        ? 'btn btn-danger me-2'
        : 'btn btn-primary me-2';

    guardarBtn.disabled = !modoEdicion || !cambiosPendientes;

    document.querySelectorAll('.asistencia-td:not(.bg-light)').forEach(celda => {
        celda.style.cursor = modoEdicion ? 'pointer' : 'default';
        celda.title = modoEdicion ? 'Click para cambiar estado' : '';
    });
}

// Inicializar estado inicial
document.addEventListener('DOMContentLoaded', actualizarEstadoUI);