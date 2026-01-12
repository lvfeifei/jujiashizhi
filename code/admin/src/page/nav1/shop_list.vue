<template>
  <div id="subpage">
    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>商品管理</b></el-breadcrumb-item>
        <el-breadcrumb-item>商品列表</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>

    <div class="content">
      <div class="xcx-head">

        <div>
          <el-input clearable style="width: 400px;" placeholder="请输入关键字" v-model="key" class="input-with-select">
            <el-select clearable v-model="class_id" slot="prepend" placeholder="请选择" class="w120">
              <el-option v-for='( item, i ) in typeList' :key='i' :label="item.name" :value="item.id"></el-option>
            </el-select>
          </el-input>

          <el-button type="primary" class="mar_L_30 right" @click="search()" plain>筛选</el-button>
        </div>
        <span class="xcx-add left font14" @click="toAdd">发布商品</span>
      </div>
      <div class="xcx-content">
        <el-tabs class="tabs" v-model="activeName" @tab-click="handleClick">
          <el-tab-pane v-bind:label="item.text" v-bind:name="item.name" v-for="item in label" :key="item.id">
            <template>
              <el-table border :data="tableData" stripe style="width: 100%">
                <el-table-column prop="goods" label="商品" width="400">
                  <div slot-scope='scope' class="flex_align_center mar_L_10">
                    <img class="img_square" :src="scope.row.picture" alt="">
                    <div class="mar_L_20 h120 dis_fd_1 dis_sb" style="text-align: left;width:200px;">
                      <div class="shop_tit" style="webkiBoxOrient:vertical">{{scope.row.title}}</div>
                      <span>价格: <span class="red">¥{{scope.row.min_price}}</span></span>
                    </div>
                  </div>
                </el-table-column>
                <el-table-column prop="total_stock" label="总库存" width="90"></el-table-column>
                <el-table-column prop="remaining_stock" label="剩余库存">
                  <div slot-scope="scope">
                    <span v-if="scope.row.remaining_stock > 0">{{scope.row.remaining_stock}}</span>
                    <span v-else class="danger">{{scope.row.remaining_stock}}</span>
                  </div>
                </el-table-column>
                <el-table-column prop="sale_count" label="总销量" width="90"></el-table-column>
                <el-table-column prop="evaluate_count" label="评论未处理数" width="120"></el-table-column>
                <el-table-column prop="status_text" label="状态" width="80"></el-table-column>
                <el-table-column prop="sort" label="排序" width="80"></el-table-column>
                <el-table-column label="操作">
                  <div slot-scope="scope" class="dis_fd font14">
                    <span class="text primary" @click="toEdit(scope.row.id)">编辑</span>
                    <span class="text primary"
                      @click="lowershelf(scope.row)">{{scope.row.status == 1 ? '下架' : '上架'}}</span>
                    <span class="text danger" @click="del_item(scope.row)">删除商品</span>
                    <span class="text primary" @click="to_comm_list(scope.row)">评论列表</span>
                    <span class="text primary" @click="see_link(scope.row.id)">链接地址</span>
                  </div>
                </el-table-column>
              </el-table>
            </template>
          </el-tab-pane>
        </el-tabs>
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

<script type="text/javascript" src="../../../static/js/shop_list.js">
</script>

<style>
.shop_tit {
  width: 100%;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  overflow: hidden;
  /*! autoprefixer: off */
  -webkit-box-orient: vertical;
  /*! autoprefixer: on */
}
/*引入公共样式*/
</style>
