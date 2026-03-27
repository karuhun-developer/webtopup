<x-mail::message>
👥 Permintaan Konfirmasi Teman
<br />
Halo **{{ $order->name }}**, kami telah mengirimkan permintaan pertemanan untuk pesanan hadiah Anda dengan ID **{{ $order->reference }}**. Mohon segera konfirmasi permintaan pertemanan tersebut di dalam game Anda.
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

Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi tim dukungan kami.
</x-mail::message>
