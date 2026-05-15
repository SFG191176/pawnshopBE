<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Contract; // Quan trọng: Phải import Model Contract
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    // API Lấy danh sách thông báo (Kết hợp Tự động làm mới dữ liệu)
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        // --- BƯỚC 1: QUÉT VÀ CẬP NHẬT DỮ LIỆU MỚI NHẤT ---
        // Chỉ quét các hợp đồng đang trong trạng thái cầm của user này
        $contracts = Contract::where('user_id', $user->id)
                             ->where('status', 'Đang cầm')
                             ->get();

        foreach ($contracts as $contract) {
            $dueDate = Carbon::parse($contract->next_payment_date)->startOfDay();
            $diffDays = $today->diffInDays($dueDate, false);

            $title = '';
            $message = '';
            $type = '';

            // Logic phân loại thông báo
            if ($diffDays >= 0 && $diffDays <= 3) {
                $title = "Nhắc đóng lãi: {$contract->asset_name}";
                $type = 'reminder';
                $message = $diffDays == 0
                    ? "Hôm nay là hạn đóng lãi của [{$contract->asset_name}]. Vui lòng thanh toán."
                    : "Hợp đồng [{$contract->asset_name}] còn {$diffDays} ngày nữa là đến hạn đóng lãi.";
            } elseif ($diffDays < 0) {
                $title = "CẢNH BÁO QUÁ HẠN: {$contract->asset_name}";
                $type = 'warning';
                $message = "Hợp đồng [{$contract->asset_name}] đã quá hạn " . abs($diffDays) . " ngày. Vui lòng thanh toán gấp!";
            }

            // Nếu có nội dung cần thông báo, tiến hành lưu/cập nhật Database
            if ($title !== '') {
                // Kiểm tra xem đã có thông báo CHƯA ĐỌC cho hợp đồng này chưa
                $existingNotif = Notification::where('user_id', $user->id)
                    ->where('title', 'LIKE', "%{$contract->asset_name}%")
                    ->where('is_read', false)
                    ->first();

                if ($existingNotif) {
                    // Nếu nội dung thông báo khác đi (VD: từ quá hạn 1 ngày sang 2 ngày)
                    if ($existingNotif->message !== $message) {
                        $existingNotif->update([
                            'message' => $message,
                            'type' => $type,
                            'created_at' => now() // Đẩy thông báo lên đầu danh sách
                        ]);
                    }
                } else {
                    // Nếu chưa có thông báo chưa đọc nào, tạo mới hoàn toàn
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'is_read' => false
                    ]);
                }
            }
        }

        // --- BƯỚC 2: TRẢ DỮ LIỆU ĐÃ LÀM MỚI VỀ CHO FRONTEND ---
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Format lại thời gian hiển thị
        $notifications->transform(function ($notif) {
            $notif->created_at_formatted = Carbon::parse($notif->created_at)->format('H:i d/m/Y');
            return $notif;
        });

        return response()->json($notifications);
    }

    // API Đánh dấu tất cả đã đọc
    public function markAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}
