<template>
    <div id="subpage">
        <el-breadcrumb separator="/">
            <el-breadcrumb-item><b>首页</b></el-breadcrumb-item>
            <el-breadcrumb-item>专家互动列表</el-breadcrumb-item>
        </el-breadcrumb>

        <div class="content">
            <!-- <div class="xcx-head">
                <div style="width:100%;">
                    <el-input class="dis_input" v-model="key_words" placeholder="昵称/手机号码" clearable></el-input>
                    <el-button class="dis_button" plain @click="search()">筛选</el-button>
                </div>  
            </div> -->
            <div class="xcx-content" style="padding-top:50px;">
                <!--列表-->
                <el-table border :data="tableData" stripe style="width: 100%">
                    <el-table-column prop="user_id"  width="100" align="left" label="用户编号"></el-table-column>
                    <el-table-column prop="avatar" width="100" align="center" label="头像">
                        <div slot-scope="scope" class="user">
                            <img width="100" :src="scope.row.avatar_url" alt="头像" class="user_picture"
                            />
                        </div>
                    </el-table-column> 
                    <el-table-column prop="create_time"  align="center" label="互动时间"></el-table-column>
                    <el-table-column prop="no_read_count"  align="center" label="未读数量"></el-table-column>
                    <el-table-column label="操作"   align="center">
                        <div slot-scope="scope" class="dis_fd">
                            <span class="text primary" @click="to_detail(scope.row.user_id)">查看</span>
                        </div>
                    </el-table-column>
                </el-table>

                <!--分页-->
                <div class="paging">
                    <el-pagination
                        class="left"
                        @current-change="handleCurrentChange"
                        :current-page="page"
                        background
                        layout="prev, pager, next"
                        :total="count"
                    ></el-pagination>
                    <span class="demonstration left">共 {{ count }} 条 每页10条</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script src="../../../static/js/index/user_chat.js"></script>

<style scoped>
    .dis_input {
        width: 20%;
        margin-right: 20px;
    }

    .dis_button {
        width: 79px;
        border: 1px solid #409eff;
        color: #409eff;
    }
    .user {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user_picture {
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }
    .user_name {
        display: inline-block;
        margin-left: 20px;
    }
</style>