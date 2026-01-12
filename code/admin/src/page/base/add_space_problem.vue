<template>
	<div id="add_shop">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>基础</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/aid/space_manage">空间管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{banner_id ? '编辑' : '新增'}}问题</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content">
				<div class="head">{{banner_id ? '编辑' : '新增'}}问题</div>
				<div id="head"></div>
				<div class="rebate">
					<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
							<el-form-item label="缺陷名称：" prop="">
									<el-input class="w400" v-model="form_data.name" placeholder="请输入缺陷名称"></el-input>
							</el-form-item>
                            <el-form-item label="评估问题：" prop="problem">
									<el-input class="w400" v-model="form_data.problem" placeholder="请输入评估问题"></el-input>
							</el-form-item>
                            <el-form-item label="关联空间：" prop="tag">
                                <el-checkbox-group class="checkList" v-model="checkList">
                                    <el-checkbox label="空间1"></el-checkbox>
                                    <el-checkbox label="空间2"></el-checkbox>
                                    <el-checkbox label="空间3"></el-checkbox>
                                    <el-checkbox label="空间4"></el-checkbox>
                                    <el-checkbox label="空间5"></el-checkbox>
                                    <el-checkbox label="空间6"></el-checkbox>
                                    <el-checkbox label="空间7"></el-checkbox>
                                    <el-checkbox label="空间8"></el-checkbox>
                                    <el-checkbox label="空间9"></el-checkbox>
                                </el-checkbox-group>
							</el-form-item>
                            <el-form-item label="关联标签：" prop="tag">
                                <el-checkbox-group class="checkList" v-model="checkList">
                                    <el-checkbox label="标签1"></el-checkbox>
                                    <el-checkbox label="标签2"></el-checkbox>
                                    <el-checkbox label="标签3"></el-checkbox>
                                    <el-checkbox label="标签4"></el-checkbox>
                                    <el-checkbox label="标签5"></el-checkbox>
                                    <el-checkbox label="标签6"></el-checkbox>
                                    <el-checkbox label="标签7"></el-checkbox>
                                    <el-checkbox label="标签8"></el-checkbox>
                                    <el-checkbox label="标签9"></el-checkbox>
                                </el-checkbox-group>
							</el-form-item>
							<el-form-item class="mar_T_50">
									<el-button type="primary" @click="save()">确认</el-button>
									<el-button @click="back">返回</el-button>
							</el-form-item>
					</el-form>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/base/add_space_problem.js">
</script>

<style scoped>
	@import '../../../static/css/global.css';
	/*引入公共样式*/
	#add_shop .rebate .label {
		width: 100px !important;
	}

	#add_shop #operation{
		width: 300px;
		height: 40px;
		margin: 10px 0 0 50px;
		display: block;
		overflow: hidden;
	}
    #add_shop .checkList .el-checkbox+.el-checkbox{
        margin-left: 0;
    }
    #add_shop .checkList{
        max-width: 800px;
    }
    #add_shop .checkList .el-checkbox{
        margin-right: 20px;
        width: 80px;
    }
</style>
