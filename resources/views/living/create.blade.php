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
                <div class="col-md-8">
                    <div class="form-inline">
                        <div class="">
                            <label>选择公司：</label>
                            <select class="form-control select2" v-model="companyId" v-select="companyId" style="width: 300px;">
                                <option v-for="company in companies" :key="company.id" :value="company.id">@{{company.company_name}}</option>
                            </select>
                        </div>
                        <div class="">
                            <label>选择类型：</label>
                            <select class="form-control" v-model="categoryId">
                                <option v-for="category in categories" :key="category.id" :value="category.id">@{{category.title}}</option>
                            </select>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <tr>
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
                                <input type="text" v-model="selectedRooms[index].deposit" class="form-control input-sm" style="width: 80px;">
                            </td>
                            <td>
                                <input type="text" v-model="selectedRooms[index].rent" class="form-control input-sm" style="width: 80px;">

                            </td>
                            <td>
                                <input type="text" v-model="selectedRooms[index].electric_start_base" class="form-control input-sm" style="width: 80px;">

                            </td>
                            <td>
                                <input type="text" v-model="selectedRooms[index].water_start_base" class="form-control input-sm" style="width: 80px;">

                            </td>
                            <td>
                                <button class="btn btn-link btn-sm" @@click="remove(index)">删除</button>
                            </td>
                        </tr>
                    </table>
                    <button class="btn btn-success" @@click="submit">提交</button>
                </div>
                <div class="col-md-4">
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
    $(function() {

        // 注册select2指令
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
                companies: [],
                categories: [],
                companyId: undefined,
                categoryId: undefined,
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
                companyId: function() {
                    const company = this.companies.find(company => company.id === this.companyId)
                    this.categoryId = company.category_id
                    const category = this.categories.find(category => category.id === this.categoryId)
                }
            },
            methods: {
                select(index, e) {
                    if (!this.filteredEmptyRooms[index].selected) {
                        const current = this.filteredEmptyRooms[index]
                        console.log(current)
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
                submit() {
                    console.log(this.companyId)
                }
            },
        })

        // 不能放在vue上面，会出错
        $('.select2').select2()
    })
</script>