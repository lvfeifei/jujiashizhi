<template>
  <div style="padding-bottom: 120px;" id="subpage">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item>
        <b>分销管理</b>
      </el-breadcrumb-item>
      <el-breadcrumb-item :to="{path: '/user/user_list'}">分销员列表</el-breadcrumb-item>
      <el-breadcrumb-item>用户详情</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content mb30">
      <div class="xcx-head">
        <span class="title">用户详情</span>
      </div>
      <div class="xcx-content" style="padding: 0;">
        <div class="info_item">
          <p class="mar_L_20 flex_align_center">
            <span class="border_crude"></span>
            <span class="title">用户信息</span>
          </p>

          <div class="dis_jcsb mar_T_30 w60_B mar_L_30 info_box">
            <div class style="display: flex;align-items: center;">
              <div class="img_box mar_R_50" style="width: 100px;height: 100px;">
                <!-- <img style="width: 100px;height: 100px;" :src="avatar_url" alt=""> -->
                <img
                  style="width: 100px;height: 100px;border-radius: 5px;"
                  :src="avatar_url"
                  alt
                />
                <!-- <span v-else>暂无头像</span> -->
              </div>
              <div>
                <p>用户昵称：{{nickname}}</p>
                <p>手机号码：{{gender}}</p>
                <p>申请时间：{{timestamp_phone}}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="info_item">
          <p class="mar_L_20 flex_align_center">
            <span class="border_crude"></span>
            <span class="title">数据统计</span>
          </p>
          <div class="data_info">
            <div class="data_info_li">
              <div class="li_tit">
                <b>{{ profit_total_price }}</b>
              </div>
              <div class="li_txt">收益总金额</div>
            </div>
            <div class="data_info_li">
              <div class="li_tit">
                <b>{{ can_extract }}</b>
              </div>
              <div class="li_txt">可提现总金额</div>
            </div>
            <div class="data_info_li">
              <div class="li_tit">
                <b>￥{{ my_reward_count }}</b>
              </div>
              <div class="li_txt">已提现总金额</div>
            </div>
            <div class="data_info_li">
              <div class="li_tit">
                <b>{{ freeze_price }}</b>
              </div>
              <div class="li_txt">冻结总金额</div>
            </div>
            <div class="data_info_li">
              <div class="li_tit">
                <b>{{ my_friend_count }}</b>
              </div>
              <div class="li_txt">邀请总人数</div>
            </div>
          </div>
        </div>

        <div class="info_item">
          <p class="mar_L_20 flex_align_center">
            <span class="border_crude"></span>
            <span class="title">邀请人列表</span>
          </p>
          <el-table border :data="invite_people" stripe class="mar_L_30 mar_T_30 w90_B">
            <el-table-column prop="title" label="用户头像">
              <div slot-scope="scope">
                <img style="width: 50px;height: 50px;border-radius: 50%;" :src='scope.row.avatar_url' alt />
              </div>
            </el-table-column>
            <el-table-column prop="nickname" label="昵称"></el-table-column>
            <el-table-column prop="mobile" label="电话"></el-table-column>
            <el-table-column prop="expected_price" label="预计收益金额"></el-table-column>
            <el-table-column prop="acquired_price" label="已收益金额"></el-table-column>
            <el-table-column prop="invite_time" label="邀请时间"></el-table-column>
          </el-table>
          <el-button class="mar_L_30 mar_T_50 w120" plain @click="back()">返回</el-button>
        </div>
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/distribution_detail.js"></script>

<style scoped>
.info_item {
  padding: 0 30px 40px 30px;
  border-bottom: 1px solid #eee;
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
  font-family: PingFang-SC-Medium;
  font-size: 16px;
  color: #303133;
}

.info_box img {
  width: 100px;
  height: 100px;
}

.data_info {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding-top: 30px;
}

.data_info .data_info_li {
  text-align: center;
}

.data_info .data_info_li .li_tit {
  font-size: 22px;
  color: #323232;
  margin-bottom: 6px;
}

.data_info .data_info_li .li_txt {
  font-size: 14px;
  color: #909399;
}
</style>
