<template>
	<div id="subpage">
		<el-col :span="24" class="warp-breadcrum">
				<el-breadcrumb separator="/">
						<el-breadcrumb-item><b>用户</b></el-breadcrumb-item>
						<el-breadcrumb-item>用户管理</el-breadcrumb-item>
				</el-breadcrumb>
		</el-col>
		<div class="content">
			<div class="xcx-head">
				<div style="display: flex">
                    <el-select clearable style="width:200px;" v-model="type" placeholder="请选择用户类型" class="title mar_R_10">
                        <el-option label="评估员" value="0"></el-option>
                        <el-option label="施工员" value="1"></el-option>
                        <el-option label="管理员" value="2"></el-option>
                        <el-option label="普通用户" value="2"></el-option>
                        <el-option label="民政机构" value="2"></el-option>
                    </el-select>
                    <el-input class="font14" style="width:240px;" placeholder="按手机号、真实姓名搜索" v-model="key" clearable></el-input>
                    <el-button class="hollow_out mar_L_10" @click="search()" plain>确认</el-button>
                </div>
			</div>
			<div class="xcx-content">
				<el-table border :data="tableData" stripe>
					<el-table-column prop="realname" label="真实姓名" ></el-table-column>
					<el-table-column prop="mobile" label="手机号" ></el-table-column>					
					<el-table-column prop="create_time" label="当前权限" ></el-table-column>
                    <el-table-column label="操作">
                        <div slot-scope="scope" class="doSonimg_box font14">
                            <span class="text primary" @click="edit(scope.row.id)">编辑</span>
                            <span class="text primary" @click="dialogVisible=true">权限设置</span>
                        </div>
                    </el-table-column>
				</el-table>
                <!--分页-->
                <div class="paging">
                    <el-pagination class="left" @current-change="handleCurrentChange" :current-page="page" background layout="prev, pager, next" :total="count"></el-pagination>
                    <span class="demonstration left">共 {{ count }} 条 每页10条</span>
                </div>
			</div>
		</div>
        <!--添加选项-->
        <el-dialog title="权限设置" :visible.sync="dialogVisible" width="600px" :before-close="close_table">
            <el-form rel="add_spec" class="demo-ruleForm">
                <el-form-item prop="rool_set">
                    <el-radio v-model="form_data.rool_set" label="1">普通用户</el-radio>
                    <el-radio v-model="form_data.rool_set" label="2">评估人员</el-radio>
                    <el-radio v-model="form_data.rool_set" label="3">施工人员</el-radio>
                    <el-radio v-model="form_data.rool_set" label="4">民政机构</el-radio>
                    <el-radio v-model="form_data.rool_set" label="5">管理员</el-radio>
                </el-form-item>
                <el-form-item label="所在区域：" prop="area" v-if="form_data.rool_set==4">
                        <el-select style="width:450px;" clearable v-model="form_data.area" placeholder="请选择所在区域">
                            <el-option label="区域1" value="0"></el-option>
                            <el-option label="区域2" value="1"></el-option>
                            <el-option label="区域3" value="2"></el-option>
                        </el-select>
                </el-form-item>
                <el-form-item class="dialog-footer">
                    <el-button @click="dialogVisible = false">取 消</el-button>
                    <el-button type="primary" @click="confirm">确 定</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
	</div>
</template>

<script  type="text/javascript" src="../../../static/js/user/user_manage.js">

</script>

<style scoped>
	@import '../../../static/css/global.css';
	/*引入公共样式*/
</style>
