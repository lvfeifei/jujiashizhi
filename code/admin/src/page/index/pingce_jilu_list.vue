 
<template>
	<div id="subpage">
		<el-breadcrumb separator="/">
			<el-breadcrumb-item><b>首页</b></el-breadcrumb-item>	
			<el-breadcrumb-item>评测记录列表</el-breadcrumb-item>
		</el-breadcrumb>
		<div class="content">
			<div class="xcx-head">
				<div style="display: flex"> 

			<el-select v-if="is_show_bead_house"  v-model="bead_house_id" class="w230 mar_L_10" filterable placeholder="选择养老院">
				<el-option label="选择养老院" value="0"></el-option> 
				<el-option
				v-for="(item, index) in bead_house_list"
				:key="index" :label="item.title" :value="item.id"
				></el-option> 
			</el-select>

					<el-select v-model="sex_id" class="w230 mar_L_10" filterable  placeholder="请选择性别" >
						<el-option label="请选择性别" value="0"></el-option>
                        <el-option v-for="(item,index) in sex_list" :key="index" :label="item.name" :value="item.id"></el-option>
					</el-select>

                    <el-select v-model="xueli_id" class="w230 mar_L_10" filterable  >
						<el-option label="请选择学历" value="0"></el-option>
                        <el-option v-for="(item,index) in xueli_list" :key="index" :label="item.name" :value="item.id"></el-option>
					</el-select>

                    <el-select v-model="bqcd_id" class="w230 mar_L_10" filterable  >
						<el-option label="请选择病情程度" value="0"></el-option>
                        <el-option v-for="(item,index) in bqcd_list" :key="index" :label="item.name" :value="item.id"></el-option>
					</el-select>

                    <el-select v-model="jblx_id" class="w230 mar_L_10" filterable  >
						<el-option label="请选择疾病类型" value="0"></el-option>
                        <el-option v-for="(item,index) in jblx_list" :key="index" :label="item.name" :value="item.id"></el-option>
					</el-select>

                    <el-select v-model="xznl_id" class="w230 mar_L_10" filterable  >
						<el-option label="请选择行走能力" value="0"></el-option>
                        <el-option v-for="(item,index) in xznl_list" :key="index" :label="item.name" :value="item.id"></el-option>
					</el-select>
  
				</div> 
			</div>

			<div style="display: flex;padding: 0 30px;margin-bottom: 30px;"> 
				<el-input class="font14 mar_L_10 w251" placeholder="输入搜索内容" v-model="key" clearable></el-input>
				<el-button class="hollow_out mar_L_10" @click="search()" plainx>搜索</el-button>
            </div>
 
			<div class="xcx-content"> 
				 <div class="tab_tit mar_B_10">
					<div class="tab_first" @click="addstatus(0)" :class="[status == 0 ? 'tab_first_color' : '']">
						<b>全部</b>
					</div>
					<div class="line"></div>
					<div class="tab_first" @click="addstatus(1)" :class="[status == 1 ? 'tab_first_color' : '']">
						<b>待出方案</b>
					</div>
					<div class="line"></div> 
					<div class="tab_first" @click="addstatus(2)" :class="[status == 2 ? 'tab_first_color' : '']">
						<b>待发送方案</b>
					</div> 
					<div class="line"></div>
					<div class="tab_first" @click="addstatus(3)" :class="[status == 3 ? 'tab_first_color' : '']">
						<b>已发送方案</b>
					</div>
					 <div class="line"></div>
					<div class="tab_first" @click="addstatus(4)" :class="[status == 4 ? 'tab_first_color' : '']">
						<b>待评价</b>
					</div>
 					<div class="line"></div>
					<div class="tab_first" @click="addstatus(5)" :class="[status == 5 ? 'tab_first_color' : '']">
						<b>已评价</b>
					</div>
				</div>

				<!--列表-->
				<el-table border :data="tableData" stripe style="width: 100%"  @selection-change="handleSelectionChange"> 
					<!-- <el-table-column type="selection" width="55"> </el-table-column> -->
					<!-- <el-table-column prop="id" label="id" align="center"></el-table-column>   -->
					<el-table-column prop="user_id" label="用户编号"  ></el-table-column>  
					<el-table-column prop="id" label="患者编号"  ></el-table-column>   
                    <el-table-column prop="create_time" label="测评时间"  ></el-table-column>   
                    <el-table-column prop="gender_name" label="患者性别"  ></el-table-column>  
                    <el-table-column prop="age" label="患者年龄"  ></el-table-column>  
					<el-table-column prop="education_name" label="患者文化程度"  ></el-table-column>  
					<el-table-column prop="illness_name" label="疾病程度"  ></el-table-column>  
					<el-table-column prop="disease_type_name" label="疾病类型" width="150" ></el-table-column>  
					<el-table-column prop="hobby_name" label="确诊前兴趣爱好"  width="200" ></el-table-column>  
					<el-table-column prop="walk_name" label="行走能力"  width="150"></el-table-column>  
					<el-table-column prop="status_name" label="测评状态"  ></el-table-column>  
					<el-table-column prop="is_join_research_name" label="是否参加调查"  ></el-table-column>   
					<el-table-column label="操作"  width="100"  align="center">
						<div slot-scope="scope" class="doSonimg_box font14">
							<el-button class="mar_B_10" size="mini" type="primary" @click="edit(scope.row)">查看详情</el-button> 
                        </div> 
					</el-table-column>
				</el-table>

				<!--分页-->
				<div class="paging">
					<!-- <el-button class="left" size="mini" type="primary" @click="delete_all">批量删除</el-button> -->
					<el-pagination class="left" @current-change="handleCurrentChange" :page-size="limit" :current-page="page" background
						layout="prev, pager, next" :total="count"></el-pagination>
					<span class="demonstration left">共 {{ count }} 条 每页{{ limit }}条</span>
				</div>

			</div>
		</div>
	</div>
</template>
<script src="../../../static/js/index/pingce_jilu_list.js"></script>
<style scoped>
	.activity_img {
		width: 160px;
		height: 90px;
	}

	.img_logo {
		width: 80px;
		margin: 0 auto;
	}

	.wx_er {
		width: 100%;
	}

	.wx_er .erCode {
		width: 100%;
	}

	/*  */
	.xcx-head {
		border: none;
		margin-bottom: 0;
	}

	.shop_img {
		width: 50px;
		height: 50px;
		vertical-align: middle;
	}
</style>