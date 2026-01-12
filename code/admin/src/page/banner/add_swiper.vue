<template>
	<div id="add_shop">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/banner/swiper">banner管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{banner_id ? '修改' : '新增'}}banner</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content">
				<div class="head">{{banner_id ? '修改' : '新增'}}banner</div>
				<div id="head"></div>
				<div class="rebate">
					<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
							<el-form-item label="banner上传：" prop="picture">
									<el-upload class="updata_img" :headers="header" accept="image/*" :on-exceed='descExceed' :limit="1" :on-remove='del_img' :action="upload_img_url" :file-list="form_data.picture" list-type="picture-card" :on-success="img_succ" :data="postData">
										<i class="el-icon-plus"></i>
										<div style="font-size: 12px; color: #606266;" slot="tip">图片大小（750px * 360px）像素，图片格式png、jpg，默认第一张为封面图</div>
									</el-upload>
							</el-form-item>
							<el-form-item label="banner顺序：" prop="sort">
									<el-input class="w400" v-model="form_data.sort" placeholder="请输入排序顺序"></el-input>
							</el-form-item>
							<el-form-item label="点击跳转地址：" prop="url_type">
								<el-radio v-model="form_data.url_type" label="1">内部链接</el-radio>
								<el-radio v-model="form_data.url_type" label="2">外部链接</el-radio>
							</el-form-item>
							<el-form-item label="链接地址：" prop="url" v-if="form_data.url_type==1">
								<el-input class="w400" v-model="form_data.url" placeholder="请输入内部链接地址"></el-input>
							</el-form-item>
							<el-form-item label="链接地址：" prop="url" v-if="form_data.url_type==2">
								<el-input class="w400" v-model="form_data.url" placeholder="请输入外部链接地址"></el-input>
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

<script type="text/javascript" src="../../../static/js/banner/add_swiper.js">
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
