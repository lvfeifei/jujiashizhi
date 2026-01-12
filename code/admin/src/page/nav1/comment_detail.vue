
<template>
    <div id="subpage">

        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>商品管理</b></el-breadcrumb-item>
                <el-breadcrumb-item> <b>评论列表</b> </el-breadcrumb-item>
                <el-breadcrumb-item>评论详情</el-breadcrumb-item>
            </el-breadcrumb>        
        </el-col>                 

        <div class="content">
            <div class="xcx-head">
                <span class="title">评论详情</span>
            </div>
            <div class="xcx-content">
                <el-form ref="form_data" label-width="140px" style="width: 80%;">
                    <el-form-item label="商品名称: " prop="title">{{detail.title}}</el-form-item>
                    <el-form-item label="评论用户头像: " prop="title">
                        <img class="img_round" :src="detail.avatar_url" alt="">
                    </el-form-item>
                    <el-form-item label="评论用户昵称: " prop="nickname">{{detail.nickname}}</el-form-item>
                    <el-form-item label="评论星数: " prop="star">
                        <span>{{detail.star}}星</span>
                    </el-form-item>
                    <el-form-item label="评论内容: " prop="content" class="w800">{{detail.content}}</el-form-item>
                    <el-form-item label="评论图片: ">
                        <div class="w800">
                            <img class="img_square mar_R_10" v-for='( item, i ) in detail.imgs' :key='i' :src="item" alt="">
                        </div>
                    </el-form-item>
                    <el-form-item label="评论时间: " prop="title">{{detail.create_time}}</el-form-item>

                    <el-form-item v-if="detail.status == 2" label="状态: " prop="status">
                        <p>
                            <el-radio v-model="radio" label="1">审核通过</el-radio>
                            <el-radio v-model="radio" label="2">审核失败</el-radio>
                        </p> 
                        <el-input class="w400" v-if="radio == 2" v-model="reason" type="textarea" rows="6" placeholder="请输入审核失败理由"></el-input>
                    </el-form-item>

                    <el-form-item v-if="detail.status == 3" label="状态: " prop="status">
                        <span class="danger">审核失败</span>
                        <span>(理由: {{detail.reason}})</span>
                    </el-form-item>

                    <el-form-item v-if="detail.status == 1" label="状态: " prop="status">
                        <span class="success">审核成功</span>
                    </el-form-item>

                    <el-form-item class="mar_T_50">
                        <el-button type="primary" v-if="detail.status == 2" @click="save()">处理</el-button>
                        <el-button @click="back">返回</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>
<script src="../../../static/js/comment_detail.js"></script>

<style scoped>
</style>