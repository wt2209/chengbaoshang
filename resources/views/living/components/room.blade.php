@foreach ($rooms as $index => $room)
<div class="col-sm-6 col-md-3 room-container">
    <div class="room">
        <div class="header">
            <h3 class="room-title">
                <a target="_blank" href="{{route('admin.rooms.index', ['title'=>'1-101'])}}">
                    {{$room->title}}
                    <span style="font-size: 14px">
                        @if($room->default_number > 0)
                        （{{$room->default_number}}人间）
                        @endif
                    </span>
                </a>
                <span class="serial">{{$index+1}}</span>
            </h3>
            <p class="room-remark">{{$room->remark}}&nbsp;</p>
        </div>
        @if(count($room->records)>0)
        @foreach ($room->records as $record)
        <div class="body">
            <div class="room-content">
                <p class="company-name">{{$record->company->company_name}}</p>
                <p style="display:flex;">
                    <span style="flex:2;"> 属于：{{$record->category->title}}</span>
                    <span style="flex:1;text-align:right;">{{$record->rent}}/月</span>
                </p>
                <p>
                    入住日期：{{$record->entered_at}}
                </p>
                <p></p>
                <p>
                    日常联系人：{{$record->company->linkman}} {{$record->company->linkman_phone}}
                </p>
                @if($record->company->manager)
                <p>
                    公司负责人：{{$record->company->manager}} {{$record->company->manager_phone}}
                </p>
                @endif
                @if($record->business)
                <p>业务：{{$record->business}}</p>
                @endif
                @if ($record->has_lease)
                <p>租期：{{$record->lease_start}}—{{$record->lease_end}}</p>
                @endif
                @if ($record->remark)
                <p>备注：{{$record->remark}}</p>
                @endif
            </div>
            <ul class="actions">
                <li><a href="">详情</a></li>
                <li><a href="">修改</a></li>
                <li><a href="">操作1</a></li>
                <li><a href="">操作1</a></li>
            </ul>
        </div>
        @endforeach
        @else
        <div class="body">
            <div class="room-content"></div>
            <ul class="actions"></ul>
        </div>
        @endif
    </div>
</div>
@endforeach