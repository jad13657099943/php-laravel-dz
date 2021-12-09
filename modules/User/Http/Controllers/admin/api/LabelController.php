<?php


namespace Modules\User\Http\Controllers\admin\api;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Models\ArticleCate;
use Modules\User\Models\ArticleLabel;

class LabelController extends Controller
{
    public function list(Request $request){
      return  ArticleLabel::query()->paginate($request->limit??10);
    }

    public function add(Request $request){
        $name=$request->name;
        if (!empty($name)){
            $label= ArticleLabel::query()->where('name',$name)->first();
            if ($label) throw new \Exception('标签已存在');
            ArticleLabel::query()->insert(['name'=>$name,'created_at'=>date('Y-m-d H:i:s')]);
        }
        return [
            'code'=>1
        ];
    }
}
