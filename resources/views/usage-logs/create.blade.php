@extends('layouts.app')

@section('title', 'Tambah Log Penggunaan')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Tambah Log Penggunaan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('usage-logs.index') }}">Status Penggunaan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('usage-logs.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_id" class="form-label">Account <span class="text-danger">*</span></label>
                                    <select class="form-select @error('account_id') is-invalid @enderror" 
                                            id="account_id" 
                                            name="account_id"
                                            required
                                            onchange="loadAccountTools()">
                                        <option value="">Pilih Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" 
                                                    data-tools="{{ $account->tools->pluck('id')->join(',') }}"
                                                    {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tool_id" class="form-label">Tool <span class="text-danger">*</span></label>
                                    <select class="form-select @error('tool_id') is-invalid @enderror" 
                                            id="tool_id" 
                                            name="tool_id"
                                            required>
                                        <option value="">Pilih Tool</option>
                                        @foreach($tools as $tool)
                                            <option value="{{ $tool->id }}" {{ old('tool_id') == $tool->id ? 'selected' : '' }}>
                                                {{ $tool->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tool_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="toolHint" style="display:none;">💡 Pilih tool yang terhubung dengan account</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                           id="tanggal" 
                                           name="tanggal" 
                                           value="{{ old('tanggal', date('Y-m-d')) }}"
                                           required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="limit_used" class="form-label">Limit Digunakan <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('limit_used') is-invalid @enderror" 
                                           id="limit_used" 
                                           name="limit_used" 
                                           value="{{ old('limit_used', 0) }}" 
                                           step="0.01"
                                           min="0"
                                           placeholder="3.6"
                                           required
                                           oninput="calculatePercentage()">
                                    @error('limit_used')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="limit_total" class="form-label">Total Limit <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('limit_total') is-invalid @enderror" 
                                           id="limit_total" 
                                           name="limit_total" 
                                           value="{{ old('limit_total', 50) }}" 
                                           step="0.01"
                                           min="0"
                                           placeholder="50"
                                           required
                                           oninput="calculatePercentage()">
                                    @error('limit_total')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar Preview -->
                        <div class="mb-3">
                            <label class="form-label">Preview Penggunaan</label>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar" 
                                     id="progressPreview"
                                     role="progressbar" 
                                     style="width: 0%">
                                    0%
                                </div>
                            </div>
                            <small class="text-muted">Sisa: <span id="remainingLimit">0</span></small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status"
                                    required>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', 'Ready') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" 
                                      name="catatan" 
                                      rows="3"
                                      placeholder="Catatan penggunaan hari ini...">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="{{ route('usage-logs.index') }}" class="btn btn-secondary">
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
function calculatePercentage() {
    const limitUsed = parseFloat(document.getElementById('limit_used').value) || 0;
    const limitTotal = parseFloat(document.getElementById('limit_total').value) || 0;
    
    if (limitTotal === 0) {
        updateProgress(0, 0);
        return;
    }
    
    const percentage = (limitUsed / limitTotal) * 100;
    const remaining = limitTotal - limitUsed;
    
    updateProgress(percentage, remaining);
    autoSetStatus(percentage);
}

function updateProgress(percentage, remaining) {
    const progressBar = document.getElementById('progressPreview');
    const remainingSpan = document.getElementById('remainingLimit');
    
    progressBar.style.width = Math.min(percentage, 100) + '%';
    progressBar.textContent = percentage.toFixed(1) + '%';
    remainingSpan.textContent = remaining.toFixed(2);
    
    // Update color based on percentage
    progressBar.className = 'progress-bar';
    if (percentage >= 95) {
        progressBar.classList.add('bg-danger');
    } else if (percentage >= 70) {
        progressBar.classList.add('bg-warning');
    } else if (percentage > 0) {
        progressBar.classList.add('bg-primary');
    } else {
        progressBar.classList.add('bg-success');
    }
}

function autoSetStatus(percentage) {
    const statusSelect = document.getElementById('status');
    
    if (percentage >= 95) {
        statusSelect.value = 'Limit';
    } else if (percentage >= 70) {
        statusSelect.value = 'Warning';
    } else if (percentage > 0) {
        statusSelect.value = 'Sedang Dipakai';
    } else {
        statusSelect.value = 'Ready';
    }
}

function loadAccountTools() {
    const accountSelect = document.getElementById('account_id');
    const toolSelect = document.getElementById('tool_id');
    const toolHint = document.getElementById('toolHint');
    const selectedOption = accountSelect.options[accountSelect.selectedIndex];
    const toolIds = selectedOption.getAttribute('data-tools');
    
    if (toolIds) {
        const toolIdArray = toolIds.split(',').map(id => parseInt(id));
        
        // Highlight tools yang terhubung dengan account
        Array.from(toolSelect.options).forEach(option => {
            if (option.value && toolIdArray.includes(parseInt(option.value))) {
                option.style.fontWeight = 'bold';
                option.style.color = '#0d6efd';
            } else if (option.value) {
                option.style.fontWeight = 'normal';
                option.style.color = '#666';
            }
        });
        
        toolHint.style.display = 'block';
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculatePercentage();
    if (document.getElementById('account_id').value) {
        loadAccountTools();
    }
});
</script>
@endpush
@endsection
