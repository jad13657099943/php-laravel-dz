@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layui-form-label">跳转链接</label>
                <div class="layui-input-inline">
                    <input type="text" value="{{$list->url}}" id="url" autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">缩略图</label>
                <div class="layui-input-inline" >
                    <input type="text" id="img" disabled value="{{$list->img}}"  placeholder="" autocomplete="off" class="layui-input" >
                </div>
                <button style="float: left;" type="button" class="layui-btn" id="layuiadmin-upload-useradmin">上传图片</button>
                <div class="layui-form-mid layui-word-aux">建议尺寸343*140px</div>
            </div>
            <div class="layui-input-block" id="div_logo_img" >
                <img src="{{$list->img}}" id="logo_img" style="width: 100px;height: 100px">
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">

                    <button class="layui-btn" lay-submit lay-filter="add" id="add">立即修改</button>
                </div>
            </div>

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

            $('#add').click(function () {
                var  id={{$list->id}};
                var     url = '{{ route('m.dsy.api.admin.api.banner.edit') }}';
                var    title=$('#url').val();

                var    img=$('#img').val();


                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data:{id:id,url:title,img:img
                    },
                    success: function (data) {

                        window.parent.location.reload();
                    },
                    error:function (data) {
                        alert('编辑失败');
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
