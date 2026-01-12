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

                <div class="content2" style="background:#fff; margin-right:20px;">
				<div class="head">照护者信息</div>
				<div style="padding: 0 20px;">
					<!-- <p style="font-size:14px;color:#303133;"><span>照护者ID：</span>{{detail_data.id}}</p> -->
					<p style="font-size:14px;color:#303133;"><span>照护者头像：</span>
						<img class="user_avatar" :src="detail_data.avatar_url" alt=""> 
					</p>
					<p style="font-size:14px;color:#303133;"><span>照护者昵称：</span>{{detail_data.nickname}}</p>
					<p style="font-size:14px;color:#303133;"><span>性别：</span>{{detail_data.gender_name}}</p>
					<p style="font-size:14px;color:#303133;"><span>年龄：</span>{{detail_data.age}}</p>
					<p style="font-size:14px;color:#303133;"><span>教育程度：</span>{{detail_data.education_name}}</p>
					<p style="font-size:14px;color:#303133;"><span>照顾年限：</span>{{detail_data.care_years_name}}</p>
					<p style="font-size:14px;color:#303133;"><span>与患者关系：</span>{{detail_data.relation_name}}</p>
					<p style="font-size:14px;color:#303133;"><span>是否与患者同住：</span>{{detail_data.live_name}}</p>
                </div>
                </div>

                <div class="content2" style="background:#fff;">
                    <div class="head">患者信息</div>
                    <div style="padding: 0 20px;"> 
                        <!-- <p style="font-size:14px;color:#303133;"><span>患者ID：</span>{{order_detail_data.patient_age}}</p> -->
						<p style="font-size:14px;color:#303133;" v-if="detail_data.bead_house_title"><span>养老院名称：{{detail_data.bead_house_title}}</span></p> 
                        <p style="font-size:14px;color:#303133;"><span>患者性别：</span>{{order_detail_data.gender_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>患者年龄：</span>{{order_detail_data.age}}</p>
                        <p style="font-size:14px;color:#303133;"><span>患者教育程度：</span>{{order_detail_data.education_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>疾病类型：</span>{{order_detail_data.disease_type_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>病情严重程度：</span>{{order_detail_data.illness_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>行走能力：</span>{{order_detail_data.walk_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>确诊前兴趣爱好：</span>{{order_detail_data.hobby_name}}</p>
                      </div>
                </div>

            </div>
			 

			 <!-- 用户测评问题 -->
			<div class="content2" style="background:#fff; padding:20px;margin-bottom:30px;">  
                <div class="head" style="margin-bottom:30px;"> 用户测评问题</div>
                    <!-- <div class="question_box">  
						<el-alert title="一、日常生活照护问题" type="info"   :closable="false">  </el-alert>
				    	<div class="list_box">
							<div class="list_item">
								<div class="one_title">1.吃饭</div> 
								<div class="two_title">1.1 忘记吃过饭，吃完还想吃（有效度20%）</div> 
								<div class="answer_box">
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
								</div>
							</div>
							<div class="list_item">
								<div class="one_title">1.吃饭</div> 
								<div class="two_title">1.1 忘记吃过饭，吃完还想吃（有效度20%）</div> 
								<div class="answer_box">
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
								</div>
							</div>
							<div class="list_item">
								<div class="one_title">1.吃饭</div> 
								<div class="two_title">1.1 忘记吃过饭，吃完还想吃（有效度20%）</div> 
								<div class="answer_box">
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					 <div class="question_box">  
						<el-alert title="一、日常生活照护问题" type="info"   :closable="false">  </el-alert>
				    	<div class="list_box">
							<div class="list_item">
								<div class="one_title">1.吃饭</div> 
								<div class="two_title">1.1 忘记吃过饭，吃完还想吃（有效度20%）</div> 
								<div class="answer_box">
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
								</div>
							</div>
							<div class="list_item">
								<div class="one_title">1.吃饭</div> 
								<div class="two_title">1.1 忘记吃过饭，吃完还想吃（有效度20%）</div> 
								<div class="answer_box">
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
								</div>
							</div>
							<div class="list_item">
								<div class="one_title">1.吃饭</div> 
								<div class="two_title">1.1 忘记吃过饭，吃完还想吃（有效度20%）</div> 
								<div class="answer_box">
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
									<div class="answer_item">
										<div class="answer_left">0. XXXXXXX</div>
										<div class="answer_right">有用</div>
									</div>
								</div>
							</div>
						</div>
					</div> -->

					<div class="question_box" v-for="item in question_data" :key="item.id">  
						<el-alert :title="item.name" type="info"   :closable="false">  </el-alert>
				    	<div class="list_box">
							<div class="list_item" v-for="question in item.capability" :key="question.id">
								<div class="one_title">{{question.capability[0].sn + '-' +question.capability[0].name}}</div> 
								<!-- <div class="two_title">1.1 忘记吃过饭，吃完还想吃（有效度20%）</div>  -->
                                <div  class="answer_box_wrapper">
									<div class="answer_item_wrapper" v-for="(question_item,index) in question.options" :key="question_item.id">
										<div class="answer_left">{{index + 1}}. {{question_item.name}}</div>
										<!-- <div class="answer_right">有用</div> -->
										 <div  class="answer_box">
											<div class="answer_item"  v-for="(program_child,program_child_index) in question_item.program_child" :key="program_child.id">
												<div class="answer_left">
													<div class="content_block"> 
														<div class="number_tip">{{index + 1}}.{{program_child_index + 1}}</div>
														<div class="content_tip">
															<div v-for="(content,content_index) in program_child.content" :key="content_index">
																<div v-if="content.type == 'text'">{{content.con}}</div> 
															</div>
														</div> 
													</div> 
												</div>
												<div class="answer_right">{{program_child.evaluate}}</div>
											</div> 
										</div>
									</div> 
								</div>  
							</div> 
						</div>
					</div>
					 
			</div>


			<!-- 照护方案 -->
			<div class="content2" style="background:#fff; padding:20px;margin-bottom:30px;">  
                <div class="head" style="margin-bottom:30px;">
					<span>照护方案</span> 
					<span class="dqr_zhfa_title color_red" v-if="order_detail_data.status == 1 ">待确认照护方案</span>
					<span class="dqr_zhfa_title" v-if="order_detail_data.status == 2">待发送照护方案, 发送时间: {{order_detail_data.confirm_send_time}}</span>
					<span class="dqr_zhfa_title" v-if="order_detail_data.status == 3">已发送照护方案</span>
					<span class="dqr_zhfa_title" v-if="order_detail_data.status == 3 && order_detail_data.is_evaluate == 2 ">待评价</span>
					<span class="dqr_zhfa_title" v-if="order_detail_data.status == 3 && order_detail_data.is_evaluate == 1 ">已评价</span>
				</div>
                   
				<!-- 请求失败 的展示 -->
			    <div class="no_reslut_box" v-if="order_detail_data.is_send == 2" >
					<div class="one_title">照护方案请求失败</div>
					<div class="answer_left error_tip">失败原因: 请求失败</div>
					<el-button type="primary" size="mini" class="request_on_btn"  @click="request_on">重新请求</el-button>
				</div> 

				<!-- 待确认照护方案 -->
				<div class="dqr_zhfa_box" v-else>
					<div class="dqr_zhfa_content">
						  
						 <div class="dqr_zhfa_list" v-for="one_item in program_data" :key="one_item.id">
							<el-alert :title="one_item.program_class_name" type="info"   :closable="false">  </el-alert> 
							
							<div v-for="(two_item,i) in one_item.program_care" :key="i">
								<div class="is_caina_text">
									<span class="caina_title">{{ i+1}}.编号：{{two_item.sn}}</span>  
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
														{{item.con}}
													</div> 

													<div class="one_title_auther" v-if="item.type == 'image'">
														<img  class="content_img " :src="item.con" >
													</div>

													<div class="one_title_auther" v-if="item.type == 'video'"> 
														<video width="320" height="240" controls>
															<source :src="item.con"  type="video/mp4">  
														</video> 
													</div>
											 
												</div>
												
											</div>
									  </div>
									  
									</div>
 
									<div v-else class="content_warper" v-for="(item,index) in two_item.content" :key="index"> 
										<div class="one_title one_title_auther" v-if="item.type == 'text'">
											{{item.con}}
										</div> 

										<div class="one_title_auther" v-if="item.type == 'image'">
											<img  class="content_img " :src="item.con" >
										</div>

										<div class="one_title_auther" v-if="item.type == 'video'"> 
											<video width="320" height="240" controls>
												<source :src="item.con"  type="video/mp4">  
											</video> 
										</div>
 
									</div>
 
								</div>

							</div>
							
						</div> 

					</div>
				</div>
 
		    </div>
 
			<!-- 失智关爱调查 -->
			<div class="content2"  v-if="order_detail_data.is_join_research == 1"  style="background:#fff; padding:20px;margin-bottom:30px;">  
                <div class="head" style="margin-bottom:30px;"> 失智关爱调查</div>
				<div class="diaocha_box">
					<div class="diaocha_item">
						<div class="diaocha_item_left">家庭态度表总分:</div>
						<div class="diaocha_item_right">
							<div class="score_text">{{research_family.score}}分</div>  
						</div>
					</div>
					<div class="diaocha_item">
						<div class="diaocha_item_left">家庭态度表明细:</div>
						<div class="diaocha_item_right">
							<div class="table_box">
								<el-table border :data="research_family.family" stripe style="width: 100%"   > 
									<el-table-column prop="family_relation_id" label="编号"  ></el-table-column>  
									<el-table-column width="300" prop="family_relation_name" label="描述"  ></el-table-column>  
									<el-table-column prop="family_relation_option_id" label="选择"  ></el-table-column>   
								</el-table>
							</div> 
						</div>
					</div>

					<div class="diaocha_item">
						<div class="diaocha_item_left">场景语音:</div>
						<div class="diaocha_item_right">
							<!-- <div class="score_text">共录制3段音频</div>   -->
							<div class="score_text"><el-button class="download_btn" size="mini" type="primary"   @click="download_file">下载音频文件</el-button></div>  
							<div class="music_list">

								<div class="music_item">
									<div class="music_title">场景1：指导失智老人吃饭的录音</div> 
									<div class="music_status" v-if="!scenes.scenes_one">
									 <el-tag>未录制</el-tag>	
									</div> 
									<template v-else>
										<audio controls >
											<source :src="scenes.scenes_one" type="audio/mpeg" /> 
										</audio>   
										<!-- <el-button class="download_btn" size="mini" type="primary"   @click="back">下载音频</el-button> -->
									</template> 
								</div>
								
								<div class="music_item">
									<div class="music_title">场景2：指导失智老人洗澡时的情景录音</div> 
									<div class="music_status" v-if="!scenes.scenes_two"><el-tag>未录制</el-tag></div> 
									<template v-else>
										<audio controls >
											<source :src="scenes.scenes_two" type="audio/mpeg" /> 
										</audio>   
										<!-- <el-button class="download_btn" size="mini" type="primary"   @click="back">下载音频</el-button> -->
									</template> 
								</div>

								<div class="music_item">
									<div class="music_title">场景3：带领老人做活动时的情景录音</div> 
									<div class="music_status" v-if="!scenes.scenes_three"><el-tag>未录制</el-tag></div> 
									<template v-else>
										<audio controls >
											<source :src="scenes.scenes_three" type="audio/mpeg" /> 
										</audio>   
										<!-- <el-button class="download_btn" size="mini" type="primary"   @click="back">下载音频</el-button> -->
									</template> 
								</div>

								<div class="music_item">
									<div class="music_title">场景4：老人出现激越行为，如摔东西、骂人、打人时的情景录音</div> 
									<div class="music_status" v-if="!scenes.scenes_four"><el-tag>未录制</el-tag></div> 
									<template v-else>
										<audio controls >
											<source :src="scenes.scenes_four" type="audio/mpeg" /> 
										</audio>   
										<!-- <el-button class="download_btn" size="mini" type="primary"   @click="back">下载音频</el-button> -->
									</template> 
								</div>

								<div class="music_item">
									<div class="music_title">场景5：与老人沟通一件具体事情是的情景录音</div> 
									<div class="music_status" v-if="!scenes.scenes_five"><el-tag>未录制</el-tag></div> 
									<template v-else>
										<audio controls >
											<source :src="scenes.scenes_five" type="audio/mpeg" /> 
										</audio>   
										<!-- <el-button class="download_btn" size="mini" type="primary"   @click="back">下载音频</el-button> -->
									</template> 
								</div>
 

							</div>
						</div>
					</div>

				</div>
			</div>
			  
			<el-button type="primary"   @click="go_edit"   v-if="order_detail_data.status == 1 && order_detail_data.is_send == 1 && is_show_bead_house">去确认照顾方案</el-button>
			<el-button type="primary"   @click="back">返回</el-button>
			<el-button type="primary"   @click="export_file">导出Excel</el-button> 

		</div>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/index/pingce_jilu_detail.js"></script>
  
<style>

.answer_left {
	padding-left: 20px;
}
.answer_box {
	padding-left: 40px;
}
.answer_item_wrapper {
	margin-top: 15px;
}

.content_tip {
	width: 500px;
}

.content_tip>div {
	margin-bottom: 10px;
}

.content_block {
	display: flex;
}

.number_tip {
	margin-right: 10px;
}

.one_title_auther {
	padding-left: 70px;
	font-size: 16px !important;
	margin: 10px;
}

/* .music_list {
	margin-top:20px;
} */
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
	width: 80%;
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
	/* max-width: 450px; */
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
