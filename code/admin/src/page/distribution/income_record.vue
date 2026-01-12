<template>
  <div id="subpage">

    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>分销管理</b></el-breadcrumb-item>
        <el-breadcrumb-item>收益记录</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>

    <div class="content">
      <div class="xcx-head">
        <div style="width: 100%;">

          <el-select clearable class="w150 mar_R_10" v-model="status" placeholder="全部状态">
            <el-option label="已结算" value="1"></el-option>
            <el-option label="已退款" value="2"></el-option>
			<el-option label="待完成" value="3"></el-option>
          </el-select>

          <el-date-picker class="mar_R_10" v-model="date" style="width: 23%;" type="daterange" value-format="yyyy-MM-dd" range-separator="至"
            start-placeholder="开始日期" end-placeholder="结束日期">
          </el-date-picker>

          <el-input class="font14" style="width: 13%;" placeholder="请输入订单编号" v-model="key" clearable>
          </el-input>
          <el-button class="mar_L_20 hollow_out" @click="search()" plain>搜索</el-button>
        </div>

        <!-- <el-button class="mar_L_20" type="primary" @click="export_file()">导出Eexcel数据</el-button> -->
      </div>
      <div class="xcx-content">
        <!--列表-->
        <el-table border :data="tableData" stripe style="width: 100%">
          <el-table-column prop="price" label="收益人头像">
            <div slot-scope="scope">
              <img class="img_round" :src="scope.row.distribution_avatar_url" alt="">
            </div>
          </el-table-column>
          <el-table-column prop="distribution_nickname" label="收益人昵称"></el-table-column>
          <el-table-column prop="type_name" label="贡献人头像">
            <div slot-scope="scope">
              <img class="img_round" :src="scope.row.avatar_url" alt="">
            </div>
          </el-table-column>
          <el-table-column prop="nickname" label="贡献人昵称"></el-table-column>
          <el-table-column prop="sn" label="订单号"></el-table-column>
          <el-table-column prop="price" label="收益金额"></el-table-column>
          <el-table-column prop="status" label="状态">
			  <div slot-scope="scope">
				  <span class="success" v-if="scope.row.status == 1">已结算</span>
				  <span class="danger" v-if="scope.row.status == 2">待收益</span>
				  <span class="red" v-if="scope.row.status == 3">待完成</span>
			  </div>
		  </el-table-column>
          <el-table-column prop="update_time" label="最后更新时间"></el-table-column>
          <el-table-column label="操作">
            <div slot-scope="scope" class="dis_fd font14">
              <span class="text primary" @click="detail(scope.row.id)">查看明细</span>
            </div>
          </el-table-column>
        </el-table>
        <!--分页-->
        <!-- <div class="paging">
          <el-pagination class="left" @current-change="handleCurrentChange" :current-page="page" background
            layout="prev, pager, next" :total="count"></el-pagination>
          <span class="demonstration left">共 {{ count }} 条 每页10条</span>
        </div> -->
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/income_record.js"></script>

<style scoped>
</style>
