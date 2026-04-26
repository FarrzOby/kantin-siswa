@extends('layouts.app')
@section('title', 'Scan QRIS')
@section('page-title', 'Scan QRIS')

@section('content')
<div class="max-w-2xl mx-auto" x-data="qrisScanner()">

    {{-- Order summary --}}
    <div class="ks-card mb-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Pesanan</p>
                <p class="font-mono font-bold text-gold">{{ $order->order_number }}</p>
                <p class="text-sm text-gray-300 mt-0.5">{{ $order->user->name }}
                    @if($order->user->kelas) · {{ $order->user->kelas }} @endif
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">Total</p>
                <p class="font-display text-gold text-2xl font-bold">{{ $order->formatted_total }}</p>
            </div>
        </div>
        <div class="mt-3 text-xs text-gray-400">
            @foreach($order->items as $item)
            <span>{{ $item->quantity }}× {{ $item->item_name }}</span>@if(!$loop->last), @endif
            @endforeach
        </div>
    </div>

    {{-- Scanner --}}
    <div class="ks-card">
        <h3 class="font-display text-gold text-xl mb-4 text-center">Scan QR Code QRIS Pelanggan</h3>

        {{-- Camera view --}}
        <div id="scanner-container" class="relative rounded-xl overflow-hidden bg-ks-muted" style="aspect-ratio: 1;">
            <video id="qr-video" class="w-full h-full object-cover" playsinline></video>
            <canvas id="qr-canvas" class="hidden"></canvas>

            {{-- Scan overlay --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="relative w-56 h-56">
                    {{-- Corner brackets --}}
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-gold rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-gold rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-gold rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-gold rounded-br-lg"></div>
                    {{-- Scan line --}}
                    <div id="scan-line" class="absolute left-0 right-0 h-0.5 bg-gold/70"
                         style="animation: scanLine 2s ease-in-out infinite; box-shadow: 0 0 6px rgba(200,169,110,0.8);"></div>
                </div>
            </div>

            {{-- Status overlay --}}
            <div id="scan-status" class="absolute inset-0 hidden items-center justify-center bg-black/80 rounded-xl">
                <div class="text-center">
                    <div id="status-icon" class="text-6xl mb-3"></div>
                    <p id="status-text" class="text-white font-semibold text-lg"></p>
                </div>
            </div>
        </div>

        {{-- Camera controls --}}
        <div class="flex gap-3 mt-4">
            <button id="start-btn" onclick="startCamera()" class="btn-gold flex-1 flex items-center justify-center gap-2">
                📷 Mulai Kamera
            </button>
            <button id="stop-btn" onclick="stopCamera()" class="btn-outline flex-1 hidden">
                ⏹ Stop
            </button>
        </div>

        {{-- Manual input fallback --}}
        <div class="mt-5 border-t border-ks-border pt-4">
            <p class="text-xs text-gray-500 mb-2 text-center">Atau masukkan kode QR secara manual:</p>
            <div class="flex gap-2">
                <input type="text" id="manual-qr" class="ks-input" placeholder="Paste data QR di sini...">
                <button onclick="submitManualQR()" class="btn-gold px-4 flex-shrink-0">Verifikasi</button>
            </div>
        </div>
    </div>

    {{-- Success / Back --}}
    <div class="mt-4 flex gap-3">
        <a href="{{ route('cashier.orders') }}" class="btn-outline">← Kembali</a>
        <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="btn-outline">🖨️ Cetak Struk</a>
    </div>
</div>

<style>
@keyframes scanLine {
    0%, 100% { top: 10%; }
    50%       { top: 90%; }
}
</style>
@endsection

@push('scripts')
{{-- jsQR library --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
let stream       = null;
let scanInterval = null;
let scanning     = true;
const orderId    = {{ $order->id }};
const verifyUrl  = '{{ route('cashier.qris.verify', $order) }}';
const receiptUrl = '{{ route('orders.receipt', $order) }}';

async function startCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 720 } }
        });
        const video = document.getElementById('qr-video');
        video.srcObject = stream;
        video.play();

        document.getElementById('start-btn').classList.add('hidden');
        document.getElementById('stop-btn').classList.remove('hidden');

        scanning = true;
        scanInterval = setInterval(scanFrame, 300);
    } catch (e) {
        showToast('Kamera tidak dapat diakses: ' + e.message, 'error');
    }
}

function stopCamera() {
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    if (scanInterval) { clearInterval(scanInterval); scanInterval = null; }
    document.getElementById('start-btn').classList.remove('hidden');
    document.getElementById('stop-btn').classList.add('hidden');
}

function scanFrame() {
    if (!scanning) return;
    const video  = document.getElementById('qr-video');
    const canvas = document.getElementById('qr-canvas');
    if (video.readyState !== video.HAVE_ENOUGH_DATA) return;

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code      = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });

    if (code) {
        scanning = false;
        clearInterval(scanInterval);
        submitQR(code.data);
    }
}

async function submitQR(qrData) {
    showScanStatus('⏳', 'Memverifikasi...');
    try {
        const res = await apiFetch(verifyUrl, {
            method: 'POST',
            body: JSON.stringify({ qr_data: qrData })
        });
        const data = await res.json();
        if (data.success) {
            showScanStatus('✅', 'Pembayaran Berhasil!');
            stopCamera();
            setTimeout(() => { window.location.href = data.receipt_url; }, 1500);
        } else {
            showScanStatus('❌', data.error || 'QR tidak valid');
            setTimeout(() => { hideScanStatus(); scanning = true; scanInterval = setInterval(scanFrame, 300); }, 2000);
        }
    } catch (e) {
        showScanStatus('❌', 'Terjadi kesalahan');
        setTimeout(() => { hideScanStatus(); scanning = true; }, 2000);
    }
}

async function submitManualQR() {
    const val = document.getElementById('manual-qr').value.trim();
    if (!val) { showToast('Masukkan data QR terlebih dahulu', 'error'); return; }
    stopCamera();
    await submitQR(val);
}

function showScanStatus(icon, text) {
    const el = document.getElementById('scan-status');
    document.getElementById('status-icon').textContent = icon;
    document.getElementById('status-text').textContent = text;
    el.classList.remove('hidden');
    el.classList.add('flex');
}

function hideScanStatus() {
    const el = document.getElementById('scan-status');
    el.classList.add('hidden');
    el.classList.remove('flex');
}

function qrisScanner() { return {}; }
</script>
@endpush
