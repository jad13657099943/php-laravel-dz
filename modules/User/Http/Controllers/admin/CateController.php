<?php


namespace Modules\User\Http\Controllers\admin;


class CateController
{
    public function index(){
        return view('user::admin.article_cate.index');
    }

    public function  add(){
        return view('user::admin.article_cate.add');
    }


}
