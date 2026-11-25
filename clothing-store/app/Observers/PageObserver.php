<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Page;
use App\Services\HtmlSanitizer;

class PageObserver
{
    protected HtmlSanitizer $sanitizer;

    public function __construct(HtmlSanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function saving(Page $page): void
    {
        if ($page->isDirty('content')) {
            $page->content = $this->sanitizer->cleanPageContent($page->content);
        }

        if ($page->isDirty('meta_description')) {
            $page->meta_description = $this->sanitizer->clean($page->meta_description);
        }
    }
}
