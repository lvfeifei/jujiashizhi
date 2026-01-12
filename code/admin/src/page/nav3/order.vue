<template>
  <div id="order" class="subpage">
    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>订单管理</b></el-breadcrumb-item>
        <el-breadcrumb-item>工单管理</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>
    <div id="content" class="content">
      <div class="xcx-head">
        <div class="model" style="width:100%;">
          <el-select placeholder="请选择省" v-model="select_province_name" @change="getcity">
              <el-option v-for="item in province" :key="item.province_code" :label="item.province_name" :value="item.province_code"></el-option>
          </el-select>
          <el-select placeholder="请选择市" v-model="select_city_name" @change="getcounty">
            <el-option v-for="item in city" :key="item.city_code" :label="item.city_name" :value="item.city_code"></el-option>
          </el-select>
          <el-select placeholder="请选择区" v-model="select_county_name" @change="getstreet">
            <el-option v-for="item in county" :key="item.area_code" :label="item.area_name" :value="item.area_code"></el-option>
          </el-select>
          <el-select placeholder="请选择街道" v-model="select_street_name">
            <el-option v-for="item in street" :key="item.street_code" :label="item.street_name" :value="item.street_code"></el-option>
          </el-select>
          <el-input style="width: 270px;" placeholder="根据申请号、真实姓名、手机号查询" v-model="input_content" class="input-with-select"></el-input>

          <div class="mar_L_30" style="float: right;">
            <span class="xcx-add font14" size="mini" @click="search">确认</span>
          </div>
        </div>
      </div>
      <div id="list">
        <el-tabs class="tabs_1" v-model="activeName" @tab-click="handleClick">
          <el-tab-pane v-bind:label="item.text" v-bind:name="item.name" v-for="item in label" :key="item.id">
            <el-table border :data="orderList" stripe style="width: 100%">
                <el-table-column prop="sn" label="申请号"></el-table-column>
                <el-table-column prop="create_time" label="申请时间"></el-table-column>
                <el-table-column prop="name" label="申请人姓名"></el-table-column>
                <el-table-column prop="mobile" label="申请人联系方式"></el-table-column>
                <el-table-column prop="status" label="当前申请进度"></el-table-column>
                <el-table-column label="操作">
                    <div slot-scope="scope" class="doSonimg_box font14">
                        <span class="text primary" @click="toDetail(scope.row.id)">查看详情</span>
                    </div>
                </el-table-column>
            </el-table>
          </el-tab-pane>
        </el-tabs>
        <div class="block">
          <el-pagination @current-change="handleCurrentChange" :current-page="pageNum" :page-size="limit" background
            layout="prev, pager, next,slot" :total="count"></el-pagination>
          <span class="demonstration">共 {{ count }} 条 每页10条</span>
        </div>
      </div>
    </div>
  </div>
</template>
<script type="text/javascript" src="../../../static/js/order.js"></script>
<style>
.shop_tit {
  width: 100%;
  display: -webkit-box;
  /*! autoprefixer: off */
  -webkit-box-orient: vertical;
  /*! autoprefixer: on */
  -webkit-line-clamp: 3;
  overflow: hidden;
  color: #606266;
  margin: 0;
}
/*引入公共样式*/
.subpage {
  padding: 140px 20px 20px 20px;
  background: #f0f2f6;
  position: relative;
  min-height: 100%;
  overflow: hidden;
}
</style>
