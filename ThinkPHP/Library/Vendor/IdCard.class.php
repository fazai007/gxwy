<?php

class IdCard {

    private $id_card;

    public function __construct($id_card) {
        $this->id_card = $id_card;
    }

    /**
     * 获取籍贯
     * @return string
     */
    public function getBirthplace() {
        if (!$this->isIdCard())
            return '';
        $prov = substr($this->id_card, 0, 2);
        $city = array(
            '11' => '北京市',
            '12' => '天津市',
            '13' => '河北省',
            '14' => '山西省',
            '15' => '内蒙古自治区',
            '21' => '辽宁省',
            '22' => '吉林省',
            '23' => '黑龙江省',
            '31' => '上海市',
            '32' => '江苏省',
            '33' => '浙江省',
            '34' => '安徽省',
            '35' => '福建省',
            '36' => '江西省',
            '37' => '山东省',
            '41' => '河南省',
            '42' => '湖北省',
            '43' => '湖南省',
            '44' => '广东省',
            '45' => '广西壮族自治区',
            '46' => '海南省',
            '50' => '重庆市',
            '51' => '四川省',
            '52' => '贵州省',
            '53' => '云南省',
            '54' => '西藏自治区',
            '61' => '陕西省',
            '62' => '甘肃省',
            '63' => '青海省',
            '64' => '宁夏回族自治区',
            '65' => '新疆维吾尔自治区',
            '71' => '台湾',
            '81' => '香港',
            '82' => '澳门',
            '91' => '国外',
        );
        return isset($city[$prov]) ? $city[$prov] : '';
    }

    /**
     * 获取星座
     * @return string
     */
    public function getConstellation() {
        if (!$this->isIdCard())
            return '';
        $bir = substr($this->id_card, 10, 4);
        $month = (int) substr($bir, 0, 2);
        $day = (int) substr($bir, 2);
        $str_value = '';
        if (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) {
            $str_value = '水瓶座';
        } else if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) {
            $str_value = '双鱼座';
        } else if (($month == 3 && $day > 20) || ($month == 4 && $day <= 19)) {
            $str_value = '白羊座';
        } else if (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) {
            $str_value = '金牛座';
        } else if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 21)) {
            $str_value = '双子座';
        } else if (($month == 6 && $day > 21) || ($month == 7 && $day <= 22)) {
            $str_value = '巨蟹座';
        } else if (($month == 7 && $day > 22) || ($month == 8 && $day <= 22)) {
            $str_value = '狮子座';
        } else if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) {
            $str_value = '处女座';
        } else if (($month == 9 && $day >= 23) || ($month == 10 && $day <= 23)) {
            $str_value = '天秤座';
        } else if (($month == 10 && $day > 23) || ($month == 11 && $day <= 22)) {
            $str_value = '天蝎座';
        } else if (($month == 11 && $day > 22) || ($month == 12 && $day <= 21)) {
            $str_value = '射手座';
        } else if (($month == 12 && $day > 21) || ($month == 1 && $day <= 19)) {
            $str_value = '魔羯座';
        }
        return $str_value;
    }

    /**
     * 获取生肖
     * @return string
     */
    public function getZodiac() {
        if (!$this->isIdCard($this->id_card))
            return '';
        $start = 1901;
        $end = (int) substr($this->id_card, 6, 4);
        $x = ($start - $end) % 12;
        $value = '';
        if ($x == 1 || $x == -11) {
            $value = '鼠';
        }
        if ($x == 0) {
            $value = '牛';
        }
        if ($x == 11 || $x == -1) {
            $value = '虎';
        }
        if ($x == 10 || $x == -2) {
            $value = '兔';
        }
        if ($x == 9 || $x == -3) {
            $value = '龙';
        }
        if ($x == 8 || $x == -4) {
            $value = '蛇';
        }
        if ($x == 7 || $x == -5) {
            $value = '马';
        }
        if ($x == 6 || $x == -6) {
            $value = '羊';
        }
        if ($x == 5 || $x == -7) {
            $value = '猴';
        }
        if ($x == 4 || $x == -8) {
            $value = '鸡';
        }
        if ($x == 3 || $x == -9) {
            $value = '狗';
        }
        if ($x == 2 || $x == -10) {
            $value = '猪';
        }
        return $value;
    }

    /**
     * 获取性别
     * @return string
     */
    public function getSex() {
        if (!$this->isIdCard($this->id_card))
            return '';
        $sex_int = (int) substr($this->id_card, 16, 1);
        return $sex_int % 2 === 0 ? '女' : '男';
    }

    /**
     * 获取出生年月
     * @return array $birthday
     */
    public function getBirthday() {
        if (!$this->isIdCard($this->id_card))
            return '';
        $birthday = array(
            'year' => (int) substr($this->id_card, 6, 4),
            'month' => (int) substr($this->id_card, 10, 2),
            'day' => (int) substr($this->id_card, 12, 2)
        );
        return $birthday;
    }

    /**
     * 检查是否是身份证号
     * @return boolean
     */
    public function isIdCard() {
        //转化为大写，如出现x
        $this->id_card = strtoupper($this->id_card);
        //加权因子
        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码串
        $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        //按顺序循环处理前17位
        $sigma = 0;
        for ($i = 0; $i < 17; $i++) {
            //提取前17位的其中一位，并将变量类型转为实数
            $b = (int) $this->id_card{$i};
            //提取相应的加权因子
            $w = $wi[$i];
            //把从身份证号码中提取的一位数字和加权因子相乘，并累加
            $sigma += $b * $w;
        }
        //计算序号
        $snumber = $sigma % 11;
        //按照序号从校验码串中提取相应的字符
        $check_number = $ai[$snumber];
        if ($this->id_card{17} == $check_number) {
            return true;
        } else {
            return false;
        }
    }

}
