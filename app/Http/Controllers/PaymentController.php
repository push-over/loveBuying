<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    /*付款*/
    public function payByAlipay(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        if ($order->paid || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        return app('alipay')->web([
            'out_trade_no' => $order->no,
            'total_amount' => $order->total_amount,
            'subject' => '支付 loveBuying 爱购网 的订单：' . $order->no,
        ]);
    }

    /*支付前端返回页面*/
    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    /*支付后端*/
    public function alipayNotify()
    {
        $data = app('alipay')->verify();

        $order = Order::where('no', $data->out_trade_no)->first();

        if (!$order) {
            return 'fail';
        }
        if ($order->paid_at) {
            return app('支付宝')->success();
        }

        $order->update([
            'paid_at' => Carbon::now(),
            'payment_method' => '支付宝',
            'payment_no' => $data->trade_no,
        ]);

        /*触发事件*/
        $this->afterPaid($order);
        return app('alipay')->success();
    }

    /*事件*/
    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }
}
