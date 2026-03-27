<x-mail::message>
⚠️ Pesanan Gagal Diproses
<br />
Halo **{{ $order->name }}**, sayangnya pesanan Anda dengan ID **{{ $order->reference }}** gagal diproses. Silakan hubungi tim dukungan kami untuk bantuan lebih lanjut.
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

Mohon maaf atas ketidaknyamanan ini. Jika Anda membutuhkan bantuan, jangan ragu untuk menghubungi tim dukungan kami.
</x-mail::message>
