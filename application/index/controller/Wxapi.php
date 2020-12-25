<?php
namespace app\index\controller;
use think\Controller;
class Wxapi extends Controller
{
    public function __construct()
    {
        $this->index();
    }

    public function index(){

		$data = input();
		file_put_contents('./a3.log',\json_encode($data));
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$token = 'M16kxQCL9KGNyOU5';
			$array = array( $token, $data['timestamp'], $data['nonce']);
			sort($array, SORT_STRING);
			$str = implode($array);
			$ress = sha1($str);
			if( $ress == $data['signature'] ){
				echo $data['echostr'];exit;
			}else{
				return null;
			}
		} else {
            $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
            $objectxml = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);//将文件转换成 对象
            $datas = array();
            foreach ($objectxml as $k => $v) {
                $datas[(string) $k] = (string) $v;
            }
            $url = 'https://possji.com:8088/yinheorder/wxpublic/getJsonInfo';
			$this->sendpostss($url,$datas);
			exit;
		}
	}

    /**
     * 发送post formdata请求
     */
    public function sendpostss($url,array $data)
    {
        $data = @json_encode($data);

        $headers = [
            'Content-Type: application/json;charset=utf-8',
            'Content-Length: ' . strlen($data)
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_TIMEOUT, 8);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($curl);
        curl_close($curl);
        return @json_decode($output, true);
    }


}