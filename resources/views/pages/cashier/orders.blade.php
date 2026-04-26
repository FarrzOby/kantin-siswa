@extends('layouts.app')
@section('title', 'Antrian Pesanan')
@section('page-title', 'Antrian Pesanan')

@section('content')
<div x-data="cashierOrders()" x-init="init()">

    {{-- Stats bar --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="ks-card text-center">
            <p class="text-2xl font-display text-yellow-400 font-bold">{{ $orders->where('status','pending')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu</p>
        </div>
        <div class="ks-card text-center">
            <p class="text-2xl font-display text-blue-400 font-bold">{{ $orders->where('status','processing')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Diproses</p>
        </div>
        <div class="ks-card text-center">
            <p class="text-2xl font-display text-green-400 font-bold">{{ $orders->where('status','ready')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Siap Diambil</p>
        </div>
    </div>

    {{-- Columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @foreach(['pending' => ['Menunggu','yellow'], 'processing' => ['Diproses','blue'], 'ready' => ['Siap Diambil','green']] as $status => [$label, $color])
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2.5 h-2.5 rounded-full bg-{{ $color }}-400"></div>
                <h3 class="font-display text-{{ $color }}-400 text-lg">{{ $label }}</h3>
            </div>

            <div class="space-y-3" id="col-{{ $status }}">
                @foreach($orders->where('status', $status) as $order)
                <div class="ks-card hover:border-{{ $color }}-400/30 transition-all" id="order-card-{{ $order->id }}">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="font-mono text-xs text-gray-500">{{ $order->order_number }}</p>
                            <p class="font-semibold text-gray-200 text-sm">{{ $order->user->name }}</p>
                            @if($order->user->kelas)
                            <p class="text-xs text-gray-500">{{ $order->user->kelas }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-gold font-semibold text-sm">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                            <p class="text-xs {{ $order->payment_status === 'paid' ? 'text-green-400' : 'text-yellow-400' }}">
                                {{ $order->payment_status === 'paid' ? '✓ Lunas' : '⏳ Belum bayar' }}
                            </p>
                            <p class="text-xs text-gray-600">{{ $order->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}</p>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="text-xs text-gray-400 space-y-0.5 mb-3">
                        @foreach($order->items as $item)
                        <div>{{ $item->quantity }}× {{ $item->item_name }}</div>
                        @endforeach
                    </div>

                    @if($order->notes)
                    <div class="text-xs text-gold/70 italic mb-3">📝 {{ $order->notes }}</div>
                    @endif

                    <div class="text-xs text-gray-600 mb-3">{{ $order->created_at->diffForHumans() }}</div>

                    {{-- Actions --}}
                    <div class="flex gap-2 flex-wrap">
                        @if($order->payment_status === 'unpaid')
                            @if($order->payment_method === 'qris')
                            <a href="{{ route('cashier.qris', $order) }}"
                               class="btn-gold text-xs px-3 py-1.5 flex items-center gap-1">
                                📱 Scan QRIS
                            </a>
                            @else
                            <button onclick="openPayModal({{ $order->id }}, {{ $order->total }})"
                                    class="btn-gold text-xs px-3 py-1.5">
                                💵 Bayar Tunai
                            </button>
                            @endif
                        @endif

                        @if($status === 'pending')
                        <button onclick="updateStatus({{ $order->id }}, 'processing')"
                                class="btn-outline text-xs px-3 py-1.5">Proses →</button>
                        @elseif($status === 'processing')
                        <button onclick="updateStatus({{ $order->id }}, 'ready')"
                                class="btn-outline text-xs px-3 py-1.5 border-green-400/40 text-green-400">Siap ✓</button>
                        @elseif($status === 'ready')
                        <button onclick="updateStatus({{ $order->id }}, 'completed')"
                                class="btn-gold text-xs px-3 py-1.5">Selesai ✓</button>
                        @endif

                        <a href="{{ route('orders.receipt', $order) }}" target="_blank"
                           class="btn-outline text-xs px-3 py-1.5">🖨️</a>

                        <button onclick="updateStatus({{ $order->id }}, 'cancelled')"
                                class="btn-outline text-xs px-3 py-1.5 border-red-400/30 text-red-400">✕</button>
                    </div>
                </div>
                @endforeach

                @if($orders->where('status', $status)->isEmpty())
                <div class="ks-card text-center py-8 border-dashed">
                    <p class="text-gray-600 text-sm">Tidak ada pesanan</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Cash payment modal --}}
<div id="pay-modal" class="fixed inset-0 bg-black/70 z-50 hidden flex items-center justify-center" x-data="payModal()">
    <div class="ks-card w-full max-w-sm mx-4" @click.stop>
        <h3 class="font-display text-gold text-xl mb-4">Pembayaran Tunai</h3>
        <p class="text-gray-400 text-sm mb-1">Total yang harus dibayar:</p>
        <p id="modal-total" class="text-gold font-display text-2xl font-bold mb-4"></p>

        <div>
            <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Uang Diterima</label>
            <input type="number" id="amount-paid" class="ks-input text-xl font-bold" placeholder="0" oninput="calcChange()">
        </div>

        <div id="change-display" class="hidden mt-3 p-3 bg-green-900/30 border border-green-700/40 rounded-lg">
            <p class="text-xs text-gray-400">Kembalian:</p>
            <p id="change-amount" class="text-green-400 font-bold text-xl"></p>
        </div>

        <div class="flex gap-3 mt-4">
            <button onclick="submitPayment()" class="btn-gold flex-1">Konfirmasi Bayar</button>
            <button onclick="closePayModal()" class="btn-outline flex-1">Batal</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentOrderId = null;
let currentTotal   = 0;

function cashierOrders() { return {}; }
function payModal()       { return {}; }

function openPayModal(orderId, total) {
    currentOrderId = orderId;
    currentTotal   = total;
    document.getElementById('modal-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('amount-paid').value = '';
    document.getElementById('change-display').classList.add('hidden');
    document.getElementById('pay-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('amount-paid').focus(), 100);
}

function closePayModal() {
    document.getElementById('pay-modal').classList.add('hidden');
}

function calcChange() {
    const paid   = parseFloat(document.getElementById('amount-paid').value) || 0;
    const change = paid - currentTotal;
    const el     = document.getElementById('change-display');
    if (paid >= currentTotal) {
        el.classList.remove('hidden');
        document.getElementById('change-amount').textContent = 'Rp ' + change.toLocaleString('id-ID');
    } else {
        el.classList.add('hidden');
    }
}

async function submitPayment() {
    const paid = parseFloat(document.getElementById('amount-paid').value);
    if (paid < currentTotal) { showToast('Uang tidak cukup!', 'error'); return; }

    try {
        const res = await apiFetch(`/cashier/orders/${currentOrderId}/pay`, {
            method: 'POST',
            body: JSON.stringify({ amount_paid: paid })
        });
        const data = await res.json();
        if (data.success) {
            showToast('Pembayaran berhasil! Kembalian: ' + data.change);
            closePayModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Gagal', 'error');
        }
    } catch(e) { showToast('Terjadi kesalahan', 'error'); }
}

async function updateStatus(orderId, newStatus) {
    try {
        const res = await apiFetch(`/cashier/orders/${orderId}/status`, {
            method: 'PATCH',
            body: JSON.stringify({ status: newStatus })
        });
        const data = await res.json();
        if (data.success) {
            showToast('Status diperbarui: ' + data.status);
            setTimeout(() => location.reload(), 800);
        }
    } catch(e) { showToast('Gagal update status', 'error'); }
}

// Auto-refresh every 30 seconds
function cashierOrders() {
    return {
        init() {
            setInterval(() => location.reload(), 30000);
        }
    };
}
</script>
@endpush
