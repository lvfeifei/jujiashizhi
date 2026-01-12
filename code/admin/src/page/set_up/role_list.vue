<template>
    <div id="subpage">

        <el-breadcrumb separator="/">
            <el-breadcrumb-item><b>系统设置</b></el-breadcrumb-item>
            <el-breadcrumb-item>角色管理</el-breadcrumb-item>
        </el-breadcrumb>

        <div class="content">
            <div class="xcx-head">
                <span class="title">角色列表</span>
                <span class="xcx-add font14" @click="addRegion()">添加角色</span>
            </div>
            <div class="xcx-content">

                <el-table border :data="tableData" stripe style="width: 100%">
                    <el-table-column prop="name" label="角色名称"></el-table-column>
                    <el-table-column prop="sort" sortable label="排序"></el-table-column>
                    <el-table-column prop="status" label="状态">
                        <div slot-scope="scope">
                            <el-switch v-model="scope.row.status">
                            </el-switch>
                        </div>
                    </el-table-column>
                    <el-table-column label="操作">
                        <div slot-scope="scope" class="doSonimg_box font14">
                            <span class="text primary" @click="edit(scope.row.id)">修改</span>
                            <span class="text danger" @click="del(scope.row.id)">删除</span>
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
<script src='../../../static/js/set_up/role_list.js'></script>

<style scoped lang='less'>
</style>





