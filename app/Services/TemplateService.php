<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\WhatsAppTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TemplateService
{
    public function getTemplates(): LengthAwarePaginator
    {
        $query = WhatsAppTemplate::query();

        $search = request()->input('search');
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%");
        }

        $status = request()->input('status');
        if ($status !== null) {
            $query->where('is_active', $status);
        }

        return $query->latest()->paginate(config('settings.default_pagination', 10));
    }
}