<x-mail::message>
🎉 Konfirmasi Pembayaran Ditolak
<br />
Halo **{{ $order->name }}**, kami menyesal memberitahukan bahwa konfirmasi pembayaran Anda untuk pesanan **{{ $order->reference }}** telah ditolak.
<br />
Silakan periksa kembali detail pembayaran Anda dan coba lagi, detail pesanan anda bisa dilihat di link berikut:
<br />
<x-mail::button :url="route('transaction.show', [
    'order' => $order->reference,
])">
Detail Pesanan
</x-mail::button>
Terima kasih telah memilih {{ config('app.name') }} untuk kebutuhan top up Anda. Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi tim dukungan kami.
</x-mail::message>
