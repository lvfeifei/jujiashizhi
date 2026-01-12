<template>
  <div style="padding-bottom: 120px;" id="subpage">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item>
        <b>分销管理</b>
      </el-breadcrumb-item>
      <el-breadcrumb-item>分销设置</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content mb30">
      <div class="xcx-head">
        <span class="title">分销设置</span>
      </div>
      <div class="xcx-content" style="padding: 0;">
        <div class="info_item">
		  <!-- 是否开启分销 -->
          <p class="mar_L_20 flex_align_center mar_B_30" style="width: 90%">
            <span class="border_crude"></span>
            <span class="title">
              <b>是否开启分销：</b>
            </span>
            <span>
              <el-switch class="mar_L_20" v-model="is_switch_distribution" active-text inactive-text></el-switch>
            </span>
          </p>
          <div>
            <!-- 佣金设置 -->
            <p class="mar_L_20 flex_align_center" style="width: 90%" v-if="is_switch_distribution">
              <span class="border_crude"></span>
              <span class="title">
                <b>佣金设置</b>
              </span>
            </p>
            <el-form ref="form_data" :model="form_data" class="mar_L_20 mar_T_30 user_info" :rules="rules" label-width="140px" style="width: 80%;">
				<div v-if="is_switch_distribution">
						<!-- 一级分销 -->
					<el-form-item label="是否开启一级分销：" prop="address">
						<el-switch v-model="form_data.is_one_level_distribution" active-text inactive-text></el-switch>
					</el-form-item>
					<el-form-item class="text_ccc" label="佣金计算方式：" prop="" v-if="form_data.is_one_level_distribution">
						<el-radio v-model="form_data.one_level_distribution_type" label="1">按订单总金额%</el-radio>
						<el-radio v-model="form_data.one_level_distribution_type" label="2">按固定金额</el-radio>
					</el-form-item>
					<el-form-item class="text_ccc" label="输入佣金比例：" prop="one_level_distribution" v-if="form_data.is_one_level_distribution">
						<el-input class="mar_R_20" v-model="form_data.one_level_distribution" :placeholder="form_data.one_level_distribution_type == 1 ?'请输入百分比':'请输入金额'" style="width:170px;"></el-input>
						{{ form_data.one_level_distribution_type == 1 ?'%':'元' }}
					</el-form-item>
					<!-- 二级分销 -->
					<el-form-item label="是否开启二级分销：" prop="address">
						<el-switch v-model="form_data.is_two_level_distribution" active-text inactive-text></el-switch>
					</el-form-item>
					<el-form-item class="text_ccc" label="佣金计算方式：" prop="" v-if="form_data.is_two_level_distribution">
						<el-radio v-model="form_data.two_level_distribution_type" label="1">按订单总金额%</el-radio>
						<el-radio v-model="form_data.two_level_distribution_type" label="2">按固定金额</el-radio>
					</el-form-item>
					<el-form-item class="text_ccc" label="输入佣金比例：" prop="two_level_distribution" v-if="form_data.is_two_level_distribution">
						<el-input class="mar_R_20" v-model="form_data.two_level_distribution" :placeholder="form_data.two_level_distribution_type == 1 ?'请输入百分比':'请输入金额'" style="width:170px;"></el-input>
						{{ form_data.two_level_distribution_type == 1 ?'%':'元' }}
					</el-form-item>
					<!-- 三级分销 -->
					<el-form-item label="是否开启三级分销：" prop="">
						<el-switch v-model="form_data.is_three_level_distribution" active-text inactive-text></el-switch>
					</el-form-item>
					<el-form-item class="text_ccc" label="佣金计算方式：" prop="" v-if="form_data.is_three_level_distribution">
						<el-radio v-model="form_data.three_level_distribution_type" label="1">按订单总金额%</el-radio>
						<el-radio v-model="form_data.three_level_distribution_type" label="2">按固定金额</el-radio>
					</el-form-item>
					<el-form-item class="text_ccc" label="输入佣金比例：" prop="three_level_distribution" v-if="form_data.is_three_level_distribution">
						<el-input class="mar_R_20" v-model="form_data.three_level_distribution" :placeholder="form_data.three_level_distribution_type == 1 ?'请输入百分比':'请输入金额'" style="width:170px;"></el-input>
						{{ form_data.three_level_distribution_type == 1 ?'%':'元' }}
					</el-form-item>
					<el-form-item label="申请分销介绍：" prop="distribution_content">
						<!-- 富文本 -->
						<richText placeholder='请输入内容' ref="richText" @editor_change='editor_change'
						:describe='form_data.distribution_content'>
						</richText>
					</el-form-item>
					<p class="mar_L_20 flex_align_center" style="width: 90%">
					<span class="border_crude"></span>
					<span class="title">
						<b>分销规则</b>
					</span>
					</p>
					<el-form-item class="text_ccc" label="到账方式：" prop="">
						<el-radio v-model="form_data.distribution_settlement_type" label="1">支付即到账</el-radio>
						<el-radio v-model="form_data.distribution_settlement_type" label="2">退货期过后到账</el-radio>
					</el-form-item>
					<el-form-item label="分销规则：" prop="distribution_rule">
						<!-- 富文本 -->
						<richText_two placeholder='请输入分销规则' ref="richText_two" @editor_change_two='distribution_rule_change'
						:describe_two='form_data.distribution_rule'>
						</richText_two>
					</el-form-item>
			  </div>
              <el-form-item>
                <el-button type="primary" class="w120 mar_T_50" @click="save('form_data')">保存</el-button>
                <!-- <el-button class="w120 mar_L_20 mar_T_50" @click="back()">返回</el-button> -->
              </el-form-item>
            </el-form>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/set_distribution.js"></script>

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
</style>
