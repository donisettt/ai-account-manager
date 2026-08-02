<?php

namespace App\Services\Tool;

use App\Models\Tool;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ToolService
{
    public function getAllTools()
    {
        return Tool::latest()->paginate(10);
    }

    public function getToolById(int $id): ?Tool
    {
        return Tool::find($id);
    }

    public function createTool(array $data): Tool
    {
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        return Tool::create($data);
    }

    public function updateTool(Tool $tool, array $data): bool
    {
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            // Hapus logo lama jika ada
            if ($tool->logo) {
                $this->deleteLogo($tool->logo);
            }
            
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        return $tool->update($data);
    }

    public function deleteTool(Tool $tool): bool
    {
        // Hapus logo jika ada
        if ($tool->logo) {
            $this->deleteLogo($tool->logo);
        }

        return $tool->delete();
    }

    public function toggleStatus(Tool $tool): bool
    {
        return $tool->update(['status_aktif' => !$tool->status_aktif]);
    }

    private function uploadLogo(UploadedFile $file): string
    {
        return $file->store('tools/logos', 'public');
    }

    private function deleteLogo(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
