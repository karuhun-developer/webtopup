<?php

namespace App\Exceptions;

use Illuminate\Contracts\Debug\ShouldntReport;
use RuntimeException;

class ProductCatalogPendingException extends RuntimeException implements ShouldntReport
{
    public function __construct()
    {
        parent::__construct('Katalog H2H masih dimuat. Tunggu sampai daftar produk lengkap sebelum import.');
    }
}
