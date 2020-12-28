<style>
    .room {
        border: 1px solid #ddd;
        padding: 0px;
    }

    .room .header {
        border-bottom: 1px solid #ddd;
    }

    .room .header .room-title {
        line-height: 1.6em;
        margin: 6px 10px;
        font-size: 16px;
        font-weight: bold;
    }

    .room .header .room-remark {
        margin-left: 10px;
    }

    .room .body .room-content {
        padding: 10px;
        background-color: #5DADE2;
        min-height: 140px;
    }

    .room .body .room-content p {
        text-align: center;
    }

    .room .body .room-content .company-name {
        font-weight: bold;
    }

    .room .body .actions {
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: start;
        list-style-type: none;
        border-top: 1px solid #ddd;
    }

    .room .body .actions li {
        text-align: center;
        padding-top: 6px;
        padding-bottom: 6px;
        flex: 1;
        border-right: 1px solid #ddd;
    }

    .room .body .actions li:last-child {
        border-right: none;
    }
</style>

<div class="room col-md-3">
    <div class="header">
        <h3 class="room-title">QW1-101（6人间）</h3>
        <p class="room-remark">房间备注</p>
    </div>
    <div class="body">
        <div class="room-content">
            <p class="company-name">青岛润兴盛造修船有限公司</p>
            <p>2020-12-1 — 2022-12-31</p>
            <p>张三：13333333333</p>
            <p>张三：13333333333</p>
        </div>
        <ul class="actions">
            <li><a href="">修改</a></li>
            <li><a href="">续签</a></li>
            <li><a href="">操作1</a></li>
            <li><a href="">操作1</a></li>
        </ul>
    </div>
</div>

<div class="room col-md-3">
    <div class="header">
        <h3 class="room-title">QW1-101（6人间）</h3>
        <p class="room-remark">房间备注</p>
    </div>
    <div class="body">
        <div class="room-content">
        </div>
        <ul class="actions">
            <li><a href="">修改</a></li>
            <li><a href="">续签</a></li>
            <li><a href="">操作1</a></li>
            <li><a href="">操作1</a></li>
        </ul>
    </div>
</div>