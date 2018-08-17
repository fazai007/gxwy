<?php

class TemplateMessage {

    public function __construct() {
        
    }

    /**
     * 获取新会员加入通知模板消息数据
     * @param string $openid 用户openid
     * @param string $nickname 用户昵称
     * @param string $keyword1 会员编号
     * @param string $keyword2 加入时间
     * @return array
     */
    public function getXhyjrtzData($openid, $nickname, $keyword1, $keyword2) {
        $data = array(
            'touser' => $openid,
            'template_id' => 'AG0BHKEEFxPhwe2ZrGsr0d6qx5-1aRPf7Nkuf9_DCW8',
            'url' => U('Wx/Customer/index', '', true, true),
            'data' => array(
                'first' => array(
                    'value' => '您的朋友' . $nickname . '光顾了你的微银行，请注意维护好客户关系，做好服务工作，如有疑问可直接公众号中咨询系统客服！',
                    'color' => ''
                ),
                'keyword1' => array(
                    'value' => $keyword1,
                    'color' => ''
                ),
                'keyword2' => array(
                    'value' => $keyword2,
                    'color' => ''
                )
            )
        );
        return $data;
    }
    
    /**
     * 获取佣金提醒模板消息数据
     * @param string $openid 用户openid
     * @param string $first
     * @param string $keyword1 佣金金额
     * @param string $keyword2 时间
     * @return array
     */
    public function getYjtxData($openid, $first, $keyword1, $keyword2) {
        $data = array(
            'touser' => $openid,
            'template_id' => '9sVYeZkivro8TPI7NNxlMqbZRtsHN3u-XtCNKzLqJjk',
            'url' => U('Wx/Income/index', '', true, true),
            'data' => array(
                'first' => array(
                    'value' => $first,
                    'color' => ''
                ),
                'keyword1' => array(
                    'value' => $keyword1 . '元',
                    'color' => ''
                ),
                'keyword2' => array(
                    'value' => $keyword2,
                    'color' => ''
                )
            )
        );
        return $data;
    }
    
}
