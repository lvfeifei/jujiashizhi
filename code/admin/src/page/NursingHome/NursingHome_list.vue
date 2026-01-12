 
<template>
  <div id="subpage">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item><strong>养老院管理</strong></el-breadcrumb-item>
      <el-breadcrumb-item>养老院管理列表页</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content">
      <div style="display: flex; padding: 0 21px; margin: 30px 0;justify-content: space-between;">
        
        <div class="left_box">
          <el-input
          class="font14 mar_L_10 w251"
          placeholder="养老院名称/地址/联系人"
          v-model="key" 
          clearable
         @change="search()"
        ></el-input>
        <el-button class="hollow_out mar_L_10" @click="search()" plainx>搜索</el-button>
         
        </div>
        
        <el-button class="mar_L_10" @click="add()" style="background-color: #0486FE;color: #fff;width: 120px;"
          >添加</el-button
        >
      </div>
 
      <div
        class="tab_tit mar_B_10"
       
      >
        <div
          class="tab_first"
          @click="addstatus(0)"
          :class="[status == 0 ? 'tab_first_color' : '']"
        >
          <b>全部</b>
        </div>
        <div class="line"></div>
        <div
          class="tab_first"
          @click="addstatus(1)"
          :class="[status == 1 ? 'tab_first_color' : '']"
        >
          <b>合作中</b>
        </div>
        <div class="line"></div>
        <div
          class="tab_first"
          @click="addstatus(2)"
          :class="[status == 2 ? 'tab_first_color' : '']"
        >
          <b>已终止</b>
        </div>
      </div>

      <div class="xcx-content">
        <!--列表   @selection-change="handleSelectionChange"-->
        <el-table
          border
          :data="tableData"
          stripe
          style="width: 100%"
        
        >
          <!-- <el-table-column type="selection" width="55"> </el-table-column> -->
          <!-- <el-table-column prop="id" label="id" align="center"></el-table-column>   -->
          <!-- <el-table-column type="selection" width="55"> </el-table-column> -->
          <el-table-column label="养老院logo" width="100">
            <div slot-scope="scope">
              <img style="width: 80px; height: 80px; border-radius: 50%"  :src="scope.row.logo" />
            </div>
          </el-table-column>
          <el-table-column prop="title" label="养老院名称"></el-table-column>
          <el-table-column prop="address" label="地址"></el-table-column>
          <el-table-column prop="name" label="联系人"></el-table-column>
          <el-table-column  prop="mobile"   label="联系电话"  ></el-table-column>
          <el-table-column   prop="username"   label="账号"   ></el-table-column>
          <el-table-column   prop="relation_name"   label="合作状态">
            <div slot-scope="scope" class="doSonimg_box font14">
                <span v-if="scope.row.status == 1">合作中</span>
                <span v-if="scope.row.status == 2">已终止</span>
            </div>
          </el-table-column>
          <el-table-column prop="user_count" label="用户数"></el-table-column> 
          <el-table-column label="操作" width="200" align="center">
          <div slot-scope="scope" class="doSonimg_box font14">
          <el-button class="mar_B_10" size="mini" type="primary" @click="edit(scope.row.id)">修改</el-button>
          <el-button class="mar_B_10" size="mini" type="danger" @click="del_item(scope.row.id)">删除</el-button> 
          <el-button class="mar_B_10" size="mini" type="success" @click="open_dialog(scope.row)">邀请码</el-button> 

          <el-dialog title="邀请码"  :visible.sync="dialogVisible"  width="25%"   >
                <p>{{dialog_visible_item.title}}</p>
                <img :src="dialog_visible_item.invitation_code"  style="width:150px; height: 150px;"/>
                <p>请使用微信扫一扫</p>
                <span slot="footer" class="dialog-footer" >
                  <el-button type="primary" style="text-align: center;" @click="down_invitation_code">下载</el-button >
                  <el-button  @click="dialogVisible=false">取消</el-button>
                </span>
              </el-dialog>
            </div>
          </el-table-column>
        </el-table>

        <!--分页-->
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/NursingHome/NursingHome_list.js"></script>
<style scoped lang="less">

/deep/.el-dialog__footer {
  text-align: center;
}
/deep/.el-button+.el-button {
    margin-left: 5px;
}
/deep/.tab_tit{
    padding: 0 32px;
}
/deep/.el-tabs__nav-wrap::after {
  width: 0;
}
/deep/.el-tabs__header {
  padding: 0;
  position: relative;
  margin: 0 30px 15px;
}

.scope_button {
  cursor: pointer;
}

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