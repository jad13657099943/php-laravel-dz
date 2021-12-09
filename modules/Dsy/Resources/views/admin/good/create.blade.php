@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{csrf_field()}}

            {{-- <div class="layui-form-item">
                 <label class="layui-form-label">模块</label>
                 <div class="layui-input-inline" style="width: 161px">
                     <select id="chain" class="layui-input" lay-filter="aihao">
                         <option value=""></option>
                         @foreach ($list as $item)
                             <option value={{$item}}>{{$item}}</option>
                         @endforeach
                     </select>
                 </div>
             </div>--}}
            {{--  <div class="layui-form-item">
                  <label class="layui-form-label">产出币种</label>
                  <div class="layui-input-inline" style="width: 161px">
                      <select id="to_symbol" class="layui-input" lay-filter="aihao">
                          <option value="FIL">FIL</option>
                          @foreach ($lists as $item)
                              <option value={{$item}}>{{$item}}</option>
                          @endforeach
                      </select>
                  </div>
              </div>--}}
            {{--<div class="layui-form-item">
                <label class="layui-form-label">释放规则</label>
                <div class="layui-input-inline" style="width: 161px">
                    <select id="to_type" class="layui-input" lay-filter="aihao">
                        <option value="1">基础+线性</option>
                        <option value='2'>线性</option>
                        <option value="3">无规则</option>
                    </select>
                </div>
            </div>--}}

            <div class="layui-form-item">
                <label class="layui-form-label">名称</label>
                <div class="layui-input-inline">
                    <input type="text" id="title" value="SWARM物理存储服务器--单节点" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">算力类型</label>
                <div class="layui-input-inline">
                    <input type="text" id="hashrate_type" value="Swarm" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">单位</label>
                <div class="layui-input-inline">
                    <input type="text" id="unit" value="节点" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">节点数量</label>
                <div class="layui-input-inline">
                    <input type="text" id="saves" value="1" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">价格(元)</label>
                <div class="layui-input-inline">
                    <input type="text" id="cny" value="6600" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">价格(USDT)</label>
                <div class="layui-input-inline">
                    <input type="text" id="money" value="1034" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">产品周期(天)</label>
                <div class="layui-input-inline">
                    <input type="text" id="period" value="360" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">产品周期说明</label>
                <div class="layui-input-inline">
                    <input type="text" id="period_text" value="360天" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">续约价格</label>
                <div class="layui-input-inline">
                    <input type="text" id="vt_text" value="以主网实时情况为准" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">上架期</label>
                <div class="layui-input-inline">
                    <input type="text" id="putawat_text" value="T+7天" autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">所有设备</label>
                <div class="layui-input-inline">
                    <input type="text" id="equipment" value="分布式物理存储服务器" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">分币比例</label>
                <div class="layui-input-inline">
                    <input type="text" id="bl" value="0.8" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">管理费比例</label>
                <div class="layui-input-inline">
                    <input type="text" id="cost" value="0.2" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">总份数</label>
                <div class="layui-input-inline">
                    <input type="text" id="total_number" value="1000" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">剩余份数</label>
                <div class="layui-input-inline">
                    <input type="text" id="residual_fraction" value="500" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">质押币</label>
                <div class="layui-input-inline">
                    <input type="text" id="pledge_text" value="自行质押" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">开挖时间(下单后多久开挖天)</label>
                <div class="layui-input-inline">
                    <input type="text" id="start_day" value="7" autocomplete="off" class="layui-input">
                </div>
            </div>

            {{-- <div class="layui-form-item">
                 <label class="layui-form-label">质押模式</label>
                 <div class="layui-input-inline" style="width: 161px">
                     <select id="pledge" class="layui-input" lay-filter="aihao">
                         <option value="0">无需质押</option>
                         <option value="1">官方每T质押</option>
                     </select>
                 </div>
             </div>--}}
            <div class="layui-form-item">
                <label class="layui-form-label">缩略图</label>
                <div class="layui-input-inline">
                    <input type="text" id="img" disabled placeholder="" autocomplete="off" class="layui-input">
                </div>
                <button style="float: left;" type="button" class="layui-btn" id="layuiadmin-upload-useradmin">上传图片
                </button>
                <div class="layui-form-mid layui-word-aux">建议尺寸84*112px</div>
            </div>
            <div class="layui-input-block" id="div_logo_img" style="display: none">
                <img src="" id="logo_img" style="width: 100px;height: 100px">
            </div>


            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="layui-upload">
                            <button type="button" class="layui-btn" id="test-upload-more">
                                上传产品相册图片(建议尺寸：144*198px，可上传多个)
                            </button>
                            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                                预览图：
                                <div class="layui-upload-list" id="test-upload-more-list"></div>
                            </blockquote>
                            <input type="hidden" name="album" id="album" value="">
                        </div>
                    </div>
                </div>
            </div>


            {{--   <div class="layui-form-item">
                   <label class="layui-form-label">详情</label>
                   <div class="layui-input-block">
                       <textarea name="content" id="content" lay-verify="content" style="display: none;"></textarea>
                   </div>
               </div>--}}
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

        function ityzl_SHOW_LOAD_LAYER() {
            return layer.msg('上传中，快慢取决您的网络情况...', {icon: 16, shade: [0.5, '#b2b2b2'], scrollbar: false, time: 0});
        }

        function ityzl_CLOSE_LOAD_LAYER(index) {
            layer.closeAll();
            layer.close(index);
        }

        layui.use(['form', 'table', 'layedit', 'laydate', 'upload', 'jquery'], function () {
            var $ = layui.jquery
                , form = layui.form
                , upload = layui.upload
                , layedit = layui.layedit;


            form.render(null, 'test1');

            layedit.set({
                uploadImage: {
                    'url': '{{route('m.dsy.api.admin.api.good.add')}}',
                    'type': 'post',
                    before: function (obj) {
                        i = ityzl_SHOW_LOAD_LAYER();
                    }
                }
            });

            //建立编辑器
            var index = layedit.build('content');

            //单图上传
            upload.render({
                elem: '#layuiadmin-upload-useradmin'
                , url: '{{route('admin.api.media.upload')}}'
                , accept: 'images'
                , method: 'POST'
                , acceptMime: 'image/*',
                before: function (obj) {
                    i = ityzl_SHOW_LOAD_LAYER();
                }
                , done: function (res) {
                    ityzl_CLOSE_LOAD_LAYER(i)
                    if (res.message === undefined) {
                        $("#div_logo_img").show();
                        $(this.item).prev("div").children("input").val(res.path);
                        $("#logo_img").attr("src", res.path);
                    } else {
                        layer.msg(res.msg, {time: 2000});
                    }
                }
            });


            //多图片上传
            upload.render({
                elem: '#test-upload-more'
                , url: '{{route('admin.api.media.upload')}}'
                , accept: 'images'
                , method: 'POST'
                , acceptMime: 'image/*'
                //,multiple: true //只上传一张
                , before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    i = ityzl_SHOW_LOAD_LAYER();
                    obj.preview(function (index, file, result) {
                        $('#test-upload-more-list').append('<img src="' + result + '" class="layui-upload-img" style="width: 120px;height: 120px">')
                    });
                }
                , done: function (res) {
                    //上传完毕
                    if (res.message === undefined) {
                        var va = $('#album').val();
                        $('#album').val(va + '|' + res.path);
                        console.log(res.path);
                    }
                    //延长3s关闭，批量上传是逐一返回
                    setTimeout(function () {
                        ityzl_CLOSE_LOAD_LAYER(i)
                    }, 3000);
                }
            });

            $('#add').click(function () {

                var url = '{{ route('m.dsy.api.admin.api.good.add') }}';
                var chain = 'SWARM';
                var to_symbol = 'BZZ';
                var to_type = 3;
                var title = $('#title').val();
                var hashrate_type = $('#hashrate_type').val();
                var unit = $('#unit').val();
                var saves = $('#saves').val();
                var cny = $('#cny').val();
                var money = $('#money').val();
                var period = $('#period').val();
                var period_text = $('#period_text').val();
                var vt_text = $('#vt_text').val();
                var putawat_text = $('#putawat_text').val();
                var equipment = $('#equipment').val();
                var bl = $('#bl').val();
                var cost = $('#cost').val();
                var total_number = $('#total_number').val();
                var residual_fraction = $('#residual_fraction').val();
                var pledge_text = $('#pledge_text').val();
                var start_day = $('#start_day').val();
                var img = $('#img').val();
                var album = $('#album').val();


                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        chain:chain,
                        to_symbol:to_symbol,
                        to_type:to_type,
                        title: title,
                        hashrate_type:hashrate_type,
                        unit:unit,
                        saves: saves,
                        cny:cny,
                        money: money,
                        period: period,
                        period_text: period_text,
                        vt_text: vt_text,
                        putawat_text:putawat_text,
                        equipment:equipment,
                        bl: bl,
                        cost: cost,
                        total_number:total_number,
                        residual_fraction:residual_fraction,
                        pledge_text: pledge_text,
                        start_day: start_day,
                        img: img,
                        imgs: album,
                    },
                    success: function (data) {

                        window.parent.location.reload();
                    },
                    error: function (data) {
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
