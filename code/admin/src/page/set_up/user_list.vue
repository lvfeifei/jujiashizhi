
<template>
    <div id="subpage">

        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>系统设置</b></el-breadcrumb-item>
                <el-breadcrumb-item>员工管理</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>

        <div class="content">
            <div class="xcx-head">
                <span class="title">员工列表</span>
                <span class="xcx-add left font14" @click="addRegion()">添加员工</span>
            </div>
            <div class="xcx-content">
                <!--列表-->
                <el-table border :data="tableData" stripe style="width: 100%">
                    <el-table-column prop="username" label="员工账号"></el-table-column>
                    <el-table-column prop="truename" label="姓名"></el-table-column>
                    <el-table-column prop="mobile" label="手机号码"></el-table-column>
                    <el-table-column prop="register_time" label="添加时间"></el-table-column>
                    <el-table-column prop="role_name" label="员工权限"></el-table-column>
                    <el-table-column prop="operation" label="操作">
                        <div slot-scope="scope" class="doSonimg_box font14">
                            <span class="text primary" @click="edit(scope.row.id)">编辑</span>
                            <span class="text danger" @click="deleteRow(scope.row.id)">删除</span>
                        </div>
                    </el-table-column>
                </el-table>
                <!--分页-->
                <div class="paging">
                    <el-pagination class="left" @current-change="handleCurrentChange" :current-page="page" background layout="prev, pager, next" :total="count"></el-pagination>
                    <span class="demonstration left">共 {{ count }} 条 每页10条</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script src="../../../static/js/set_up/user_list.js"></script>

<style scoped>
</style>