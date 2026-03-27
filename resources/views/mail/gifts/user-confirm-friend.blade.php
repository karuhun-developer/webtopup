<x-mail::message>
✅ Konfirmasi Teman Berhasil!
<br />
Halo **{{ $order->name }}**, kami telah menerima konfirmasi pertemanan Anda untuk pesanan hadiah dengan ID **{{ $order->reference }}**. Hadiah akan segera dikirimkan ke akun game Anda.
<br />
🎮 Berikut adalah detail pesanan Anda:
- 🆔 ID Pesanan: **{{ $order->reference }}**
- 🛒 Produk: **{{ $order->product->name }}**
- 🧾 Total: **{{ numberToCurrency($order->total_amount) }}**
<br />
<x-mail::button :url="route('transaction.show', [
    'order' => $order->reference,
])">
Detail Pesanan
</x-mail::button>

Terima kasih telah menggunakan layanan {{ config('app.name') }}. Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi tim dukungan kami.
</x-mail::message>
