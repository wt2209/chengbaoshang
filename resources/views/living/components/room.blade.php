@foreach ($rooms as $room)
    <div class="col-sm-6 col-md-3 col-lg-2 room-container">
        <div class="room">
            <div class="header">
                <h3 class="room-title">
                    <a target="_blank" href="{{route('admin.rooms.index', ['title'=>'1-101'])}}">
                        {{$room->title}}
                        <span style="font-size: 14px">（{{$room->default_number}}人间）</span>
                    </a>
                </h3>
                <p class="room-remark">&nbsp;</p>
            </div>
            @if(count($room->records)>0)
                @foreach ($room->records as $record)
                    <div class="body">
                        <div class="room-content">
                                <p class="company-name">{{$record->company->company_name}}</p>
                                <p>入住：{{$record->entered_at}}&nbsp;&nbsp;&nbsp;{{$record->rent}}/月</p>
                                <p></p>
                                <p>
                                    {{$record->company->linkman}} {{$record->company->linkman_phone}}
                                </p>
                                @if ($record->has_lease)
                                <p>租期：{{$record->lease_start}}—{{$record->lease_end}}</p>
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
