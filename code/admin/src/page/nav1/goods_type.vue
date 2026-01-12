<template>
    <div id="subpage">
        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>商品管理</b></el-breadcrumb-item>
                <el-breadcrumb-item>商品分类</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>

        <div class="content">
            <div class="xcx-head">
                <span class="title">商品分类</span>
                <span class="xcx-add left font14" @click="add()">添加分类</span>
            </div>
            <div class="xcx-content">
                <!--列表-->
                <el-table border :data="tableData" stripe style="width: 100%">
                    <el-table-column prop="name" label="分类名称"></el-table-column>
                    <el-table-column prop="icon" label="图片">
                        <div slot-scope="scope">
                            <img class="img_lit" :src="scope.row.icon" alt="">
                        </div>
                    </el-table-column>
                    <el-table-column prop="sort" label="排序" width="80"></el-table-column>
                    <el-table-column prop="status" label="状态" width="80">
                        <div slot-scope="scope">
                            <span>{{scope.row.status == 1 ? '开启' : '关闭 '}}</span>
                        </div>
                    </el-table-column>
                    <el-table-column label="操作">
                        <div slot-scope="scope" class="doSonimg_box font14">
                            <span class="text primary" @click="edit(scope.row.id)">编辑</span>
                            <span class="text primary" @click="see_url(scope.row.id)">小程序链接</span>
                            <span class="text danger" @click="del_item(scope.row.id)">删除</span>
                        </div>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript" src="../../../static/js/goods_type.js"></script>
