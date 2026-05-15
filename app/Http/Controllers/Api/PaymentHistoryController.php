<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Lấy lịch sử của riêng khách đang đăng nhập, xếp mới nhất lên đầu
            $histories = PaymentHistory::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Chỉnh sửa lại format thời gian
            $histories->transform(function ($item) {
                $item->date = Carbon::parse($item->created_at)->format('H:i d/m/Y');
                return $item;
            });

            return response()->json($histories);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
