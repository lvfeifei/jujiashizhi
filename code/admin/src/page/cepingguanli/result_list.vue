<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>测评管理</b></el-breadcrumb-item>
				<el-breadcrumb-item>数据分析</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
			  <div style="display: flex">
					<!-- <el-input class="font14" placeholder="请输入内容" v-model="key" clearable></el-input>
					<el-button class="hollow_out mar_L_10" @click="search()" plain>搜索</el-button>
				   -->
                </div>
				 
				<span class="xcx-add left font14" @click="export_file">导出excel</span>
			</div>
			<div class="xcx-content">
				<!--列表--> 
                <div class="table_box" v-for="item in tableData" :key="item.id">
                    <div class="table_title">{{item.name}}</div> 
                    <div class="innner_content" v-for="(two_item,two_index) in item.capability" :key="two_item.id">
                        <div class="two_title"> 第{{two_index + 1}}题： {{two_item.name}}</div>
                        <el-table border :data="two_item.options" stripe style="width: 100%"   >  
                            <el-table-column prop="name" label="选项"  width="150"  align="center"></el-table-column>   
                            <el-table-column   label="推荐照护建议及数据统计"  >
							 <div slot-scope="scope"> 
                                  <el-table border :data="scope.row.group_program" stripe style="width: 100%"   >  
									<el-table-column  label="编号"  width="100" >  
									    <div slot-scope="scope_two"> 
											<span>{{scope_two.row.sn}}</span>
										</div>   
									</el-table-column>   
									<el-table-column label="推荐建议"  align="center">
 										<div slot-scope="scope_two">  
											<div  v-for="i in scope_two.row.content">
											    <span v-if="i.type == 'text'">{{i.con}}</span> 
											    <div class="one_title_auther" v-if="i.type == 'image'">
													<img  class="content_img " :src="i.con" >
												</div>
												<div class="one_title_auther" v-if="i.type == 'video'"> 
													<video width="320" height="240" controls>
														<source :src="i.con"  type="video/mp4">  
													</video> 
												</div>
											</div>
										</div> 
									</el-table-column> 
									<el-table-column prop="adopt_number" label="采纳人数" width="100"  ></el-table-column>   
									<el-table-column  label="有效率"  width="100" >  
									    <div slot-scope="scope_two"> 
											<span >{{scope_two.row.efficiency + '%'}}</span>
										</div>   
									</el-table-column>   
									    
								</el-table> 
                              </div> 
							</el-table-column>   
                            <el-table-column prop="question_number" label="问题人数"  width="80"  ></el-table-column>   
                            <el-table-column prop="feedback_number" label="参与反馈人数"  width="120"  ></el-table-column>   
                            <el-table-column label="后反馈率"  width="80"  >
								<div slot-scope="scope"> 
									<span >{{scope.row.feedback + '%'}}</span>
								</div> 
							</el-table-column>   
                                
                        </el-table> 
                    </div> 
                </div>
 
			</div>
		</div>
	</div>
</template>
<script src="../../../static/js/cepingguanli/result_list.js"></script>
  
<style scoped>

.two_title {
    margin:20px 0;
}
 .table_box {
    margin-bottom: 50px;
 }
 .table_title {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
 }
	.img_rectangle {
		width: 150px;
		margin: 0 auto;
	}

	.one_title_auther {
	padding-left: 70px;
	font-size: 16px !important;
	margin: 10px;
}

.content_img {
	height: 100px;
	margin:10px 0;
}

</style>
