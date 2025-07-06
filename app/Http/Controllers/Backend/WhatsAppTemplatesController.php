<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppTemplate;
use App\Services\TemplateService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhatsAppTemplatesController extends Controller
{
    public function __construct(
        private readonly TemplateService $templateService
    ) {
    }
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['templates.view']);

        $templates = $this->templateService->getTemplates(); // Call paginated method

        return view('backend.pages.whatsapp_templates.index', [
            'templates' => $templates,
        ]);
    }


    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['templates.create']);

        return view('backend.pages.whatsapp_templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['templates.create']);

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $template = new WhatsAppTemplate();
        $template->title = $request->title;
        $template->message = $request->message;
        $template->is_active = $request->boolean('is_active', true);

        $template->save();

        session()->flash('success', 'Template has been created.');

        return redirect()->route('admin.whatsapp-templates.index');
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['templates.edit']);

        $template = WhatsAppTemplate::findOrFail($id);

        return view('backend.pages.whatsapp_templates.edit', [
            'template' => $template
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['templates.edit']);

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $template = WhatsAppTemplate::findOrFail($id);
        $template->title = $request->title;
        $template->message = $request->message;
        $template->is_active = $request->boolean('is_active', true);
        $template->save();

        session()->flash('success', 'Template has been updated.');

        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['templates.delete']);

        $template = WhatsAppTemplate::findOrFail($id);
        $template->delete();

        session()->flash('success', 'Template has been deleted.');

        return back();
    }
}
