@extends('core::admin.layouts.app')

@section('content')
<div class="layui-card">

    <div class="layui-row layui-col-space15">
        <div class="layui-col-md6">
            <form class="layui-form-item" action="{{ route('m.dsy.admin.user.tree') }}" method="get">
                <label class="layui-form-label" style="width: auto;">搜索UID手机或邮箱</label>
                <div class="layui-input-inline">
                    <input type="text" name="uid" id="uid" value="" lay-verify="required"
                           placeholder="请输入 UID 手机邮箱 快速查找" autocomplete="off" class="layui-input">
                </div>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="add">
                    查询团队会员
                </button>
            </form>

            @if ($son_num == 0)
            <p style="color: red">该会员下面没有下级会员</p>
            @endif
            <ul id="treeDemo" class="ztree" style="margin-top: 15px;"></ul>

        </div>
    </div>

</div>
@endsection

@push('after-scripts')
<script src="/zTree/js/jquery.js"></script>
<link rel="stylesheet" href="/zTree/css/zTreeStyle/zTreeStyle.css" media="all">
<script src="/zTree/js/jquery.ztree.all.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript">
    var setting = {
        async: {
            enable: true,
            url:'{{ route('m.dsy.api.admin.api.user.tree') }}?uid={{$user_id}}',
            autoParam:["user_id"],
            dataFilter: filter
        }
    };

    function filter(treeId, parentNode, childNodes) {
        console.log(childNodes);
        if (!childNodes) return null;
//			for (var i=0, l=childNodes.length; i<l; i++) {
//				childNodes[i].mobile = childNodes[i].mobile.replace(/\.n/g, '.');
//			}
        return childNodes['data'];
    }

    $(document).ready(function(){
        $.fn.zTree.init($("#treeDemo"), setting);
    });

</script>
<script>
    layui.use(['form', 'table', 'util', 'laydate'], function () {

        var $ = layui.$
            , util = layui.util
            , form = layui.form
            , table = layui.table
            , laydate = layui.laydate;

    })
</script>
@endpush
