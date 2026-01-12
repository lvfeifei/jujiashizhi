<template>
    <div id="subpage">

        <el-breadcrumb separator="/">
            <el-breadcrumb-item><b>系统设置</b></el-breadcrumb-item>
            <el-breadcrumb-item :to="{path: '/set_up/role_list'}">角色管理</el-breadcrumb-item>
            <el-breadcrumb-item>{{role_id ? '修改' : '添加'}}角色</el-breadcrumb-item>
        </el-breadcrumb>

        <div class="content">
            <div class="xcx-head">
                <span class="title">{{role_id ? '修改' : '添加'}}角色</span>
            </div>
            <div class="xcx-content">

                <el-form :model="ruleForm" ref="ruleForm" :rules="reles" label-width="100px" class="w500">

                    <el-form-item prop="name" label="角色名称：">
                        <el-input v-model="ruleForm.name" placeholder="请输入角色名称"></el-input>
                    </el-form-item>

                      <el-form-item label="角色权限：">
                        <el-tree :data="data2" show-checkbox highlight-current node-key="id" @check="handleNodeClick"  :default-checked-keys="default_select" :props="defaultProps">
                        </el-tree>
                    </el-form-item>

                    <el-form-item prop="sort" label="排序：">
                        <el-input v-model="ruleForm.sort" placeholder="请输入角色名称"></el-input>
                    </el-form-item>

                    <el-form-item class="w500" label="是否开启：" prop="status">
                        <el-radio v-model="ruleForm.status" label="1">开启</el-radio>
                        <el-radio v-model="ruleForm.status" label="2">关闭</el-radio>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="primary" @click="save('ruleForm')">保存</el-button>
                        <router-link to="/set_up/role_list">
                            <el-button>返回</el-button>
                        </router-link>
                    </el-form-item>
                </el-form>

            </div>
        </div>
    </div>
</template>
<script src="../../../static/js/set_up/add_role.js"></script>

<style scoped>
</style>


