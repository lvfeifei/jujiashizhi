<template>
  <div style="padding-bottom: 120px;" id="subpage">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item>
        <b>优惠券</b>
      </el-breadcrumb-item>
      <el-breadcrumb-item>新人好礼</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content mb30">
      <div class="xcx-head" style="margin:0;">
        <span class="title">新人好礼设置</span>
      </div>
      <div class="xcx-content" style="padding: 0;">
        <div class="info_item">
          <div>

            <el-form ref="form_data" :model="form_data" class="mar_L_20 mar_T_30 user_info" :rules="rules"
              label-width="140px" style="width: 80%;">
              <div>
                <el-form-item class="text_ccc" label="设置开关：" prop="">
                  <el-radio v-model="form_data.status" label="1">开启新人好礼</el-radio>
                  <el-radio v-model="form_data.status" label="2">关闭新人好礼</el-radio>
                </el-form-item>
                <el-form-item label="上传图片：" prop="picture">
                  <div class="updata_box">
                    <el-upload class="updata_img1" accept="image/*" :action="upload_img_url" :show-file-list="false"
                      list-type="picture-card" :on-success="img_succ" :data="postData">
					  <img v-if="form_data.picture" :src="form_data.picture" class="avatar">
					  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                      <el-button class="click_up" size="small" type="primary">重新上传海报</el-button>
                      <div class="click_up_txt" slot="tip"> 海报大小560*700像素，图片格式png、jpg</div>
                      <div class="updata_img1_tips" slot="tip">活动弹窗海报为您提供默认图片，也可以自定义，按指定的尺寸制作您的海报，重新上传替换即可！</div>
                    </el-upload>
                  </div>

                </el-form-item>

                <el-form-item class="check_btn" label="领取方式：" prop="">
                  <el-radio v-model="form_data.receive_type" label="1">注册即领取 （用户注册后优惠券自动存入其账户）</el-radio>
                  <el-radio v-model="form_data.receive_type" label="2">手动领取优惠券 （点击进入活动详情，领取优惠券）
                  </el-radio>
                </el-form-item>

                <el-form-item label="活动详情：" prop="distribution_rule">
                  <!-- 富文本 -->
                  <richText placeholder='请输入活动详情' ref="richText" @editor_change='editor_change'
                    :describe='form_data.content'>
                  </richText>
                </el-form-item>
              </div>
              <el-form-item>
                <el-button type="primary" class="w120 mar_T_50" @click="save('form_data')">保存</el-button>
                <el-button class="w120 mar_L_20 mar_T_50" @click="back()">返回</el-button>
              </el-form-item>
            </el-form>
          </div>

        </div>
      </div>
    </div>

    <div class="content" style="margin-top:20px;">
      <div class="xcx-head" style="display:flex; justify-content: space-between; align-items: center;">
        <span class="title">配置优惠券</span>
        <el-button type="primary" @click="add_people()">添加新人优惠券</el-button>
      </div>
      <div class="xcx-content">
        <el-table border :data="tableData" stripe style="width: 100%">
		  <el-table-column prop="range_text" label="使用范围"></el-table-column>
          <el-table-column prop="name" label="优惠券名称"></el-table-column>
          <el-table-column prop="type_text" label="优惠券类型"></el-table-column>
          <el-table-column prop="date" label="有效期"></el-table-column>
          <el-table-column prop="content" label="使用说明"></el-table-column>
          <el-table-column prop="status" label="已使用/已领取">
            <div slot-scope="scope">
              {{ scope.row.use_count }} / {{ scope.row.receive_count }}
            </div>
          </el-table-column>
          <el-table-column prop="status" label="状态">
			<div slot-scope="scope" class="font14">
              <span v-if="scope.row.status == 1" style="font-size: 14px;color: #00C58D;">推广中</span>
              <span v-if="scope.row.status == 2" style="font-size: 14px;color: #FC5244;">已过期</span>
              <span v-if="scope.row.status == 3" style="font-size: 14px;color: #959A9F;">已作废</span>
            </div>
		  </el-table-column>
          <el-table-column label="操作">
            <div slot-scope="scope" class="doSonimg_box font14">
              <span class="text danger" @click="del_item(scope.row.gift_coupon_id)">删除</span>
            </div>
          </el-table-column>
        </el-table>
		<!--分页-->
		<div class="paging">
			<el-pagination class="left" @current-change="handleCurrentChange" :current-page="page" background layout="prev, pager, next" :total="count"></el-pagination>
			<span class="demonstration left">共 {{ count }} 条 每页10条</span>
		</div>
      </div>

      <el-dialog title="关联优惠券" :visible.sync="is_dialog" width="60%" :before-close="close_dialog">
        <div class="dia_head">
          <el-row :gutter="20">
            <el-col :span="5">
              <el-select v-model="range" placeholder="全部范围" clearable>
                <el-option v-for="(item,index) in range_list" :key="index" :label="item.name" :value="item.id"></el-option>
              </el-select>
            </el-col>
			<el-col :span="5">
              <el-select v-model="type" placeholder="全部类型" clearable>
                <el-option v-for="(item,index) in type_list" :key="index" :label="item.name" :value="item.id"></el-option>
              </el-select>
            </el-col>
			<!-- <el-col :span="5">
              <el-select v-model="status" placeholder="全部状态" clearable>
                <el-option v-for="(item,index) in status_list" :key="index" :label="item.name" :value="item.id"></el-option>
              </el-select>
            </el-col> -->
            <el-col :span="6">
              <el-input v-model="keys" placeholder="请输入关键字进行搜索"></el-input>
            </el-col>
            <el-col :span="6">
              <el-button plain @click="search">搜索</el-button>
            </el-col>
          </el-row>
        </div>

        <div class="dia_cont" style="margin: 20px 0">
          <!--列表-->
          <el-table class="mar_T_10" border ref="multipleTable" :data="can_use_list"  @selection-change="change_select" stripe style="width: 100%">
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column prop="range_text" label="使用范围"></el-table-column>
			<el-table-column prop="name" label="优惠券名称"></el-table-column>
			<el-table-column prop="type_text" label="优惠券类型"></el-table-column>
			<el-table-column prop="date" label="优惠券有效期"></el-table-column>
          	<el-table-column prop="sup_count" label="可领取数量"></el-table-column>
            <el-table-column prop="status" label="状态" width="120">
				<div slot-scope="scope" class="font14">
					<span v-if="scope.row.status == 1" style="font-size: 14px;color: #00C58D;">推广中</span>
					<span v-if="scope.row.status == 2" style="font-size: 14px;color: #FC5244;">已过期</span>
					<span v-if="scope.row.status == 3" style="font-size: 14px;color: #959A9F;">已作废</span>
				</div>
			</el-table-column>
          </el-table>
        </div>
        <el-row :align="middle">
          <el-col :span="18">
            <el-button type="primary" @click="confirm_can_use">批量添加优惠券</el-button>
          </el-col>
          <el-col :span="6">
            <el-pagination class="left" @current-change="can_use_next" :current-page="can_use_page" :page-size='can_use_limit'
              background layout="prev, pager, next" :total="can_use_count"></el-pagination>
          </el-col>
        </el-row>
      </el-dialog>
    </div>
  </div>
</template>
<script src="../../../static/js/coupon/new_people.js"></script>

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

.avatar {
width: 192px;
height: 240px;
display: block;
}
</style>

