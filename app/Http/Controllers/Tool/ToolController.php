<?php

namespace App\Http\Controllers\Tool;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tool\StoreToolRequest;
use App\Http\Requests\Tool\UpdateToolRequest;
use App\Models\Tool;
use App\Services\Tool\ToolService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ToolController extends Controller
{
    public function __construct(
        private ToolService $toolService
    ) {}

    public function index(): View
    {
        $tools = $this->toolService->getAllTools();
        
        return view('tools.index', compact('tools'));
    }

    public function create(): View
    {
        return view('tools.create');
    }

    public function store(StoreToolRequest $request): RedirectResponse
    {
        $this->toolService->createTool($request->validated());

        return redirect()
            ->route('tools.index')
            ->with('success', 'Tool berhasil ditambahkan');
    }

    public function edit(Tool $tool): View
    {
        return view('tools.edit', compact('tool'));
    }

    public function update(UpdateToolRequest $request, Tool $tool): RedirectResponse
    {
        $this->toolService->updateTool($tool, $request->validated());

        return redirect()
            ->route('tools.index')
            ->with('success', 'Tool berhasil diupdate');
    }

    public function destroy(Tool $tool): RedirectResponse
    {
        $this->toolService->deleteTool($tool);

        return redirect()
            ->route('tools.index')
            ->with('success', 'Tool berhasil dihapus');
    }

    public function toggleStatus(Tool $tool): RedirectResponse
    {
        $this->toolService->toggleStatus($tool);

        return redirect()
            ->route('tools.index')
            ->with('success', 'Status tool berhasil diubah');
    }
}
