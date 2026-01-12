
<template>
    <div id="subpage">

        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>商品管理</b></el-breadcrumb-item>
				<el-breadcrumb-item :to="{path: '/nav1/shop_list'}">商品列表</el-breadcrumb-item>
                <el-breadcrumb-item>评论列表</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>

        <div class="content">
            <div class="xcx-head">
                <span>商品名称: {{goods_title}}</span>

                <div>
                    <span class="font14">状态：</span>
                    <el-select clearable class="w200" v-model="type" placeholder="请选择状态">
                        <el-option label="全部" value=" "></el-option>
                        <el-option label="待审核" value="1"></el-option>
                        <el-option label="审核通过" value="2"></el-option>
                        <el-option label="审核失败" value="3"></el-option>
                    </el-select>

                    <span class="mar_L_20 font14">关键词：</span>
                    <el-input class="w251 font14" placeholder="请输入键词" v-model="keys" clearable> </el-input>
                    <el-button class="mar_L_20" type="primary" @click="search()" plain>筛选</el-button>
                </div>
            </div>
            <div class="xcx-content">
                <!--列表-->
                <el-table border :data="tableData" stripe style="width: 100%">
                    <el-table-column label="评论用户头像">
                        <div slot-scope="scope">
                            <img class="year_cover" :src="scope.row.avatar_url" alt="">
                        </div>
                    </el-table-column>
                    <el-table-column prop="nickname" label="评论用户昵称"></el-table-column>
                    <el-table-column prop="star" label="评论星数"></el-table-column>
                    <el-table-column prop="content" label="评论内容"></el-table-column>
                    <el-table-column prop="title" label="是否处理">
                        <div slot-scope="scope">
                            <span class="danger" v-if="scope.row.status == 2"> 未处理</span>
                            <span class="success" v-else>已处理</span>
                        </div>
                    </el-table-column>
                    <el-table-column prop="create_time" label="评论时间"></el-table-column>
                    <el-table-column label="操作">
                        <div slot-scope="scope" class="dis_fd font14">
                            <span class="text primary" @click="to_detail(scope.row.id)">{{scope.row.status == 2 ? '审核' : '查看'}}</span>
                            <span class="text danger" @click="del_ele(scope.row.id)">删除</span>
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
<script src="../../../static/js/comment_list.js"></script>

<style scoped>
</style>
