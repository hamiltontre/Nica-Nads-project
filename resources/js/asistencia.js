document.addEventListener('DOMContentLoaded', function() {
    let modoEdicion = false;
    let cambiosPendientes = false;
    const iniciarBtn = document.getElementById('iniciarBtn');
    const guardarBtn = document.getElementById('guardarBtn');

    if (!iniciarBtn || !guardarBtn) {
        console.error('Botones no encontrados');
        return;
    }

    // Mostrar/ocultar radios según modo edición
    function toggleModoEdicion() {
        const radios = document.querySelectorAll('.form-check-input');
        radios.forEach(radio => {
            radio.style.display = modoEdicion ? 'block' : 'none';
        });
        
        const iconos = document.querySelectorAll('.asistencia-cell i');
        iconos.forEach(icono => {
            icono.style.display = modoEdicion ? 'none' : 'block';
        });
    }

    iniciarBtn.addEventListener('click', function(e) {
        e.preventDefault();
        modoEdicion = !modoEdicion;
        toggleModoEdicion();
        actualizarEstadoUI();

        if (modoEdicion) {
            Swal.fire({
                title: 'Modo edición activado',
                text: 'Puedes registrar asistencias',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                title: 'Modo edición desactivado',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });

    guardarBtn.addEventListener('click', function(e) {
        e.preventDefault();
        guardarAsistencias();
    });

    // Handle radio button changes
    document.querySelectorAll('.form-check-input').forEach(radio => {
        radio.addEventListener('change', function() {
            if (!modoEdicion) return;
            
            const celda = this.closest('.asistencia-td');
            if (!celda) return;
            
            celda.dataset.estado = this.value;
            cambiosPendientes = true;
            actualizarEstadoUI();
        });
    });

    function actualizarEstadoUI() {
        iniciarBtn.innerHTML = modoEdicion 
            ? '<i class="bi bi-x-circle"></i> Cancelar' 
            : '<i class="bi bi-pencil"></i> Iniciar Asistencia';

        iniciarBtn.className = modoEdicion 
            ? 'btn btn-danger me-2' 
            : 'btn btn-primary me-2';

        guardarBtn.disabled = !modoEdicion || !cambiosPendientes;
    }

    // Inicializar
    toggleModoEdicion();
    actualizarEstadoUI();

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
            const radioSeleccionado = celda.querySelector('input[type="radio"]:checked');
            if (radioSeleccionado) {
                asistencias.push({
                    atleta_id: celda.dataset.atletaId,
                    fecha: celda.dataset.fecha,
                    turno: celda.dataset.turno,
                    estado: radioSeleccionado.value
                });
            }
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
});