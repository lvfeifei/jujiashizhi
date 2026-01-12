<template>
    <div id="shop_grouping">
        <div id="content">
            <div id="head">
                <div class="model" style="width: 220px;">
                    <span class="title">分类名称：</span>
                    <el-input style="position: absolute;left: 70px;top: 20px;" v-model="class_name"
                              placeholder="请输入商品名称"></el-input>
                </div>
                <div class="model">
                    <el-button size="mini" @click="search">筛选</el-button>
                </div>
                <router-link class="release" to="/nav1/add_grouping"><span>新建商品分组</span></router-link>
            </div>

            <div id="list">
                <el-table :data="tableData.slice((page-1)*limit,page*limit)" stripe style="width: 97%;margin: 0 1.5%;">
                    <el-table-column prop="name" label="分组名称">
                    </el-table-column>
                    <el-table-column prop="number" label="商品数">
                    </el-table-column>
                    <el-table-column prop="add_time" label="创建时间" width="200">
                    </el-table-column>
                    <el-table-column prop="weight" label="权重">
                    </el-table-column>
                    <el-table-column prop="src" label="小程序链接">
                    </el-table-column>
                    <el-table-column prop="operation" label="操作">
                        <template slot-scope="scope">
                            <el-button @click="edit(scope.row.id)" type="text" size="small">编辑</el-button>
                            <el-button type="text" @click="inquiry(scope.row.id)" size="small"><span style="color: red">删除</span></el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="block">
                    <el-pagination @current-change="handleCurrentChange" background layout="prev, pager, next" :total="count"></el-pagination>
                    <span class="demonstration">共 {{ count }}条    每页10条</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript" src="../../../static/js/shop_grouping.js"></script>

<style scoped>
	@import '../../../static/css/global.css';/*引入公共样式*/
</style>