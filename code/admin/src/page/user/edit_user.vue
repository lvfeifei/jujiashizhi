<template>
	<div id="add_shop">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item><b>用户</b></el-breadcrumb-item>
				<el-breadcrumb-item to="/user/user_manage">用户管理</el-breadcrumb-item>
				<el-breadcrumb-item>编辑用户</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content">
				<div class="head">编辑用户</div>
				<div id="head"></div>
				<div class="rebate">
					<el-form ref="form_data" :model="form_data" :rules="rules" label-width="140px" style="width: 80%;">
							<el-form-item label="用户头像：" prop="picture">
                                <div class="user_pic">
                                    <img class="user_img" src="../../../img/1.png" />
                                </div>
							</el-form-item>
							<el-form-item label="真实姓名：" prop="realname">
									<el-input class="w400" v-model="form_data.realname" placeholder="请输入真实姓名"></el-input>
							</el-form-item>
                            <el-form-item label="当前权限：" prop="realname">
                                <div style="font-size:14px;">普通用户</div>
							</el-form-item>
                            <el-form-item label="手机号码：" prop="tel">
									<el-input class="w400" v-model="form_data.tel" placeholder="请输入手机号码"></el-input>
							</el-form-item>
                            <el-form-item label="邮箱地址：" prop="email">
									<el-input class="w400" v-model="form_data.email" placeholder="请输入邮箱地址"></el-input>
							</el-form-item>
                            <el-form-item label="身份证号：" prop="identify_num">
									<el-input class="w400" v-model="form_data.identify_num" placeholder="请输入身份证号"></el-input>
							</el-form-item>
                            <!-- <el-form-item label="所在区域：" prop="area">
                                <el-cascader class="w400" :key="new Date()" ref="cascaderAddr" :options="options" :props="props" v-model="form_data.area" :placeholder="defaultaddress" :collapse-tags="iscollapsetag" @change="visibleChange"></el-cascader>
							</el-form-item> -->
                            <el-form-item label="所在区域：" prop="area">
									<el-select class="w400" clearable v-model="form_data.area" placeholder="请选择所在区域">
                                        <el-option label="区域1" value="0"></el-option>
                                        <el-option label="区域2" value="1"></el-option>
                                        <el-option label="区域3" value="2"></el-option>
                                    </el-select>
							</el-form-item>
                            <el-form-item label="详细地址：" prop="detail_site">
								<el-input class="w400" v-model="form_data.detail_site" placeholder="请输入详细地址"></el-input>
							</el-form-item>
                            <el-form-item label="所属社区：" prop="social">
								<el-input class="w400" v-model="form_data.social" placeholder="请输入所属社区"></el-input>
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

<script type="text/javascript" src="../../../static/js/user/edit_user.js">
</script>

<style scoped>
	@import '../../../static/css/global.css';
	/*引入公共样式*/

	.avatar-uploader .el-upload {
		border: 1px dashed #d9d9d9;
		border-radius: 6px;
		cursor: pointer;
		position: relative;
		overflow: hidden;
	}

	.avatar-uploader .el-upload:hover {
		border-color: #409EFF;
	}

	.avatar-uploader-icon {
		font-size: 28px;
		color: #8c939d;
		width: 178px;
		height: 178px;
		line-height: 178px;
		text-align: center;
	}

	.avatar {
		width: 178px;
		height: 178px;
		display: block;
	}

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
    #add_shop .user_pic .user_img{
        width: 80px;
        height: 80px;
        border-radius: 100%;
    }
</style>
