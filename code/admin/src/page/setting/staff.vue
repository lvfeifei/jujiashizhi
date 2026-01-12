<template>
	<div id="staff">
		<div id="content">
			<div id="head">
				<router-link to="/setting/add_staff">
					<el-button class="add_staff" size="mini">添加员工</el-button>
				</router-link>
			</div>

			<div id="list">
				<el-table :data="tableData.slice((page-1)*limit,page*limit)" style="width: 97%;margin: 0 1.5%;">
					<el-table-column prop="account" label="员工账号">
					</el-table-column>
					<el-table-column prop="name" label="姓名">
					</el-table-column>
					<el-table-column prop="mobile" label="手机号码">
					</el-table-column>
					<el-table-column prop="add_time" label="添加时间" width="200">
					</el-table-column>
					<el-table-column prop="jurisdiction" label="权限">
					</el-table-column>
					<el-table-column prop="operation" label="操作">
						<template slot-scope="scope">
							<div v-if="user_id !== scope.row.id">
								<el-button @click="edit(scope.row.id)" type="text" size="small">编辑</el-button>
								<el-button type="text" @click="inquiry(scope.row.id)" size="small"><span style="color: red">删除</span></el-button>
							</div>
							<div v-else>
								当前账号
							</div>
						</template>
					</el-table-column>
				</el-table>
				<div class="block">
					<el-pagination @current-change="handleCurrentChange" background layout="prev, pager, next" :total="count"></el-pagination>
					<span class="demonstration">共 {{ count }}条    每页10条</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script  type="text/javascript" src="../../../static/js/staff.js">

</script>

<style scoped>
	@import '../../../static/css/global.css';/*引入公共样式*/
</style>