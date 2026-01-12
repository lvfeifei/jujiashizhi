<template>
	<div id="add_shop">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/banner/swiper">资讯管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{article_id ? '修改' : '新增'}}文章</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content">
				<div class="head">{{article_id ? '修改' : '新增'}}文章</div>
				<div id="head"></div>
				<div class="rebate">
					<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
							<el-form-item label="文章图片：" prop="picture">
									<el-upload class="updata_img" accept="image/*" :on-exceed='descExceed' :limit="1" :on-remove='del_img' :action="upload_img_url" :headers="uploadToken" :file-list="form_data.picture" list-type="picture-card" :on-success="img_succ" :data="postData">
											<i class="el-icon-plus"></i>
											<div style="font-size: 12px; color: #606266;" slot="tip">图片大小（750px * 360px）像素，图片格式png、jpg，默认第一张为封面图</div>
									</el-upload>
							</el-form-item>
<!--                            <el-form-item label="类型：" prop="type">
                                <el-radio v-model="form_data.type" label="1">大图</el-radio>
                                <el-radio v-model="form_data.type" label="2">小图</el-radio>
                            </el-form-item>-->
                            <el-form-item label="文章标题：" prop="title">
									<el-input class="w400" v-model="form_data.title" placeholder="请输入文章标题"></el-input>
							</el-form-item>
                            <el-form-item label="文章内容：" prop="content">
                                <!-- 富文本 -->
                                <richText placeholder='请输入内容' :disabled="false" @editor_change='editor_change' ref="richText" :describe='form_data.content'></richText>
                            </el-form-item>
							<el-form-item label="文章顺序：" prop="sort">
									<el-input class="w400" v-model="form_data.sort" placeholder="请输入文章顺序"></el-input>
							</el-form-item>
                            <el-form-item label="状态：" prop="status">
                                <el-radio v-model="form_data.status" label="1">显示</el-radio>
                                <el-radio v-model="form_data.status" label="2">不显示</el-radio>
                            </el-form-item>
							<el-form-item class="mar_T_50">
									<el-button type="primary" @click="save()">确认</el-button>
									<el-button @click="back">返回</el-button>
							</el-form-item>
					</el-form>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/banner/add_article.js">
</script>

<style scoped>
	@import '../../../static/css/global.css';
	/*引入公共样式*/

	.avatar-uploader .el-upload {
		border: 1px dashed #d9d9d9;
		border-radius: 6px;
		cursor: pointer;
		position: relative;
		overflow: hidden;
	}

	.avatar-uploader .el-upload:hover {
		border-color: #409EFF;
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

	#add_shop #operation{
		width: 300px;
		height: 40px;
		margin: 10px 0 0 50px;
		display: block;
		overflow: hidden;
	}
</style>
