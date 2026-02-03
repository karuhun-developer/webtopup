<?php

namespace App\Actions\Cms\Web\Faq;

use App\Models\Web\Faq;

class DeleteFaqAction
{
    /**
     * Handle the action.
     */
    public function handle(Faq $faq): ?bool
    {
        return $faq->delete();
    }
}
