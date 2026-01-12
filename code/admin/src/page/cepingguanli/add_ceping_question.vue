<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>测评管理</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/cepingguanli/ceping_question_list">测评问题列表</el-breadcrumb-item>
				<el-breadcrumb-item>{{id ? '修改' : '添加'}}测评问题</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
				<span class="title">{{id ? '修改' : '添加'}}测评问题</span>
			</div>
			<div class="xcx-content">
				<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px"  >
					<!-- <el-form-item label="问题格式：">
						<el-radio v-model="form_data.type" :label="1">单选</el-radio>
						<el-radio v-model="form_data.type" :label="2">多选</el-radio>
					</el-form-item>  -->

					<el-form-item label="问题名称：" prop="name">
						<el-input class="w400"   v-model="form_data.name" placeholder="请输入测评问题名称"></el-input>
					</el-form-item>

					<el-form-item label="问题编号：" prop="sn">
						<el-input class="w400"   v-model="form_data.sn" placeholder="请输入测评问题编号"></el-input>
					</el-form-item>

					<el-form-item label="问题分类：" prop="classify_id">
						<el-select class="w400" v-model="form_data.classify_id" clearable placeholder="请选择问题分类">
						<el-option v-for="item in evaluationclass_list"
							:key="item.id" :label="item.name" :value="item.id"> </el-option>
						</el-select>
					</el-form-item>
 
					<el-form-item label="选项内容：" >
						 <el-button class="mar_B_10" size="mini" type="primary" @click="open_dialog">添加选项</el-button>
					</el-form-item>
 
					<el-form-item label="选项列表："  > 
						<el-table border :data="tableData" stripe style="width: 100%"   @selection-change="handleSelectionChange"> 
						<el-table-column prop="sn" label="选项编号" width="80" align="center"></el-table-column>   
						<el-table-column prop="name" label="选项内容"  ></el-table-column>  
						<el-table-column  label="图片" width="180">
							<div slot-scope="scope">
								<img class="img_rectangle" v-if="scope.row.picture" :src="scope.row.picture" alt="">
								<span v-else>无</span>
							</div>
						</el-table-column>
						<el-table-column prop="sort" label="排序" width="60" ></el-table-column>
						<el-table-column  label="是否自定义" width="100"  align="center"> 
							<div slot-scope="scope"> 
								<span v-if="scope.row.type == 1">是</span>
								<span v-if="scope.row.type == 2">否</span> 
							</div>
						</el-table-column>
						<el-table-column  label="状态"  width="60"  align="center"> 
							<div slot-scope="scope"> 
								<span v-if="scope.row.status == 1">开启</span>
								<span v-if="scope.row.status == 2">关闭</span> 
							</div>
						</el-table-column>
						<el-table-column label="操作"   align="center">
							<div slot-scope="scope" class="doSonimg_box font14" style="display:block;">
								<el-button class="mar_B_10" size="mini" type="primary" @click="edit(scope.row)">编辑</el-button>
								<el-button class="mar_B_10" size="mini" type="danger" @click="del_item(scope.row)">删除</el-button> 
							</div>
						</el-table-column>
						</el-table>
					</el-form-item>
				   
					<el-form-item label="问题排序：" prop="sort">
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

		<!-- 弹窗 -->
		<el-dialog
			title="编辑选项"
			:visible.sync="dialogVisible"
			width="45%"
			:close-on-click-modal="false"
			:show-close="false">
			
			<el-form ref="form_data_dialog" :model="form_data_dialog" :rules="rules_dialog" label-width="140px" style="width: 80%;">
					  
					<el-form-item label="选项内容：" prop="name">
						<el-input class="w400"   v-model="form_data_dialog.name" placeholder="请输入选项内容"></el-input>
					</el-form-item>

					<el-form-item label="选项编号：" prop="sn">
						<el-input class="w400"   v-model="form_data_dialog.sn" placeholder="请输入选项编号"></el-input>
					</el-form-item>
 
  					<el-form-item label="是否自定义项：">
						<el-radio v-model="form_data_dialog.type" :label="1">是</el-radio>
						<el-radio v-model="form_data_dialog.type" :label="2">否</el-radio>
					</el-form-item> 
 
					<el-form-item label="选项排序：" prop="sort">
						<el-input class="w400" v-model="form_data_dialog.sort" placeholder="请输入排序数字"></el-input>
					</el-form-item>
 
 
					<el-form-item label="选项图片："  >
						<el-upload   :headers="upload_header" accept="image/*" :on-exceed='descExceed' :limit="1"
							:on-remove='del_img' :action="upload_img_url" :file-list="form_data_dialog.picture"
							list-type="picture-card" :on-success="img_succ" :data="postData">
							<i class="el-icon-plus"></i>
							<div style="font-size: 12px; color: #606266;" slot="tip">文件格式：gif、jpg，不超过2M。
							</div>
						</el-upload>
					</el-form-item>

					<el-form-item label="状态：">
						<el-radio v-model="form_data_dialog.status" :label="1">开启</el-radio>
						<el-radio v-model="form_data_dialog.status" :label="2">关闭</el-radio>
					</el-form-item> 
 
					<el-form-item class="mar_T_50">
						<el-button type="primary" @click="confirm_add">保存</el-button>
						<el-button @click="close_dialog">取 消</el-button>
					</el-form-item>

				</el-form>
 
		</el-dialog>


	</div>
</template>
<script src="../../../static/js/cepingguanli/add_ceping_question.js"></script>
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