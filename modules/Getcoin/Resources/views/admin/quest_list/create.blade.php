@extends('core::admin.layouts.app')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form method="post" class="layui-form" action="{{ route($url) }}">
                {{csrf_field()}}
                <div class="layui-tab" lay-filter="locale">
                    <ul class="layui-tab-title">
                        @foreach (config('app.supported_locales') as $key => $locale)
                            <li @if($loop->first)class="layui-this"@endif>{{$locale['name']}}</li>
                        @endforeach
                    </ul>
                    <div class="layui-tab-content">

                        <div class="layui-form-item">
                            <label class="layui-form-label">分发状态</label>
                            <div class="layui-input-inline">
                                <input type="radio" name="state" value="1" title="可分发"
                                       @if($info->state == 1)  checked @endif>
                                <input type="radio" name="state" value="0" title="下架"
                                       @if($info->state == 0)  checked @endif>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">用户端显示状态</label>
                            <div class="layui-input-inline">
                                <input type="radio" name="is_show" value="1" title="可分发"
                                       @if($info->is_show == 1)  checked @endif>
                                <input type="radio" name="is_show" value="0" title="下架"
                                       @if($info->is_show == 0)  checked @endif>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">排序</label>
                            <div class="layui-input-inline">
                                <input type="number" name="sort" required value="{{$info->sort}}"
                                       placeholder="数值越大越排前面" autocomplete="off" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">最大可报名数量</label>
                            <div class="layui-input-inline">
                                <input type="number" name="enroll_num" required value="{{$info->enroll_num}}"
                                       placeholder="必须大于0" autocomplete="off" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">CNY单份奖励</label>
                            <div class="layui-input-inline">
                                <input type="number" name="money_cny" required value="{{$info->money_cny}}"
                                       placeholder="不要小于0" autocomplete="off" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">USD单份奖励</label>
                            <div class="layui-input-inline">
                                <input type="number" name="money_usd" required value="{{$info->money_usd}}"
                                       placeholder="不要小于0" autocomplete="off" class="layui-input">
                            </div>
                        </div>



                        @foreach (config('app.supported_locales') as $key => $locale)
                            <div
                                @if($loop->first)
                                class="layui-tab-item layui-show"
                                @else
                                class="layui-tab-item"
                                @endif>

                                <div class="layui-form-item">
                                    <label class="layui-form-label ">奖励单位说明</label>
                                    <div class="layui-input-inline">
                                        <input type="text" required name="unit[{{$key}}]"
                                               value="{{$info->getTranslation('unit', $key) ?? ''}}"
                                               placeholder="必填" autocomplete="off" class="layui-input">
                                    </div>
                                </div>

                                <div class="layui-form-item">
                                    <label class="layui-form-label">任务标题</label>
                                    <div class="layui-input-inline">
                                        <input type="text" required name="title[{{$key}}]"
                                               value="{{$info->getTranslation('title', $key) ?? ''}}"
                                               placeholder="必填" autocomplete="off" class="layui-input">
                                    </div>
                                </div>



                                <div class="layui-form-item">
                                    <label class="layui-form-label">简述</label>
                                    <div class="layui-input-inline">
                                        <textarea name="summary[{{$key}}]" required placeholder="请输入简述内容" class="layui-textarea">{{$info->getTranslation('summary', $key) ?? ''}}</textarea>
                                    </div>
                                </div>

                                <div class="layui-form-item upload" data-locale="{{$key}}">
                                    <label class="layui-form-label">封面图</label>
                                    <div class="layui-input-inline">
                                        <div class="layui-upload">
                                            <button type="button" class="layui-btn" id="covers_btn">
                                                上传封面图
                                            </button>
                                            <blockquote class="layui-elem-quote layui-quote-nm"
                                                        style="margin-top: 10px;">
                                                预览图：
                                                <div class="layui-upload-list" id="covers_image">
                                                    <div class="image-preview-box">
                                                        <img src="{{$info->getTranslation('image', $key) ?? ''}}"
                                                             class="layui-upload-img">
                                                        <input type="hidden" name="image[{{$key}}]"
                                                               value="{{$info->getTranslation('image', $key) ?? ''}}"/>
                                                        <button type="button"
                                                                class="layui-btn layui-btn-danger layui-btn-xs covers_remove">
                                                            删除
                                                        </button>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                    </div>
                                </div>

                                <div class="layui-form-item">
                                    <label class="layui-form-label">任务内容</label>
                                    <div class="layui-input-inline">
                                    <textarea type="text" required name="content[{{$key}}]"
                                              placeholder="请输入任务内容" autocomplete="off" class="content layui-textarea">
                                        {{$info->getTranslation('content', $key) ?? ''}}
                                    </textarea>
                                    </div>
                                </div>

                                <div class="layui-form-item">
                                    <label class="layui-form-label">平台收费规则</label>
                                    <div class="layui-input-inline">
                                    <textarea type="text" required name="cost_content[{{$key}}]"
                                              placeholder="请输入任务内容" autocomplete="off" class="content layui-textarea">
                                        {{$info->getTranslation('cost_content', $key) ?? ''}}
                                    </textarea>
                                    </div>
                                </div>


                                <div class="layui-form-item">
                                    <label class="layui-form-label">奖励说明内容</label>
                                    <div class="layui-input-inline">
                                    <textarea type="text" required name="reward_content[{{$key}}]"
                                              placeholder="请输入任务内容" autocomplete="off" class="content layui-textarea">
                                        {{$info->getTranslation('reward_content', $key) ?? ''}}
                                    </textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <input type="hidden" value="{{$id}}" name="id">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="lay-announce">立即提交</button>
                        <button type="button" class="layui-btn layui-btn-normal" id="return_list">返回列表</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('after-scripts')
    <script>

        function ityzl_SHOW_LOAD_LAYER() {
            return layer.msg('处理中...', {icon: 16, shade: [0.5, '#b2b2b2'], scrollbar: false, time: 0});
        }

        function ityzl_CLOSE_LOAD_LAYER(index) {
            layer.closeAll();
            layer.close(index);
        }

        layui.use(['form', 'table', 'layedit', 'element', 'upload'], function () {
            var $ = layui.$
                , layedit = layui.layedit
                , form = layui.form
                , table = layui.table
                , upload = layui.upload
                , element = layui.element;
            form.render();
            $(document).on('click', '.covers_remove', function () {
                $(this).parent().remove();
            });
            $('.upload').each(function () {
                console.log(1);
                var $this = $(this);
                const $btn = $('#covers_btn', $this);
                const $images = $('#covers_image', $this);
                const locale = $this.data('locale');
                //多图片上传
                upload.render({
                    elem: $btn
                    , url: '{{route('admin.api.media.upload')}}'
                    , multiple: true
                    , done: function (res) {
                        if (res.message === undefined) {
                            //上传成功
                            $images.append('<div class="image-preview-box"><img src="' + res.url + '" class="layui-upload-img"><input type="hidden" name="image[' + locale + ']" value="' + res.url + '"/><button type="button" class="layui-btn layui-btn-danger layui-btn-xs covers_remove">删除</button></div>')
                        } else {
                            layer.msg('上传失败');
                        }
                    }
                });

            });

            $('textarea.content').each(function () {

                layedit.set({
                    uploadImage: {
                        url: '{{route('m.user.api.admin.api.upload.upload_for_layedit')}}'
                        , type: '' //默认post
                    }
                });

                layedit.build(this, {
                    height: 320,
                    tool: [
                        'strong' //加粗
                        , 'italic' //斜体
                        , 'underline' //下划线
                        , 'del' //删除线
                        , '|' //分割线
                        , 'left' //左对齐
                        , 'center' //居中对齐
                        , 'right' //右对齐
                        , 'link' //超链接
                        , 'unlink' //清除链接
                        , 'face' //表情
                        , 'image' //插入图片
                    ]
                })
            });


            /*form.on('submit(add)', function(data){

                i = ityzl_SHOW_LOAD_LAYER();
                var url = '{{ route($url) }}';
                $.ajax({
                    type: 'post',
                    url: url,
                    data: data.field,
                    dataType: 'json',
                    success: function (resp) {
                        ityzl_CLOSE_LOAD_LAYER(i);
                        layer.msg(resp.msg, {icon: 1, time: 2000, shade: [0.8, '#393D49']}, function () {
                            window.parent.location.reload();
                        });
                    },
                    error: function (err) {
                        ityzl_CLOSE_LOAD_LAYER(i);
                        layer.msg('请求失败：code' + err.status + "，msg：" + err.statusText, {time: 2000});
                    }
                });


                return false;
            });*/

            $("#return_list").click(function (){
                var url = '{{ route('m.getcoin.admin.quest_list.index') }}';
                location.href = url;
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
