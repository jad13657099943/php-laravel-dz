<?php


namespace Modules\User\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Models\ArticleCate;

class CateController extends Controller
{

    public function list(Request $request){
      return  ArticleCate::query()->paginate($request->limit??10);
    }

    public function add(Request $request){
        $name=$request->name;
        if (!empty($name)){
            $cate= ArticleCate::query()->where('name',$name)->first();
            if ($cate) throw new \Exception('分类已存在');
            ArticleCate::query()->insert(['name'=>$name,'created_at'=>date('Y-m-d H:i:s')]);
        }
        return [
          'code'=>1
        ];
    }

}
