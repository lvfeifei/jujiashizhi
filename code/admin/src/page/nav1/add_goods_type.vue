<template>
    <div id="add_shop">
        <el-col :span="24" class="warp-breadcrum">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><b>商品管理</b></el-breadcrumb-item>
                <el-breadcrumb-item to="/nav1/goods_type">商品分类</el-breadcrumb-item>
                <el-breadcrumb-item>{{type_id ? '修改' : '添加'}}分类</el-breadcrumb-item>
            </el-breadcrumb>
        </el-col>
        <!--基本信息-->
        <div class="model">
            <div class="content">
                <div class="head">{{type_id ? '修改' : '添加'}}分类</div>
                <div id="head"></div>
                <div class="rebate">
                    <el-form ref="form_data" :model="form_data" label-width="140px" style="width: 80%;">

                        <el-form-item label="分类标题：" prop="name">
                            <el-input class="w400" v-model="form_data.name" placeholder="请输入分类标题"></el-input>
                        </el-form-item>

                        <el-form-item label="图片：" prop="picture" class="line_H">
                            <el-upload accept="image/*" :on-exceed='descExceed' :limit="1" :on-remove='del_img' :action="upload_img_url" :file-list="form_data.picture" list-type="picture-card" :on-success="img_succ" :data="postData">
                                <i class="el-icon-plus"></i>
                                <div style="font-size: 12px; color: #606266;margin-top:10px;" slot="tip">图片大小（750px * 550px）像素，图片格式png、jpg，默认第一张为封面图</div>
                            </el-upload>
                        </el-form-item>

                        <el-form-item label="排序：" prop="sort">
                            <el-input class="w400" v-model="form_data.sort" placeholder="请输入排序数字"></el-input>
                        </el-form-item>

                        <el-form-item label="状态：" prop="status">
                            <el-radio v-model="form_data.status" label="1">开启</el-radio>
                            <el-radio v-model="form_data.status" label="2">关闭</el-radio>
                        </el-form-item>

                        <el-form-item class="mar_T_50">
                            <el-button type="primary" @click="save()">保存</el-button>
                            <el-button @click="back">返回</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript" src="../../../static/js/add_goods_type.js">
</script>

<style scoped>
.avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.avatar-uploader .el-upload:hover {
    border-color: #409eff;
}

.avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
}

.avatar {
    width: 178px;
    height: 178px;
    display: block;
}

#add_shop .rebate .label {
    width: 100px !important;
}

#add_shop #operation {
    width: 300px;
    height: 40px;
    margin: 10px 0 0 50px;
    display: block;
    overflow: hidden;
}
</style>
