<template>
  <div id="order" class="subpage">
    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>商城</b></el-breadcrumb-item>
        <el-breadcrumb-item>退换货管理</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>
    <div id="content" class="content">

      <div class="xcx-heads">
        <div class="model" style="width:100%;margin:0;">
          <el-input clearable style="width: 270px;" placeholder="请输入内容" v-model="input_content"
            class="input-with-select">
            <el-select clearable v-model="select_name" slot="append" placeholder="请选择" class="w120">
              <el-option label="退换货编号" value="1"></el-option>
              <el-option label="订单编号" value="2"></el-option>
            </el-select>
          </el-input>

          <span class="mar_L_10">状态：</span>
          <el-select clearable style="width:150px;" v-model="type" placeholder="请选择订单状态" class="title">
            <el-option label="全部" value="0"></el-option>
            <el-option label="退货" value="1"></el-option>
            <el-option label="换货" value="2"></el-option>
          </el-select>

          <span class="title">下单时间：</span>
          <el-date-picker style="width: 250px;" v-model="date" type="daterange" start-placeholder="开始日期"
            end-placeholder="结束日期" :default-time="['12:00:00']"></el-date-picker>

          <div class="" style="float: right;">
            <span style="width:80px;" class="xcx-add font14" size="mini" @click="search">筛选</span>
          </div>
        </div>
      </div>

      <div id="list">

        <!-- tab选项 -->
        <el-row class="xcx-tabs">
          <el-col :span="24" style="width: 80%">
            <el-button type="text" :class="(ord_type == 1) ? '': 'disable'" @click="handleClick(1)">全部</el-button>|
            <el-button type="text" :class="(ord_type == 8) ? '': 'disable'" @click="handleClick(8)">已取消</el-button>|
            <el-button type="text" :class="(ord_type == 2) ? '': 'disable'" @click="handleClick(2)">待处理</el-button>|
            <el-button type="text" :class="(ord_type == 3) ? '': 'disable'" @click="handleClick(3)">待用户发货</el-button>|
            <el-button type="text" :class="(ord_type == 4) ? '': 'disable'" @click="handleClick(4)">待收货</el-button>|
            <el-button type="text" :class="(ord_type == 6) ? '': 'disable'" @click="handleClick(6)">拒绝退换货</el-button>|
            <el-button type="text" :class="(ord_type == 5) ? '': 'disable'" @click="handleClick(5)">待退款</el-button>|
            <el-button type="text" :class="(ord_type == 7) ? '': 'disable'" @click="handleClick(7)">已完成</el-button>
          </el-col>
        </el-row>

        <div class="rebate">
          <div class="t_head">
            <span style="width: 35%;">商品</span>
            <span style="width: 10%;">单价/数量</span>
            <span style="width: 12%;">实付金额</span>
            <span style="width: 12%;">状态</span>
            <span style="width: 15%;">退换货原因</span>
            <span style="width: 15%;">操作</span>
          </div>
          <div class="t_body" v-for="(ele, i) in order_list" :key="i">
            <div class="logistics">订单号：{{ele.o_sn }} <span
                style="margin-left: 40px;color: #F56C6C;">退换货订单号：{{ele.sn }}</span> <span
                style="margin-left: 40px;">下单时间：{{ele.create_time }}</span>
            </div>
            <div class="goods_detail">
              <div style="width: 35%;">
                <img v-bind:src="ele.goods_pic" />

                <div class="character dis_sb" style="flex-direction: column; width:55%">
                  <p class=" shop_tit" style="color:#606266;margin: 0;word-break: break-all;">商品标题：{{ele.goods_name }}
                  </p>
                  <p style="color:#909399;">商品规格：{{ele.spec }}</p>
                </div>
              </div>
              <div class="felx" style="width: 10%;">
                <span class="text">¥{{ele.price + ' / ' + ele.count}}</span>
              </div>
              <div class="felx" style="width: 12%;">¥{{ele.total_price }}</div>
              <div class="felx" style="width: 12%;position: relative;" v-if="ele.return_status == 1">待平台处理</div>
              <div class="felx" style="width: 12%;position: relative;" v-if="ele.return_status == 2">已同意退换货</div>
              <div class="felx" style="width: 12%;position: relative;" v-if="ele.return_status == 3">待平台收货</div>
              <div class="felx" style="width: 12%;position: relative;" v-if="ele.return_status == 4">待退款</div>
              <div class="felx" style="width: 12%;position: relative;" v-if="ele.return_status == 5">拒绝退换货</div>
              <div class="felx" style="width: 12%;position: relative;" v-if="ele.return_status == 6">已取消</div>
              <div class="felx" style="width: 12%;position: relative;" v-if="ele.return_status == 7">已完成</div>
              <div class="felx" style="width: 15%;">{{ele.user_remark}}</div>
              <div class="felx" style="width: 15%;">

                <!-- 待处理 -->
                <div class="dis_fd" v-if="ele.return_status == 1">
                  <span class="text primary" style="color:#ff6c00;" @click="to_detail(ele.id)">去处理</span>
                </div>

                <!-- 同意 -->
                <div class="dis_fd" v-if="ele.return_status == 2">
                  <span class="text primary" @click="to_detail(ele.id)">查看订单</span>
                </div>

                <!-- 平台待收货 -->
                <div class="dis_fd" v-if="ele.return_status == 3">
                  <span class="text primary" @click="to_detail(ele.id)">查看订单</span>
                  <span class="text primary" style="color: #e6a23c;" @click="confirm_order(ele.id)">确认收货</span>
                </div>

                <!-- 收货完成 -->
                <div class="dis_fd" v-if="ele.return_status == 4">
                  <span class="text primary" @click="to_detail(ele.id)">查看订单</span>
                  <span class="text" style="color: #F56C6C;"
                    v-if="ele.type == 1 && ele.status == 2 && ele.is_audit == 1" @click="to_refund(i)">退款处理</span>
                </div>

                <!-- 不同意 -->
                <div class="dis_fd" v-if="ele.return_status == 5">
                  <span class="text primary" @click="to_detail(ele.id)">查看订单</span>
                </div>

                <!-- 已取消 -->
                <div class="dis_fd" v-if="ele.return_status == 6">
                  <span class="text primary" @click="to_detail(ele.id)">查看订单</span>
                </div>

                <!-- 已完成 -->
                <div class="dis_fd" v-if="ele.return_status == 7">
                  <span class="text primary" @click="to_detail(ele.id)">查看订单</span>
                </div>

                <!-- 同意 -->
                <div class="dis_fd" v-if="!ele.return_status">
                  <span class="text primary" @click="to_detail(ele.id)">查看订单</span>
                  <span class="text" style="color: #F56C6C;"
                    v-if="ele.type == 1 && ele.status == 2 && ele.is_audit == 1" @click="to_refund(i)">退款处理</span>
                </div>
              </div>
            </div>
            <div class="remarks" v-if="ele.seller_remark">卖家备注：{{ele.seller_remark }}</div>
          </div>
        </div>

        <!-- 退款 -->
        <el-dialog title="主动退款" :visible.sync="refund_dia" width="35%">
          <el-form ref="form" :model="form">
            <el-form-item label-width="125px" label="当前可退款金额：">¥{{totle_pic}}</el-form-item>
            <el-form-item label="">
              <el-input type="number" placeholder="请输入退款金额" v-model="refund_pic"></el-input>
            </el-form-item>
          </el-form>

          <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="confirm_refund()">处理</el-button>
            <el-button @click="refund_dia = false">返回</el-button>
          </span>
        </el-dialog>

        <div class="block" style="margin-left: -10px;">
          <el-pagination @current-change="handleCurrentChange" :current-page="page" :page-size="limit" background
            layout="prev, pager, next,slot" :total="count"></el-pagination>
          <span class="demonstration">共 {{ count }} 条 每页10条</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript" src="../../../static/js/return_goods.js"></script>
<style>
.shop_tit {
  width: 100%;
  display: -webkit-box;
  /*! autoprefixer: off */
  -webkit-box-orient: vertical;
  /*! autoprefixer: on */
  -webkit-line-clamp: 3;
  overflow: hidden;
}

/*引入公共样式*/
.subpage {
  width: 100%;
  padding: 140px 20px 20px 20px;
  background: #f0f2f6;
  position: relative;
  min-height: 100%;
  box-sizing: border-box;
  overflow: hidden;
}
.xcx-tabs {
  color: #ccc;
}

.xcx-tabs .el-button--text {
  margin-right: 10px;
}

.xcx-tabs .disable {
  color: #999;
}
</style>
