0. Mô tả tổ chức dự án Laravel Reverb

	Tạo source:
		-> Tạo dự án laravel
		-> Set up cấu hình môi trường cho Reverb 
		-> Cài các thư viện npm phụ thuộc 
		-> Cài các thư viện php phụ thuộc
		-> Tạo Event phát tín hiệu reverb
		-> Tạo view chuẩn (có layout)
	Cấu hình:
		-> Setup view nhận tín hiệu reverb
	Thực thi:
		-> Chạy các dịch vụ và dự án
	
=== TẠO SOURCE ===
1. Đầu tiên khởi tạo dự án laravel
	- Tạo file .env
	- Copy từ .env.example vào .env để chương trình hoạt động
	- Trong .env
		+ Xoá dòng BROADCAST_CONNECTION (mình sẽ dùng reverb thay thế)
		+ Xoá dòng CACHE_STORE (mình sẽ dùng redis thay thế)
		+ ... REDIS_CLIENT ...tương tự
		+ ... REDIS_HOST ..tương tự
		+ ... REDIS_PORT ..tương tự
		+ ... REDIS_PASSWORD ..tương tự
		+ ... SESSION_DRIVER ..tương tự
		+ ... SESSION_LIFETIME ..tương tự
		+ ... SESSION_ENCRYPT ..tương tự
		+ ... SESSION_PATH ..tương tự
		+ ... SESSION_DOMAIN ..tương tự
		+ ... QUEUE_CONNECTION ..tương tự
		
	- Thêm cấu hình cho cache + reverb + laravel-echo ở cuối .env
		### NON DATABASE PROJECT ###
		QUEUE_CONNECTION=redis
		SESSION_DRIVER=redis
		SESSION_LIFETIME=120
		SESSION_ENCRYPT=false
		SESSION_PATH=/
		SESSION_DOMAIN=null
	
		### CACHE (REDIS) ###
		CACHE_STORE=redis
		CACHE_DRIVER=redis
		
		REDIS_CLIENT=predis
		REDIS_HOST=127.0.0.1
		REDIS_PORT=6379
		REDIS_PASSWORD=null

		### BROADCAST (REVERB) ###
		BROADCAST_CONNECTION=reverb

		### REVERB (BACKEND) ###
		REVERB_APP_ID=local
		REVERB_APP_KEY=local
		REVERB_APP_SECRET=local

		REVERB_HOST=127.0.0.1
		REVERB_PORT=8080
		REVERB_SCHEME=http

		### REVERB (FRONTEND) ###
		VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
		VITE_REVERB_HOST="${REVERB_HOST}"
		VITE_REVERB_PORT="${REVERB_PORT}"
		VITE_REVERB_SCHEME="${REVERB_SCHEME}"



3. Chạy npm cài pusher-js và laravel-echo cho frontend
	npm install pusher-js (lõi source của reverb, bắt buộc)
	npm install laravel-echo (lõi source cho hàm window.Echo. phải dùng trong <script type=module> view, bắt buộc)
	
4. Chạy lệnh cài đặt phụ thuộc
	- Chạy lệnh tạo key dự án laravel (bắt buộc)
		+ php artisan key:generate
	- Chạy composer cài phụ thuộc predis (REDIS cache) cho laravel
		+	php composer.phar require predis/predis
	
	- Chạy laravel cài phụ thuộc reverb cho laravel
		+	php artisan install:broadcasting
			-> chọn reverb (Laravel reverb) khi được hỏi
			-> cài thành công sẽ thông báo:
				INFO  Installing and building Node dependencies.
				INFO  Node dependencies installed successfully.  
			-> cài thất bại
				Xem lại .env đã cấu hình như bước 1 chưa
				Đã cài npm pusher-js và laravel-echo chưa

5. Tạo Event (một dạng job dispatching được custom riêng cho hệ Event - driven)
	php artisan make:event MessageSent
	-> implement class tới ShouldBroadcast (event chuẩn dùng cho việc cập nhật realtime Website GUI)
	Tạo biến cần thiết:
		private	string $message; // Bắt buộc set cứng string để thông nhất bảo trì (nếu không người ta truyền array, Object... khó debug
		private $chanel;
		private $event;
	Tạo hàm khởi tạo:
		public function _construct($message) {
			$this->message = $message;
			$this->chanel = 'tp-net-chanel';
			$this->event = 'tp-net-event';
		}
	Tạo kênh:
		public function broadcastOn() {
			return [ new Chanel($this->chanel)];
		}
	Tạo sự kiện cho kênh:
		public function broadcastAs() {
			return $this->event;
		}
	Tạo kết quả được mapping (không bắt buộc, mặc định return $this->message;) 
		Đây là nơi map lại dữ liệu trả cho view có window.Echo.listen(.tp-net-event, (event) => {...});
		public function broadcastWith() {
			return ["message" => $this->message];
		}

6. Tạo view.
	- Khởi tạo view chuẩn
		php artisan make:view layouts/app
		php artisan make:view dashboard/index 
	
	- Tạo một file đặt ở project root: .bladeformatterrc.json
		{
			"indentSize": 4,
			"wrapAttributes": "auto",
			"wrapAttributesMinAttrs": 2,
			"wrapLineLength": 120,
			"endWithNewLine": true,
			"noMultipleEmptyLines": false,
			"useTabs": false,
			"sortTailwindcssClasses": true,
			"sortHtmlAttributes": "none",
			"noPhpSyntaxCheck": false,
			"noSingleQuote": false,
			"noTrailingCommaPhp": false,
			"componentPrefix": ["x-", "livewire:"],
			"phpVersion": "8.4"
		}
		
		-> Mục đích để tránh bị các extension khác trong VScode làm vỡ cấu trúc file khó đọc, đây là prettier dành riêng cho bladeformatterrc
	
	- View layouts/app:
		<!DOCTYPE html>
		<html lang="vi">

		<head>
			{{-- ================= META ================= --}}
			<meta charset="UTF-8">
			<title>@yield('title')</title>

			{{-- ================= VIEWPORT ================= --}}
			<meta name="viewport" content="width=device-width, initial-scale=1">

			{{-- ================= VITE CSS ================= --}}
			@vite(['resources/css/app.css'])

			{{-- ================= STYLES ================= --}}
			@stack('styles')
		</head>

		<body>

			{{-- ================= CONTENT ================= --}}
			<main>
				@yield('content')
			</main>

			{{-- ================= VITE JS ================= --}}
			@vite(['resources/js/app.js'])

			{{-- ================= SCRIPTS ================= --}}
			@stack('scripts')
		</body>

		</html>

	
	- View dashboard/index:
		{{-- ================= LAYOUT ================= --}}
		@extends('layouts.app')

		{{-- ================= META ================= --}}
		@section('title', 'Trang chủ')

		{{-- ================= CONTENT ================= --}}
		@section('content')
			<h1>Khởi tạo cấu trúc cho dự án dùng Laravel Reverb</h1>
		@endsection

		{{-- ================= STYLES ================= --}}
		@push('styles')
		@endpush

		{{-- ================= SCRIPTS ================= --}}
		@push('scripts')
		@endpush

=== CẤU HÌNH ===
7. Nhúng vite Echo vào view
	Ở view dashboard/index:
		- Tại section content sửa như sau:
			@section("content")
				<h1>Khởi tạo cấu trúc cho dự án dùng Laravel Reverb</h1>
				<div id="messages">
					<p>[INFO] Nội dung nếu nhận thành công sẽ được render tại đây...</p>
				</div>
			@endsection
		
		- Tại push script sửa như sau:
			@push('scripts')
				<script type="module">
					window.Echo.channel('tp-net-chanel').listen('.tp-net-event', (event) => {
						console.log('[INFO] Đã nhận được tin nhắn:', event);
						document.getElementById('messages').innerHTML += `<p>[INFO]${e.message}</p>`;
					});
				</script>
			@endpush	
	
	Việc nhúng như trên:
		- window.Echo.
		
=== THỰC THI ===
8. Khởi động dịch vụ cần thiết cho dự án
	- Chạy tuần tự các dịch vụ sau trên từng proces shell khác nhau:
		+ Không bắt buộc: 
			-> Tải Another Redis Desktop Manager để quản lý REDIS hiệu quả hơn.
		+ Bắt buộc:
			-> Redis: Tải và chạy redis-server.exe
			-> Webserver: php artisan serve
			-> Vite bundle (đóng gói resource vite để truy cập an toàn bằng source frontend bằng @vite[resource/...]): npm install
			-> Vite: npm run dev
			-> Reverb service: php artisan reverb:start
			-> Worker (bắt buộc cho bất kì job/event): php artisan queue:work

9. Tạo API test thử dự án reverb
	- Tại routes/web.php thêm:
		use Illuminate\Support\Facades\Route;
		use App\Events\MessageSent;

		Route::prefix('api')->group(function () {
			Route::get('reverb-test', function () {
				$data = [
					'meta' => [
						'code' => 200,
						'message' => 'Success',
					],
					'data' => [
						"api" => '/api/reverb-test',
					],
				];

				# Đưa array/object... về string json
				$message = json_encode($data);

				# Gọi Event MessageSent cho queue:worker xử lí
				event(new MessageSent($message));

				return response()->json("queued");
			})->name('reverb-test');
		});

		Route::get('dashboard', function () {
			return view('dashboard/index');
		})->name('dashboard');

10. Kiểm tra
	- Truy cập bằng web/postman... vào GET 127.0.0.1:8000/api/reverb-test (hoặc host, port custom) để kiểm tra dự án reverb đã hoạt động chưa
