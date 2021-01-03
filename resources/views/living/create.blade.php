<div class="row">
    <div class="col-md-12">
        <div class="box box-info" id="enter-operation">
            <div class="box-header with-border">
                <div class="box-title">入住</div>
                <div class="box-tools">
                    <div class="btn-group pull-right" style="margin-right: 5px">
                        <a href="{{route('admin.livings.index')}}" class="btn btn-sm btn-default" title="居住信息"><i class="fa fa-list"></i><span class="hidden-xs">&nbsp;居住信息</span></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9">
                    <form class="form-inline enter-create-area" pjax-container method="POST" action="{{route('admin.livings.store')}}">
                        {{ csrf_field() }}
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul style="padding-left:10px">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="item">
                            <label>选择公司：</label>
                            <select class="form-control company-id select2-hidden-accessible" name="company_id" style="width: 300px;">
                                <option value=""></option>
                                @foreach($companies as $company)
                                <option data-category-id="{{$company->category_id}}" data-lease-end="{{$company->lease_end}}" value="{{$company->id}}">{{$company->company_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item">
                            <label>选择类型：</label>
                            <select class="form-control category-id select2-hidden-accessible" name="category_id" style="width: 300px;">
                                <option></option>
                                @foreach($categories as $category)
                                <option data-has-lease="{{$category->has_lease}}" value="{{$category->id}}">{{$category->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item" id="entered_at">
                            <label>入住时间：</label>
                            <input type="text" name="entered_at" class="form-control">
                        </div>
                        <div class="item">
                            <label>存在租期：</label>
                            <label style="font-weight: normal;">
                                <input type="radio" name="has_lease" value="1">&nbsp;是&nbsp;&nbsp;
                            </label>
                            <label style="font-weight: normal;">
                                <input type="radio" name="has_lease" value="0" checked>&nbsp;否&nbsp;&nbsp;
                            </label>
                            <span style="color:#777;font-size:12px;margin-left:20px;">
                                说明：若存在租期，则视为预交房租的房间，将自动生成整个租期的房租。
                            </span>
                        </div>
                        <div class="item" id="lease-start" style="display: none;">
                            <label>租期开始：</label>
                            <input type="text" name="lease_start" class="form-control">
                        </div>
                        <div class="item" id="lease-end" style="display: none;">
                            <label>租期结束：</label>
                            <input type="text" name="lease_end" class="form-control">
                        </div>
                        <div class="item" style="margin-top: 20px;">
                            <table class="table table-striped" id="selected-rooms" style="display: none;">
                                <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>区域</th>
                                        <th>房间号</th>
                                        <th>人数</th>
                                        <th>性别</th>
                                        <th>押金</th>
                                        <th>租金</th>
                                        <th>电表底数</th>
                                        <th>水表底数</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="item">
                            <button class="btn btn-primary" style="margin-top:20px;" disabled type="submit">提 交</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-3">
                    <div class="empty-room-container">
                        <div class="filter">
                            <input type="text" id="filter-input" class="form-control" placeholder="筛选">
                        </div>

                        <ul class="empty-room-list">
                            @foreach($emptyRooms as $room)
                            <li id="room-{{$room->id}}" ondblclick="selectRoom('{{$room->id}}')" data-id="{{$room->id}}" data-area="{{$room->area}}" data-title="{{$room->title}}" data-number="{{$room->default_number}}" data-deposit="{{$room->default_deposit}}" data-rent="{{$room->default_rent}}">
                                <div class="left">
                                    <span>{{$room->area}}</span>
                                    <span class="empty-room-title">{{$room->title}}</span>
                                    <span>{{$room->default_number}}人间</span>
                                </div>
                                <div class="right">
                                    <button onclick="selectRoom('{{$room->id}}')" class="btn btn-link btn-xs">选择</button>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // 已经选中的房间
    const selectedRooms = []
    // 选择房间
    function selectRoom(roomId) {
        const li = $('#room-' + roomId)
        const currentId = li.data('id')
        if (selectedRooms.findIndex(r => r.room_id === currentId) === -1) {
            const row = {
                room_id: li.data('id'),
                area: li.data('area'),
                title: li.data('title'),
                number: li.data('number'),
                deposit: li.data('deposit'),
                rent: li.data('rent'),
            }
            selectedRooms.push(row)
            $(`#room-${roomId} .right`).hide()
            render()
        }
    }
    // 删除已选房间
    function removeRoom(roomId) {
        const index = selectedRooms.findIndex(r => r.room_id === parseInt(roomId))
        if (index !== -1) {
            selectedRooms.splice(index, 1)
        }
        $(`#room-${roomId} .right`).show()
        render()
    }
    // 渲染页面
    function render() {
        if (selectedRooms.length > 0) {
            $('#selected-rooms').show()
            $('button[type=submit]').removeAttr('disabled')
        } else {
            $('#selected-rooms').hide()
            $('button[type=submit]').attr('disabled', true)
        }
        let html = ''
        selectedRooms.forEach((room, index) => {
            html += `<tr>
                <td>
                    ${index + 1}
                    <input value="${room.room_id}" name="rooms[${index}][room_id]" type="hidden" />
                </td>
                <td>${room.area}</td>
                <td>${room.title}</td>
                <td>${room.number}人间</td>
                <td>
                    <label style="font-weight: normal;">
                        <input type="radio" name="rooms[${index}][gender]" value="男" checked>&nbsp;男&nbsp;&nbsp;
                    </label>
                    <label style="font-weight: normal;">
                        <input type="radio" name="rooms[${index}][gender]" value="女">&nbsp;女&nbsp;&nbsp;
                    </label>
                </td>
                <td>
                    <input type="text" name="rooms[${index}][deposit]" value="${room.deposit}" class="form-control input-sm" style="max-width: 80px;">
                </td>
                <td>
                    <input type="text" name="rooms[${index}][rent]" value="${room.rent}" class="form-control input-sm" style="max-width: 80px;">
                </td>
                <td>
                    <input type="text" name="rooms[${index}][electric_start_base]" class="form-control input-sm" style="max-width: 80px;">
                </td>
                <td>
                    <input type="text" name="rooms[${index}][water_start_base]" class="form-control input-sm" style="max-width: 80px;">
                </td>
                <td>
                    <button class="btn btn-link btn-sm" onclick="removeRoom('${room.room_id}')">删除</button>
                </td>
            </tr>`
        })
        $('#selected-rooms tbody').html(html)
    }


    $(function() {
        $('.company-id').select2({
            allowClear: true,
            placeholder: {
                id: '',
                text: '请选择一个公司'
            }
        })
        $('.category-id').select2({
            allowClear: true,
            placeholder: {
                id: '',
                text: '请选择类型'
            }
        })

        $('#entered_at input').datetimepicker({
            date: new Date,
            locale: "zh-CN",
            format: 'YYYY-MM-DD',
            allowInputToggle: true,
        })
        $('#lease-end').datetimepicker({
            date: new Date,
            locale: "zh-CN",
            format: 'YYYY-MM-DD',
            allowInputToggle: true,
        })
        $('#lease-start input').datetimepicker({
            date: new Date,
            locale: "zh-CN",
            format: 'YYYY-MM-DD',
            allowInputToggle: true,
        })

        $('[name=company_id]').change(function() {
            const categoryId = this.options[this.options.selectedIndex].dataset.categoryId
            const leaseEnd = this.options[this.options.selectedIndex].dataset.leaseEnd
            // 当前公司的租期结束日（暂存到此）
            $('[name=company_id]').data('leaseEnd', leaseEnd)
            $('[name=category_id]').val(categoryId).trigger('change')
        })

        $('[name=category_id]').change(function() {
            const options = this.options
            const hasLease = !!parseInt(this.options[this.options.selectedIndex].dataset.hasLease) // 转成bool值

            if (hasLease) {
                $('[name=has_lease][value=0]').prop('checked', false)
                $('[name=has_lease][value=1]').prop("checked", true);
                $('#lease-start').show()
                $('#lease-end').show()
                // 之前暂存的日期
                const leaseEnd = $('[name=company_id]').data('leaseEnd')
                // 更新租期结束日的值
                $('#lease-end input').val(leaseEnd)
                $('#lease-end').datetimepicker('update')

            } else {
                $('[name=has_lease][value=1]').prop('checked', false)
                $('[name=has_lease][value=0]').prop("checked", true);
                $('#lease-start').hide()
                $('#lease-end').hide()
            }
        })
        $('[name=has_lease]').change(function(){
            if (parseInt($(this).val()) > 0) {
                $('#lease-start').show()
                $('#lease-end').show()
            } else {
                $('#lease-start').hide()
                $('#lease-end').hide()
            }
        })

        // 筛选
        $('#filter-input').on('input',function() {
            const text = $(this).val()
            if ($(this).val()) {
                $('.empty-room-title').each(function(index, element) {
                    if ($(element).html().indexOf(text) === -1) {
                        $(element).parents('li').hide()
                    } else {
                        $(element).parents('li').show()
                    }
                })
            } else {
                $('.empty-room-title').parents('li').show()
            }
        })
    })
</script>
