<?php

namespace Admin\Controller;

class DataStatisticsController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 用户统计
     */
    public function user($type = 'day', $start_date = '', $end_date = '') {
        $user_model = D('Admin/User');
        $map = [];
        //按照天统计
        if ($type == 'day') {
            $today = strtotime(date('Y-m-d', time()));
            $start_date = $start_date ? strtotime($start_date) : $today - 86400 * 14;
            $end_date = $end_date ? (strtotime($end_date) + 1) : $today + 86400;
            $count_day = ($end_date - $start_date) / 86400;
            for ($i = 0; $i < $count_day; $i++) {
                $day_stamp = $start_date + $i * 86400;
                $day_after_stamp = $start_date + ($i + 1) * 86400;
                $day = date('Y-m-d H:i:s', $day_stamp);
                $day_after = date('Y-m-d H:i:s', $day_after_stamp);
                $map['reg_time'] = [
                    ['egt', $day],
                    ['lt', $day_after]
                ];
                $xaxis_data[] = date('m月d日', $day_stamp);
                $user_data[] = intval($user_model->where($map)->count('id'));
            }
            $start_date = date('Y-m-d', $start_date);
            $end_date = date('Y-m-d', $end_date - 1);
        }
        //按照月统计
        elseif ($type == 'month') {
            $start_date = $start_date ? substr($start_date, 0, 7) : date('Y-m', strtotime('-12 month'));
            $end_date = $end_date ? substr($end_date, 0, 7) : date('Y-m', strtotime('+0 month'));
            $i = 0;
            $month = '';
            while ($month != $end_date) {
                $month = date('Y-m', strtotime('+' . $i . ' month ' . $start_date));
                $next_month = date('Y-m', strtotime('+' . ($i + 1) . ' month' . $start_date));
                $map['reg_time'] = [
                    ['egt', $month . '-00'],
                    ['lt', $next_month . '-00'],
                ];
                $xaxis_data[] = date('y年m月', strtotime(($month)));
                $user_data[] = intval($user_model->where($map)->count('id'));
                $i++;
            }
            $count_day = $i;
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date . ' +1 month') - 1);
        }
        //按照年统计
        elseif ($type == 'year') {
            $start_date = $start_date ? $start_date : date('Y-m-d', strtotime('-5 year'));
            $end_date = $end_date ? $end_date : date('Y-m-d', strtotime('+0 year'));
            $i = 0;
            $end_date = substr($end_date, 0, 4);
            $year = '';
            while ($year != $end_date) {
                $year = date('Y', strtotime('+' . $i . ' year ' . $start_date));
                $next_year = date('Y', strtotime('+' . ($i + 1) . ' year' . $start_date));
                $map['reg_time'] = [
                    ['egt', $year . '-00-00'],
                    ['lt', $next_year . '-00-00'],
                ];
                $xaxis_data[] = date('Y年', strtotime(($year . '-01-01')));
                $user_data[] = intval($user_model->where($map)->count('id'));
                $i++;
            }
            $count_day = $i;
            $start_date = date('Y', strtotime($start_date)) . '-01-01';
            $end_date = date('Y', strtotime($end_date . ' +1 year')) . '-01-01';
        }
        $this->assign('type', $type);
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $this->assign('count_day', $count_day);
        $this->assign('xaxis_data', json_encode($xaxis_data));
        $this->assign('user_data', json_encode($user_data));
        $this->display();
    }
    
}
