# NLC Online

## Sitemap

- `/`: Login di sini. Kalo udah login langsung redirect ke `/sesi`
- `/administrasi`: Halaman administrasi NLC Online. Buat nambahin soal/session. Cuma bisa diakses akun tipe `Employee`
- `/sesi`: Daftar sesi yang bisa diikuti. Diakses dari sisi peserta (tipe akun `Registered`). Contoh sesi: warmup, penyisihan.
- `/sesi/{nama_sesi}`: Ini ntar isinya soal-soal dari sesi terkait. Misal kalo di `/sesi/warmup` berarti peserta jawab soal2 yang ada di sesi ini