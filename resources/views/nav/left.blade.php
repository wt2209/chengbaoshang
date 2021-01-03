<div style="height: 50px;display:flex;align-items:center;">
    <div class="btn-group btn-group-xs" role="group" style="margin-left:20px;">
        <a href="{{route('admin.livings.index')}}" type="button" class="btn btn-success">居住信息</a>
        <a href="{{route('admin.livings.create')}}"  type="button" class="btn btn-success">入住</a>
        <a type="button" class="btn btn-success">退房</a>
    </div>
    <div class="btn-group btn-group-xs" role="group" style="margin-left:10px;">
        <a href="{{route('admin.deposits.index')}}" type="button" class="btn btn-warning">房屋押金</a>
        <a href="{{route('admin.rents.index')}}" type="button" class="btn btn-warning">预交费房租</a>
        <a href="{{route('admin.reports.index')}}" type="button" class="btn btn-warning">月度报表</a>
        <a href="{{route('admin.bills.index')}}" type="button" class="btn btn-warning">其他费用</a>
    </div>
</div>
