@extends('layouts.app')

@section('title', 'Tambah Tool')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Tambah Tool</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Master Tool</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tools.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Tool <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" 
                                   name="nama" 
                                   value="{{ old('nama') }}" 
                                   placeholder="Contoh: Antigravity, Kiro, Claude"
                                   required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" 
                                   class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" 
                                   name="logo"
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            <small class="text-muted">Format: JPG, PNG, GIF, SVG. Maksimal 2MB</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="mt-2" id="imagePreview" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status_aktif" class="form-label">Status</label>
                            <select class="form-select @error('status_aktif') is-invalid @enderror" 
                                    id="status_aktif" 
                                    name="status_aktif">
                                <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            @error('status_aktif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="4"
                                      placeholder="Deskripsi singkat tentang tool ini">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="{{ route('tools.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}
</script>
@endpush
@endsection
