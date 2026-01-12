<template>
  <div id="subpage">

    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>用户管理</b></el-breadcrumb-item>
        <el-breadcrumb-item>领取优惠券</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>

    <div class="content">
      <div class="xcx-head">
        <div style="width: 100%;">
          <!-- <el-select clearable class="w130" v-model="subject_id" placeholder="用户">
            <el-option v-for="(item, i) in subject_data" :key="i" :label="item.name" :value="item.id"></el-option>
          </el-select> -->

          <el-date-picker class="mar_R_10" v-model="date" style="width: 30%;" type="daterange" value-format="yyyy-MM-dd"
            range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期">
          </el-date-picker>
          <el-select clearable class="w130" v-model="coupon_id" placeholder="优惠券">
            <el-option v-for="(item, i) in subject_data" :key="i" :label="item.coupon_name" :value="item.coupon_id"></el-option>
          </el-select>

          <el-button class="mar_L_20 hollow_out" @click="search()" plain>搜索</el-button>
        </div>

        <el-button class="mar_L_20" type="primary" @click="export_file()">导出记录</el-button>
      </div>
      <div class="xcx-content">
        <!--列表-->
        <el-table border :data="tableData" stripe style="width: 100%">
          <el-table-column prop="avatar_url" label="用户头像">
            <div slot-scope="scope">
              <img class="img_round" :src="scope.row.avatar_url" alt="">
            </div>
          </el-table-column>
          <el-table-column prop="nick_name" label="昵称姓名"></el-table-column>
          <el-table-column prop="mobile" label="手机号码"></el-table-column>
          <el-table-column prop="coupon_name" label="优惠券名称"></el-table-column>
          <el-table-column prop="" label="使用范围">
			<div slot-scope="scope">
              {{ scope.row.range == 1 ? '全场优惠券' :'指定商品优惠券' }}
            </div>
		  </el-table-column>
          <el-table-column prop="" label="优惠券有效期">
			<div slot-scope="scope">
              {{ scope.row.start_time }} -- {{ scope.row.end_time }}
            </div>
		  </el-table-column>
          <el-table-column prop="status" label="使用状态">
            <div slot-scope="scope">
              <span v-if="scope.row.is_use == 2" style="color: #00C58D;">待使用</span>
              <span v-if="scope.row.is_use == 1 && scope.row.is_over == false" style="color: #FC5244;">已使用</span>
              <span v-if="scope.row.is_use == 2 && scope.row.is_over" style="color: #959A9F;">已过期</span>
            </div>
          </el-table-column>
          <el-table-column prop="use_time" label="使用日期"></el-table-column>
        </el-table>
        <!--分页-->
        <div class="paging">
          <el-pagination class="left" @current-change="handleCurrentChange" :current-page="page" background
            layout="prev, pager, next" :total="count"></el-pagination>
          <span class="demonstration left">共 {{ count }} 条 每页10条</span>
        </div>
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/receive_coupon.js"></script>

<style scoped>
</style>
