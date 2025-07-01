document.addEventListener('DOMContentLoaded', function() {
    let modoEdicion = false;
    let cambiosPendientes = false;
    const iniciarBtn = document.getElementById('iniciarBtn');
    const guardarBtn = document.getElementById('guardarBtn');

    if (!iniciarBtn || !guardarBtn) {
        console.error('Botones no encontrados');
        return;
    }

    // Estados en orden cíclico
    const ESTADOS = ['libre', 'presente', 'ausente', 'justificado'];
    const ICONOS = {
        libre: 'bi-circle text-muted',
        presente: 'bi-check-circle-fill text-success',
        ausente: 'bi-x-circle-fill text-danger',
        justificado: 'bi-exclamation-circle-fill text-warning'
    };

    // Mostrar/ocultar modo edición
    function toggleModoEdicion() {
        document.querySelectorAll('.asistencia-td').forEach(celda => {
            celda.style.cursor = modoEdicion ? 'pointer' : 'default';
        });
    }

    // Actualizar icono según estado
    function actualizarIcono(celda) {
        const estado = celda.dataset.estado || 'libre';
        const icono = celda.querySelector('i');
        if (icono) {
            icono.className = `bi fs-5 ${ICONOS[estado]}`;
        }
        
        // Actualizar el radio oculto
        const radio = celda.querySelector(`input[value="${estado}"]`);
        if (radio) {
            radio.checked = true;
        }
    }

    // Configurar eventos de clic
    function configurarClics() {
        document.querySelectorAll('.asistencia-td:not(.bg-light)').forEach(celda => {
            celda.addEventListener('click', function(e) {
                if (!modoEdicion) return;
                
                const estadoActual = this.dataset.estado || 'libre';
                const indexActual = ESTADOS.indexOf(estadoActual);
                const siguienteIndex = (indexActual + 1) % ESTADOS.length;
                const siguienteEstado = ESTADOS[siguienteIndex];
                
                this.dataset.estado = siguienteEstado;
                cambiosPendientes = true;
                actualizarIcono(this);
                actualizarEstadoUI();
            });
        });
    }

    // Actualizar UI de botones
    function actualizarEstadoUI() {
        iniciarBtn.innerHTML = modoEdicion 
            ? '<i class="bi bi-x-circle"></i> Cancelar' 
            : '<i class="bi bi-pencil"></i> Iniciar Asistencia';

        iniciarBtn.className = modoEdicion 
            ? 'btn btn-danger me-2' 
            : 'btn btn-primary me-2';

        guardarBtn.disabled = !modoEdicion || !cambiosPendientes;
    }

    // Evento para iniciar/cancelar edición
    iniciarBtn.addEventListener('click', function(e) {
        e.preventDefault();
        modoEdicion = !modoEdicion;
        toggleModoEdicion();
        actualizarEstadoUI();

        Swal.fire({
            title: modoEdicion ? 'Modo edición activado' : 'Modo edición desactivado',
            icon: modoEdicion ? 'success' : 'info',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Evento para guardar
    guardarBtn.addEventListener('click', guardarAsistencias);

    async function guardarAsistencias() {
        if (!cambiosPendientes) {
            Swal.fire({
                title: 'Info',
                text: 'No hay cambios para guardar',
                icon: 'info',
                timer: 1500
            });
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
                body: JSON.stringify({ asistencias })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Error al guardar');

            await Swal.fire({
                title: 'Éxito',
                text: data.message,
                icon: 'success',
                timer: 2000
            });

            cambiosPendientes = false;
            modoEdicion = false;
            toggleModoEdicion();
            actualizarEstadoUI();
            location.reload();
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', error.message, 'error');
        }
    }

    // Inicialización
    function inicializar() {
        // Ocultar todos los radios (seguirán funcionando pero no se verán)
        document.querySelectorAll('.asistencia-td input[type="radio"]').forEach(radio => {
            radio.style.display = 'none';
        });
        
        // Configurar estados iniciales
        document.querySelectorAll('.asistencia-td').forEach(celda => {
            if (!celda.dataset.estado) {
                const radioSeleccionado = celda.querySelector('input[type="radio"]:checked');
                celda.dataset.estado = radioSeleccionado ? radioSeleccionado.value : 'libre';
            }
            actualizarIcono(celda);
        });
        
        toggleModoEdicion();
        configurarClics();
        actualizarEstadoUI();
    }

    inicializar();
});