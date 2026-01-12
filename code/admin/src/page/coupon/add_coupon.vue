<template>
  <div style="padding-bottom: 120px;" id="subpage">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item>
        <b>优惠券</b>
      </el-breadcrumb-item>
      <el-breadcrumb-item>优惠券管理</el-breadcrumb-item>
      <el-breadcrumb-item>{{ coupon_id ? '修改' :'添加' }}优惠券</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content mb30">
      <div class="xcx-head" style="margin:0;">
        <span class="title">{{ coupon_id ? '修改' :'添加' }}优惠券</span>
      </div>
      <div class="xcx-content" style="padding: 0;">
        <div class="info_item">
          <div>

            <el-form ref="form_data" :model="form_data" class="mar_L_20 mar_T_30 user_info" :rules="rules"
              label-width="140px" style="width: 80%;">
              <el-form-item class="text_ccc" label="优惠券名称：" prop="name">
                <el-input class="w400" v-model="form_data.name" placeholder="优惠券名称"></el-input>
              </el-form-item>

              <el-form-item class="text_ccc" label="优惠券类型：" prop="type">
                <el-radio v-model="form_data.type" label="1">直减券</el-radio>
                <el-radio v-model="form_data.type" label="2">满减券</el-radio>
              </el-form-item>

              <el-form-item label="直减金额：" prop="price" v-if="form_data.type == 1">
                <el-input class="w400" v-model="form_data.price" placeholder="金额"></el-input> 元
              </el-form-item>

              <el-form-item class="text_ccc" label="满减优惠券：" prop="total_price" v-if="form_data.type == 2">
                满 <el-input class="w100" v-model="form_data.total_price" placeholder="金额"></el-input> 元，减 <el-input class="w100" v-model="form_data.price" placeholder="金额">
                </el-input> 元
              </el-form-item>

              <el-form-item label="使用范围：" prop="range">
                <el-radio v-model="form_data.range" label="1">全场优惠券</el-radio>
                <el-radio v-model="form_data.range" label="2">指定商品优惠券</el-radio>
              </el-form-item>

              <el-form-item label="指定商品：" prop="check_goods_list" v-if="form_data.range == 2">
                <el-button type="primary" @click="relation_shop()">关联指定商品</el-button>
                <el-table class="mar_T_10" border :data="form_data.check_goods_list" stripe style="width: 100%">
                  <el-table-column prop="nickname" label="商品信息">
                    <div slot-scope="scope">
                      <div class="shop_info">
                        <div class="shop_info_img"><img :src="scope.row.picture" alt=""></div>
                        <div class="shop_info_txts">
                          <div class="info_txts_title">{{ scope.row.title }}</div>
                          <div class="info_txts_price">￥{{ scope.row.min_price }}</div>
                        </div>
                      </div>
                    </div>
                  </el-table-column>
                  <el-table-column prop="sup_count" label="库存" width="120"></el-table-column>
                  <el-table-column prop="status" label="状态" width="120">
						<div slot-scope="scope">
							<div v-if="scope.row.status==1">出售中</div>
							<div v-if="scope.row.status==2">已下架</div>
							<div v-if="scope.row.status==3">已售罄</div>
				  		</div>
				  </el-table-column>
                  <el-table-column label="操作" width="120">
                    <div slot-scope="scope" class="font14" style="cursor:pointer;">
                      <span class="mar_L_10 text danger"
                        @click="del_goods(scope.$index,form_data.check_goods_list)">取消关联</span>
                    </div>
                  </el-table-column>
                </el-table>
              </el-form-item>

              <el-form-item label="日期设置：" prop="date_type">
                <el-radio v-model="form_data.date_type" label="1">指定使用时间</el-radio>
                <el-radio v-model="form_data.date_type" label="2">领取后开始计算</el-radio>
              </el-form-item>

              <el-form-item label="使用时间：" prop="day"  v-if="form_data.date_type == 2">
                <el-input class="w400" v-model="form_data.day" placeholder="天数"></el-input> 天
              </el-form-item>

              <el-form-item label="使用时间：" prop="date" v-if="form_data.date_type == 1">
                <el-date-picker v-model="form_data.date" value-format="yyyy-MM-dd" type="daterange" range-separator="至" start-placeholder="开始日期"
                  end-placeholder="结束日期">
                </el-date-picker>
              </el-form-item>

              <el-form-item class="text_ccc" label="可领取数量：" prop="total_count">
                <el-input class="w400" v-model="form_data.total_count" placeholder="请输入库存如：1000"></el-input>
              </el-form-item>

              <el-form-item class="text_ccc" label="优惠券说明：" prop="content">
                <el-input type="textarea" v-model="form_data.content" :rows="5" class="w400" placeholder="500字以内"></el-input>
              </el-form-item>

              <el-form-item>
                <el-button type="primary" class="w120 mar_T_50" @click="save('form_data')">保存</el-button>
                <el-button class="w120 mar_L_20 mar_T_50" @click="back()">返回</el-button>
              </el-form-item>
            </el-form>
          </div>

        </div>
        <el-dialog title="关联指定产品" :visible.sync="is_dialog" width="60%" :before-close="close_dialog">
          <div class="dia_head">
            <el-row :gutter="20">
              <el-col :span="5">
                <el-select clearable v-model="class_id" placeholder="全部商品分类">
                  <el-option  v-for='( item, i ) in class_list' :key='i' :label="item.name" :value="item.id"></el-option>
                </el-select>
              </el-col>
              <el-col :span="13">
                <el-input v-model="key" placeholder="请输入关键字进行搜索"></el-input>
              </el-col>
              <el-col :span="6">
                <el-button plain @click="search()">搜索</el-button>
              </el-col>
            </el-row>
          </div>

          <div class="dia_cont" style="margin: 20px 0">
            <!--列表-->
            <el-table class="mar_T_10" border ref="multipleTable" :data="goods_list" stripe style="width: 100%"  @selection-change="change_select">
              <el-table-column type="selection" width="55"></el-table-column>
              <el-table-column prop="nickname" label="商品信息">
                <div slot-scope="scope">
                  <div class="shop_info">
                    <div class="shop_info_img"><img :src="scope.row.picture" alt=""></div>
                    <div class="shop_info_txts">
                      <div class="info_txts_title">{{ scope.row.title }}</div>
                      <div class="info_txts_price">￥{{ scope.row.min_price }}</div>
                    </div>
                  </div>
                </div>
              </el-table-column>
              <el-table-column prop="sup_count" label="剩余库存" width="120"></el-table-column>
              <el-table-column prop="status" label="状态" width="120">
				  <div slot-scope="scope">
						<div v-if="scope.row.status==1">出售中</div>
						<div v-if="scope.row.status==2">已下架</div>
						<div v-if="scope.row.status==3">已售罄</div>
				  </div>
			  </el-table-column>
            </el-table>
          </div>
          <el-row :align="middle">
            <el-col :span="18">
              <el-button type="primary" @click="confirm_can_use">批量添加商品</el-button>
            </el-col>
            <el-col :span="6">
              <el-pagination class="left" @current-change="handleCurrentChange" :current-page="page" :page-size='limit'
                background layout="prev, pager, next" :total="count"></el-pagination>
            </el-col>
          </el-row>
        </el-dialog>
      </div>
    </div>

  </div>
</template>
<script src="../../../static/js/coupon/add_coupon.js"></script>

<style scoped>
.info_item {
  padding: 0 30px 30px 30px;
  /* border-bottom: 1px solid #eee; */
  margin-bottom: 27px;
}

.border_crude {
  background: #0486fe;
  width: 6px;
  height: 16px;
  margin-right: 10px;
}

.info_box {
  line-height: 40px;
  font-size: 14px;
}

.info_item .title {
  font-family: "PingFang-SC-Medium";
  font-size: 16px;
  color: #303133;
}

.info_box img {
  width: 100px;
  height: 100px;
}

.updata_box {
  display: flex;
  align-items: center;
  position: relative;
}

.updata_box .click_up {
  position: absolute;
  left: 230px;
  top: 50%;
}

.updata_box .click_up_txt {
  position: absolute;
  left: 230px;
  top: 70%;
  font-size: 12px;
  color: #909399;
}

.updata_box .updata_img1_tips {
  position: absolute;
  left: 230px;
  top: 0;
  width: 267px;
  padding: 15px;
  border: 1px dashed #9bc0e2;
  line-height: 21px;
  text-align: left;
  font-size: 14px;
  color: #5188bb;
}

.shop_info {
  width: 100%;
  display: flex;
  align-items: center;
  text-align: left;
}
.shop_info .shop_info_img {
  width: 70px;
  height: 70px;
  margin-right: 15px;
  background: antiquewhite;
}

.shop_info .shop_info_img img {
  width: 100%;
  height: 100%;
  display: block;
}

.shop_info .shop_info_txts {
  width: 80%;
  height: 70px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.shop_info .shop_info_txts .info_txts_title {
  width: 100%;
}

.shop_info .shop_info_txts .info_txts_price {
  color: #e33a3a;
}
</style>

