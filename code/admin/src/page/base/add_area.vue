<template>
	<div id="add_shop">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>基础</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/aid/category_manage">区域管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{banner_id ? '编辑' : '新增'}}区域</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content">
				<div class="head">{{banner_id ? '编辑' : '新增'}}区域</div>
				<div id="head"></div>
				<div class="rebate">
					<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
							<el-form-item label="区域名称：" prop="name">
									<el-input class="w400" v-model="form_data.name" placeholder="请输入区域名称"></el-input>
							</el-form-item>
                            <el-form-item label="关联标签：" prop="tag">
                              <el-checkbox-group class="checkList" v-model="checkList">
                                <el-checkbox v-for="item in labelData" :key="item.id" :label="item.id" :value="item.id" @change="checkboxClick">{{item.name}}</el-checkbox>
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

<script type="text/javascript" src="../../../static/js/base/add_area.js">
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
