<template>
	<div id="add_staff">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{path:'/index/swiper'}"><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item :to="{path: '/setting/staff'}">员工管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{ (role_id ===0) ? '添加员工' : '编辑员工内容'}}</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<div id="content">
			<div id="head">{{ (role_id ===0) ? '添加员工' : '编辑员工内容'}}</div>
			<div id="rebate">
				<template>
					<div class="input_model">
						<span class="label">员工手机：&nbsp;&nbsp;</span>
						<el-input v-model='mobile' placeholder="请输入员工手机" clearable></el-input>
					</div>
				</template>
				<template>
					<div class="input_model">
						<span class="label">员工姓名：&nbsp;&nbsp;</span>
						<el-input v-model='name' placeholder="请输入员工姓名"  clearable></el-input>
					</div>
				</template>
				<template>
					<div class="input_model">
						<span class="label">账号：&nbsp;&nbsp;</span>
						<el-input v-model='username' placeholder="请输入账号"  clearable></el-input>
					</div>
				</template>
				<template>
					<div class="input_model">
						<span class="label">密码：&nbsp;&nbsp;</span>
						<el-input  v-model='userPassWord' type = "password" placeholder="请输入密码"  clearable></el-input>
					</div>
				</template>
				<template>
					<div class="input_model">
						<span class="label">权限：&nbsp;&nbsp;</span>
						<span class="radio">
							<el-radio v-model="identity" label="1">高级管理员</el-radio>
							<el-tooltip class="item" style="font-size: 14px;" effect="dark" content="拥有所有权限" placement="top">
						      <el-button class="icon"><i class="el-icon-question"></i></el-button>
						    </el-tooltip>
						</span>
						<span class="radio">
							<el-radio v-model="identity" label="2">普通管理员</el-radio>
							<el-tooltip class="item" style="font-size: 14px;" effect="dark" content="拥有员工管理以外的所有权限" placement="top">
						      <el-button class="icon"><i class="el-icon-question"></i></el-button>
						    </el-tooltip>
						</span>
					</div>
				</template>
				<div id="operation">
					<el-button size="mini" v-if="role_id === 0" @click="addClass">保存</el-button>
					<el-button size="mini" v-else @click="editClass">修改</el-button>
					<el-button size="mini" @click="cancel">返回</el-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script  type="text/javascript" src="../../../static/js/add_staff.js"></script>

<style scoped>
	@import '../../../static/css/global.css';/*引入公共样式*/
</style>