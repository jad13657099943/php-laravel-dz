@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{csrf_field()}}

            <div class="layui-form-item">
                <label class="layui-form-label">模块</label>
                <div class="layui-input-inline" style="width: 161px">
                    <select id="chain" class="layui-input" lay-filter="aihao">
                        <option value="UNITE">UNITE</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">产出币种</label>
                <div class="layui-input-inline" style="width: 161px">
                    <select id="to_symbol" class="layui-input" lay-filter="aihao">
                        <option value="FIL">FIL</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">折合T(充值除以该值获得实际T数)</label>
                <div class="layui-input-inline">
                    <input type="text" id="zhe" value="{{$list->zhe}}" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">名称</label>
                <div class="layui-input-inline">
                    <input type="text" id="title" value="{{$list->title}}" autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">价格说明</label>
                <div class="layui-input-inline">
                    <input type="text" id="money_text" value="{{$list->price_text}}" autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">挖矿周期(天)</label>
                <div class="layui-input-inline">
                    <input type="text" id="period" value="{{$list->period}}" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">挖矿周期说明</label>
                <div class="layui-input-inline">
                    <input type="text" id="period_text" value="{{$list->period_text}}" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">开挖说明</label>
                <div class="layui-input-inline">
                    <input type="text" id="start_time" value="{{$list->start_time}}" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">开挖时间(下单后多久开挖天)</label>
                <div class="layui-input-inline">
                    <input type="text" id="start_day" value="{{$list->start_day}}" autocomplete="off" class="layui-input">
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
            <div class="layui-input-block" id="div_logo_img" >
                <img src="{{$list->img}}" id="logo_img" style="width: 100px;height: 100px">
            </div>


            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="layui-upload">
                            <button type="button" class="layui-btn" id="test-upload-more">上传产品相册图片(建议尺寸：375*200px，可上传多个)</button>
                            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                                预览图：
                                <div class="layui-upload-list" id="test-upload-more-list"></div>
                                @foreach ($list->imgs as $user)
                                    <img src='{{$user}}' class="layui-upload-img" onclick="del(this)" style="width: 120px;height: 120px">
                                @endforeach
                            </blockquote>
                            <input type="hidden" name="album" id="album" value="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline" style="margin-left: 100px">
                    <label class="layui-form-label" style="width: 50px !important;">范围1</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_min1"  value="{{$list['bl'][0]['min']}}" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_max1" value="{{$list['bl'][0]['max']}}" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">客户分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fen1" value="{{$list['bl'][0]['abl']}}"  autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">公司分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fens1" value="{{$list['bl'][0]['bbl']}}"  autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline" style="margin-left: 100px">
                    <label class="layui-form-label" style="width: 50px !important;">范围2</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_min2" value="{{$list['bl'][1]['min']}}"  autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_max2" value="{{$list['bl'][1]['max']}}" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">客户分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fen2" value="{{$list['bl'][1]['abl']}}" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">公司分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fens2" value="{{$list['bl'][1]['bbl']}}"  autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline" style="margin-left: 100px">
                    <label class="layui-form-label" style="width: 50px !important;">范围3</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_min3" value="{{$list['bl'][2]['min']}}"  autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_max3" value="{{$list['bl'][2]['max']}}"  autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">客户分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fen3"  value="{{$list['bl'][2]['abl']}}" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">公司分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fens3" value="{{$list['bl'][2]['bbl']}}" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline" style="margin-left: 100px">
                    <label class="layui-form-label" style="width: 50px !important;">范围4</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_min4" value="{{$list['bl']['3']['min']}}"  autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_max4" value="{{$list['bl']['3']['max']}}"  autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">客户分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fen4" value="{{$list['bl']['3']['abl']}}"  autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">公司分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fens4" value="{{$list['bl']['3']['bbl']}}"  autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline" style="margin-left: 100px">
                    <label class="layui-form-label" style="width: 50px !important;">范围5</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_min5" value="{{$list['bl'][4]['min']}}" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_max5" value="{{$list['bl'][4]['max']}}" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">客户分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fen5" value="{{$list['bl'][4]['abl']}}"  autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">公司分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fens5" value="{{$list['bl'][4]['bbl']}}" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline" style="margin-left: 100px">
                    <label class="layui-form-label" style="width: 50px !important;">范围6</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="price_min6"  value="{{$list['bl'][5]['min']}}" autocomplete="off" class="layui-input">
                    </div>

                    <label class="layui-form-label" style="width: 60px !important;">客户分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fen6" value="{{$list['bl'][5]['abl']}}" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label" style="width: 60px !important;">公司分配</label>
                    <div class="layui-input-inline" style="width: 50px !important;">
                        <input type="text" id="fens6" value="{{$list['bl'][5]['bbl']}}" autocomplete="off" class="layui-input">
                    </div>
                </div>
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
                var     url = '{{ route('m.dsy.api.admin.api.unite.edit') }}';
                var     title=$('#title').val();
                var chain=$('#chain').val();
                var     money_text=$('#money_text').val();
                var     img=$('#img').val();
                var     start_time=$('#start_time').val();
                var     period=$('#period').val();
                var     period_text=$('#period_text').val();
                var     start_day=$('#start_day').val();
                var     album=$('#album').val();
                let     price_min1=$('#price_min1').val();
                let     price_max1=$('#price_max1').val();
                let     fen1=$('#fen1').val();
                let     fens1=$('#fens1').val();
                let     price_min2=$('#price_min2').val();
                let     price_max2=$('#price_max2').val();
                let     fen2=$('#fen2').val();
                let     fens2=$('#fens2').val();
                let     price_min3=$('#price_min3').val();
                let     price_max3=$('#price_max3').val();
                let     fen3=$('#fen3').val();
                let     fens3=$('#fens3').val();
                let     price_min4=$('#price_min4').val();
                let     price_max4=$('#price_max4').val();
                let     fen4=$('#fen4').val();
                let     fens4=$('#fens4').val();
                let     price_min5=$('#price_min5').val();
                let     price_max5=$('#price_max5').val();
                let     fen5=$('#fen5').val();
                let     fens5=$('#fens5').val();
                let     price_min6=$('#price_min6').val();
                let     zhe=$('#zhe').val();
                let     fen6=$('#fen6').val();
                let     fens6=$('#fens6').val();
                let data=[{'min':price_min1,'max':price_max1,'abl':fen1,'bbl':fens1},
                    {'min':price_min2,'max':price_max2,'abl':fen2,'bbl':fens2},
                    {'min':price_min3,'max':price_max3,'abl':fen3,'bbl':fens3},
                    {'min':price_min4,'max':price_max4,'abl':fen4,'bbl':fens4},
                    {'min':price_min5,'max':price_max5,'abl':fen5,'bbl':fens5},
                    {'min':price_min6,'max':999999,'abl':fen6,'bbl':fens6}
                ];
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data:{id:id,title:title,img:img,start_time:start_time,period:period,zhe:zhe,chain:chain,
                        price_text:money_text,period_text:period_text,imgs:album,start_day:start_day,bl:data
                    },
                    success: function (data) {

                        window.parent.location.reload();
                    },
                    error:function (data) {
                        alert('添加失败');
                    }
                });

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
