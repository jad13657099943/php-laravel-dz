@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{csrf_field()}}


            <div class="layui-form-item">
                <label class="layui-form-label" id="chain">{{$chain}}</label>
                <label class="layui-form-label">已释放:{{$num2}}</label>
                <label class="layui-form-label">未释放:{{$num}}</label>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">数额</label>
                <div class="layui-input-inline">
                    <input type="text" value=""  id="num" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">

                    <button class="layui-btn" lay-submit lay-filter="add" id="add">立即释放</button>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">未释放</label>
                <div class="layui-input-inline">
                    <input type="text" value=""  id="num2" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">

                    <button class="layui-btn" lay-submit lay-filter="add" id="add2">立即修改</button>
                </div>
            </div>

        </div>
    </div>


@endsection

@push('after-scripts')
    <script>
        function del(a) {
            $(a).remove();
        }
        function ityzl_SHOW_LOAD_LAYER(){
            return layer.msg('上传中，快慢取决您的网络情况...', {icon: 16,shade: [0.5, '#b2b2b2'],scrollbar: false, time:0}) ;
        }
        function ityzl_CLOSE_LOAD_LAYER(index){
            layer.closeAll();
            layer.close(index);
        }

        layui.use(['form', 'table', 'layedit','laydate','upload','jquery'], function () {
            var $ = layui.jquery
                , form = layui.form
                , upload = layui.upload
                , layedit = layui.layedit;


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
                        $('#test-upload-more-list').append('<img src="'+ result +'" class="layui-upload-img" onclick="del(this)" style="width: 120px;height: 120px">')
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

            $('#add').click(function () {
                var   id={{$id}};
                var   chain= $('#chain').text();
                var   url = '{{ route('m.dsy.api.admin.api.user.sy') }}';
                let   money=$('#num').val();
                let   data={'user_id':id,'money':money,'chain':chain};
                ajax(url,data);
            });

            $('#add2').click(function () {
                var   id={{$id}};
                var   chain= $('#chain').text();
                var   url = '{{ route('m.dsy.api.admin.api.user.sys') }}';
                let   money=$('#num2').val();
                let   data={'user_id':id,'money':money,'chain':chain};
                ajax(url,data);
            });
            function  ajax(url,data) {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data:data,
                    success: function (data) {

                        window.location.reload();
                    },
                    error:function (data) {
                        alert('操作失败');
                    }
                });
            }



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
