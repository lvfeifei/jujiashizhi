<template>
	<div id="add_grouping">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{path:'/index/swiper'}"><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item :to="{path: '/nav1/shop_grouping'}">商品分组</el-breadcrumb-item>
				<el-breadcrumb-item>{{ (class_id ===0) ? '新建商品分组' : '修改商品分组'}}</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<div id="content">
			<div id="head"></div>
			<div id="rebate">
				<template>
					<div class="input_model">
						<span class="label">分组名称：&nbsp;&nbsp;</span>
						<el-input v-model="class_name" placeholder="请输入分组名称"  clearable></el-input>
					</div>
				</template>
				<template>
					<div class="input_model">
						<span class="label">权重：&nbsp;&nbsp;</span>
						<el-input v-model="class_sort" placeholder="请输入排序"  clearable></el-input>
					</div>
				</template>
				<div id="operation">
					<el-button size="mini" v-if="class_id === 0" @click="addClass">保存</el-button>
					<el-button size="mini" v-else @click="editClass">修改</el-button>
					<el-button size="mini" @click="cancel">返回</el-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/add_grouping.js">
</script>

<style scoped>
	@import '../../../static/css/global.css';/*引入公共样式*/
</style>