<?php

namespace App\Actions\Cms\Web\Faq;

use App\Models\Web\Faq;

class UpdateFaqAction
{
    /**
     * Handle the action.
     */
    public function handle(Faq $faq, array $data): bool
    {
        return $faq->update($data);
    }
}
