<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>测评管理</b></el-breadcrumb-item>
				<el-breadcrumb-item>测评分类列表</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
			  <div style="display: flex">
					<!-- <el-input class="font14" placeholder="请输入内容" v-model="key" clearable></el-input>
					<el-button class="hollow_out mar_L_10" @click="search()" plain>搜索</el-button>
				   -->
                </div>
				 
				<span class="xcx-add left font14" @click="add()">添加分类</span>
			</div>
			<div class="xcx-content">
				<!--列表-->
				<el-table border :data="tableData" stripe style="width: 100%"   @selection-change="handleSelectionChange"> 
					<!-- <el-table-column type="selection" width="55"> </el-table-column> -->
					<el-table-column prop="name" label="分类名称" width="300" align="center"></el-table-column>   
					<el-table-column prop="content" label="分类描述"  ></el-table-column>  
					<el-table-column prop="sort" label="排序"  ></el-table-column>
					<el-table-column  label="状态"   align="center"> 
						<div slot-scope="scope"> 
                            <span v-if="scope.row.status == 1">开启</span>
                            <span v-if="scope.row.status == 2">关闭</span> 
                        </div>
					</el-table-column>
					<el-table-column label="操作"   align="center">
						<div slot-scope="scope" class="doSonimg_box font14" style="display:block;">
							<el-button class="mar_B_10" size="mini" type="primary" @click="edit(scope.row.id)">编辑</el-button>
							<el-button class="mar_B_10" size="mini" type="danger" @click="del_item(scope.row.id)">删除</el-button> 
						</div>
					</el-table-column>
				</el-table>
				<!--分页-->
				<!-- <div class="paging">
					<el-button class="left" size="mini" type="primary" @click="delete_all">批量删除</el-button>
					<el-pagination class="left" @current-change="handleCurrentChange" :page-size="limit" :current-page="page" background
						layout="prev, pager, next" :total="count"></el-pagination>
					<span class="demonstration left">共 {{ count }} 条 每页{{ limit }}条</span>
				</div> -->
			</div>
		</div>
	</div>
</template>
<script src="../../../static/js/cepingguanli/ceping_type_list.js"></script>
  
<style scoped>
	.img_rectangle {
		width: 150px;
		margin: 0 auto;
	}
</style>
