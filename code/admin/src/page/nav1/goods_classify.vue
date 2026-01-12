<template>
    <div id="subpage">
        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>首页</b></el-breadcrumb-item>
                <el-breadcrumb-item>首页楼层</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>

        <div class="content">
            <div class="xcx-head">
                <span class="title">楼层列表</span>
                <span class="xcx-add left font14" @click="add()">添加楼层</span>
            </div>
            <div class="xcx-content">
                <!--列表-->
                <el-table border :data="tableData" stripe style="width: 100%">
                    <el-table-column prop="name" label="标签名称"></el-table-column>
                    <el-table-column prop="picture" label="图片" width="200">
                        <div slot-scope="scope">
                            <img class="img_rectangle" :src="scope.row.picture" alt="">
                        </div>
                    </el-table-column>
                    <el-table-column prop="url" label="链接地址"></el-table-column>
                    <el-table-column prop="sort" label="排序值" width="80"></el-table-column>
                    <el-table-column prop="label_count" label="关联产品数量" width="120"></el-table-column>
                    <el-table-column prop="status" label="状态" width="80"></el-table-column>
                    <el-table-column label="操作">
                        <div slot-scope="scope" class="doSonimg_box font14">
                            <span class="text primary" @click="edit(scope.row.id)">编辑</span>
                            <span class="text danger" @click="del_item(scope.row.id)">删除</span>
                        </div>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript" src="../../../static/js/goods_classify.js"></script>
