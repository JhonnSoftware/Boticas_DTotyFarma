<div class="modal-header text-white" style="background-color:#0A7ABF;">
  <h5 class="modal-title fw-semibold">Editar Genérico</h5>
  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form action="{{ route('genericos.actualizar', $generico->id) }}" method="POST" class="needs-validation" novalidate>
  @csrf
  @method('PUT')

  <div class="modal-body" style="background-color:#F9F9F9;">
    <div class="mb-3">
      <label class="form-label fw-semibold">Nombre</label>
      <input type="text" class="form-control rounded-3" name="nombre"
             value="{{ old('nombre', $generico->nombre) }}" required>
      <div class="invalid-feedback">Se debe ingresar el nombre del genérico.</div>
    </div>
  </div>

  <div class="modal-footer bg-white">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn text-white" style="background-color:#25A6D9;">Guardar Cambios</button>
  </div>
</form>
