<?php
namespace  app\admin\controller;

use think\Db;
use think\Request;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Text;

/**
 * EasyWechat
 */
class Easy extends Common
{
    private $config=[];
    private $toMiniProgram='<a href="http://www.qq.com" data-miniprogram-appid="appid" data-miniprogram-path="pages/index/index">点击跳小程序</a>';
    public function _initialize()
    {
        parent::_initialize();
        $config=Db::name("config")->find();
        if (!$config)
        {
            return false;
        }
        $this->config = [
            'app_id' => $config["wx_appId"],
            'secret' => $config["wx_secret"],
            'response_type' => 'array',
        ];
    }

    /**
     * 公众号实例 实例化
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function getEasyWechatOfficialApp()
    {
        return Factory::officialAccount($this->config);
    }

    /**
     * 发送客服消息
     * @param $openId
     * @param $content
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function sendCustomerService($openId,$content)
    {
        $message = new Text($content);
        $app=$this->getEasyWechatOfficialApp();
        return $app->customer_service->message($message)->to($openId)->send();
    }


}