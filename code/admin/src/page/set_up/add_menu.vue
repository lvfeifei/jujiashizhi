<template>
  <div id="subpage">

    <el-breadcrumb separator="/">
      <el-breadcrumb-item><b>系统设置</b></el-breadcrumb-item>
      <el-breadcrumb-item :to="{path: '/set_up/menu_list'}">菜单管理</el-breadcrumb-item>
      <el-breadcrumb-item>{{!menu_id ? '添加' : '修改'}}菜单</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="content">
      <div class="xcx-head">
        <span class="title">{{!menu_id ? '添加' : '修改'}}菜单</span>
      </div>
      <div class="xcx-content">

        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="w500">
          <el-form-item v-if="ruleForm.higher_level || !menu_id" prop="higher_level" label="上级菜单：">
            <el-select clearable style="width: 400px;" v-model="ruleForm.higher_level" placeholder="请选择上级系统菜单（不选择创建一级菜单）">
              <el-option v-for="item in options" :key="item.id" :label="item.name" :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>

          <el-form-item prop="menu_name" label="菜单名称：">
            <el-input v-model="ruleForm.menu_name" placeholder="请输入菜单名称"></el-input>
          </el-form-item>

          <el-form-item prop="url" label="链接地址：">
            <el-input v-model="ruleForm.url" placeholder="请输入链接地址"></el-input>
          </el-form-item>

          <el-form-item v-if='!ruleForm.higher_level' label="图标：">
            <el-upload accept="image/png, image/jpg" :action="upload_img_url" :headers="uploadToken" class="group_img" :limit="1" :file-list="ruleForm.img" list-type="picture-card" :on-preview="descPictureCardPreview" :on-remove="descRemove" :on-success="descAvatarSuccess" :on-error="descError" :on-exceed="descExceed" :data="postData">
              <i class="el-icon-plus"></i>
            </el-upload>
          </el-form-item>

          <el-form-item prop="sort" label="排序：">
            <el-input v-model="ruleForm.sort" placeholder="请输入菜单排序"></el-input>
          </el-form-item>

          <el-form-item label="状态：">
            <el-radio v-model="ruleForm.status" label="1">开启</el-radio>
            <el-radio v-model="ruleForm.status" label="2">关闭</el-radio>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" @click="save()">保存</el-button>
            <router-link class="ml20" to="/set_up/menu_list">
              <el-button>返回</el-button>
            </router-link>
          </el-form-item>
        </el-form>

      </div>
    </div>
  </div>
</template>
<script src="../../../static/js/set_up/add_menu.js"></script>

<style scoped>
</style>


