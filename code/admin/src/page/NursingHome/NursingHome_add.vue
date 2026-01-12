<template>
  <div id="subpage">
    <el-col :span="24" class="warp-breadcrum">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item><b>养老院管理</b></el-breadcrumb-item>
        <el-breadcrumb-item to="/NursingHome/NursingHome_list" >养老院列表</el-breadcrumb-item
        >
        <el-breadcrumb-item  >{{ banner_id ? "修改" : "添加" }}养老院</el-breadcrumb-item
        >
      </el-breadcrumb>
    </el-col>
    <div class="content">
      <div class="xcx-head">
        <span class="title">{{ banner_id ? "修改" : "添加" }}养老院</span>
      </div>
      <div class="xcx-content">
        <el-form
          ref="form_data"
          :model="form_data"
          :rules="rules"
          label-width="140px"
          style="width: 80%"
        >
          <el-form-item label="养老院logo:" prop="picture">
						<el-upload class="upload_banner_pic_small"  :headers="upload_header" accept="image/*" :on-exceed='descExceed' :limit="1"
							:on-remove='del_img' :action="upload_img_url" :file-list="form_data.picture"
							list-type="picture-card" :on-success="img_succ" :data="postData">
							<i class="el-icon-plus"></i>
							<div style="font-size: 12px; color: #606266;" slot="tip">请上传300X300大小图片，文件格式：gif、jpg，不超过1M
							</div>
						</el-upload>
					</el-form-item>
          <el-form-item label="养老院名称:" prop="title">
            <el-input  class="w400"  v-model="form_data.title"  placeholder="请输入养老院名称"   ></el-input>
          </el-form-item>

          <el-form-item label="养老院地址:" prop="address">
            <el-input class="w400"  v-model="form_data.address"    placeholder="请输入养老院地址"
            ></el-input>
          </el-form-item>
          <el-form-item label="联系人姓名:" prop="name">
            <el-input  class="w400"  v-model="form_data.name"   placeholder="请输入联系人姓名"   ></el-input>
          </el-form-item>
          <el-form-item label="联系人电话:" prop="mobile">
            <el-input  maxlength="11"   class="w400"  v-model="form_data.mobile"  placeholder="请输入联系人电话"
            ></el-input>
          </el-form-item>
          <el-form-item label="登陆账号:" prop="username">
            <el-input class="w400" :disabled="id"  v-model="form_data.username" placeholder="请输入养老院登陆系统后台账号"
            ></el-input>
          </el-form-item>
          <el-form-item label="登陆密码:">
            <el-input  class="w400" type="password" v-model="form_data.password" placeholder="请输入养老院登陆系统后台密码"
            ></el-input>
          </el-form-item>

          <el-form-item label="合作状态:" prop="status" >
            <el-radio v-model="form_data.status" :label="1">合作中</el-radio >
            <el-radio v-model="form_data.status" :label="2">已终止</el-radio >
          </el-form-item>

          <el-form-item class="mar_T_50">
            <el-button type="primary" @click="save()">保存</el-button>
            <el-button @click="back">返回</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/NursingHome/NursingHome_add.js"></script>
<style>
.upload_banner_pic_small .el-upload--picture-card {
  width: 150px;
  height: 150px;
  line-height: 150px;
}

.upload_banner_pic_small .el-upload-list--picture-card .el-upload-list__item {
  width: 150px;
  height: 150px;
}

.upload_banner_pic .el-upload--picture-card {
  width: 300px;
  height: 170px;
  line-height: 170px;
}

.upload_banner_pic .el-upload-list--picture-card .el-upload-list__item {
  width: 300px;
  height: 170px;
}

.upload_banner_pic_video .el-upload--picture-card {
  width: 300px;
  height: 170px;
  line-height: 170px;
}

.upload_banner_pic_video .el-upload-list--picture-card .el-upload-list__item {
  width: 300px;
  height: 170px;
}

.video_box {
  position: relative;
  width: 300px;
}

.video_inner,
.video_box video {
  width: 300px;
  height: 300px;
}

.delete_icon {
  width: 30px;
  height: 30px;
  position: absolute;
  top: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.6);
  font-size: 20px;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
}
</style>