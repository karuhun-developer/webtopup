<x-mail::message>
🎉 Pesanan Berhasil Diproses!
<br />
Halo **{{ $order->name }}**, pesanan Anda telah berhasil diproses di {{ config('app.name') }}. Terima kasih atas kepercayaan Anda!
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

Terima kasih telah memilih {{ config('app.name') }} untuk kebutuhan top up Anda. Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi tim dukungan kami.
</x-mail::message>
