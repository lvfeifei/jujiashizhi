<template>
    <div id="subpage">
        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>基础</b></el-breadcrumb-item>
                <el-breadcrumb-item>应对方案管理</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>
        <div class="content">
            <div class="xcx-head">
                <div style="display: flex">
                    <el-select clearable style="width:200px;" v-model="type" placeholder="请选择标签名称" class="title mar_R_10">
                        <el-option label="标签1" value="0"></el-option>
                        <el-option label="标签2" value="1"></el-option>
                        <el-option label="标签3" value="2"></el-option>
                    </el-select>
                    <el-select clearable style="width:200px;" v-model="type" placeholder="请选择区域名称" class="title mar_R_10">
                        <el-option label="区域1" value="0"></el-option>
                        <el-option label="区域2" value="1"></el-option>
                        <el-option label="区域3" value="2"></el-option>
                    </el-select>
                    <el-select clearable style="width:200px;" v-model="type" placeholder="请选择类目名称" class="title mar_R_10">
                        <el-option label="类目1" value="0"></el-option>
                        <el-option label="类目2" value="1"></el-option>
                        <el-option label="类目3" value="2"></el-option>
                    </el-select>
                    <el-button class="hollow_out mar_L_10" @click="search()" plain>确认</el-button>
                </div>
                <span class="xcx-add left font14" @click="add()">新增应对方案</span>
            </div>
            <div class="xcx-content">
                <!--列表-->
                <el-table border :data="tableData" stripe style="width: 100%">
                    <el-table-column prop="tag" label="标签"></el-table-column>
                    <el-table-column prop="desc" label="区域"></el-table-column>
                    <el-table-column prop="classify" label="类目"></el-table-column>
                    <el-table-column label="操作">
                        <div slot-scope="scope" class="doSonimg_box font14">
                            <!-- <span class="text primary" @click="edit(scope.row.id)">编辑</span> -->
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

<script type="text/javascript" src="../../../static/js/base/solution_manage.js"></script>
