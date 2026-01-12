<template>
    <div id="subpage">
        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>系统设置</b></el-breadcrumb-item>
                <el-breadcrumb-item to="/set_up/user_list">员工管理</el-breadcrumb-item>
                <el-breadcrumb-item>添加员工</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>

        <div class="content">
            <div class="xcx-head">
                <span class="title">添加员工</span>
            </div>
            <el-form :model="formData" :rules="rules" ref="ruleForm" class="demo-ruleForm" label-width="150px">
                <el-form-item prop="username" label="账号：">
                    <el-input :disabled="staff_id" v-model='formData.username' class="w400" clearable></el-input>
                </el-form-item>
                <el-form-item prop="password" label="密码：">
                    <el-input type="password" v-model='formData.password' class="w400" clearable></el-input>
                </el-form-item>
                <el-form-item prop="name" label="姓名：">
                    <el-input v-model='formData.name' class="w400" clearable></el-input>
                </el-form-item>
                <el-form-item prop="phone_num" label="手机号码：">
                    <el-input v-model='formData.phone_num' class="w400" maxlength="11" clearable></el-input>
                </el-form-item>
                <el-form-item prop="auth" label="权限：">
                    <div class="radio addStaff_radio">

                        <el-radio-group v-model="formData.auth">
                            <el-radio v-for="(item, i) in Role_list" :key="i" :label="item.id">{{item.name}}</el-radio>
                        </el-radio-group>
                    </div>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="submitForm('ruleForm')">保存</el-button>
                    <el-button @click="back()">返回</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script src="../../../static/js/set_up/addStaff.js"></script>

<style scoped>
.addStaff_radio .el-radio-group {
    width: 800px;
    display: flex;
    flex-wrap: wrap;
    padding-top: 15px;
}

.addStaff_radio .el-radio-group .el-radio {
    margin-left: 0 !important;
    width: 150px;
    margin-bottom: 10px;
}
</style>