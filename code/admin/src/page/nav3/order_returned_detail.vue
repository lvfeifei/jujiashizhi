<template>
  <div id="order_detail">
    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>商城</b></el-breadcrumb-item>
        <el-breadcrumb-item>退换货管理</el-breadcrumb-item>
        <el-breadcrumb-item>查看订单</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>
    <!--基本信息-->
    <div class="model">
      <div class="content">
        <div class="head">订单流程</div>

        <el-steps :active="order_status" align-center>
          <el-step title="买家提交申请" :description="order_data.create_time ? order_data.create_time : ''"></el-step>
          <el-step title="卖家审核" :description="order_data.audit_time ? order_data.audit_time : ''"></el-step>
          <el-step title="买家提交退货单" :description="order_data.user_return_time ? order_data.user_return_time : ''">
          </el-step>
          <el-step title="卖家验收无误" :description="order_data.delivery_time ? order_data.delivery_time : ''"></el-step>
          <el-step title="退款成功" :description="order_data.last_time ? order_data.last_time : ''"></el-step>
        </el-steps>
      </div>

      <div class="content2">

        <div v-if="order_data.return_status == 1" style="float: left;width: 49.5%;height: 390px; background: #fff;">
          <div class="head">退货流程</div>
          <div style="padding: 0 0 0 40px;">
            <p style="font-size:14px;color:#303133;"><span>订单编号：</span>{{ order_data.sn }}</p>
            <p style="font-size:14px;color:#303133;"><span>买家昵称：</span>{{ order_data.user_name }}</p>
            <p style="font-size:14px;color:#303133;"><span>姓名：</span>{{ order_data.consignee }}</p>
            <p style="font-size:14px;color:#303133;"><span>手机号码：</span>{{ order_data.mobile }}</p>
            <p style="font-size:14px;color:#303133;">
              <span>收货地址：</span>{{order_data.province + order_data.city + order_data.address}}</p>
          </div>
        </div>

        <div style="float: left;width:100%;height: 390px; background: #fff;" v-else>
          <div class="head">退货流程</div>
          <div style="padding: 0 0 0 40px;">
            <p style="font-size:14px;color:#303133;"><span>订单编号：</span>{{ order_data.sn }}</p>
            <p style="font-size:14px;color:#303133;"><span>买家昵称：</span>{{ order_data.user_name }}</p>
            <p style="font-size:14px;color:#303133;"><span>姓名：</span>{{ order_data.consignee }}</p>
            <p style="font-size:14px;color:#303133;"><span>手机号码：</span>{{ order_data.mobile }}</p>
            <p style="font-size:14px;color:#303133;">
              <span>收货地址：</span>{{order_data.province + order_data.city + order_data.address}}</p>
          </div>
        </div>

        <!-- 买家提交申请  待处理 -->
        <div v-if="order_data.return_status == 1" style="float: right;width: 49.5%;height: 390px;background: #fff;">
          <div class="head">订单状态</div>
          <div>
            <p class="text_C mar_T_50">订单状态: 买家提交申请 等待卖家审核</p>

            <p class="do_soming mar_T_30">
              <el-button @click="refuse_return()">拒绝退换货</el-button>
              <el-button type="primary" @click="agreed_to_return()">同意退货</el-button>
              <el-button type="primary" @click="remarks()">备注</el-button>
            </p>

            <div class="line_order"></div>
            <div class="mar_L_30">
              <div class="mar_T_10 font14">退货原因：{{order_data.user_remark}}</div>
              <div class="mar_T_10 font14">系统备注：{{order_data.system_remark}}</div>
            </div>
          </div>
        </div>

        <!-- 同意等等用户发货 -->
        <div v-if="order_data.return_status == 2" style="float: right;width: 49.5%;height: 390px;background: #fff;">
          <div class="head">订单状态</div>
          <div>
            <p class="text_C mar_T_50">订单状态: 同意退货 等待用户填写退货单</p>

            <p class="do_soming mar_T_30">
              <el-button type="primary" @click="remarks()">备注</el-button>
            </p>

            <div class="line_order"></div>
            <div class="mar_L_30">
              <div class="mar_T_10 font14">退货原因：{{order_data.user_remark}}</div>
              <div class="mar_T_10 font14">系统备注：{{order_data.system_remark}}</div>
            </div>
          </div>
        </div>

        <!-- 用户发货等等商家收货  -->
        <div v-if="order_data.return_status == 3" style="float: right;width: 49.5%;height: 390px;background: #fff;">
          <div class="head">订单状态</div>
          <div>
            <p class="text_C mar_T_50">订单状态: 买家提交退货单 等待验收</p>

            <p class="do_soming mar_T_30">
              <el-button type="primary" @click="confirm_receipt()">确认收货</el-button>
              <el-button type="primary" @click="remarks()">备注</el-button>
            </p>

            <div class="line_order"></div>
            <div class="mar_L_30">
              <div class="mar_T_10 font14">退货原因：{{order_data.user_remark}}</div>
              <div class="mar_T_10 font14">系统备注：{{order_data.system_remark}}</div>
              <div class="mar_T_10 mar_R_50 font14 flex_ flex_jsb">
                <span>退货单号：{{order_data.express_name + ' ' + order_data.express_sn}}</span>
                <span>{{order_data.user_return_time}}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 商家收货 -->
        <div v-if="order_data.return_status == 4" style="float: right;width: 49.5%;height: 390px;background: #fff;">
          <div class="head">订单状态</div>
          <div>
            <p class="text_C mar_T_50">订单状态: 已完成</p>

            <p class="do_soming mar_T_30">
              <el-button type="primary" @click="remarks()">备注</el-button>
            </p>

            <div class="line_order"></div>
            <div class="mar_L_30">
              <div class="mar_T_10 font14">退货原因：{{order_data.user_remark}}</div>
              <div class="mar_T_10 font14">系统备注：{{order_data.system_remark}}</div>
              <div class="mar_T_10 mar_R_50 font14 flex_ flex_jsb">
                <span>退货单号：{{order_data.express_name + ' ' + order_data.express_sn}}</span>
                <span>{{order_data.user_return_time}}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 审核没通过 -->
        <div v-if="order_data.return_status == 5" style="float: right;width: 49.5%;height: 390px;background: #fff;">
          <div class="head">订单状态</div>
          <div>
            <p class="text_C mar_T_50">订单状态: 拒绝退货</p>

            <p class="do_soming mar_T_30">
              <el-button type="primary" @click="remarks()">备注</el-button>
            </p>

            <div class="line_order"></div>
            <div class="mar_L_30">
              <div class="mar_T_10 font14">退货原因：{{order_data.user_remark}}</div>
              <div class="mar_T_10 font14">系统备注：{{order_data.system_remark}}</div>
              <div class="mar_T_10 font14">拒绝退换货原因：{{order_data.refuse}}</div>
            </div>
          </div>
        </div>

        <!-- 已取消 -->
        <div v-if="order_data.return_status == 6" style="float: right;width: 49.5%;height: 390px;background: #fff;">
          <div class="head">订单状态</div>
          <div>
            <p class="text_C mar_T_50">订单状态: 已取消</p>

            <p class="do_soming mar_T_30">
              <el-button type="primary" @click="remarks()">备注</el-button>
            </p>

            <div class="line_order"></div>
            <div class="mar_L_30">
              <div class="mar_T_10 font14">退货原因：{{order_data.user_remark}}</div>
              <div class="mar_T_10 font14">系统备注：{{order_data.system_remark}}</div>
            </div>
          </div>
        </div>

        <!-- 拒绝退货原因 -->
        <el-dialog title="拒绝退货原因" :visible.sync="refuse_return_dia" width="40%" :before-close="handleClose">

          <el-input type="textarea" v-model="refuse_return_text" placeholder="请输入拒绝退款原因" rows="6"></el-input>

          <span slot="footer" class="dialog-footer">
            <el-button @click="refuse_return_dia = false">取 消</el-button>
            <el-button type="primary" @click="con_refuse_return">确 定</el-button>
          </span>
        </el-dialog>

        <!-- 备注 -->
        <el-dialog title="备注" :visible.sync="remarks_dia" width="40%" :before-close="handleClose">

          <el-input type="textarea" v-model="order_data.system_remark" placeholder="请输入备注" rows="6"></el-input>

          <span slot="footer" class="dialog-footer">
            <el-button @click="remarks_dia = false">取 消</el-button>
            <el-button type="primary" @click="confirm_remarks()">确 定</el-button>
          </span>
        </el-dialog>

      </div>

      <!-- 商品信息表格 -->
      <div class="content mar_T_30" style="padding: 0 0 30px 0;">
        <div class="head">商品信息</div>

        <div style="padding: 30px;">
          <el-table :data="order_data.order_goods" border style="width: 100%">
            <el-table-column label="商品" width="400">
              <div slot-scope="scope" class="shop_box">
                <img :src="scope.row.picture" alt="" style="width: 120px; height: 120px;" />

                <div class="shop_box_info">
                  <div class="shop_tit">{{scope.row.goods_name}}</div>
                  <div>规格：{{scope.row.spec}}</div>
                </div>
              </div>
            </el-table-column>
            <el-table-column prop="price" label="单价"></el-table-column>
            <el-table-column prop="count" label="数量"></el-table-column>
            <el-table-column prop="total_price" label="小计"></el-table-column>
          </el-table>

          <div class="dis_sb font14 mar_T_20" style="align-items: center;">
            <div></div>

            <div>
              <span class=" mar_T_20 w120 text_R">实付总价：</span>
              <span class="mar_L_20 mar_R_10 danger">¥{{order_data.total_price}}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript" src="../../../static/js/order_returned_detail.js"></script>

<style>
.shop_box {
  display: flex;
  align-content: center;
}

.shop_box_info {
  width: 55%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  text-align: left;
  margin-left: 10px;
}

.shop_tit {
  width: 100%;
  display: -webkit-box; 
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3; 
  overflow: hidden; 
}
@import "../../../static/css/shop_global.css";
/*引入公共样式*/
.text_R {
  text-align: right;
}

.text_C {
  text-align: center;
}
.do_soming {
  display: flex;
  justify-content: center;
}

.do_soming_fdc {
  display: flex;
  justify-content: center;
  flex-direction: column;
}

.do_soming_fdc .text {
  margin: 5px 0;
  cursor: pointer;
}

.do_soming .text {
  margin: 0 10px;
  cursor: pointer;
}
.mar_T_10 {
  margin-top: 10px;
}

.line_order {
  width: 100%;
  height: 1px;
  background-color: #dcdfe6;
  margin-top: 50px;
}
/* 左 边距 */
.mar_L_10 {
  margin-left: 10px;
}

.mar_L_20 {
  margin-left: 20px;
}

.mar_L_30 {
  margin-left: 30px;
}

.mar_L_50 {
  margin-left: 50px !important;
}

/* 右 边距 */
.mar_R_10 {
  margin-right: 10px;
}

.mar_R_20 {
  margin-right: 20px;
}

.mar_R_30 {
  margin-right: 30px;
}

.mar_R_50 {
  margin-right: 50px;
}
.text_R {
  text-align: right;
}

.text_C {
  text-align: center;
}
.dis_sb {
  display: flex !important;
  justify-content: space-between;
}
.el-button--primary.is-plain {
  color: #409eff;
  background: #fff;
  border-color: #409eff;
}
</style>
