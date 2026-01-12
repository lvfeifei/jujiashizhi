
<template>
    <div id="subpage">

        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>用户管理</b></el-breadcrumb-item>
                <el-breadcrumb-item>关于我们</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>

        <div class="content">
            <div class="xcx-head">
                <span class="title">关于我们</span>
            </div>
            <div class="xcx-content">
                <el-form ref="form_data" :model="form_data" :rules="rules" label-width="100px" style="width: 80%;">
                    <el-form-item label="关于我们：" prop="content">
                        <!-- 富文本 -->
                        <richText placeholder='请输入内容' :disabled="false" @editor_change='editor_change' ref="richText" :describe='form_data.content'></richText>
                    </el-form-item>

                    <el-form-item class="mar_T_50">
                        <el-button type="primary" @click="save">保存</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>
<script src="../../../static/js/about_us.js"></script>

<style scoped>
</style>