<template>
  <div style="padding-bottom: 120px;" id="subpage">

    <el-breadcrumb separator="/">
      <el-breadcrumb-item><b>分销管理</b></el-breadcrumb-item>
      <el-breadcrumb-item :to="{path: '/distribution/income_record'}">收益记录</el-breadcrumb-item>
      <el-breadcrumb-item>明细</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content mb30">
      <div class="xcx-head">
        <span class="title">用户详情</span>
      </div>
      <div class="xcx-content" style="padding: 0;">
        <div class="info_item">
          <p class="mar_L_20 flex_align_center">
            <span class="border_crude"></span>
            <span class="title">收益人</span>
          </p>
          <div class="dis_jcsb mar_T_30 mar_L_30 info_box">
            <div class="user_info_box">
              <div class="mar_R_20" style="width: 70px;height: 70px;">
                <!-- <img style="width: 100px;height: 100px;" :src="avatar_url" alt=""> -->
                <img style="width: 70px;height: 70px;border-radius: 6px;"
                  :src="avatar_url" alt="">
                <!-- <span v-else>暂无头像</span> -->
              </div>
              <div>
                <p>用户昵称：{{nickname}}</p>
                <p>手机号码：{{mobile}}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="info_item">
          <p class="mar_L_20 flex_align_center">
            <span class="border_crude"></span>
            <span class="title">贡献人</span>
          </p>
          <div class="dis_jcsb mar_T_30 mar_L_30 info_box">
            <div class="user_info_box">
              <div class="mar_R_20" style="width: 70px;height: 70px;">
                <!-- <img style="width: 100px;height: 100px;" :src="avatar_url" alt=""> -->
                <img style="width: 70px;height: 70px;border-radius: 6px;"
                  :src="contribution_avatar_url" alt="">
                <!-- <span v-else>暂无头像</span> -->
              </div>
              <div>
                <p>用户昵称：{{contribution_nickname}}</p>
                <p>手机号码：{{contribution_mobile}}</p>
              </div>
            </div>
            <div class="distribution_level">
              <div class="tit" style="font-size: 14px;color: #303133;">分销路径</div>
              <div class="level_list">
                <div class="level_li" v-for="(item,index) in distribution_avatar_url" :key='index'>
                  <div class="img"><img :src="item.avatar_url"
                      alt="">
                  </div>
                  <div class="li_texts"
                    style="width: 140px;display: flex;flex-direction: column;justify-content: space-between;">
                    <div class="text_tit" style="font-size: 14px;color: #000000;"><b>{{ item.nickname }}</b></div>
                    <div class="text_txt" style="font-size: 12px;color: #606266;">{{ item.text }}</div>
                  </div>
                  <div><i class="el-icon-arrow-right"></i></div>
                </div>
                <!-- <div class="level_li">
                  <div class="img"><img src="http://b-ssl.duitang.com/uploads/item/201709/22/20170922162149_snyk3.jpeg"
                      alt="">
                  </div>
                  <div class="li_texts"
                    style="width: 140px;display: flex;flex-direction: column;justify-content: space-between;">
                    <div class="text_tit" style="font-size: 14px;color: #000000;"><b>Makenzie</b></div>
                    <div class="text_txt" style="font-size: 12px;color: #606266;">一级分销员</div>
                  </div>
                  <div><i class="el-icon-arrow-right"></i></div>
                </div>
                <div class="level_li">
                  <div class="img"><img src="http://b-ssl.duitang.com/uploads/item/201709/22/20170922162149_snyk3.jpeg"
                      alt="">
                  </div>
                  <div class="li_texts"
                    style="width: 140px;display: flex;flex-direction: column;justify-content: space-between;">
                    <div class="text_tit" style="font-size: 14px;color: #000000;"><b>Makenzie</b></div>
                    <div class="text_txt" style="font-size: 12px;color: #606266;">一级分销员</div>
                  </div>
                  <div><i class="el-icon-arrow-right"></i></div>
                </div> -->
              </div>
            </div>
          </div>
        </div>


        <div class="info_item" style="border: 0;">
          <p class="mar_L_20 flex_align_center">
            <span class="border_crude"></span>
            <span class="title">订单信息</span>
          </p>
          <el-table border :data="distribution_list" stripe class="mar_L_30 mar_T_30 w90_B">
            <el-table-column prop="sn" label="订单编号"></el-table-column>
            <el-table-column prop="total_price" label="订单总金额"></el-table-column>
            <el-table-column prop="price" label="收益金额"></el-table-column>
            <el-table-column prop="pay_price" label="收益状态">
              <div slot-scope="scope">
				  <span class="success" v-if="scope.row.status == 1">已结算</span>
				  <span class="danger" v-if="scope.row.status == 2">待收益</span>
				  <span class="red" v-if="scope.row.status == 3">待完成</span>
              </div>
            </el-table-column>
            <el-table-column prop="create_time" label="创建时间"></el-table-column>
            <el-table-column prop="update_time" label="收益时间"></el-table-column>
          </el-table>
          <el-button class="mar_L_30 mar_T_50 w120" plain @click="back()">返回</el-button>
        </div>

      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/income_details.js"></script>

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

  .info_box .user_info_box {
    line-height: 40px;
    font-size: 14px;
  }

  .info_item .title {
    font-family: 'PingFang-SC-Medium';
    font-size: 16px;
    color: #303133;
  }

  .info_box img {
    width: 100px;
    height: 100px;
  }

  .user_info_box {
    display: flex;
    align-items: center;
    padding-right: 100px;
  }

  .distribution_level {
    border-left: 1px solid #EBEEF5;
    padding-left: 50px;
  }

  .distribution_level .tit {
    font-size: 14px;
    color: #303133;
    margin-bottom: 14px;
  }

  .distribution_level .level_list {
    display: flex;
    align-items: center;
  }

  .distribution_level .level_list .level_li {
    display: flex;
    align-items: center;
    margin-right: 49px;
  }

  .distribution_level .level_list .level_li .img {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    overflow: hidden;
    margin-right: 15px;
    background: aliceblue;
  }

  .distribution_level .level_list .level_li .img img {
    width: 100%;
    height: 100%;
    display: block;
  }

</style>
