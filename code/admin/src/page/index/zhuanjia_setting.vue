<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item >专家干预设置</el-breadcrumb-item> 
			</el-breadcrumb>
		</el-col>
		<div class="content"> 
			<div class="xcx-content" style="padding-top:50px;">
				<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
					 
                    <el-form-item label="专家头像：" prop="picture" >
						<el-upload   :headers="upload_header" accept="image/*" :on-exceed='descExceed' :limit="1"
							:on-remove='del_img' :action="upload_img_url" :file-list="form_data.picture"
							list-type="picture-card" :on-success="img_succ" :data="postData">
							<i class="el-icon-plus"></i>
							<div style="font-size: 12px; color: #606266;" slot="tip">请上传专家头像,建议大小：200x200不超过1M
							</div>
						</el-upload>
					</el-form-item>

                    <el-form-item label="照护方案：" >
                        <el-checkbox v-model="form_data.checked">专家确认后发送给照护者</el-checkbox>
					</el-form-item>
 
					<el-form-item label="发送时间：" prop="time">
						<el-time-select
                            v-model="form_data.time"
                            :picker-options="{
                                start: '08:30',
                                step: '00:30',
                                end: '20:30'
                            }"
                            placeholder="选择时间">
                            </el-time-select>
					</el-form-item>
				    
					<el-form-item class="mar_T_50">
						<el-button type="primary" @click="save()">保存</el-button>
					</el-form-item>

				</el-form>
			</div>
		</div>
	</div>
</template>
<script src="../../../static/js/index/zhuanjia_setting.js"></script>
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