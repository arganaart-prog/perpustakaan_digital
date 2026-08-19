<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengingat Pengambilan Buku</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 24px; color: #1e293b;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="text-align: center; margin-bottom: 24px;">
            <h1 style="color: #059669; font-size: 20px; margin: 0;">Perpustakaan Digital Skarifta</h1>
            <p style="color: #64748b; font-size: 12px; margin-top: 4px;">Pemberitahuan Pengambilan Buku Booking</p>
        </div>

        <p style="font-size: 14px; line-height: 1.6;">
            Halo <strong>{{ $bookQueue->user->name }}</strong>,
        </p>

        <p style="font-size: 14px; line-height: 1.6;">
            Buku yang kamu antre/booking yaitu <strong>"{{ $bookQueue->book->title }}"</strong> telah tersedia dan siap diambil sejak 24 jam yang lalu di meja sirkulasi perpustakaan.
        </p>

        <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; margin: 20px 0;">
            <p style="margin: 0; color: #991b1b; font-size: 13px; font-weight: bold;">
                ⚠️ PENTING: Batas Waktu Pengambilan Tersisa 24 Jam Lagi!
            </p>
            <p style="margin: 6px 0 0 0; color: #b91c1c; font-size: 12px;">
                Batas akhir pengambilan: <strong>{{ optional($bookQueue->deadline)->format('d M Y, H:i') ?? '-' }}</strong>.
                Jika tidak diambil sebelum batas waktu, antreanmu akan otomatis hangus dan buku akan diberikan kepada siswa di antrean berikutnya.
            </p>
        </div>

        <div style="margin-top: 24px; text-align: center;">
            <a href="{{ url('/member/loans') }}" style="display: inline-block; background-color: #059669; color: #ffffff; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: bold;">
                Lihat Status Antrean Buku
            </a>
        </div>

        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin-top: 32px; border-top: 1px dashed #e2e8f0; padding-top: 16px;">
            Email ini dikirim otomatis oleh Sistem Perpustakaan Skarifta.
        </p>
    </div>
</body>
</html>
