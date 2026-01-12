<template>
	<div id="order_detail">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item ><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item :to="{path: '/index/pingce_jilu_list'}">评测记录列表</el-breadcrumb-item>
				<el-breadcrumb-item>评测记录详情</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">

            <div class="top_box" style="display:flex; ">
 
				<div class="left_box" style=" margin-right:20px;flex:1;">
					<div class="content2" style="background:#fff;">
						<div class="head">患者信息</div>
						<div style="padding: 0 20px;">
							<p style="font-size:14px;color:#303133;"><span>患者性别：</span>{{detail_data.gender_name}}</p>
							<p style="font-size:14px;color:#303133;"><span>患者年龄：</span>{{detail_data.age}}</p>
							<p style="font-size:14px;color:#303133;"><span>患者教育程度：</span>{{detail_data.education_name}}</p>
							<p style="font-size:14px;color:#303133;"><span>疾病类型：</span>{{detail_data.disease_type_name}}</p>
							<p style="font-size:14px;color:#303133;"><span>病情严重程度：</span>{{detail_data.illness_name}}</p>
							<p style="font-size:14px;color:#303133;"><span>行走能力：</span>{{detail_data.walk_name}}</p>
							<p style="font-size:14px;color:#303133;"><span>确诊前兴趣爱好：</span>{{detail_data.hobby_name}}</p>
						</div>
					</div>
					<div class="content2" style="background:#fff;">  
						<div class="head" style="margin-bottom:30px;"> 用户测评问题</div>
						<div class="question_box" v-for="item in question_data" :key="item.id">  
						<el-alert :title="item.name" type="info"   :closable="false">  </el-alert>
				    	<div class="list_box">
							<div class="list_item" v-for="question in item.capability" :key="question.id">
								<div class="one_title">{{question.capability[0].sn + '-' +question.capability[0].name}}</div> 
								<!-- <div class="two_title">1.1 忘记吃过饭，吃完还想吃（有效度20%）</div>  -->
                                <div  class="answer_box">
									<div class="answer_item" v-for="(question_item,index) in question.options" :key="question_item.id">
										<div class="answer_left">{{index + 1}}. {{question_item.name}}</div>
										<!-- <div class="answer_right">有用</div> -->
									</div> 
								</div>  
							</div> 
						</div>
					</div>
				    </div>
				</div>
               
                <div class="content2" style="background:#fff;flex:1;">
					<div class="head" style="margin-bottom:30px;">
					<span>照护方案</span> 
					<span class="dqr_zhfa_title color_red">待确认照护方案</span> 
				</div>
               

				<!--  照护方案 -->  
				<div class="dqr_zhfa_box">
					<div class="dqr_zhfa_content">

						<div class="dqr_zhfa_list" v-for="one_item in program_data" :key="one_item.id">
							<el-alert :title="one_item.program_class_name" type="info"   :closable="false">  </el-alert> 
							
							<div v-for="(two_item,i) in one_item.program_care" :key="i">
								<div class="is_caina_text">
									<span class="caina_title"> {{ i+1}}.编号：{{two_item.sn}} 
										<span v-if="two_item.advice == 'problem_advice'">({{two_item.content}})</span>	
									</span>  
								</div>
								<div class="conent_item_box"> 
									<div v-if="two_item.advice_child && two_item.advice_child.length"> 
									  <div  v-for="(advice_item,advice_index) in two_item.advice_child" :key="advice_index"> 
										   <div class="is_caina_text" style="margin-bottom:10px;">
												<span class="caina_title">{{ advice_index+1}}.编号：{{advice_item.sn}}</span>  
											</div>
											<div v-for="(item,index) in advice_item.content" :key="index">
												
												<div class="content_warper" > 
													<div class="one_title one_title_auther" v-if="item.type == 'text'">
													    <!-- {{item.con}} -->
														<el-input class="my_textarea" rows="5"  type="textarea" v-model="item.con"></el-input>
													</div> 

													<div class="one_title_auther" v-if="item.type == 'image'">
														<img  class="content_img " :src="item.con" >
													</div>
 
													<div class="one_title_auther" v-if="item.type == 'video'"> 
														<video width="320" height="240" controls>
															<source :src="item.con"  type="video/mp4">  
														</video> 
													</div>
											
													<div class="btn_list">
														<el-button   class="item_btn" size="mini"  @click="open_dialog('text',index,advice_item.content)">添加文本</el-button>
														<el-button type="primary" class="item_btn" size="mini" @click="open_dialog('image',index,advice_item.content)">添加图片</el-button> 
														<el-button type="danger" class="item_btn" size="mini" @click="del_item(index,advice_item.content)">删除此项</el-button> 
													</div>	
												
												</div>
												
											</div>
									  </div>
									  
									</div>
 
									<div v-if="two_item.advice != 'problem_advice'">  
										<div  class="content_warper" v-for="(item,index) in two_item.content" :key="index"> 
											<div class="one_title one_title_auther" v-if="item.type == 'text'">
												<el-input rows="5" class="my_textarea" type="textarea" v-model="item.con"></el-input>
											</div> 

											<div class="one_title_auther" v-if="item.type == 'image'">
												<img  class="content_img " :src="item.con" >
											</div>

											<div class="one_title_auther" v-if="item.type == 'video'"> 
												<video width="320" height="240" controls>
													<source :src="item.con"  type="video/mp4">  
												</video> 
											</div>

											<div class="btn_list">
												<el-button   class="item_btn" size="mini"  @click="open_dialog('text',index,two_item.content)">添加文本</el-button>
												<el-button type="primary" class="item_btn" size="mini" @click="open_dialog('image',index,two_item.content)">添加图片</el-button> 
												<el-button type="danger" class="item_btn" size="mini" @click="del_item(index,two_item.content)">删除此项</el-button> 
											</div>
										</div> 
									</div>
									 
									
								</div>

							</div>
							
						</div> 

					</div>
				</div>
 
                </div>

            </div>
			 
			<div class="btn_group_box">
				<el-button type="primary"   @click="confirm_send"> 确认发送</el-button>
				<el-button type="primary"   @click="back">返回</el-button> 
			</div>


		<!-- <el-dialog title="提示" :visible.sync="confirm_dialog" width="30%" :close-on-click-modal="false"
			:show-close="false">  
			   <p style="margin-bottom:20px;">发送方案后，次日照护者就会收到照护方案通知</p>
				<div class="btn-box" style="text-align:center;">
					<el-button size="mini" type="primary" @click="confirm_send">确认</el-button>
					<el-button size="mini" @click="close_confirm_send">取消</el-button> 
				</div>
				</el-form> 
		</el-dialog> -->


		<!-- 弹窗 -->
		<el-dialog
			title="编辑内容"
			:visible.sync="dialogVisible"
			width="45%"
			:close-on-click-modal="false"
			:show-close="false"> 
			<el-form ref="form_data_dialog" :model="form_data_dialog" :rules="rules_dialog" label-width="140px" style="width: 80%;">
					  
					<el-form-item label="内容：" v-if="type == 'text'" >
						<el-input class="w400" :rows="5" type="textarea"  v-model="form_data_dialog.text" placeholder="请输入内容"></el-input>
					</el-form-item>
 
					<el-form-item label="图片："  v-if="type == 'image'" >
						<el-upload   :headers="upload_header" accept="image/*" :on-exceed='descExceed' :limit="1"
							:on-remove='del_img' :action="upload_img_url" :file-list="form_data_dialog.picture"
							list-type="picture-card" :on-success="img_succ" :data="postData">
							<i class="el-icon-plus"></i>
							<div style="font-size: 12px; color: #606266;" slot="tip">文件格式：gif、jpg，不超过2M。
							</div>
						</el-upload>
					</el-form-item>
 
					<el-form-item class="mar_T_50">
						<el-button type="primary" @click="confirm_add">保存</el-button>
						<el-button @click="close_dialog">取 消</el-button>
					</el-form-item>

			</el-form>
 
		</el-dialog>

		</div>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/index/edit_jilu.js"></script>

<style>

.item_btn {
	margin-bottom: 10px;
	margin-left: 0 !important;
}

.one_title_auther {
	border: 1px solid #eee;
	padding: 10px 10px 10px 0 ;
	flex: 1;
	margin-right: 20px;
}

.my_textarea  textarea{
	border:none;
	outline: none; 
}

.content_warper {
	display:flex; 
	justify-content: space-between;  
	margin-bottom: 20px;
}

.btn_list {
	display:flex; 
	flex-direction: column; 
	justify-content: center; 
}

.btn_group_box {
	margin-top:20px;
	text-align: center;
}
.music_list {
	margin-top:20px;
}
.music_status,
.music_title {
	 margin-right: 20px;
}

.download_btn {
	 margin-left: 20px;
}

.music_item {
	display:flex; 
	margin-bottom:20px;
	align-items: center;
}

.diaocha_box {
	padding: 0 20px;
}

.diaocha_item_left {
  margin-right: 50px;
  width:150px;
}

.diaocha_item {
	display:flex;
	margin-bottom:20px;
}

.is_caina_text {
	display:flex;
	justify-content: space-between;
	margin: 20px 20px 0;
}

.caina_title { 
	font-size: 16px;
}

.content_img {
	height: 100px;
	margin:10px 0;
}

.conent_item_box {
	margin: 20px ;
}

.dqr_zhfa_content {
	padding: 0 20px;
}
.color_red {
	color:red;
}

.dqr_zhfa_title { 
	font-size: 16px;
	margin-left: 50px;
}

.no_reslut_box {
	padding: 0 50px;  
	font-weight: bold;
	text-align: center;
}
.error_tip {
	margin: 20px 0;
	color: #666;
}
 

.user_avatar {
	width: 80px;
	height: 80px;
	border-radius: 50%;
	vertical-align: middle;
}
  
.question_box {
	padding: 0 30px;
}

.list_box {
	padding-top: 30px;
}

.list_item {
	margin-bottom: 30px;
}

.one_title {
	font-size: 18px; 
	color: #333;
}
.two_title {
	margin: 10px 0;
	font-size: 16px; 
	color: #333;
}
.answer_item{
	max-width: 450px;
	display: flex; 
	align-items: center;
	justify-content: space-between;
	font-size: 14px; 
	padding: 10px;
}

.signPic{
	width: 200px;
	height: 100px;
	margin-right: 10px;
}
.floorPic{
	width: 100px;
	height: 100px;
	margin-right: 40px;
}
.dianwei_modual{
	font-size: 14px;
}
/* .dianwei_modual .dianwei_content{
	display: flex;
	justify-content: space-between;
} */
.dianwei_modual .title{
	margin: 32px 0;
}
#order_detail .dianwei_modual .dianwei_content .dianwei1{
	margin: 32px 0 0 0;
}
#order_detail .dianwei_modual .dianwei_content .dianwei1 .pre_txt{
	margin-left: 10px;
}
#order_detail .dianwei_modual .zhujuPic{
	width: 100px;
	height: 100px;
	margin-right: 20px;
}
#order_detail .dianwei_modual .zhuju_modual{
	display: flex;
	align-items: center;
}
#order_detail .dianwei_modual .zhuju_modual p{
	line-height: 32px;
	margin: 0;
}
#order_detail .backABtn{
	margin: 30px 0 0 20px;
}
</style>
