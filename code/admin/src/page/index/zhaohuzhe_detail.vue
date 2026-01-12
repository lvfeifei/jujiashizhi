<template>
	<div id="order_detail">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item ><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item :to="{path: '/index/zhaohuzhe_list'}">照护者列表</el-breadcrumb-item>
				<el-breadcrumb-item>照护者详情</el-breadcrumb-item>
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
					<p style="font-size:14px;color:#303133;"><span>是否同意协议：</span>{{detail_data.is_agree_t}}</p>
					<p style="font-size:14px;color:#303133;" v-if="detail_data.is_agree === 2"><span>同意协议时间：</span>{{detail_data.agree_time}}</p>
                </div>
                </div>

                <div class="content2" style="background:#fff;">
                    <div class="head">患者信息</div>
                    <div style="padding: 0 20px;">
                        <!-- <p style="font-size:14px;color:#303133;"><span>患者ID：</span>{{detail_data.patient_age}}</p> -->
						<p style="font-size:14px;color:#303133;" v-if="detail_data.bead_house_title"><span>养老院名称：{{detail_data.bead_house_title}}</span></p>
                        <p style="font-size:14px;color:#303133;"><span>患者性别：</span>{{detail_data.patient_gender}}</p>
                        <p style="font-size:14px;color:#303133;"><span>患者年龄：</span>{{detail_data.patient_age}}</p>
                        <p style="font-size:14px;color:#303133;"><span>患者教育程度：</span>{{detail_data.patient_education_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>疾病类型：</span>{{detail_data.patient_disease_type_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>病情严重程度：</span>{{detail_data.patient_illness_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>行走能力：</span>{{detail_data.patient_walk_name}}</p>
                        <p style="font-size:14px;color:#303133;"><span>确诊前兴趣爱好：</span>{{detail_data.patient_hobby_name}}</p>
                      </div>
                </div>

            </div>
			 
			<div class="content2" style="background:#fff; padding:20px;margin-bottom:30px;">  
                <!--列表-->
				<el-table border :data="tableData" stripe style="width: 100%"  @selection-change="handleSelectionChange"> 
					 
                    <el-table-column prop="create_time" label="测评时间"  ></el-table-column>  
					<el-table-column prop="gender_name" label="患者性别"  ></el-table-column>  
					<el-table-column prop="age" label="患者年龄"  ></el-table-column>  
					<el-table-column prop="education_name" label="患者教育程度"  ></el-table-column>  
					<el-table-column prop="disease_type_name" label="疾病类型"  ></el-table-column>  
					<el-table-column prop="illness_name" label="病情严重程度"  ></el-table-column>  
					<el-table-column prop="hobby_name" label="确诊前兴趣爱好"  ></el-table-column>  
					<el-table-column prop="walk_name" label="行走能力"  ></el-table-column>  
					<el-table-column prop="status_name" label="测评结果"  ></el-table-column>  
					<el-table-column prop="is_join_research_name" label="失智关爱研究调查"  ></el-table-column>  
                    <el-table-column label="操作"  width="200"  align="center">
						<div slot-scope="scope" class="doSonimg_box font14">
							<el-button class="mar_B_10" size="mini" type="primary" @click="edit(scope.row)">查看详情</el-button> 
                        </div> 
					</el-table-column>
				</el-table>

				<!--分页-->
			    <div class="paging">
					<el-pagination class="left" @current-change="handleCurrentChange" :page-size="limit" :current-page="page" background
						layout="prev, pager, next" :total="count"></el-pagination>
					<span class="demonstration left">共 {{ count }} 条 每页{{ limit }}条</span>
				</div> 
				
			</div>
		 
			<el-button type="primary"   @click="back">返回</el-button>
		</div>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/index/zhaohuzhe_detail.js"></script>

<style>
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


.user_avatar {
	width: 80px;
	height: 80px;
	border-radius: 50%;
	vertical-align: middle;
}
</style>
