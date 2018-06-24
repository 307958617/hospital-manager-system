@extends('layouts.app')

@section('head')
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/Nestable/2012-10-15/jquery.nestable.min.js"></script>
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .dd{position:relative;display:block;margin:0;padding:0;max-width:600px;list-style:none;line-height:20px}.dd-list{display:block;position:relative;margin:0;padding:0;list-style:none}.dd-list .dd-list{padding-left:30px}.dd-collapsed .dd-list{display:none}.dd-item,.dd-empty,.dd-placeholder{display:block;position:relative;margin:0;padding:0;min-height:20px;line-height:20px}.dd-handle,.dd2-content{display:block;min-height:38px;margin:5px 0;padding:8px 12px;background:#f8faff;border:1px solid #dae2ea;color:#7c9eb2;text-decoration:none;font-weight:bold;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.dd-handle:hover,.dd2-content:hover{color:#438eb9;background:#f4f6f7;border-color:#dce2e8}.dd-handle[class*="btn-"],.dd2-content[class*="btn-"]{color:#FFF;border:0;padding:9px 12px}.dd-handle[class*="btn-"]:hover,.dd2-content[class*="btn-"]:hover{opacity:.85;color:#FFF}.dd2-handle+.dd2-content,.dd2-handle+.dd2-content[class*="btn-"]{padding-left:44px}.dd-handle[class*="btn-"]:hover,.dd2-content[class*="btn-"] .dd2-handle[class*="btn-"]:hover+.dd2-content[class*="btn-"]{color:#FFF}.dd-item>button:hover ~ .dd-handle,.dd-item>button:hover ~ .dd2-content{color:#438eb9;background:#f4f6f7;border-color:#dce2e8}.dd-item>button:hover ~ .dd-handle[class*="btn-"],.dd-item>button:hover ~ .dd2-content[class*="btn-"]{opacity:.85;color:#FFF}.dd2-handle:hover ~ .dd2-content{color:#438eb9;background:#f4f6f7;border-color:#dce2e8}.dd2-handle:hover ~ .dd2-content[class*="btn-"]{opacity:.85;color:#FFF}.dd2-item.dd-item>button{margin-left:34px}.dd-item>button{display:block;position:relative;z-index:1;cursor:pointer;float:left;width:25px;height:20px;margin:5px 1px 5px 5px;padding:0;text-indent:100%;white-space:nowrap;overflow:hidden;border:0;background:transparent;font-size:12px;line-height:1;text-align:center;font-weight:bold;top:4px;left:1px;color:#707070}.dd-item>button:before{font-family:FontAwesome;content:'\f067';display:block;position:absolute;width:100%;text-align:center;text-indent:0;font-weight:normal;font-size:14px}.dd-item>button[data-action="collapse"]:before{content:'\f068'}.dd-item>button:hover{color:#707070}.dd-item.dd-colored>button,.dd-item.dd-colored>button:hover{color:#EEE}.dd-placeholder,.dd-empty{margin:5px 0;padding:0;min-height:30px;background:#f0f9ff;border:2px dashed #bed2db;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.dd-empty{border-color:#AAA;border-style:solid;background-color:#e5e5e5}.dd-dragel{position:absolute;pointer-events:none;z-index:999;opacity:.8}.dd-dragel>li>.dd-handle{color:#4b92be;background:#f1f5fa;border-color:#d6e1ea;border-left:2px solid #777;position:relative}.dd-dragel>li>.dd-handle[class*="btn-"]{color:#FFF}.dd-dragel>.dd-item>.dd-handle{margin-top:0}.dd-list>li[class*="item-"]{border-width:0;padding:0}.dd-list>li[class*="item-"]>.dd-handle{border-left:2px solid;border-left-color:inherit}.dd-list>li>.dd-handle .sticker{position:absolute;right:0;top:0}.dd2-handle,.dd-dragel>li>.dd2-handle{left:0;top:0;width:36px;margin:0;border-width:1px 1px 0 0;text-align:center;padding:0!important;line-height:38px;height:38px;background:#ebedf2;border:1px solid #dee4ea;cursor:pointer;overflow:hidden;position:absolute;z-index:1}.dd2-handle:hover,.dd-dragel>li>.dd2-handle{background:#e3e8ed}.dd2-content[class*="btn-"]{text-shadow:none!important}.dd2-handle[class*="btn-"]{text-shadow:none!important;background:rgba(0,0,0,0.1)!important;border-right:1px solid #EEE}.dd2-handle[class*="btn-"]:hover{background:rgba(0,0,0,0.08)!important}.dd-dragel .dd2-handle[class*="btn-"]{border-color:transparent;border-right-color:#EEE}.dd2-handle.btn-yellow{text-shadow:none!important;background:rgba(0,0,0,0.05)!important;border-right:1px solid #FFF}.dd2-handle.btn-yellow:hover{background:rgba(0,0,0,0.08)!important}.dd-dragel .dd2-handle.btn-yellow{border-color:transparent;border-right-color:#FFF}.dd-item>.dd2-handle .drag-icon{display:none}.dd-dragel>.dd-item>.dd2-handle .drag-icon{display:inline}.dd-dragel>.dd-item>.dd2-handle .normal-icon{display:none}.dropzone{border-radius:0;border:1px solid rgba(0,0,0,0.06)}
    </style>
@endsection

@section('content')
<div class="container">
    <div id="xx">123</div>
    <department-component></department-component>
</div>
@endsection

@section('js')
    <script type="text/javascript">
        jQuery(function($){
            $('.dd').nestable();
            $('#nestable').nestable().on('change', function(){
                var r = $('.dd').nestable('serialize');
//                console.log(r);
                $.post('/department/change',{'_token':'{{csrf_token()}}','tree':r},function(res){
                    console.log(res)
                });
                $("#xx").html(JSON.stringify(r));    //改变排序之后的数据
            });
        });
    </script>
@endsection