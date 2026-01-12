<template>
  <div id="subpage">

    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>分销管理</b></el-breadcrumb-item>
        <el-breadcrumb-item>提现记录</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>

    <div class="content">
      <div class="xcx-head">
        <div style="width: 100%;">

          <el-select clearable class="w150 mar_R_10" v-model="type" placeholder="全部状态">
            <el-option label="已到账" value="1"></el-option>
            <el-option label="未到账" value="2"></el-option>
          </el-select>

          <el-date-picker class="mar_R_10" v-model="date" style="width: 23%;" value-format="yyyy-MM-dd" type="daterange" range-separator="至"
            start-placeholder="开始日期" end-placeholder="结束日期">
          </el-date-picker>
          <el-button class="hollow_out" @click="search()" plain>搜索</el-button>
        </div>

        <!-- <el-button class="mar_L_20" type="primary" @click="export_file()">导出Eexcel数据</el-button> -->
      </div>
      <div class="xcx-content">
        <!--列表-->
        <el-table border :data="tableData" stripe style="width: 100%">
          <el-table-column prop="price" label="用户头像">
            <div slot-scope="scope">
              <img class="img_round" :src="scope.row.avatar_url" alt="">
            </div>
          </el-table-column>
          <el-table-column prop="nickname" label="昵称"></el-table-column>
          <el-table-column prop="mobile" label="手机号码"></el-table-column>
          <el-table-column prop="available_price" label="当前可提现金额"></el-table-column>
          <el-table-column prop="price" label="提现金额"></el-table-column>
          <el-table-column prop="time" label="申请时间"></el-table-column>
          <el-table-column prop="status" label="处理状态">
            <div slot-scope="scope">
              <span class="success" v-if="scope.row.status == 1">已结算</span>
              <span class="danger" v-if="scope.row.status == 2">未结算</span>
              <span class="red" v-if="scope.row.status == 3">已驳回</span>
            </div>
          </el-table-column>
          <el-table-column prop="processing_time" label="处理时间"></el-table-column>
          <el-table-column label="操作">
            <div slot-scope="scope" class="dis_fd font14">
              <span class="text primary" v-if="scope.row.status != 1" @click="give_price(scope.row.id)">处理</span>
            </div>
          </el-table-column>
        </el-table>
        <!--分页-->
        <!-- <div class="paging">
          <el-pagination class="left" @current-change="handleCurrentChange" :current-page="page" background
            layout="prev, pager, next" :total="count"></el-pagination>
          <span class="demonstration left">共 {{ count }} 条 每页10条</span>
		</div> -->

        <el-dialog title="提现处理" :visible.sync="is_handle" width="28%" :before-close="close_peice">
          <el-form ref="form_data" label-width="100px" style="width: 85%;">
            <el-form-item label="是否同意：" prop="">
              <el-radio v-model="status" label="1">同意</el-radio>
              <el-radio v-model="status" label="2">拒绝</el-radio>
            </el-form-item>
            <el-form-item label="操作备注：" prop="">
				<el-input type="textarea" v-model="describe" :rows="4" placeholder="请输入备注信息"></el-input>
            </el-form-item>
          </el-form>
          <span slot="footer" class="dialog-footer">
            <el-button @click="close_peice">取 消</el-button>
            <el-button type="primary" @click="commit_prcie">确 定</el-button>
          </span>
        </el-dialog>
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/cash_withdrawal.js"></script>

<style scoped>
</style>
