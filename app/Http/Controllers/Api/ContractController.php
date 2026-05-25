<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentHistory;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ContractController extends Controller
{
    // 1. LẤY DANH SÁCH TẤT CẢ HỢP ĐỒNG (Kèm Ảnh)
    public function index()
    {
        $contracts = Contract::with('images')->orderBy('created_at', 'desc')->get();
        return response()->json($contracts);
    }

    // 1.5. LẤY DANH SÁCH HỢP ĐỒNG CỦA RIÊNG KHÁCH HÀNG (Kèm Ảnh)
    public function myContracts(Request $request)
    {
        $userId = $request->user()->id;
        $contracts = Contract::with('images')->where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        return response()->json($contracts);
    }

    /**
     * Helper: Upload file lên storage disk (NÉN ẢNH IPHONE GIẢM DUNG LƯỢNG)
     */
    private function uploadImage($file): string
    {
        // 1. Tạo tên file ngẫu nhiên nhưng chuẩn hóa (tránh lỗi ký tự tiếng Việt từ iPhone)
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $extension;

        // 2. Đường dẫn thư mục đích
        $destinationPath = public_path('uploads');

        // 3. (Tuyệt chiêu): Nếu là ảnh, mình sẽ thử dùng Intervention Image để nén (Nếu server bạn có cài)
        // Nhưng cách an toàn nhất cho mọi Server là cứ lưu trực tiếp trước, PHP sẽ tự lo phần còn lại nếu ta mở khóa dung lượng.
        $file->move($destinationPath, $filename);

        return '/uploads/' . $filename;
    }

    /**
     * Helper: Xóa file từ storage disk
     */
    private function deleteStorageImage(string          $imageUrl): void
    {
        // Vẫn giữ lại đoạn S3 này để phòng ngừa rủi ro (lỡ trong database của bạn đang có sẵn link s3 cũ thì không bị lỗi sập web khi bấm xóa)
        if (str_contains($imageUrl, 's3') || str_contains($imageUrl, 'amazonaws.com')) {
            $path = parse_url($imageUrl, PHP_URL_PATH);
            $path = ltrim($path, '/');
            $bucket = config('filesystems.disks.s3.bucket');
            if ($bucket && str_starts_with($path, $bucket)) {
                $path = substr($path, strlen($bucket) + 1);
            }
            Storage::disk('s3')->delete($path);
            return;
        }

        // Xử lý file local (Xóa ảnh chuẩn của Vietnix)
        if (str_starts_with($imageUrl, '/storage/')) {
            $path = str_replace('/storage/', '', $imageUrl);
            Storage::disk('public')->delete($path);
        } elseif (str_starts_with($imageUrl, '/uploads/')) {
            // Legacy: file cũ hoặc file vừa lưu trực tiếp trong public/uploads
            $filePath = public_path($imageUrl);
            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
        }
    }

    // 2. TẠO HỢP ĐỒNG MỚI (ĐÃ NÂNG CẤP LƯU NHIỀU ẢNH)
    public function store(Request $request)
    {
        try {
            $userId = null;

            if (!empty($request->customer_phone)) {
                $user = User::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    [
                        'name' => $request->customer_name ?? 'Khách hàng',
                        'password' => Hash::make('123456'),
                        'role' => 'customer'
                    ]
                );
                $userId = $user->id;
            }

            $contract = Contract::create([
                'user_id' => $userId,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_id_card' => $request->customer_id_card,

                // --- 4 TRƯỜNG MỚI BỔ SUNG ---
                'customer_id_date' => $request->customer_id_date,
                'customer_id_place' => $request->customer_id_place,
                'customer_address' => $request->customer_address,
                'asset_condition' => $request->asset_condition,
                // -----------------------------

                'asset_name' => $request->asset_name,
                'asset_description' => $request->asset_description,
                'appraised_value' => $request->appraised_value,
                'loan_amount' => $request->loan_amount,
                'interest_rate' => $request->interest_rate,
                'cycle_days' => $request->cycle_days,
                'loan_date' => $request->loan_date,
                'next_payment_date' => $request->next_payment_date,
                'paid_interest' => '0',
                'status' => 'Đang cầm',
                'status_color' => 'bg-green-100 text-green-700'
            ]);

            // ==========================================
            // XỬ LÝ LƯU NHIỀU ẢNH
            // ==========================================
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $index => $file) {
                    $imageUrl = $this->uploadImage($file);

                    \App\Models\ContractImage::create([
                        'contract_id' => $contract->id,
                        'image_url' => $imageUrl,
                        'is_main' => $index === 0 ? true : false
                    ]);

                    // Ảnh đầu tiên được chọn làm ảnh đại diện cho hợp đồng
                    if ($index === 0) {
                        $contract->image = $imageUrl;
                        $contract->save();
                    }
                }
            }

            // Lấy lại hợp đồng vừa tạo kèm theo danh sách ảnh để trả về cho Frontend
            $contractWithImages = Contract::with('images')->find($contract->id);
            // ==========================================

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo hợp đồng thành công!',
                'data' => $contractWithImages
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 3. CẬP NHẬT TRẠNG THÁI
    public function update(Request $request, $id)
    {
        $contract = Contract::find($id);
        if (!$contract) return response()->json(['status' => 'error', 'message' => 'Lỗi!'], 404);

        $contract->status = $request->status;
        $contract->status_color = $request->status_color;
        $contract->save();

        return response()->json(['status' => 'success', 'data' => $contract], 200);
    }

    // 4. CẬP NHẬT ẢNH VÀ GIÁ BÁN (UP NHIỀU ẢNH)
    public function updateForSale(Request $request, $id)
    {
        $contract = Contract::find($id);
        if (!$contract) return response()->json(['status' => 'error', 'message' => 'Lỗi!'], 404);

        $request->validate([
            'sale_price' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        if ($request->has('sale_price')) {
            $contract->sale_price = $request->sale_price;
            $contract->save();
        }

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $index => $file) {
                $imageUrl = $this->uploadImage($file);

                \App\Models\ContractImage::create([
                    'contract_id' => $contract->id,
                    'image_url' => $imageUrl,
                    'is_main' => $index === 0 ? true : false
                ]);

                if ($index === 0) {
                    $contract->image = $imageUrl;
                    $contract->save();
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Đã cập nhật!'], 200);
    }

    // 5. GIA HẠN / ĐÓNG LÃI
    public function renew(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);
        $newCycleDays = $request->input('new_cycle_days', $contract->cycle_days);
        $loanAmount = (float) preg_replace('/[^0-9]/', '', $contract->loan_amount);
        $rate = (float) $contract->interest_rate;
        $interestThisTime = ($loanAmount * ($rate / 100) / 30) * (int)$newCycleDays;

        $currentPaid = (float) preg_replace('/[^0-9]/', '', $contract->paid_interest);
        $contract->paid_interest = (string) ($currentPaid + $interestThisTime);

        $date = new \DateTime($contract->next_payment_date);
        $date->modify('+' . (int)$newCycleDays . ' days');
        $contract->next_payment_date = $date->format('Y-m-d');
        $contract->cycle_days = (int)$newCycleDays;

        // Lưu dữ liệu hợp đồng
        $contract->save();

        // ==========================================
        // BƯỚC 4: BÓP CÒ TẠO THÔNG BÁO CHO KHÁCH HÀNG
        // ==========================================
        if ($contract->user_id) {
            Notification::create([
                'user_id' => $contract->user_id,
                'title' => 'Thanh toán lãi thành công',
                'message' => "Chủ tiệm đã xác nhận đóng lãi cho tài sản {$contract->asset_name}. Ngày đến hạn mới của bạn là " . Carbon::parse($contract->next_payment_date)->format('d/m/Y') . " (Quỹ {$contract->cycle_days} ngày).",
                'is_read' => false,
            ]);
        }

        // ==========================================
        // BƯỚC 3: GHI LẠI VÀO SỔ LỊCH SỬ THANH TOÁN
        // ==========================================
        if ($contract->user_id) {
            PaymentHistory::create([
                'user_id' => $contract->user_id,
                'contract_id' => $contract->id,
                'asset_name' => $contract->asset_name,
                'amount' => $interestThisTime,
                'type' => 'Đóng lãi',
                'status' => 'Thành công'
            ]);
        }
        // ==========================================

        return response()->json(['status' => 'success', 'data' => $contract], 200);
    }

    // 6. THỐNG KÊ
    public function statistics()
    {
        $activeContracts = Contract::where('status', 'Đang cầm')->get();
        $totalActiveCount = $activeContracts->count();
        $totalLoanAmount = 0;
        foreach ($activeContracts as $contract) {
            $totalLoanAmount += (int)preg_replace('/[^0-9]/', '', $contract->loan_amount);
        }
        $liquidatedCount = Contract::where('status', 'Thanh lý')->count();
        $formattedTotal = number_format($totalLoanAmount, 0, ',', '.') . 'đ';

        return response()->json([
            'total_loan_amount' => $formattedTotal,
            'total_active' => $totalActiveCount,
            'total_liquidated' => $liquidatedCount,
        ], 200);
    }

    // 7. CHỐT BÁN
    public function closeSale(Request $request, $id)
    {
        $request->validate(['actual_sold_price' => 'required|string']);
        $contract = Contract::findOrFail($id);
        $contract->status = 'Đã bán thanh lý';
        $contract->actual_sold_price = $request->actual_sold_price;
        $contract->save();

        return response()->json(['status' => 'success', 'data' => $contract], 200);
    }

    // 8. XÓA ẢNH
    public function deleteImage($id)
    {
        $image = \App\Models\ContractImage::find($id);
        if (!$image) return response()->json(['status' => 'error'], 404);

        // Xóa file từ storage
        $this->deleteStorageImage($image->image_url);

        $image->delete();
        return response()->json(['status' => 'success'], 200);
    }
}
