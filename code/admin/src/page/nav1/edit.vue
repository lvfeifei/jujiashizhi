<template>
	<div id="add_shop">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{path:'/banner/swiper'}"><b>首页</b></el-breadcrumb-item>
				<el-breadcrumb-item :to="{path: '/nav1/shop_list'}">商品管理</el-breadcrumb-item>
				<el-breadcrumb-item>编辑商品</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content">
				<div class="head">基本信息</div>
				<div class="rebate">
					<div class="input_model">
						<span class="label">商品名称：&nbsp;&nbsp;</span>
						<el-input placeholder="请输入商品名称" v-model="goods_name" style="width: 380px !important;" clearable></el-input>
						<br/>
						<p class="tagging">商品名称长度限制1~100个字</p>
					</div>
					<div class="input_model">
						<span class="label">产地：&nbsp;&nbsp;</span>
						<el-input v-model="place_origin" placeholder="请输入商品产地" style="width: 380px !important;" clearable></el-input>
					</div>
					<div class="input_model">
						<span class="label">排序：&nbsp;&nbsp;</span>
						<el-input v-model="goods_sort" placeholder="请输入商品排序,默认为1" style="width: 380px !important;" clearable></el-input>
					</div>
                    <div class="input_model">
						<span class="label">商品分类：&nbsp;&nbsp;</span>
						<el-select class="input1" v-model="class_id" placeholder="请选择商品分类" style="width: 380px !important;">
							<el-option v-for="item in goods_type_list" :key="item.id" :label="item.name" :value="item.id">
							</el-option>
						</el-select>
					</div>
                    <div class="input_model">
                        <span class="label">市场价：&nbsp;&nbsp;</span>
                        <el-input v-model="market_price" placeholder="请输入市场价" style="width: 380px !important;" clearable></el-input>
                    </div>
                    <div class="input_model">
                        <span class="label">是否上架：&nbsp;&nbsp;</span>
                        <template>
                            <el-radio v-model="status" label="1">上架</el-radio>
                            <el-radio v-model="status" label="3">下架</el-radio>
                        </template>
                    </div>
				</div>
			</div>
			<!--第一期添加规格-->
			<div class="content">
				<div class="head">商品规格</div>
				<div class="one-table mar_T_20">
					<el-button type="primary" @click="dialogVisible = true">添加规格</el-button>
				</div>
				<div id="list">
					<template>
						<el-table :data="class_table" border style="width: 100%" class="mar_B_30">
							<el-table-column prop="name" label="规格值"></el-table-column>
							<el-table-column prop="price" label="售价"></el-table-column>
							<el-table-column prop="total_stock" label="库存"></el-table-column>
							<el-table-column prop="remaining_stock" label="剩余库存"></el-table-column>
							<el-table-column prop="sale_stock" label="销售数量"></el-table-column>
							<el-table-column prop="cost_price" label="成本价"></el-table-column>
							<el-table-column label="操作">
								<template slot-scope="scope">
									<el-button @click='edit_spec_dialog(scope.$index)' type="text" size="small">修改</el-button>
									<el-button @click="delete_spec(scope.$index)" type="text" size="small" style="color:red;">移除</el-button>
								</template>
							</el-table-column>
						</el-table>
					</template>
				</div>
			</div>

            <div class="content">

                <div class="head">图片简介</div>
                <div class="rebate">
                        
                <el-form ref="form" class="mar_T_30" label-width="80px">
                    <el-form-item label="轮播图：" class="line_H">
                        <el-upload action="http://up-z2.qiniup.com/" :limit="10" list-type="picture-card" :file-list="fileList" :on-preview="handlePictureCardPreview" :on-remove="handleRemove" :on-exceed="handleExceed" :on-success="handleAvatarSuccess" :on-error="handleError" :data="postData">
							<i class="el-icon-plus"></i>
						</el-upload>
						<el-dialog :visible.sync="goods_banner">
							<img width="100%" :src="dialogImageUrl" alt="">
						</el-dialog>
						<p style="font-size: 12px;color: #909399;">在商品详情页顶部显示，请上传1-10张,尺寸750*750。</p>
                    </el-form-item>
                    <el-form-item label="详情图：">

                        <!-- 富文本 -->
                        <richText placeholder='请输入内容' :disabled="false" @editor_change='editor_change' ref="richText" :describe='desc'></richText>
                    </el-form-item>
                </el-form>
                </div>
            </div>
		</div>
		<div class="footer">
			<span class="prompt"><i class="el-icon-warning" style="color: #e6a23c;"></i>&nbsp;提交前请仔细检查各项信息！</span>
			<div class="butn">
				<el-button size="mini" @click="cancel">放弃编辑</el-button>
				<el-button size="mini" class="last" @click="exitData">提交</el-button>
			</div>
		</div>

		<el-dialog title="添加规格" :visible.sync="dialogVisible" width="30%" :before-close="close_table">
			<el-form class="demo-ruleForm" label-width="80px">
				<el-form-item label="规格值:" prop="OldPass">
					<el-input placeholder="请输入商品规格值" v-model="class_name" clearable></el-input>
				</el-form-item>
				<el-form-item label="售价：" prop="pass">
					<el-input placeholder="请输入商品库存" v-model="class_price" clearable></el-input>
				</el-form-item>
				<el-form-item label="库存：" prop="pass">
					<el-input placeholder="请输入商品售价" v-model="class_stock" clearable></el-input>
				</el-form-item>
				<el-form-item label="成本价:" prop="checkPass">
					<el-input placeholder="请输入商品成本价" v-model="cost" clearable></el-input>
				</el-form-item>

				<el-form-item class="dialog-footer">
					<el-button @click="dialogVisible = false">取 消</el-button>
					<el-button type="primary" @click="confirm">确 定</el-button>
				</el-form-item>
			</el-form>
		</el-dialog>


		<!-- <el-dialog title="修改规格" :visible.sync="dialogVisible2" width="30%" :before-close="close_table">
			<el-form class="demo-ruleForm" label-width="80px">
				<el-form-item label="规格值:" prop="OldPass">
					<el-input placeholder="请输入商品规格值" v-model="class_name" clearable></el-input>
				</el-form-item>
				<el-form-item label="售价：" prop="pass">
					<el-input placeholder="请输入商品库存" v-model="class_price" clearable></el-input>
				</el-form-item>
				<el-form-item label="库存：" prop="pass">
					<el-input placeholder="请输入商品售价" v-model="class_stock" clearable></el-input>
				</el-form-item>
				<el-form-item label="成本价:" prop="checkPass">
					<el-input placeholder="请输入商品成本价" v-model="cost" clearable></el-input>
				</el-form-item>

				<el-form-item class="dialog-footer">
					<el-button @click="dialogVisible2 = false">取 消</el-button>
					<el-button type="primary" @click="confirm_edit_spec">确 定</el-button>
				</el-form-item>
			</el-form>
		</el-dialog> -->
	</div>
</template>

<script type="text/javascript" src="../../../static/js/edit_shop.js">
</script>

<style scoped>
	@import '../../../static/css/global.css';
	/*引入公共样式*/
</style>
