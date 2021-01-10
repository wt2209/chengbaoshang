<div class="row">
    <div class="col-md-12">
        <div class="box box-default" id="enter-operation">
            <div class="box-header with-border">
                <div class="box-title">共有 {{count($deposits) + count($rents) + count($reports) + count($bills)}} 条未缴费记录（按公司计算）</div>
                <div class="box-tools">

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>类型</th>
                                <th>当前公司名称</th>
                                <th>费用生成时公司名称</th>
                                <th>年月</th>
                                <th>金额</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deposits as $deposit)
                            <tr>
                                <td>押金</td>
                                <td>{{$deposit->current_company_name}}</td>
                                <td>{{$deposit->old_company_name}}</td>
                                <td></td>
                                <td>{{$deposit->money}}</td>
                                <td>
                                    <a class="btn btn-xs btn-success"
                                    href="{{route(
                                        'admin.deposits.index',
                                        [
                                            '9a842d720832a63471ef8e9de3640b75' => $deposit->current_company_name,
                                            '327da71ab6ad28f3e4bdb02a059f5817' => 'uncharged'
                                        ]
                                        )}}">去缴费</a>
                                </td>
                            </tr>
                            @endforeach

                            @foreach ($rents as $rent)
                            <tr>
                                <td>预交费房租</td>
                                <td>{{$rent->current_company_name}}</td>
                                <td>{{$rent->old_company_name}}</td>
                                <td>{{$rent->year}}-{{$rent->month}}</td>
                                <td>{{$rent->money}}</td>
                                <td>
                                    <a class="btn btn-xs btn-success"
                                    href="{{route(
                                        'admin.rents.index',
                                        [
                                            '89c2863fe54a1e0ef1458edcc0fd9350' => $rent->current_company_name,
                                            'e64cf1a1498b024943fd0ec3c5841695' => 'uncharged'
                                        ]
                                        )}}">去缴费</a>
                                </td>
                            </tr>
                            @endforeach

                            @foreach ($reports as $report)
                            <tr>
                                <td>月度报表</td>
                                <td>{{$report->current_company_name}}</td>
                                <td>{{$report->old_company_name}}</td>
                                <td>{{$report->year}}-{{$rent->month}}</td>
                                <td>{{$report->money}}</td>
                                <td>
                                    <a class="btn btn-xs btn-success"
                                    href="{{route(
                                        'admin.rents.index',
                                        [
                                            'a17d8c2b3979f6fa8686dacc804f8422' => $report->current_company_name,
                                            '8c2fef08e2439e3b2ec5ac9066d7dc4a' => 'discounted'
                                        ]
                                        )}}">去缴费</a>
                                </td>
                            </tr>
                            @endforeach

                            @foreach ($bills as $bill)
                            <tr>
                                <td>其他费用</td>
                                <td>{{$bill->current_company_name}}</td>
                                <td></td>
                                <td></td>
                                <td>{{$bill->money}}</td>
                                <td>
                                    <a class="btn btn-xs btn-success"
                                    href="{{route(
                                        'admin.rents.index',
                                        [
                                            '06fa1ac9d0305c812e07512fd230adad' => $bill->current_company_name,
                                            'fcc92bdd1fc2a17f621d3fc1471d7fb3' => 'uncharged'
                                        ]
                                        )}}">去缴费</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

