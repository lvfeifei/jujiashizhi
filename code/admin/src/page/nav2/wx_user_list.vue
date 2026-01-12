<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
				<el-breadcrumb separator="/">
						<el-breadcrumb-item><b>用户管理</b></el-breadcrumb-item>
						<el-breadcrumb-item>微信用户列表</el-breadcrumb-item>
				</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
					<span class="title">微信用户列表</span>
			</div>
			<div class="xcx-content">
				<el-table border :data="tableData" stripe style="width: 97%;">
					<el-table-column prop="avatar_url" label="头像" >
                        <div slot-scope="scope">
                            <img class="img_avatar" :src="scope.row.avatar_url" alt="">
                        </div>
                    </el-table-column>
					<el-table-column prop="nickname" label="昵称" ></el-table-column>
					<el-table-column prop="gender" label="性别" >
                        <div slot-scope="scope">
                            {{ scope.row.gender == 1 ? '男':'女' }}
                        </div>                        
                    </el-table-column>
					<el-table-column prop="user_id" label="是否绑定手机" >
                        <div slot-scope="scope">
                            {{ scope.row.user_id ? '是':'否' }}
                        </div>   
                    </el-table-column>
					<!-- <el-table-column prop="mobile" label="手机号" ></el-table-column> -->
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

<script  type="text/javascript" src="../../../static/js/wx_user_list.js">

</script>

<style>
	/*引入公共样式*/
</style>
