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
                    <ul class="empty-room-list">
                        <li v-for="room in selectedRooms" :key="room.id">
                            @{{room.area}}
                            @{{room.title}}
                            @{{room.default_number}}人间
                        </li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="empty-room-container">
                        <ul class="empty-room-list">
                            <li v-for="(room, index) in emptyRooms" :key="room.id" @@dblclick="select(index)">
                                @{{room.area}}
                                @{{room.title}}
                                @{{room.default_number}}人间
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    var as = new Vue({
        el: '#enter-operation',
        data: {
            emptyRooms:[],
            selectedRooms:[],
        },
        created() {
            axios.get('empty-rooms').then(res=>{
                if (res.status === 200) {
                    this.emptyRooms = res.data
                }
            })
        },
        methods: {
            select(index){
                console.log(index)
                this.selectedRooms.push(this.emptyRooms[index])
                this.emptyRooms.splice(index, 1)
            }
        },
    })
})

</script>
