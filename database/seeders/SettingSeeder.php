<?php

namespace Database\Seeders;

use App\Models\Setting\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Setting::first()) {
            Setting::create([
                'value' => [
                    'title' => 'TopupWok',
                    'email' => 'support@topupwok.id',
                    'phone' => '+62 812-3456-7890',
                    'faq' => '<p>GGWP</p>',
                    'terms' => '<p>GGWP</p>',
                    'footer_description' => 'Platform top up game terpercaya dengan harga terjangkau dan proses cepat. Layanan 24/7 untuk kepuasan pelanggan.',
                    'cs' => 'https://wa.me/6281234567890',
                    'template_checkout' => '🎉 Terima kasih telah melakukan pembelian di {app_name}!
🎮 Berikut adalah detail pesanan Anda:
- 🛒 Produk: {product}
- 💰 Harga: {price}
- 🔢 Jumlah: {quantity}
- 🧾 Total: {total}

Silakan lakukan pembayaran sesuai dengan metode yang Anda pilih di link berikut:
{link}

Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami di {cs_link}

🤝Terima kasih atas kepercayaan Anda kepada TopupWok! 🙏',
                ]
            ]);
        }
    }
}
