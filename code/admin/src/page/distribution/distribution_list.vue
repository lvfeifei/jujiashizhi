<template>
  <div id="subpage">

    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>分销管理</b></el-breadcrumb-item>
        <el-breadcrumb-item>分销员列表</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>

    <div class="content">
      <div class="xcx-head">
        <!-- <span class="title">用户列表</span> -->
        <div>
          <el-select clearable class="w130 mar_R_20" v-model="user_type" placeholder="全部状态">
            <el-option label="正常" value="1"></el-option>
            <el-option label="黑名单" value="2"></el-option>
          </el-select>
          <el-input class="w251 font14" placeholder="昵称/手机号码" v-model="keys" clearable></el-input>
          <el-button class="mar_L_20 hollow_out" @click="search()" plain>搜索</el-button>
        </div>
      </div>
      <div class="xcx-content">
        <!--列表-->
        <el-table border :data="tableData" stripe style="width: 100%">
          <el-table-column prop="name" label="用户头像">
            <div slot-scope="scope">
              <img class="img_round" :src="scope.row.avatar_url" alt="">
            </div>
          </el-table-column>
          <el-table-column prop="nickname" label="昵称"></el-table-column>
          <el-table-column prop="mobile" label="电话"></el-table-column>
          <!-- <el-table-column prop="gender" label="性别"></el-table-column> -->
          <el-table-column prop="profit_total_price" label="收益总金额"></el-table-column>
          <el-table-column prop="can_extract" label="可提现金额"></el-table-column>
          <el-table-column prop="already_presented_price" label="已提现金额"></el-table-column>
          <el-table-column prop="freeze_price" label="冻结总金额"></el-table-column>
		 <el-table-column label="状态">
            <div slot-scope="scope" class="font14">
              <span class="text primary" v-if="scope.row.distributor_status == 1">正常</span>
              <span class="mar_L_10 text danger" v-else>黑名单</span>
            </div>
          </el-table-column>
          <el-table-column label="操作">
            <div slot-scope="scope" class="font14" style="cursor:pointer;">
              <span class="text primary" @click="to_detail(scope.row.id)">用户详情</span>
              <span class="mar_L_10 text danger" @click="join_black(scope.row.id,scope.row.distributor_status)">{{ scope.row.distributor_status == 1 ? '加入' :'移除' }}黑名单</span>
            </div>
          </el-table-column>
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
<script src="../../../static/js/distribution_list.js"></script>

<style scoped>
</style>
