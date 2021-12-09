@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form method="post"  class="layui-form" lay-filter="test1" >
                {{csrf_field()}}

                <div class="layui-form-item">
                    <label class="layui-form-label">名称</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$list->title}}" id="title" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">缩略图</label>
                    <div class="layui-input-inline" >
                        <input type="text" id="img" value="{{$list->img}}" disabled  placeholder="" autocomplete="off" class="layui-input" >
                    </div>
                    <button style="float: left;" type="button" class="layui-btn" id="layuiadmin-upload-useradmin">上传图片</button>
                    <div class="layui-form-mid layui-word-aux">建议尺寸140*140px</div>
                </div>
                <div class="layui-input-block" id="div_logo_img" style="display: block">
                    <img src="{{$list->img}}" id="logo_img" style="width: 100px;height: 100px">
                </div>



                <div class="layui-form-item">
                    <label class="layui-form-label">详情</label>
                    <div class="layui-input-block">
                        <textarea name="content" id="content" lay-verify="content" style="display: none;">
                            {{$list->content}}
                        </textarea>
                    </div>
                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">

                        <button class="layui-btn" lay-submit lay-filter="add" id="add">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('after-scripts')
    <script>

        function ityzl_SHOW_LOAD_LAYER(){
            return layer.msg('上传中，快慢取决您的网络情况...', {icon: 16,shade: [0.5, '#b2b2b2'],scrollbar: false, time:0}) ;
        }
        function ityzl_CLOSE_LOAD_LAYER(index){
            layer.closeAll();
            layer.close(index);
        }

        layui.use(['form', 'table', 'layedit','laydate','upload'], function () {
            var $ = layui.$
                , form = layui.form
                , upload = layui.upload
                , layedit = layui.layedit;
            var laydate = layui.laydate;
            /*   laydate.render({
                  elem:'#start_time'
               });*/
            form.render(null, 'test1');

            layedit.set({
                uploadImage: {
                    'url':'{{route('m.dsy.api.admin.api.good.add')}}',
                    'type':'post',
                    before:function (obj) {
                        i = ityzl_SHOW_LOAD_LAYER();
                    }
                }
            });

            //建立编辑器
            var index = layedit.build('content');

            //单图上传
            upload.render({
                elem: '#layuiadmin-upload-useradmin'
                ,url: '{{route('admin.api.media.upload')}}'
                ,accept: 'images'
                ,method: 'POST'
                ,acceptMime: 'image/*',
                before:function (obj) {
                    i = ityzl_SHOW_LOAD_LAYER();
                }
                ,done: function(res){
                    ityzl_CLOSE_LOAD_LAYER(i)
                    if (res.message === undefined) {
                        $("#div_logo_img").show();
                        $(this.item).prev("div").children("input").val(res.path);
                        $("#logo_img").attr("src",res.path);
                    }else{
                        layer.msg(res.msg, {time: 2000});
                    }
                }
            });


            //多图片上传
            upload.render({
                elem: '#test-upload-more'
                ,url: '{{route('admin.api.media.upload')}}'
                ,accept: 'images'
                ,method: 'POST'
                ,acceptMime: 'image/*'
                //,multiple: true //只上传一张
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    i = ityzl_SHOW_LOAD_LAYER();
                    obj.preview(function(index, file, result){
                        $('#test-upload-more-list').append('<img src="'+ result +'" class="layui-upload-img" style="width: 120px;height: 120px">')
                    });
                }
                ,done: function(res){
                    //上传完毕
                    if (res.message === undefined) {
                        var va = $('#album').val();
                        $('#album').val(va+'|'+res.path);
                        console.log(res.path);
                    }
                    //延长3s关闭，批量上传是逐一返回
                    setTimeout(function () {
                        ityzl_CLOSE_LOAD_LAYER(i)
                    }, 3000);
                }
            });


            form.verify({
                content:function (value) {
                    return layedit.sync(index);
                }
            });
            form.on('submit(add)', function(data){

                var     url = '{{ route('m.dsy.api.admin.api.information.edit') }}';

                var  id={{$list->id}};
                var    title=$('#title').val();
                var    img=$('#img').val();
                var   content=$('#content').val();
                var datas={id:id,title:title,img:img,content:content};
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data: datas,
                    success: function (data) {
                        window.parent.location.reload();
                    },
                    error:function (data) {
                        window.parent.location.reload();
                    }
                });
                return false;
            });


        })
    </script>
@endpush

<style>
    .layui-form-item .layui-input-inline {
        width: 800px !important;
    }

    .layui-form-label {
        box-sizing: initial;
        width: 200px !important;
    }
</style>
