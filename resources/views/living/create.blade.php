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
                    <div class="form-inline enter-create-area">
                        <div class="item">
                            <label>选择公司：</label>
                            <select class="form-control select2" v-model="company_id" v-select="company_id" style="width: 300px;">
                                <option v-for="company in companies" :key="company.id" :value="company.id">@{{company.company_name}}</option>
                            </select>
                        </div>
                        <div class="item">
                            <label>选择类型：</label>
                            <select class="form-control" v-model="category_id">
                                <option v-for="category in categories" :key="category.id" :value="category.id">@{{category.title}}</option>
                            </select>
                        </div>
                        <div class="item">
                            <label>入住时间：</label>
                            <input type="text" style="padding-left:10px" v-model="entered_at" class="form-control">
                        </div>
                        <div class="item">
                            <label>存在租期：</label>
                            <label style="font-weight: normal;">
                                <input type="radio" v-model="has_lease" value="1">&nbsp;是&nbsp;&nbsp;
                            </label>
                            <label style="font-weight: normal;">
                                <input type="radio" v-model="has_lease" value="0">&nbsp;否&nbsp;&nbsp;
                            </label>
                            <span style="color:#777;font-size:12px;margin-left:20px;">
                                说明：若存在租期，则视为预交房租的房间，将自动生成整个租期的房租。
                            </span>
                        </div>
                        <div class="item" v-show="parseInt(has_lease)">
                            <label>租期开始：</label>
                            <input type="text" style="padding-left:10px" v-model="lease_start" class="form-control">
                        </div>
                        <div class="item" v-show="parseInt(has_lease)">
                            <label>租期结束：</label>
                            <input type="text" style="padding-left:10px" v-model="lease_end" class="form-control">
                        </div>
                        <div class="item" v-show="category_id && company_id">
                            <table class="table table-striped">
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
                                <tr v-for="(room, index) in selectedRooms" :key="room.id">
                                    <td>@{{index+1}}</td>
                                    <td>@{{room.area}}</td>
                                    <td>@{{room.title}}</td>
                                    <td>@{{room.number}}人间</td>
                                    <td>
                                        <label style="font-weight: normal;">
                                            <input type="radio" v-model="selectedRooms[index].gender" value="男">&nbsp;男&nbsp;&nbsp;
                                        </label>
                                        <label style="font-weight: normal;">
                                            <input type="radio" v-model="selectedRooms[index].gender" value="女">&nbsp;女&nbsp;&nbsp;
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" v-model="selectedRooms[index].deposit" class="form-control input-sm" style="max-width: 80px;">
                                    </td>
                                    <td>
                                        <input type="text" v-model="selectedRooms[index].rent" class="form-control input-sm" style="max-width: 80px;">
                                    </td>
                                    <td>
                                        <input type="text" v-model="selectedRooms[index].electric_start_base" class="form-control input-sm" style="max-width: 80px;">
                                    </td>
                                    <td>
                                        <input type="text" v-model="selectedRooms[index].water_start_base" class="form-control input-sm" style="max-width: 80px;">

                                    </td>
                                    <td>
                                        <button class="btn btn-link btn-sm" @@click="remove(index)">删除</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="item">
                            <button class="btn btn-primary" @@click="submit" :disabled="!(company_id && category_id && entered_at && selectedRooms.length>0)">提交</button>
                        </div>
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="empty-room-container">
                        <div class="filter">
                            <input type="text" class="form-control" placeholder="筛选" @@input="filter">
                        </div>

                        <ul class="empty-room-list">
                            <li v-for="(room, index) in filteredEmptyRooms" :key="room.id" @@dblclick.prevent.stop="select(index)">
                                <div class="left">
                                    <span>@{{room.area}}</span>
                                    <span>@{{room.title}}</span>
                                    <span>@{{room.default_number}}人间</span>
                                </div>
                                <div class="right">
                                    <button v-if="!room.selected" @@click="select(index)" class="btn btn-link btn-xs">选择</button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function today() {
        const t = new Date()
        const year = t.getFullYear()
        const month = t.getMonth()+1
        const day = t.getDate()
        return `${year}-${month}-${day}`
    }
    $(function() {
        // 注册select指令
        Vue.directive('select', {
            twoWay: true,
            bind: function(el, binding, vnode) {
                $(el).select2().on("select2:select", (e) => {
                    // v-model looks for
                    //  - an event named "change"
                    //  - a value with property path "$event.target.value"
                    el.dispatchEvent(new Event('change', {
                        target: e.target
                    }));
                });
            },
        });

        var app = new Vue({
            el: '#enter-operation',
            data: {
                // form items
                company_id: undefined,
                category_id: undefined,
                has_lease: false,
                lease_start:today(),
                lease_end: undefined,
                entered_at: today(),

                // vars
                companies: [],
                categories: [],
                emptyRooms: [],
                filteredEmptyRooms: [],
                selectedRooms: [],
            },
            created() {
                axios.get('empty-rooms').then(res => {
                    if (res.status === 200) {
                        this.emptyRooms = res.data
                        this.filteredEmptyRooms = this.emptyRooms
                    }
                })
                axios.get('all-companies').then(res => {
                    if (res.status === 200) {
                        this.companies = res.data
                    }
                })
                axios.get('all-categories').then(res => {
                    if (res.status === 200) {
                        this.categories = res.data
                    }
                })
            },
            watch: {
                company_id: function() {
                    const company = this.companies.find(company => company.id === this.company_id)
                    this.category_id = company.category_id
                    const category = this.categories.find(category => category.id === this.category_id)
                    if (company.lease_start) {
                        this.lease_start = company.lease_start
                    }
                    if (company.lease_end) {
                        this.lease_end = company.lease_end
                    }
                },
                category_id: function() {
                    const category = this.categories.find(c=>c.id === this.category_id)
                    this.has_lease = category.has_lease
                }
            },
            methods: {
                select(index, e) {
                    // 基础信息未选择完毕，不能选择房间
                    if (!this.company_id || !this.category_id) {
                        return
                    }
                    if (!this.filteredEmptyRooms[index].selected) {
                        const current = this.filteredEmptyRooms[index]
                        this.selectedRooms.push({
                            area: current.area,
                            title: current.title,
                            number: current.default_number,
                            room_id: current.id,
                            gender: '男',
                            rent: current.default_rent,
                            deposit: current.default_deposit,
                            electric_start_base: undefined,
                            water_start_base: undefined,
                        })
                        this.filteredEmptyRooms[index].selected = true
                    }
                },
                filter(e) {
                    this.filteredEmptyRooms = this.emptyRooms.filter(room => room.title.indexOf(e.target.value) > -1)
                },
                remove(index) {
                    const roomId = this.selectedRooms[index].room_id
                    // 要删除的房间
                    const removedRoom = this.filteredEmptyRooms.find(item => item.id === roomId)
                    // 恢复选中状态
                    removedRoom.selected = false
                    // 在选中的房间中删除
                    this.selectedRooms.splice(index, 1)
                },
                isValidDate(dateStr) {
                    const date = new Date(dateStr)
                    return !isNaN(date.getTime())
                },
                submit() {
                    const result = {
                        company_id: this.company_id,
                        category_id: this.category_id,
                        has_lease: this.has_lease,
                        lease_start: this.lease_start,
                        lease_end: this.lease_end,
                        entered_at: this.entered_at,
                        rooms: this.selectedRooms
                    }

                    toastr.options.positionClass='toast-top-center'
                    if (result.rooms.find(room=>!room.rent || !room.deposit )) {
                        toastr.error('押金和租金必须填写', '错误：');
                        return
                    }
                    if (!this.isValidDate(result.entered_at)) {
                        toastr.error('入住日期格式错误', '错误：');
                        return
                    }
                    if (parseInt(result.has_lease)) {
                        if (!this.isValidDate(result.lease_start)) {
                            toastr.error('租期开始日期格式错误', '错误：');
                            return
                        }
                        if (!this.isValidDate(result.lease_end)) {
                            toastr.error('租期结束日期格式错误', '错误：');
                            return
                        }
                    }
                    axios.post('store', result).then(res=>{
                        if (res.status === 200) {
                            this.selectedRooms = []
                            toastr.success('操作成功');
                        }
                    })
                }
            },
        })

        // 不能放在vue上面，会出错
        $('.select2').select2()
    })
</script>
