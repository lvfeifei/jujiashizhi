<template>
    <div id="subpage">
        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>基础</b></el-breadcrumb-item>
                <el-breadcrumb-item>区域管理</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>
        <div class="content">
            <div class="xcx-head">
                <div style="display: flex">
                    <el-input class="font14" style="width:240px;" placeholder="按区域名称搜索" v-model="key" clearable></el-input>
                    <el-button class="hollow_out mar_L_10" @click="search()" plain>确认</el-button>
                </div>
                <span class="xcx-add left font14" @click="add()">新增区域</span>
            </div>
            <div class="xcx-content">
                <!--列表-->
                <el-table border :data="tableData" stripe style="width: 100%">
                    <el-table-column prop="name" label="区域名称"></el-table-column>
                    <el-table-column prop="create_time" label="添加时间"></el-table-column>
                    <el-table-column label="操作">
                        <div slot-scope="scope" class="doSonimg_box font14">
                            <span class="text primary" @click="edit(scope.row.id)">编辑</span>
                            <span class="text danger" @click="del_item(scope.row.id)">删除</span>
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

<script type="text/javascript" src="../../../static/js/base/area_manage.js"></script>
