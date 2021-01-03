<div class="row">
    <div class="col-md-12">
        <div class="box box-info" id="enter-operation">
            <div class="box-header with-border">
                <div class="box-title">退房</div>
                <div class="box-tools">
                    <div class="btn-group pull-right" style="margin-right: 5px">
                        <a href="{{route('admin.livings.index')}}" class="btn btn-sm btn-default" title="居住信息"><i class="fa fa-list"></i><span class="hidden-xs">&nbsp;居住信息</span></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9">
                    <form class="form-inline enter-create-area" pjax-container method="POST" action="{{route('admin.livings.delete')}}">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="delete" />

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
                                <option value="{{$company->id}}">{{$company->company_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item" id="quitted_at">
                            <label>退房时间：</label>
                            <input type="text" name="quitted_at" class="form-control">
                        </div>
                        <div class="item" style="margin-top: 20px;">
                            <table class="table table-striped" id="selected-records" style="display: none;">
                                <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>区域</th>
                                        <th>记录号</th>
                                        <th>人数</th>
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
                        <ul class="empty-room-list" id="records-list"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // 已经选中的记录
    const selectedRecords = []
    // 选择记录
    function selectRecord(recordId) {
        const li = $('#record-' + recordId)
        const currentId = li.data('id')
        if (selectedRecords.findIndex(r => r.id === currentId) === -1) {
            const row = {
                id: li.data('id'),
                area: li.data('area'),
                title: li.data('title'),
                number: li.data('number'),
            }
            selectedRecords.push(row)
            $(`#record-${currentId} .right`).hide()
            render()
        }
    }
    // 删除已选记录
    function removeRecord(recordId) {
        const index = selectedRecords.findIndex(r => r.id === parseInt(recordId))
        if (index !== -1) {
            selectedRecords.splice(index, 1)
        }
        $(`#record-${recordId} .right`).show()
        render()
    }
    // 渲染页面
    function render() {
        if (selectedRecords.length > 0) {
            $('#selected-records').show()
            $('button[type=submit]').removeAttr('disabled')
        } else {
            $('#selected-records').hide()
            $('button[type=submit]').attr('disabled', true)
        }
        let html = ''
        selectedRecords.forEach((record, index) => {
            html += `<tr>
                <td>
                    ${index + 1}
                    <input value="${record.id}" name="records[${index}][id]" type="hidden" />
                </td>
                <td>${record.area}</td>
                <td>${record.title}</td>
                <td>${record.number}人间</td>
                <td>
                    <input type="text" name="records[${index}][electric_end_base]" class="form-control input-sm" style="max-width: 80px;">
                </td>
                <td>
                    <input type="text" name="records[${index}][water_end_base]" class="form-control input-sm" style="max-width: 80px;">
                </td>
                <td>
                    <button class="btn btn-link btn-sm" onclick="removeRecord('${record.id}')">删除</button>
                </td>
            </tr>`
        })
        $('#selected-records tbody').html(html)
    }

    $(function() {
        $('.company-id').select2({
            allowClear: true,
            placeholder: {
                id: '',
                text: '请选择一个公司'
            }
        })

        $('#quitted_at input').datetimepicker({
            date: new Date,
            locale: "zh-CN",
            format: 'YYYY-MM-DD',
            allowInputToggle: true,
        })

        $('[name=company_id]').change(function(e) {
            const companyId = e.target.value
            $.get('records/' + companyId, function(records, status) {
                if (status === 'success') {
                    let html = '';
                    if (records.length === 0) {
                        html = `<li>
                            <div class="left">
                                <span>该公司不存在入住记录<\/span>
                            <\/div>
                        </li>`
                    } else {
                        records.forEach(record => {
                            html += `<li ondblclick="selectRecord(${record.id})" id="record-${record.id}" data-id="${record.id}" data-area="${record.room.area}" data-title="${record.room.title}" data-number="${record.room.default_number}">
                            <div class="left">
                                <span>${record.room.area}<\/span>
                                <span class="empty-room-title">${record.room.title}<\/span>
                                <span>${record.room.default_number}人间<\/span>
                            <\/div>
                            <div class="right">
                                <button onclick="selectRecord(${record.id})" class="btn btn-link btn-xs">选择<\/button>
                            <\/div>
                        <\/li>`
                        })
                    }
                    $('#records-list').html(html)
                }
            })
        })

        // 筛选
        $('#filter-input').on('input', function() {
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