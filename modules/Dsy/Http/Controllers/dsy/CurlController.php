<?php


namespace Modules\Dsy\Http\Controllers\dsy;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurlController extends Controller
{


    /**
     * 黄小心
     * @param Request $request
     * @return bool|string
     */
    public function xxx(Request $request){
        $address=$request->address;
        $url='http://8.135.118.26:8088/api/v2/token/trx/'.$address;
        $header  = array(
            'api-nonce:'.'bizhitu',
            'api-signature:'.'bizhitu',
            'api-key:'.'app10'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}
