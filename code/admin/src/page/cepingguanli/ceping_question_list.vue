 
<template>
	<div id="subpage">
		<el-breadcrumb separator="/">
			<el-breadcrumb-item><b>测评管理</b></el-breadcrumb-item>	
			<el-breadcrumb-item>测评问题列表</el-breadcrumb-item>
		</el-breadcrumb>
		<div class="content">
			<div class="xcx-head">
				<div style="display: flex">  

					<el-select v-model="type_id" class="w230 mar_L_10" filterable  >
						<el-option label="全部" value="0"></el-option>
                        <el-option v-for="(item,index) in type" :key="index" :label="item.name" :value="item.id"></el-option>
					</el-select>

					<!-- <el-select v-model="baojia_type_id" class="w120 mar_L_10" filterable  >
						<el-option label="全部" value="0"></el-option>
                        <el-option v-for="(item,index) in baojia_list" :key="index" :label="item.name" :value="item.id"></el-option>
					</el-select> -->

                    <!-- <el-date-picker
                        style="width: 370px;"
                        v-model="date"
                        type="datetimerange"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                        :default-time="['12:00:00']"
						value-format="yyyy-MM-dd HH:mm:ss"
                    ></el-date-picker> -->

					<el-input class="font14 mar_L_10 w251" placeholder="输入搜索内容" v-model="key" clearable></el-input>
					<el-button class="hollow_out mar_L_10" @click="search()" plainx>搜索</el-button>
                  
				</div>
				  <span class="xcx-add font14 left" @click="add()">添加</span>
			</div>

			<div class="xcx-content">
				 
				<!--列表-->
				<el-table border :data="tableData" stripe style="width: 100%"  @selection-change="handleSelectionChange"> 
					<!-- <el-table-column type="selection" width="55"> </el-table-column> -->
					<!-- <el-table-column prop="id" label="id" align="center"></el-table-column>   -->
					<el-table-column prop="name" label="标题"  ></el-table-column>  
					<el-table-column prop="classname" label="分类"  ></el-table-column>   
					<!-- <el-table-column prop="type_name" label="类型"  >   </el-table-column>   -->
					<el-table-column prop="sort" label="排序"   align="center">  </el-table-column>
					<el-table-column prop="status_name" label="状态"  >   </el-table-column>  

					<!-- <el-table-column  label="大图片" width="200">
                        <div slot-scope="scope">
                            <img class="img_rectangle" :src="scope.row.bigimage" alt="">
                        </div>
                    </el-table-column> -->
					<!-- <el-table-column  label="小图" width="200">
                        <div slot-scope="scope">
                            <img class="img_rectangle" :src="scope.row.image" alt="">
                        </div>
                    </el-table-column> -->
 
					<!-- <el-table-column  label="状态"   align="center"> 
						<div slot-scope="scope">
							<span v-if="scope.row.status == 1">开启</span> 
							<span v-if="scope.row.status == 2">关闭</span>  
                        </div>
					 </el-table-column> -->
					
					<el-table-column label="操作"  width="200"  align="center">
						<div slot-scope="scope" class="doSonimg_box font14">
							<el-button class="mar_B_10" size="mini" type="primary" @click="edit(scope.row.id)">修改</el-button>
							<el-button class="mar_B_10" size="mini" type="danger" @click="del_item(scope.row.id)">删除</el-button> 
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
<script src="../../../static/js/cepingguanli/ceping_question_list.js"></script>
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