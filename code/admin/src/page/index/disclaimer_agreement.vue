<template>
    <!-- 免责协议页面 -->
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item >免责协议</el-breadcrumb-item>
				 
			</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
				<span class="title">免责协议</span>
			</div>
			<div class="xcx-content">
				<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
					  
					<el-form-item  > 
						<richText placeholder="请输入免责协议" ref="richText" @editor_change="editor_change"
							:describe="form_data.content">
						</richText>
					</el-form-item>
					  
					<el-form-item class="mar_T_50">
						<el-button type="primary" @click="save()">保存</el-button> 
					</el-form-item>

				</el-form>
			</div>
		</div>
	</div>
</template>
<script src="../../../static/js/index/disclaimer_agreement.js"></script>
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