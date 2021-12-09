@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layui-form-label">商品</label>
                <div class="layui-input-inline" style="width: 161px">
                    <select id="url" class="layui-input" lay-filter="aihao">
                        <option value=""></option>
                        @foreach ($list as $item)
                            <option value={{$item->id}}>{{$item->title}}</option>
                        @endforeach
                        @foreach ($lists as $item)
                            <option value={{$item->id}}>{{$item->title}}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">Banner图</label>
                <div class="layui-input-inline" >
                    <input type="text" id="img" disabled  placeholder="" autocomplete="off" class="layui-input" >
                </div>
                <button style="float: left;" type="button" class="layui-btn" id="layuiadmin-upload-useradmin">上传图片</button>
                <div class="layui-form-mid layui-word-aux">建议尺寸343*140px</div>
            </div>
            <div class="layui-input-block" id="div_logo_img" style="display: none">
                <img src="" id="logo_img" style="width: 100px;height: 100px">
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit lay-filter="add" id="add">立即提交</button>
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


            $('#add').click(function () {

                var     url = '{{ route('m.dsy.api.admin.api.banner.add') }}';
                var    title=$('#url').val();
                var    img=$('#img').val();

                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data:{url:title,img:img
                    },
                    success: function (data) {

                        window.parent.location.reload();
                    },
                    error:function (data) {
                        alert('添加失败');
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
