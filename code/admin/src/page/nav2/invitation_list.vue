<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
				<el-breadcrumb separator="/">
						<el-breadcrumb-item><b>用户管理</b></el-breadcrumb-item>
						<el-breadcrumb-item>用户列表</el-breadcrumb-item>
				</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
					<span class="title">用户列表</span>
			</div>
			<div class="xcx-content">
				<el-table border :data="tableData" stripe style="width: 97%;">
					<el-table-column prop="avatar_url" label="头像" >
                        <div slot-scope="scope">
                            <img class="img_round" :src="scope.row.avatar_url" alt="">
                        </div>
                    </el-table-column>
					<el-table-column prop="nickname" label="昵称" ></el-table-column>
					<!-- <el-table-column prop="gender" label="性别" > </el-table-column> -->
					<el-table-column prop="mobile" label="手机号" ></el-table-column>					
					<el-table-column prop="create_time" label="注册时间" ></el-table-column>

				</el-table>

                <!--分页-->
                <div class="paging">
                    <el-pagination class="left" @current-change="handleCurrentChange" :current-page="page" background layout="prev, pager, next" :total="count"></el-pagination>
                    <span class="demonstration left">共 {{ count }} 条 每页10条</span>
                </div>
			</div>
		</div>
	</div>
</template>

<script  type="text/javascript" src="../../../static/js/invitation_list.js">

</script>

<style scoped>
	@import '../../../static/css/global.css';
	/*引入公共样式*/
</style>
