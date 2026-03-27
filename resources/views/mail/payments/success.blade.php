<x-mail::message>
🎉 Konfirmasi Pembayaran Diterima!
<br />
Halo **{{ $order->name }}**, kami telah menerima konfirmasi pembayaran Anda untuk pesanan **{{ $order->reference }}**. Terima kasih atas pembelian Anda di {{ config('app.name') }}!
<br />
Detail pesanan anda bisa dilihat di link berikut:
<br />
<x-mail::button :url="route('transaction.show', [
    'order' => $order->reference,
])">
Detail Pesanan
</x-mail::button>

Terima kasih telah memilih {{ config('app.name') }} untuk kebutuhan top up Anda. Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi tim dukungan kami.
</x-mail::message>
