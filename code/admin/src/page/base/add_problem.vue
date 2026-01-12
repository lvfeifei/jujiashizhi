<template>
	<div id="add_shop">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>基础</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/base/sign_manage">能力问题</el-breadcrumb-item>
				<el-breadcrumb-item>{{banner_id ? '修改' : '新增'}}问题</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content">
				<div class="head">{{banner_id ? '修改' : '新增'}}问题</div>
				<div id="head"></div>
				<div class="rebate">
					<el-form ref="form_data" :model="form_data" :rules="rules" style="width: 80%;">
                            <el-form-item label="问题格式：" prop="status">
                                <el-radio v-model="form_data.status" label="1">单选</el-radio>
                                <el-radio v-model="form_data.status" label="2">多选</el-radio>
                                <el-radio v-model="form_data.status" label="3">判断</el-radio>
                            </el-form-item>
							<el-form-item label="问题名称：" prop="name">
									<el-input class="w400" v-model="form_data.sort" placeholder="请输入问题名称"></el-input>
							</el-form-item>
                            <el-form-item label="选项内容：" prop="content">
                                <el-button type="primary" @click="dialogVisible = true">添加选项</el-button>
                            </el-form-item>
                            <div>
                                <el-table border :data="class_table" style="width: 100%" class="mar_B_30">
                                    <el-table-column fixed prop="tab_num" label="序号" width="80"></el-table-column>
                                    <el-table-column prop="tab_cont" label="选项内容"></el-table-column>
                                    <el-table-column prop="rela_tag" label="关联标签"></el-table-column>
                                    <el-table-column label="操作">
                                        <template slot-scope="scope">
                                            <el-button @click="edit(scope.$index)" type="text" size="small">编辑</el-button>
                                            <el-button
                                                @click.native.prevent="deleteRow(scope.$index, class_table)"
                                                type="text"
                                                size="small"
                                                style="color:red;"
                                            >删除</el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
							<el-form-item class="mar_T_50">
									<el-button type="primary" @click="save()">确认</el-button>
									<el-button @click="back">返回</el-button>
							</el-form-item>
					</el-form>
				</div>
			</div>
		</div>
        <!--添加选项-->
        <el-dialog
            title="添加选项"
            :visible.sync="dialogVisible"
            width="30%"
            :before-close="close_table"
        >
            <el-form rel="add_spec" class="demo-ruleForm" label-width="84px">
                <el-form-item label="选项内容：" prop="OldPass">
                    <el-input placeholder="请输入选项名称" v-model="class_name" clearable></el-input>
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
                        <el-checkbox label="标签10"></el-checkbox>
                        <el-checkbox label="标签11"></el-checkbox>
                        <el-checkbox label="标签12"></el-checkbox>
                        <el-checkbox label="标签13"></el-checkbox>
                        <el-checkbox label="标签14"></el-checkbox>
                        <el-checkbox label="标签15"></el-checkbox>
                        <el-checkbox label="标签16"></el-checkbox>
                        <el-checkbox label="标签17"></el-checkbox>
                        <el-checkbox label="标签18"></el-checkbox>
                        <el-checkbox label="标签19"></el-checkbox>
                        <el-checkbox label="标签20"></el-checkbox>
                        <el-checkbox label="标签21"></el-checkbox>
                        <el-checkbox label="标签22"></el-checkbox>
                        <el-checkbox label="标签23"></el-checkbox>
                        <el-checkbox label="标签24"></el-checkbox>
                        <el-checkbox label="标签25"></el-checkbox>
                        <el-checkbox label="标签26"></el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item class="dialog-footer">
                    <el-button @click="dialogVisible = false">取 消</el-button>
                    <el-button type="primary" @click="confirm">确 定</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/base/add_problem.js">
</script>

<style scoped>
	@import '../../../static/css/global.css';
	
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
    #add_shop .checkList .el-checkbox{
        margin-right: 20px;
        width: 80px;
    }
</style>
