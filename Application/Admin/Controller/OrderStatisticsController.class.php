<?php

namespace Admin\Controller;

class OrderStatisticsController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 信用卡订单
     */
    public function creditCardOrder($start_date = '', $end_date = '') {
        $start_date = $start_date ? $start_date : date('Y-m-d', strtotime('-1 month'));
        $this->assign('start_date', $start_date);
        $end_date = $end_date ? $end_date : date('Y-m-d');
        $this->assign('end_date', $end_date);
        $days = (strtotime($end_date) - strtotime($start_date)) / 86400;
        $credit_card_order_model = D('Admin/CreditCardOrder');
        $statistics_list = [];
        for ($i = 0; $i <= $days; $i++) {
            $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($start_date)));
            $dqr_count = $credit_card_order_model
                    ->where("apply_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $dqr_bonus_sum = $credit_card_order_model
                    ->where("apply_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $shz_count = $credit_card_order_model
                    ->where("apply_status=1 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $shz_bonus_sum = $credit_card_order_model
                    ->where("apply_status=1 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $djs_count = $credit_card_order_model
                    ->where("apply_status=2 and settle_status=2 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $djs_bonus_sum = $credit_card_order_model
                    ->where("apply_status=2 and settle_status=2 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $yjs_count = $credit_card_order_model
                    ->where("apply_status=2 and settle_status=3 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $yjs_bonus_sum = $credit_card_order_model
                    ->where("apply_status=2 and settle_status=3 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $wtg_count = $credit_card_order_model
                    ->where("apply_status=3 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $wtg_bonus_sum = $credit_card_order_model
                    ->where("apply_status=3 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $total_count = $credit_card_order_model
                    ->where("create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $total_bonus_sum = $credit_card_order_model
                    ->where("create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $statistics_list[] = [
                'date' => $date,
                'dqr_count' => $dqr_count,
                'dqr_bonus_sum' => $dqr_bonus_sum,
                'shz_count' => $shz_count,
                'shz_bonus_sum' => $shz_bonus_sum,
                'djs_count' => $djs_count,
                'djs_bonus_sum' => $djs_bonus_sum,
                'yjs_count' => $yjs_count,
                'yjs_bonus_sum' => $yjs_bonus_sum,
                'wtg_count' => $wtg_count,
                'wtg_bonus_sum' => $wtg_bonus_sum,
                'total_count' => $total_count,
                'total_bonus_sum' => $total_bonus_sum
            ];
        }
        $this->assign('statistics_list', $statistics_list);
        $this->display();
    }
    
    /**
     * 保险订单
     */
    public function insuranceOrder($start_date = '', $end_date = '') {
        $start_date = $start_date ? $start_date : date('Y-m-d', strtotime('-1 month'));
        $this->assign('start_date', $start_date);
        $end_date = $end_date ? $end_date : date('Y-m-d');
        $this->assign('end_date', $end_date);
        $days = (strtotime($end_date) - strtotime($start_date)) / 86400;
        $insurance_order_model = D('Admin/InsuranceOrder');
        $statistics_list = [];
        for ($i = 0; $i <= $days; $i++) {
            $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($start_date)));
            $wwc_count = $insurance_order_model
                    ->where("apply_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $wwc_bonus_sum = $insurance_order_model
                    ->where("apply_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $djs_count = $insurance_order_model
                    ->where("apply_status=1 and settle_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $djs_bonus_sum = $insurance_order_model
                    ->where("apply_status=1 and settle_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $yjs_count = $insurance_order_model
                    ->where("apply_status=1 and settle_status=1 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $yjs_bonus_sum = $insurance_order_model
                    ->where("apply_status=1 and settle_status=1 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $total_count = $insurance_order_model
                    ->where("create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $total_bonus_sum = $insurance_order_model
                    ->where("create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $statistics_list[] = [
                'date' => $date,
                'wwc_count' => $wwc_count,
                'wwc_bonus_sum' => $wwc_bonus_sum,
                'djs_count' => $djs_count,
                'djs_bonus_sum' => $djs_bonus_sum,
                'yjs_count' => $yjs_count,
                'yjs_bonus_sum' => $yjs_bonus_sum,
                'total_count' => $total_count,
                'total_bonus_sum' => $total_bonus_sum
            ];
        }
        $this->assign('statistics_list', $statistics_list);
        $this->display();
    }

    /**
     * 贷款订单
     */
    public function loanOrder($start_date = '', $end_date = '') {
        $start_date = $start_date ? $start_date : date('Y-m-d', strtotime('-1 month'));
        $this->assign('start_date', $start_date);
        $end_date = $end_date ? $end_date : date('Y-m-d');
        $this->assign('end_date', $end_date);
        $days = (strtotime($end_date) - strtotime($start_date)) / 86400;
        $loan_order_model = D('Admin/LoanOrder');
        $statistics_list = [];
        for ($i = 0; $i <= $days; $i++) {
            $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($start_date)));
            $dqr_count = $loan_order_model
                    ->where("apply_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $dqr_bonus_sum = $loan_order_model
                    ->where("apply_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $djs_count = $loan_order_model
                    ->where("apply_status=1 and settle_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $djs_bonus_sum = $loan_order_model
                    ->where("apply_status=1 and settle_status=0 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $yjs_count = $loan_order_model
                    ->where("apply_status=1 and settle_status=1 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $yjs_bonus_sum = $loan_order_model
                    ->where("apply_status=1 and settle_status=1 and create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $total_count = $loan_order_model
                    ->where("create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->count('id');
            $total_bonus_sum = $loan_order_model
                    ->where("create_time>='{$date} 00:00:00' and create_time<='{$date} 23:59:59'")
                    ->sum('bonus');
            $statistics_list[] = [
                'date' => $date,
                'dqr_count' => $dqr_count,
                'dqr_bonus_sum' => $dqr_bonus_sum,
                'djs_count' => $djs_count,
                'djs_bonus_sum' => $djs_bonus_sum,
                'yjs_count' => $yjs_count,
                'yjs_bonus_sum' => $yjs_bonus_sum,
                'total_count' => $total_count,
                'total_bonus_sum' => $total_bonus_sum
            ];
        }
        $this->assign('statistics_list', $statistics_list);
        $this->display();
    }

    /**
     * 提现订单
     */
    public function withdrawCash($start_date = '', $end_date = '') {
        $start_date = $start_date ? $start_date : date('Y-m-d', strtotime('-1 month'));
        $this->assign('start_date', $start_date);
        $end_date = $end_date ? $end_date : date('Y-m-d');
        $this->assign('end_date', $end_date);
        $days = (strtotime($end_date) - strtotime($start_date)) / 86400;
        $withdraw_cash_model = D('Admin/WithdrawCash');
        $statistics_list = [];
        for ($i = 0; $i <= $days; $i++) {
            $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($start_date)));
            $dsh_count = $withdraw_cash_model
                    ->where("review_status=0 and apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->count('id');
            $dsh_money_sum = $withdraw_cash_model
                    ->where("review_status=0 and apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->sum('money');
            $wtg_count = $withdraw_cash_model
                    ->where("review_status=2 and apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->count('id');
            $wtg_money_sum = $withdraw_cash_model
                    ->where("review_status=2 and apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->sum('money');
            $dfk_count = $withdraw_cash_model
                    ->where("review_status=1 and (payment_status=0 or payment_status=1) and apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->count('id');
            $dfk_money_sum = $withdraw_cash_model
                    ->where("review_status=1 and (payment_status=0 or payment_status=1) and apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->sum('money');
            $yfk_count = $withdraw_cash_model
                    ->where("review_status=1 and payment_status=2 and apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->count('id');
            $yfk_money_sum = $withdraw_cash_model
                    ->where("review_status=1 and payment_status=2 and apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->sum('money');
            $total_count = $withdraw_cash_model
                    ->where("apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->count('id');
            $total_money_sum = $withdraw_cash_model
                    ->where("apply_time>='{$date} 00:00:00' and apply_time<='{$date} 23:59:59'")
                    ->sum('money');
            $statistics_list[] = [
                'date' => $date,
                'dsh_count' => $dsh_count,
                'dsh_money_sum' => $dsh_money_sum,
                'wtg_count' => $wtg_count,
                'wtg_money_sum' => $wtg_money_sum,
                'dfk_count' => $dfk_count,
                'dfk_money_sum' => $dfk_money_sum,
                'yfk_count' => $yfk_count,
                'yfk_money_sum' => $yfk_money_sum,
                'total_count' => $total_count,
                'total_money_sum' => $total_money_sum
            ];
        }
        $this->assign('statistics_list', $statistics_list);
        $this->display();
    }

}
