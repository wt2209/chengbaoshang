<div class="row">
    <div class="col-md-12">
        <div class="box grid-box">
            <div class="box-header with-border">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div class="search">
                        <form class="form-inline">
                            <div class="input-group">
                                <input type="text" class="form-control" style="width:300px;" placeholder="公司名、房间号">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <span class="icon glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </div><!-- /input-group -->
                        </form>
                    </div>
                    <div class="operations" style="text-align: right;">
                        <a href="#" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i>
                            &nbsp;&nbsp;入住
                        </a>
                        <a href="#" class="btn btn-warning btn-sm">
                            退房
                        </a>
                    </div>
                </div>
                <div class="building" style="margin-top: 10px;">
                    <ul class="nav navbar-nav">
                        @foreach($buildings as $building => $units)
                        <li class="dropdown">
                            <a id="drop1" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                {{$building}}
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="drop1">
                                @foreach($units as $unit)
                                <li>
                                    <a href="{{route('admin.livings.index', ['building'=>$building, 'unit'=>$unit])}}">
                                        {{$unit}}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="box-body bordered">
                @component('living.components.room')
                @endcomponent
            </div>
        </div>
    </div>
</div>