<template>
  <div id="add_shop">
    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>优惠券</b></el-breadcrumb-item>
        <el-breadcrumb-item to="/coupon/new_people">新人好礼</el-breadcrumb-item>
        <el-breadcrumb-item>添加优惠券</el-breadcrumb-item>
      </el-breadcrumb>
    </el-col>
    <!--基本信息-->
    <div class="model">
      <div class="content">
        <div class="head">添加优惠券</div>
        <div id="head"></div>
        <div class="rebate">
          <el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">

            <el-form-item label="优惠券名称：" prop="title">
              <el-input class="w400" v-model="form_data.title" placeholder="请输入优惠券名称"></el-input>
            </el-form-item>

            <el-form-item class="text_ccc" label="优惠券类型：" prop="">
              <el-radio v-model="form_data.one_level_distribution_type" label="1">直减券</el-radio>
              <el-radio v-model="form_data.one_level_distribution_type" label="2">满减券</el-radio>
            </el-form-item>


            <el-form-item label="使用范围：" prop="">
              <el-radio v-model="form_data.one_level_distribution_type" label="1">全场优惠券</el-radio>
              <el-radio v-model="form_data.one_level_distribution_type" label="2">指定商品优惠券</el-radio>
            </el-form-item>

            <el-form-item label="日期设置：" prop="">
              <el-radio v-model="form_data.one_level_distribution_type" label="1">指定使用时间</el-radio>
              <el-radio v-model="form_data.one_level_distribution_type" label="2">领取后开始计算</el-radio>
            </el-form-item>

            <el-form-item label="优惠券说明：" prop="url">
              <el-input type="textarea" :rows="4" class="w400" v-model="form_data.url" placeholder="500字以内"></el-input>
            </el-form-item>

            <el-form-item class="mar_T_50">
              <el-button type="primary" @click="save()">保存</el-button>
              <el-button @click="back">返回</el-button>
            </el-form-item>
          </el-form>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript" src="../../../static/js/coupon/add_people.js">
</script>

<style scoped>
@import "../../../static/css/global.css";
/*引入公共样式*/

.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}

.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  line-height: 178px;
  text-align: center;
}

.avatar {
  width: 178px;
  height: 178px;
  display: block;
}

#add_shop .rebate .label {
  width: 100px !important;
}

#add_shop #operation {
  width: 300px;
  height: 40px;
  margin: 10px 0 0 50px;
  display: block;
  overflow: hidden;
}
</style>
