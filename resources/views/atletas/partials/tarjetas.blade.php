<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
    @forelse($atletas as $atleta)
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <!-- Foto del atleta -->
                    <div class="flex-shrink-0 me-3">
                        @if($atleta->foto && Storage::disk('public')->exists($atleta->foto))
                            <img src="{{ asset('storage/'.$atleta->foto) }}" 
                                 alt="Foto de {{ $atleta->nombre }}" 
                                 class="rounded-circle" 
                                 width="70" 
                                 height="70"
                                 style="object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 70px; height: 70px;">
                                <i class="bi bi-person" style="font-size: 1.8rem;"></i>
                            </div>
                        @endif
                    </div>
                    <!-- Resto del contenido de la tarjeta... -->
                </div>
            </div>
        </div>
    </div>
    @empty
    <!-- Mensaje cuando no hay atletas -->
    @endforelse
</div>