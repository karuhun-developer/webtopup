<?php

namespace App\Actions\Cms\Web\Faq;

use App\Models\Web\Faq;

class StoreFaqAction
{

    /**
     * Handle the action.
     */
    public function handle(array $data): Faq
    {
        $faq = Faq::create($data);

        return $faq;
    }
}
