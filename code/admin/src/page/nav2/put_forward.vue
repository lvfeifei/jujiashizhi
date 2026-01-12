<template>
	<div id="invitation_list">
		<div id="content">
			<div id="list">
				<el-table :data="tableData.slice((page-1)*limit,page*limit)" stripe style="width: 97%;margin: 0 1.5%;" :default-sort="{prop: 'date', order: 'descending'}">
					<el-table-column prop="picture" label="头像" >
						<div slot-scope='scope'>
							<div v-html="scope.row.picture"></div>
						</div>
					</el-table-column>
					<el-table-column prop="nickname" label="昵称" ></el-table-column>
					<el-table-column prop="mobile" label="手机号" ></el-table-column>
					<el-table-column prop="real_name" label="真实姓名" ></el-table-column>
					<el-table-column prop="status_info" label="状态"></el-table-column>
					<el-table-column label="操作" width="153" prop="status_info">
						<template slot-scope="scope">
							<div id="operation">
								<span v-if="scope.row.status_info ==='已转账'">已转账</span>
								<el-button class="operation" v-if="scope.row.status_info === '申请中'" @ size="mini" @click="edit_status(scope.row.i)">转账</el-button>
							</div>
						</template>
					</el-table-column>
				</el-table>
				<div class="block">
					<el-pagination @current-change="handleCurrentChange" background layout="prev, pager, next" :total="count"></el-pagination>
					<span class="demonstration">共 {{ count }} 条    每页10条</span>
				</div>
			</div>
		</div>
		<!--昵称备注弹窗-->
		<el-dialog title="昵称备注" :visible.sync="dialogVisible" width="30%">
			<el-input v-bind:placeholder="nickname" v-model="nickRemark" clearable></el-input>
			<p class="monitor">仅作为内部查看，不会显示给用户看</p>
			<span slot="footer" class="dialog-footer">
			    <el-button @click="dialogVisible = false">取 消</el-button>
			    <el-button type="primary" @click="doAddRemark">保 存</el-button>
			</span>
		</el-dialog>
	</div>
</template>

<script  type="text/javascript" src="../../../static/js/put_forward.js">

</script>

<style scoped>
	@import '../../../static/css/global.css';
	/*引入公共样式*/
</style>
