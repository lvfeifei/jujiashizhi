<template>
  <div id="subpage">

    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>优惠券</b></el-breadcrumb-item>
        <el-breadcrumb-item>优惠券管理</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>

    <div class="content" >
      <div class="xcx-head">
        <!-- <span class="title">用户列表</span> -->
        <div>
          <el-select class="w150" clearable v-model="range" placeholder="全部范围" style="min-width:130px;">
            <el-option v-for="(item, i) in range_list" :key="i" :label="item.name" :value="item.id"></el-option>
          </el-select>
          <el-select class="w150" clearable v-model="type" placeholder="全部类型">
            <el-option v-for="(item, i) in type_list" :key="i" :label="item.name" :value="item.id"></el-option>
          </el-select>
          <el-select class="w150" clearable v-model="status" placeholder="全部状态">
            <el-option v-for="(item, i) in status_list" :key="i" :label="item.name" :value="item.id"></el-option>
          </el-select>
          <el-input class="w200 font14" placeholder="请输入优惠券名称" v-model="keys" clearable></el-input>
          <el-button class="hollow_out" @click="search()" plain>搜索</el-button>
        </div>
        <el-button type="primary" @click="add_coupon()">添加优惠券</el-button>
      </div>
      <div class="xcx-content">
        <!--列表-->
        <el-table border :data="tableData" stripe style="width: 100%">
          <el-table-column prop="range_text" label="使用范围"></el-table-column>
          <el-table-column prop="name" label="优惠券名称"></el-table-column>
          <el-table-column prop="type_text" label="优惠券类型" width="100"></el-table-column>
          <el-table-column prop="date" label="优惠券有效期"></el-table-column>
          <el-table-column prop="sup_count" label="可领取数量" width="90"></el-table-column>
          <el-table-column prop="receive_count" label="已领取数量" width="90">
            <div slot-scope="scope">
              <span class="receive_coupon" @click="to_list()">{{ scope.row.receive_count }}</span>
            </div>
          </el-table-column>
          <el-table-column prop="use_count" label="已使用数量" width="90"></el-table-column>
          <el-table-column label="状态" width="120">
            <div slot-scope="scope" class="font14">
              <span v-if="scope.row.status == 1" style="font-size: 14px;color: #00C58D;">推广中</span>
              <span v-if="scope.row.status == 2" style="font-size: 14px;color: #FC5244;">已过期</span>
              <span v-if="scope.row.status == 3" style="font-size: 14px;color: #959A9F;">已作废</span>
            </div>
          </el-table-column>
          <el-table-column label="操作">
            <div slot-scope="scope" class="dis_fd font14" style="cursor:pointer;">
			  <span class="text primary" v-if="scope.row.status ==1" @click="edit_coupon(scope.row.id)">修改</span>
              <span class="text primary" @click="to_detail(scope.row.id)">查看详情</span>
              <span class="mar_L_10 text danger" v-if="scope.row.status  == 1"
                @click="del_coupon(scope.row.id,scope.row.distributor_status)">作废</span>
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
<script src="../../../static/js/coupon/coupon_list.js"></script>

<style scoped>
.receive_coupon {
  color: #0486fe;
  text-decoration: underline;
  cursor: pointer;
}
</style>
