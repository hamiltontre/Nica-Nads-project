@extends('layouts.modern')

@section('title', 'Editar Atleta')

@section('content')
<!--- 
    Vista para editar atletas existentes
    Funcionalidades:
    - Formulario pre-llenado con datos actuales
    - Mantener foto existente o actualizarla
    - Validación en tiempo real
    - Vista previa de foto
--->
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-fill me-2"></i>Editar Atleta
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('atletas.update', $atleta->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!--- Sección de información básica --->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre*</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre', $atleta->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="apellido" class="form-label">Apellido*</label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                                       id="apellido" name="apellido" value="{{ old('apellido', $atleta->apellido) }}" required>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!--- Edad y grupo --->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edad" class="form-label">Edad*</label>
                                <input type="number" class="form-control @error('edad') is-invalid @enderror" 
                                       id="edad" name="edad" value="{{ old('edad', $atleta->edad) }}" min="5" max="99" required>
                                @error('edad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="grupo" class="form-label">Grupo*</label>
                                <select class="form-select @error('grupo') is-invalid @enderror" 
                                        id="grupo" name="grupo" required>
                                    <option value="" disabled>Seleccione un grupo</option>
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo }}" {{ old('grupo', $atleta->grupo) == $grupo ? 'selected' : '' }}>
                                            {{ $grupo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('grupo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!--- Foto y becado --->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Becado</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="becado" name="becado" value="1" {{ old('becado', $atleta->becado) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="becado">¿Es becado?</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="foto" class="form-label">Foto del Atleta</label>
                                <input type="file" class="form-control @error('foto') is-invalid @enderror" 
                                       id="foto" name="foto" accept="image/*">
                                @error('foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Máximo 2MB (dejar en blanco para mantener la actual)</small>
                            </div>
                        </div>

                        <!--- Vista previa de foto --->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="preview-container text-center mt-3">
                                    @if($atleta->foto && Storage::disk('public')->exists($atleta->foto))
                                        <img id="previewImage" src="{{ asset('storage/'.$atleta->foto) }}" 
                                             alt="Foto actual del atleta" 
                                             class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    @else
                                        <div id="noFotoPreview" class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                             style="width: 200px; height: 200px;">
                                            <i class="bi bi-person text-muted" style="font-size: 5rem;"></i>
                                        </div>
                                        <img id="previewImage" src="#" alt="Vista previa de la foto" 
                                             class="img-thumbnail d-none" style="max-width: 200px; max-height: 200px;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!--- Botones de acción --->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('atletas.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Actualizar Atleta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--- Script para vista previa de foto --->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('foto');
    const fotoPreview = document.getElementById('previewImage');
    const noFotoPreview = document.getElementById('noFotoPreview');
    
    if (fotoInput) {
        fotoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (fotoPreview) {
                        fotoPreview.src = e.target.result;
                        fotoPreview.classList.remove('d-none');
                    }
                    if (noFotoPreview) {
                        noFotoPreview.classList.add('d-none');
                    }
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>
@endpush

@endsection