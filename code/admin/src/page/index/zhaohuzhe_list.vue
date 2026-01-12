 
<template>
  <div id="subpage">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item><b>首页</b></el-breadcrumb-item>
      <el-breadcrumb-item>照护者列表</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content">
      <div class="xcx-head">
        <div style="display: flex">
          <el-select v-if="is_show_bead_house"  v-model="bead_house_id" class="w230 mar_L_10" filterable placeholder="选择养老院">
            <el-option label="选择养老院" value="0"></el-option> 
            <el-option
              v-for="(item, index) in bead_house_list"
              :key="index" :label="item.title" :value="item.id"
            ></el-option> 
          </el-select>

          <el-select  v-model="sex_id"  class="w230 mar_L_10" filterable
            placeholder="请选择性别"
          >
            <el-option label="请选择性别" value="0"></el-option>
            <el-option
              v-for="(item, index) in sex_list"
              :key="index"  :label="item.name" :value="item.id"
            ></el-option>
          </el-select>

          <el-select v-model="xueli_id" class="w230 mar_L_10" filterable>
            <el-option label="请选择学历" value="0"></el-option>
            <el-option
              v-for="(item, index) in xueli_list"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>

          <el-select v-model="year_id" class="w230 mar_L_10" filterable>
            <el-option label="请选择照护年限" value="0"></el-option>
            <el-option
              v-for="(item, index) in year_list"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>

          <el-select v-model="guanxi_id" class="w230 mar_L_10" filterable>
            <el-option label="与患者关系" value="0"></el-option>
            <el-option
              v-for="(item, index) in guanxi_list"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>

          <el-select v-model="room_id" class="w230 mar_L_10" filterable>
            <el-option label="是否与患者同住" value="0"></el-option>
            <el-option
              v-for="(item, index) in room_list"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </div>
      </div>

      <div style="display: flex; padding: 0 30px; margin-bottom: 30px">
        <el-input
          class="font14 mar_L_10 w251"
          placeholder="输入搜索内容"
          v-model="key"
          clearable
        ></el-input>
        <el-button class="hollow_out mar_L_10" @click="search()" plainx
          >搜索</el-button
        >
      </div>

      <div class="xcx-content">
        <!--列表-->
        <el-table
          border
          :data="tableData"
          stripe
          style="width: 100%"
          @selection-change="handleSelectionChange"
        > 
          <el-table-column prop="nickname" label="用户编号">
            <div slot-scope="scope">
              <div>{{scope.row.id}}</div>
              <div v-if="scope.row.bead_house_title">({{scope.row.bead_house_title}})</div>
          </div>
          </el-table-column>
          <el-table-column label="头像" width="100">
            <div slot-scope="scope">
              <img
                style="width: 80px; height: 80px; border-radius: 50%"
                :src="scope.row.avatar_url"
                alt=""
              />
            </div>
          </el-table-column>
         
          <el-table-column prop="gender_name" label="性别"></el-table-column>
          <el-table-column prop="age" label="年龄"></el-table-column>
          <el-table-column
            prop="education_name"
            label="教育程度"
          ></el-table-column>
          <el-table-column
            prop="care_years_name"
            label="照护年限"
          ></el-table-column>
          <el-table-column
            prop="relation_name"
            label="与患者关系"
          ></el-table-column>
          <el-table-column
            prop="live_name"
            label="与患者同住"
          ></el-table-column>
          <el-table-column prop="status_s" label="用户状态"></el-table-column>
          <el-table-column prop="is_agree_t" label="协议"></el-table-column>
          <el-table-column label="操作" width="100" align="center">
            <div slot-scope="scope" class="doSonimg_box font14">
              <el-button
                class="mar_B_10"
                size="mini"
                type="primary"
                @click="edit(scope.row.id)"
                >查看</el-button
              >
            </div>
          </el-table-column>
        </el-table>

        <!--分页-->
        <div class="paging">
          <!-- <el-button class="left" size="mini" type="primary" @click="delete_all">批量删除</el-button> -->
          <el-pagination
            class="left"
            @current-change="handleCurrentChange"
            :page-size="limit"
            :current-page="page"
            background
            layout="prev, pager, next"
            :total="count"
          ></el-pagination>
          <span class="demonstration left"
            >共 {{ count }} 条 每页{{ limit }}条</span
          >
        </div>
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/index/zhaohuzhe_list.js"></script>
<style scoped>
.activity_img {
  width: 160px;
  height: 90px;
}

.img_logo {
  width: 80px;
  margin: 0 auto;
}

.wx_er {
  width: 100%;
}

.wx_er .erCode {
  width: 100%;
}

/*  */
.xcx-head {
  border: none;
  margin-bottom: 0;
}

.shop_img {
  width: 50px;
  height: 50px;
  vertical-align: middle;
}
</style>