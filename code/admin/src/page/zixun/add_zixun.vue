<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>资讯管理</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/zixun/zixun_list">资讯列表</el-breadcrumb-item>
				<el-breadcrumb-item>{{banner_id ? '修改' : '添加'}}资讯</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
				<span class="title">{{banner_id ? '修改' : '添加'}}资讯</span>
			</div>
			<div class="xcx-content">
				<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
					<el-form-item label="标题：" prop="title">
						<el-input class="w400" v-model="form_data.title" placeholder="请输入标题"></el-input>
					</el-form-item>

					<el-form-item label="资讯分类：" prop="url_type"> 
						<el-radio v-model="form_data.help_class_id" label="1">典型案例</el-radio>
						<el-radio v-model="form_data.help_class_id" label="2">共性问题解答</el-radio> 
					</el-form-item>

					<el-form-item label="小图：" prop="picture">
						<el-upload class="upload_banner_pic_small"  :headers="upload_header" accept="image/*" :on-exceed='descExceed' :limit="1"
							:on-remove='del_img' :action="upload_img_url" :file-list="form_data.picture"
							list-type="picture-card" :on-success="img_succ" :data="postData">
							<i class="el-icon-plus"></i>
							<div style="font-size: 12px; color: #606266;" slot="tip">请上传300X300大小图片，文件格式：gif、jpg，不超过1M
							</div>
						</el-upload>
					</el-form-item>

					<el-form-item label="大图：" prop="picture">
						<el-upload class="upload_banner_pic"  :headers="upload_header" accept="image/*" :on-exceed='descExceed_bigimage' :limit="1"
							:on-remove='del_bigimage' :action="upload_img_url" :file-list="form_data.bigimage"
							list-type="picture-card" :on-success="img_succ_bigimage" :data="postData">
							<i class="el-icon-plus"></i>
							<div style="font-size: 12px; color: #606266;" slot="tip">请上传690X200大小图片，文件格式：gif、jpg，不超过1M
							</div>
						</el-upload>
					</el-form-item>

				<el-form-item label="视频：" > 
					 <div class="video_box" v-if="form_data.video" > 
						   <div class="delete_icon" @click="del_h5_img"><i class="el-icon-delete"></i></div> 
						   <video  class="video_inner"  controls :src="form_data.video"></video>
					   </div>
						<el-upload  v-else class="upload_banner_pic_video"   accept=".mp4,.avi,.wmv,.mpg,.mpeg,.mov,.flv"  :on-exceed='video_descExceed' :limit="1"
							:on-remove='del_h5_img' :action="upload_video_url" :file-list="form_data.video_arr"
							list-type="picture-card" :on-success="h5_img_succ" :data="postData">
							<i class="el-icon-plus"></i>
							<div style="font-size: 12px; color: #606266;" slot="tip">请上传视频内容</div>
						</el-upload>
				</el-form-item>



					<el-form-item label="资讯内容：" prop="content"> 
						<richText placeholder="请输入资讯内容" ref="richText" @editor_change="editor_change"
							:describe="form_data.content">
						</richText>
					</el-form-item>
					 
					<el-form-item label="发布方式：" prop="url_type"> 
						<el-radio v-model="form_data.send_status" label="1">立即发布</el-radio>
						<el-radio v-model="form_data.send_status" label="2">定时发布</el-radio>
						<el-radio v-model="form_data.send_status" label="3">暂不发布</el-radio>
					</el-form-item>
					
					 <el-form-item label="发布时间：" prop="appid" v-if="form_data.send_status == 2">
						<el-date-picker
							v-model="form_data.send_time"
							type="datetime"
							placeholder="选择日期时间"
							value-format="yyyy-MM-dd HH:mm:ss">
						</el-date-picker>
					</el-form-item> 
					 
					<el-form-item label="排序：" prop="sort">
						<el-input class="w400" v-model="form_data.sort" placeholder="数字越大排序越靠前"></el-input>
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
<script src="../../../static/js/zixun/add_zixun.js"></script>
<style>

   

	.upload_banner_pic_small .el-upload--picture-card {
		width: 150px;
		height: 150px;
		line-height: 150px;
	}

	.upload_banner_pic_small .el-upload-list--picture-card .el-upload-list__item {
		width: 150px;
		height: 150px;
	}

	.upload_banner_pic .el-upload--picture-card {
		width: 300px;
		height: 170px;
		line-height: 170px;
	}

	.upload_banner_pic .el-upload-list--picture-card .el-upload-list__item {
		width: 300px;
		height: 170px;
	}


   .upload_banner_pic_video .el-upload--picture-card {
		width: 300px;
		height: 170px;
		line-height: 170px;
	}

	.upload_banner_pic_video .el-upload-list--picture-card .el-upload-list__item {
		width: 300px;
		height: 170px;
	}



	.video_box {
		position: relative;
		width: 300px; 
	}

	.video_inner,
	.video_box video {
		width: 300px;
		height: 300px;
	}

	.delete_icon {
		width: 30px;
		height: 30px; 
		position: absolute;
		top: 0;
		right: 0; 
		background: rgba(0, 0, 0, .6);
		font-size: 20px;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		z-index: 10;
	}
</style>