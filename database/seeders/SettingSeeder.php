<?php

namespace Database\Seeders;

use App\Models\Setting\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Setting::first()) {
            Setting::create([
                'value' => [
                    'title' => 'TopupWok',
                    'email' => 'support@topupwok.id',
                    'phone' => '+62 812-3456-7890',
                    'privacy_policy' => '<p>GGWP</p>',
                    'terms' => '<p>GGWP</p>',
                    'footer_description' => 'Platform top up game terpercaya dengan harga terjangkau dan proses cepat. Layanan 24/7 untuk kepuasan pelanggan.',
                    'cs' => 'https://wa.me/6281234567890',
                    'template_checkout' => '🎉 Terima kasih telah melakukan pembelian di {app_name}!
🎮 Berikut adalah detail pesanan Anda:
- 🆔 ID Pesanan: *{order_id}*
- 🛒 Produk: *{product}*
- 🧾 Total: *{total}*

Silakan lakukan pembayaran sesuai dengan metode yang Anda pilih di link berikut:
{link}

Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami di {cs_link} Kami siap membantu Anda!',
                    'template_payment_confirmation' => '📢 Konfirmasi Pembayaran Diterima!
Halo *{customer_name}*, kami telah menerima konfirmasi pembayaran Anda untuk pesanan *{order_id}*. Terima kasih atas pembelian Anda di {app_name}!
Detail pesanan anda bisa dilihat di link berikut:
{link}

Pesanan anda akan kami segera proses. Jika ada pertanyaan, silakan hubungi kami di {cs_link}.',
                    'template_payment_rejected' => '⚠️ Konfirmasi Pembayaran Ditolak
Halo *{customer_name}*, kami menyesal memberitahukan bahwa konfirmasi pembayaran Anda untuk pesanan *{order_id}* telah ditolak.
Silakan periksa kembali detail pembayaran Anda dan coba lagi.
Detail pesanan anda bisa dilihat di link berikut:
{link}

Jika Anda memerlukan bantuan, jangan ragu untuk menghubungi kami di {cs_link}. Kami siap membantu Anda!',
                    'template_order_completed' => '✅ Pesanan Berhasil diproses!
Halo *{customer_name}*, pesanan Anda dengan ID *{order_id}* telah berhasil diproses. Terima kasih telah berbelanja di {app_name}!
Detail pesanan anda bisa dilihat di link berikut:
{link}

Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi kami di {cs_link}. Kami siap membantu Anda!',
                    'template_order_failed' => '❌ Pesanan Gagal Diproses
Halo *{customer_name}*, kami menyesal memberitahukan bahwa pesanan Anda dengan ID *{order_id}* gagal diproses.
Detail pesanan anda bisa dilihat di link berikut:
{link}

Silakan coba melakukan pembelian kembali. Jika Anda memerlukan bantuan, jangan ragu untuk menghubungi kami di {cs_link}.',
                    'template_gift_order_admin_friend_request' => '🎁 Permintaan Penambahan Teman untuk Gift Order *order_id*
Halo *{customer_name}*, untuk memproses gift order Anda, kami telah melakukan penambahan teman pada akun penerima hadiah.
Silakan konfirmasi penambahan teman tersebut dengan mengirimkan bukti screenshot accept friend request kepada kami di {cs_link}.
Terima kasih atas kerjasamanya!',
                    'template_gift_order_user_friend_confirmation' => '✅ Konfirmasi Penambahan Teman untuk Gift Order *{order_id}*
Halo *{customer_name}*, terima kasih telah mengonfirmasi penambahan teman pada gift order Anda.
Kami akan segera memproses pengiriman hadiah Anda, Silahkan tunggu 7 hari kerja untuk pengiriman hadiah Anda.
Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi kami di {cs_link}. Kami siap membantu Anda!',
                    'template_gift_order_completion' => '🎉 Gift Order Selesai!
Halo *{customer_name}*, hadiah untuk gift order Anda dengan ID *{order_id}* telah berhasil dikirimkan kepada penerima.
Terima kasih telah mempercayakan pembelian hadiah Anda kepada {app_name}!
Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi kami di {cs_link}. Kami siap membantu Anda!',
                    'manual_transfer_bank' => 'Bank BCA',
                    'manual_transfer_bank_logo' => '/public/images/MANDIRI.svg',
                    'manual_transfer_account_name' => 'PT TopupWok Indonesia',
                    'manual_transfer_account_number' => '1234567890',
                    'providers' => [
                        'digiflazz' => true,
                        'gift' => true,
                    ],
                ],
            ]);
        }
    }
}
