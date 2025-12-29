{{-- ================= LAYOUT ================= --}}
@extends('layouts.app')

{{-- ================= META ================= --}}
@section('title', 'Trang chủ')

{{-- ================= CONTENT ================= --}}
@section('content')
    <h1>Khởi tạo cấu trúc cho dự án dùng Laravel Reverb</h1>
    <div id="messages">
        <p>[INFO] Nội dung nếu nhận thành công sẽ được render tại đây...</p>
    </div>
@endsection

{{-- ================= STYLES ================= --}}
@push('styles')
@endpush

{{-- ================= SCRIPTS ================= --}}
@push('scripts')
    <script type="module">
        window.Echo.channel('reverb-websocket-chanel').listen('.reverb-websocket-event', (event) => {
            console.log('[INFO] Đã nhận được tin nhắn:', event);
            document.getElementById('messages').innerHTML += `<p>[INFO]${event.message}</p>`;
        });
    </script>
@endpush
