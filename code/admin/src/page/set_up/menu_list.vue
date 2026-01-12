<template>
    <div id="subpage">

        <el-breadcrumb separator="/">
            <el-breadcrumb-item><b>系统设置</b></el-breadcrumb-item>
            <el-breadcrumb-item>菜单管理</el-breadcrumb-item>
        </el-breadcrumb>

        <div class="content">
            <div class="xcx-head">
                <span class="title ml30">菜单列表</span>
                <span class="xcx-add mr30 font14" @click="addRegion()">添加菜单</span>
            </div>
            <div class="xcx-content">

                <!-- 主体内容 -->
                <div class="menu_list_content">
                    <!-- 手写表格 -->
                    <div class="my_menu_table">

                        <!-- top -->
                        <el-row class="top">
                            <el-col class="padd_20" :span="6">
                                <!-- <el-checkbox v-model="checked"></el-checkbox> -->
                                <span>菜单图标、名称</span>
                            </el-col>

                            <el-col class="text_al" :span="6">
                                <span>链接</span>
                            </el-col>

                            <el-col class="text_al" :span="3">
                                <span>排序</span>
                            </el-col>

                            <el-col class="text_al" :span="4">
                                <span>状态</span>
                            </el-col>

                            <el-col class="text_al" :span="4">
                                <span>操作</span>
                            </el-col>
                        </el-row>

                        <!-- 菜单项 -->
                        <div v-for='(item, i) in list_data' :key="i" class="menu_item_box">
                            <el-row class="menu_item">
                                <el-col class="padd_20" :span="6">
                                    <!-- <el-checkbox v-model="checked"></el-checkbox> -->
                                    <img :src="item.icon" class="icon_box" alt="">
                                    <h3>{{item.name}}</h3>
                                </el-col>

                                <el-col class="text_al" :span="6"></el-col>

                                <el-col class="text_al" :span="3">
                                    <span>{{item.sort}}</span>
                                </el-col>

                                <el-col class="text_al" :span="4">
                                    <el-switch disabled v-model="item.status">
                                    </el-switch>
                                </el-col>

                                <el-col class="text_al" :span="4">
                                    <span class="edit" @click="edit(item.id)">修改</span>
                                    <span class="del" @click="del(item.id)">删除</span>
                                </el-col>
                            </el-row>

                            <!-- 子菜单 -->
                            <el-row v-for="(ele, index) in item.child" :key="index" class="menu_inner_item">
                                <el-col class="padd_50" :span="6">
                                    <!-- <el-checkbox v-model="checked"></el-checkbox> -->
                                    <span>{{ele.name}}</span>
                                </el-col>

                                <el-col class="text_al" :span="6">
                                    <span>{{ele.url}}</span>
                                </el-col>

                                <el-col class="text_al" :span="3">
                                    <span>{{ele.sort}}</span>
                                </el-col>

                                <el-col class="text_al" :span="4">
                                    <el-switch disabled v-model="ele.status">
                                    </el-switch>
                                </el-col>

                                <el-col class="text_al" :span="4">
                                    <span class="edit" @click="edit(ele.id)">修改</span>
                                    <span class="del" @click="del(ele.id)">删除</span>
                                </el-col>
                            </el-row>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script src='../../../static/js/set_up/menu_list.js'></script>

<style scoped>
@import "../../../static/css/set_up/menu_list.css";
</style>
