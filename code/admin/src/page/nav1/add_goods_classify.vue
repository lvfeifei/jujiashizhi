<template>
    <div style="padding-bottom: 120px;" id="subpage">

        <el-breadcrumb separator="/">
            <el-breadcrumb-item><b>首页</b></el-breadcrumb-item>
            <el-breadcrumb-item to="/nav1/goods_classify">首页楼层</el-breadcrumb-item>
            <el-breadcrumb-item>{{floor_id ? '修改' : '添加'}}楼层</el-breadcrumb-item>
        </el-breadcrumb>

        <div class="content mb30">
            <div class="xcx-head">
                <span class="title">{{floor_id ? '修改' : '添加' }}楼层</span>
            </div>
            <div class="xcx-content">
                <el-form ref="formData" :model="formData" :rules="rules" label-width="140px" style="width: 80%;">

                    <el-form-item label="标签名称：" prop="name">
                        <el-input class="w400" v-model="formData.name" placeholder="请输入标签名称"></el-input>
                    </el-form-item>

                    <el-form-item label="上传图片：" prop="picture">
                        <el-upload class="updata_img" accept="image/*" :on-exceed='descExceed' :limit="1" :on-remove='del_img' :action="upload_img_url" :file-list="formData.picture" list-type="picture-card" :on-success="img_succ" :data="postData">
                            <i class="el-icon-plus"></i>
                            <div style="font-size: 12px; color: #606266;" slot="tip">图片大小（ 750px * 550px ）像素，图片格式png、jpg，默认第一张为封面图</div>
                        </el-upload>
                    </el-form-item>

                    <el-form-item label="链接地址：" prop="url">
                        <el-input class="w400" v-model="formData.url" placeholder="请输入小程序链接地址"></el-input>
                    </el-form-item>

                    <el-form-item label="排序值：" prop="sort">
                        <el-input v-model="formData.sort" class="w400" placeholder="请输入排序数字"></el-input>
                    </el-form-item>

                    <el-form-item label="关联产品：" prop="recommend_list">
                        <el-button type="primary" @click="show_dialog()">添加关联</el-button>

                        <div class="dis_fd_1 mar_T_20 w800 recommend_box" v-if="formData.recommend_list.length > 0">
                            <div class="dis_jcsb top">
                                <span>商品信息</span>
                                <span>操作</span>
                            </div>

                            <div class="dis_jcsb item" v-for="(item, i) in formData.recommend_list" :key="i">
                                <div class="dis_jcsb">
                                    <div class="img_box">
                                        <img class="img_square" :src="item.picture" alt="">
                                    </div>

                                    <div class="dis_fd_1 dis_sb right_box" style="height: 120px;">
                                        <div class="title font16 b w500">{{item.title}}</div>

                                        <div class="text_box">
                                            <div class="mar_B_6">库存：{{item.total_stock}}&nbsp;&nbsp;&nbsp;销量：{{item.sale_count}}</div>
                                            <div class="orange font16">¥{{item.min_price}}</div>
                                        </div>
                                    </div>
                                </div>
                                <span class="danger" @click="del_recommend(i)">删除</span>
                            </div>
                        </div>
                    </el-form-item>

                    <el-form-item label="状态：">
                        <el-radio v-model="formData.status" label="1">开启</el-radio>
                        <el-radio v-model="formData.status" label="2">关闭</el-radio>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <el-dialog title="添加关联" :visible.sync="dialogVisible" width="50%">

            <div class="flex_align_center">
                <el-select clearable class="w200" v-model="type" @change="recommend_type_chane" placeholder="请选择">
                    <el-option v-for='( item, i ) in goods_classify' :key='i' :label="item.name" :value="item.id"></el-option>
                </el-select>

                <el-input clearable class="w300 mar_L_20" v-model="keys" placeholder="请输入关键字进行搜索相应内容"></el-input>
                <el-button class="mar_L_20" type="primary" @click="search_recommend()" plain>筛选</el-button>
            </div>

            <el-table ref="multipleTable" :data="recommend_list" class="mar_T_20" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column type="selection" width="100">
                </el-table-column>
                <el-table-column prop="name" label="类型" width="120"></el-table-column>
                <el-table-column label="商品信息">
                    <div slot-scope="scope" class="dis_fd_1 recommend_box" style="border: 0; padding: 0;">
                        <div class="dis_jcsb item" style="border: 0; padding: 0;">
                            <div class="dis_jcsb">
                                <div class="img_box mar_R_30 mar_L_30">
                                    <img class="img_square" :src="scope.row.picture" alt="">
                                </div>

                                <div style="height: 120px; text-align: left;" class="dis_fd_1 dis_sb right_box">
                                    <div class="title font16 b">{{scope.row.title}}</div>

                                    <div class="text_box">
                                        <div>库存：{{scope.row.total_stock}}销量：{{scope.row.sale_count}}</div>
                                        <div class="orange font16">¥{{scope.row.min_price}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-table-column>
            </el-table>

            <div class="mar_T_30 dis_jcsb">
                <div class="flex_align_center">
                    <el-button type="primary" @click="Confirm_selection">选中关联推荐</el-button>
                    <el-button @click="dialogVisible = false">关闭</el-button>
                </div>

                <!--分页-->
                <div class="paging" style="padding: 0;">
                    <el-pagination class="right" @current-change="handleCurrentChange" :current-page="page" background layout="prev, pager, next" :total="count"></el-pagination>
                </div>
            </div>
        </el-dialog>

        <div class="Auditing">
            <div class="left">
                <span class="my_icon">!</span>
                <span class="text">提交前请仔细检查各项信息！</span>
            </div>
            <div class="right">
                <el-button class="w120" @click="back()">返回列表</el-button>

                <el-button class="w120" type="primary" v-loading="loading" @click="save()">保存</el-button>
            </div>
        </div>

    </div>
</template>
<script src="../../../static/js/add_goods_classify.js"></script>

<style scoped>
/* 推荐 */

.recommend_box {
    border-top: 1px solid #e5e5e5;
    border-left: 1px solid #e5e5e5;
    border-right: 1px solid #e5e5e5;
}

.recommend_box .top {
    background: #f3f4f6;
    border-bottom: 1px solid #e5e5e5;
    width: 100%;
    height: 38px;
    padding: 0 20px;
    box-sizing: border-box;
}

.recommend_box .item {
    border-bottom: 1px solid #e5e5e5;
    width: 100%;
    padding: 15px 20px;
    box-sizing: border-box;
}

.recommend_box .img_box {
    position: relative;
    width: 120px;
    height: 120px;
    margin-right: 10px;
}

.recommend_box .img_box img {
    width: 120px;
    height: 120px;
}

.recommend_box .text {
    position: absolute;
    bottom: 0;
    left: 0;
    color: #fff;
    font-size: 15px;
    background: rgba(0, 0, 0, 0.5);
    width: 124px;
    height: 22px;
    line-height: 22px;
    text-align: center;
}

.recommend_box .right_box {
    height: 80px;
    line-height: normal;
}

.recommend_box .danger {
    cursor: pointer;
}
</style>