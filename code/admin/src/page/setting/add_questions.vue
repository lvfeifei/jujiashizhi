<template>
    <div id="add_grouping">
        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>用户管理</b></el-breadcrumb-item>
                <el-breadcrumb-item :to="{path: '/setting/questions'}">常见问题</el-breadcrumb-item>
                <el-breadcrumb-item>{{ (question_id ===0) ? '新增常见问题' : '修改常见问题'}}</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>
        <div id="content">
            <div class="xcx-head" style="margin: 0;">{{ (question_id ===0) ? '新增常见问题' : '修改常见问题'}}</div>
            <div id="rebate">
                <div class="input_model">
                    <span class="label" style="float: left;">标题：&nbsp;&nbsp;</span>
                    <el-input v-model="question_name" placeholder="请输入分组名称" clearable></el-input>
                </div>
                <div class="">
                    <span class="label" style="float: left;">答案：&nbsp;&nbsp;</span>
                    <el-input type="textarea" rows="10"  style="width: 450px;" v-model="answer" placeholder="请输入答案" clearable></el-input>
                </div>
                <div class="input_model">
                    <span class="label" style="float: left;">排序：&nbsp;&nbsp;</span>
                    <el-input v-model="question_sort" placeholder="请输入排序" clearable></el-input>
                </div>
                <div id="operation">
                    <el-button size="mini" v-if="question_id === 0" @click="addQuestions">保存</el-button>
                    <el-button size="mini" v-else @click="editQuestion">修改</el-button>
                    <el-button size="mini" @click="cancel">返回</el-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript" src="../../../static/js/add_questions.js">
</script>

<style scoped>
@import "../../../static/css/global.css";
</style>