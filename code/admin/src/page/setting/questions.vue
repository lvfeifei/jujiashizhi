<template>
    <div id="shop_grouping">

        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>用户管理</b></el-breadcrumb-item>
                <el-breadcrumb-item>常见问题</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>

        <div id="content">
            <div id="head">
                <span style="line-height: 72px;font-size: 18px;">常见问题</span>
                <router-link class="release" to="/setting/add_questions"><span>添加常见问题</span></router-link>
            </div>

            <div id="list">
                <el-table id="tab" :data="tableData.slice((page-1)*limit,page*limit)" stripe style="width: 97%;margin: 0 1.5% 50px 1.5%;">
                    <el-table-column prop="title" label="问题" width="200">
                    </el-table-column>
                    <el-table-column prop="answer" label="答案">
                    </el-table-column>
                    <el-table-column prop="sort" label="排序" width="100">
                    </el-table-column>
                    <el-table-column prop="state" label="状态" width="100">
                        <template slot-scope="scope">
                            <span>{{ (scope.row.state === 1) ? '开启':'关闭' }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="operation" label="操作" width="180">
                        <div slot-scope="scope" class="doSonimg_box">
                            <span @click="edit(scope.row.id)" class="text primary">修改</span>
                            <span @click="inquiry(scope.row.id)" class="text danger">删除</span>
                        </div>
                    </el-table-column>
                </el-table>
                <div class="block">
                    <el-pagination @current-change="handleCurrentChange" background layout="prev, pager, next" :total="count"></el-pagination>
                    <span class="demonstration">共 {{ count }}条 每页10条</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript" src="../../../static/js/questions.js"></script>

<style scoped>
@import "../../../static/css/global.css"; /*引入公共样式*/
</style>