<template>
	<div id="add_shop">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>基础</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/base/sign_manage">标签管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{banner_id ? '修改' : '新增'}}标签</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content">
				<div class="head">{{banner_id ? '修改' : '新增'}}标签</div>
				<div id="head"></div>
				<div class="rebate">
					<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
							<el-form-item label="标签名称：" prop="name">
									<el-input class="w400" v-model="form_data.name" placeholder="请输入标签名称"></el-input>
							</el-form-item>
                            <el-form-item label="排序：" prop="sort">
                                <el-input class="w400" v-model="form_data.sort" placeholder="请输入排序数字"></el-input>
                            </el-form-item>
                            <el-form-item label="状态：" prop="status">
                                <el-radio v-model="form_data.status" label="1">开启</el-radio>
                                <el-radio v-model="form_data.status" label="2">关闭</el-radio>
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

<script type="text/javascript" src="../../../static/js/base/add_sign.js">
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
