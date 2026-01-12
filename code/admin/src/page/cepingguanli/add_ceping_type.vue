<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>测评管理</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/cepingguanli/ceping_type_list">测评分类列表</el-breadcrumb-item>
				<el-breadcrumb-item>{{id ? '修改' : '添加'}}分类</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
				<span class="title">{{id ? '修改' : '添加'}}分类</span>
			</div>
			<div class="xcx-content">
				<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
					<el-form-item label="分类名称：" prop="name">
						<el-input class="w400"   v-model="form_data.name" placeholder="请输入分类名称"></el-input>
					</el-form-item>

					<el-form-item label="分类描述：" prop="name">
						<el-input class="w400"   v-model="form_data.content" placeholder="请输入分类描述"></el-input>
					</el-form-item>
				   
					<el-form-item label="分类排序：" prop="sort">
						<el-input class="w400" v-model="form_data.sort" placeholder="请输入排序数字"></el-input>
					</el-form-item>


					<el-form-item label="状态：">
						<el-radio v-model="form_data.status" :label="1">开启</el-radio>
						<el-radio v-model="form_data.status" :label="2">关闭</el-radio>
					</el-form-item> 

   
					<el-form-item class="mar_T_50">
						<el-button type="primary" @click="save()">保存</el-button>
						<el-button @click="back">返回</el-button>
					</el-form-item>

				</el-form>
			</div>
		</div>
	</div>
</template>
<script src="../../../static/js/cepingguanli/add_ceping_type.js"></script>
<style>
	.upload_banner_pic .el-upload--picture-card {
		width: 375px;
		height: 170px;
		line-height: 170px;
	}

	.upload_banner_pic .el-upload-list--picture-card .el-upload-list__item {
		width: 375px;
		height: 170px;
	}
</style>