<template>
  <div style="padding-bottom: 120px;" id="subpage">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item>
        <b>优惠券</b>
      </el-breadcrumb-item>
      <el-breadcrumb-item>优惠券详情</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content mb30">
      <div class="xcx-head" style="margin:0;">
        <span class="title">基本信息</span>
      </div>
      <div class="xcx-content" style="padding: 0;">
        <div class="info_item">
          <div>
            <el-form ref="form_data" :model="form_data" class="mar_L_20 mar_T_30 user_info" :rules="rules"
              label-width="140px" style="width: 80%;">
              <el-form-item label="优惠券类型：">
                <div>{{ detail.type==1?'直减券':'满减券'}}</div>
              </el-form-item>
              <el-form-item label="优惠券名称：">
                <div>{{ detail.name }}</div>
              </el-form-item>
              <el-form-item label="优惠有效期：">
                <div>{{  detail.date_type == 1 ? (detail.start_time+'--'+detail.end_time):('领取后'+ detail.day +'天过期')}}</div>
              </el-form-item>
              <el-form-item label="可领取数量：">
                <div>{{detail.sup_count}}</div>
              </el-form-item>
              <el-form-item label="已领取数量：">
                <div>{{detail.receive_count}}</div>
              </el-form-item>
              <el-form-item label="已使用数量：">
                <div>{{detail.use_count}}</div>
              </el-form-item>
              <el-form-item label="优惠券说明：">
                <div>{{detail.content}}</div>
              </el-form-item>
              <el-form-item label="使用条件：">
                <div>{{  detail.range == 1 ?'全场优惠券':'指定商品优惠券' }}</div>
              </el-form-item>
              <el-form-item label="使用状态：">
				<div>
					<span v-if="detail.status == 1" style="font-size: 14px;color: #00C58D;">推广中</span>
					<span v-if="detail.status == 2" style="font-size: 14px;color: #FC5244;">已过期</span>
					<span v-if="detail.status == 3" style="font-size: 14px;color: #959A9F;">已作废</span>
				</div>
              </el-form-item>
              <!-- <el-form-item label="使用日期：">
                <div>直减券</div>
              </el-form-item> -->
            </el-form>
          </div>
        </div>
      </div>
    </div>

    <div class="content mb30 mar_T_20">
      <div class="xcx-head" style="margin:0;">
        <span class="title">基本信息</span>
      </div>
      <div class="xcx-content" style="padding: 0;">
        <div class="info_item">
          <div>
            <el-form ref="form_data" :model="form_data" class="mar_L_20 mar_T_30 user_info" :rules="rules"
              label-width="160px" style="width: 80%;">
              <el-form-item label="领取优惠券地址：">
                <div>pages/index/collect_coupons?coupon_id= {{ detail.id }}</div>
              </el-form-item>
              <el-form-item label="领取优惠券小程序码：">
                <div class="coupon_imgs_box">
                  <div class="coupon_qrcode mar_B_10"><img :src="detail.code_url" alt=""></div>
                  <el-button plain @click="downs">下载小程序码</el-button>
                </div>
              </el-form-item>
            </el-form>
          </div>
        </div>
      </div>
    </div>

    <div class="content mar_T_20" v-if="detail.range == 2">
      <div class="xcx-head" style="display:flex; justify-content: space-between; align-items: center;">
        <span class="title">指定商品列表</span>
      </div>
      <div class="xcx-content">
        <el-table border :data="goods_list" stripe style="width: 100%">
          <el-table-column prop="" label="商品信息">
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
          <el-table-column prop="sup_count" label="剩余库存" width="200"></el-table-column>
          <el-table-column prop="status" label="状态" width="200">
				<div slot-scope="scope">
					<div v-if="scope.row.status==1">出售中</div>
					<div v-if="scope.row.status==2">已下架</div>
					<div v-if="scope.row.status==3">已售罄</div>
				</div>
		  </el-table-column>

        </el-table>
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/coupon/coupon_detail.js"></script>

<style scoped>
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
.coupon_qrcode {
  width: 120px;
  height: 120px;
  background: antiquewhite;
}

.coupon_qrcode img {
  width: 100%;
  height: 100%;
  display: block;
}
.coupon_imgs_box .el-button {
  padding: 12px 16px;
}

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

/* 大于1450时隐藏 */
/* @media screen and (min-width: 1000px) {
    .user_info {
      width: 700px;
    }
  }

  @media screen and (min-width: 1300px) {
    .user_info {
      width: 1000px;
    }
  }

  @media screen and (min-width: 1600px) {
    .user_info {
      width: 1300px;
    }
  } */

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

.check_btn .el-radio {
  width: 100%;
}

.check_btn .el-radio + .el-radio {
  margin: 0;
}
</style>

