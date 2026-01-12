<template>
	<div id="order_detail">
		<el-col :span="24" class="warp-breadcrum">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{path:'/index/swiper'}"><b>订单管理</b></el-breadcrumb-item>
				<el-breadcrumb-item :to="{path: '/nav3/order'}">商品订单</el-breadcrumb-item>
				<el-breadcrumb-item>订单详情</el-breadcrumb-item>
			</el-breadcrumb>
		</el-col>
		<!--基本信息-->
		<div class="model">
			<div class="content2" style="background:#fff;">
				<div class="head">基本信息</div>
				<div style="padding: 0 20px;">
					<p style="font-size:14px;color:#303133;"><span>申请号：</span>{{detail_data.order.sn}}</p>
					<p style="font-size:14px;color:#303133;"><span>申请时间：</span>{{detail_data.order.create_time}}</p>
					<p style="font-size:14px;color:#303133;"><span>申请人姓名：</span>{{detail_data.order.name}}</p>
					<p style="font-size:14px;color:#303133;"><span>用户联系方式：</span>{{detail_data.order.mobile}}</p>
					<p style="font-size:14px;color:#303133;"><span>所属街道：</span>{{detail_data.order.address}}</p>
					<p style="font-size:14px;color:#303133;"><span>所属社区：</span>{{detail_data.order.community}}</p>
					<p style="font-size:14px;color:#303133;"><span>照料情况：</span>{{detail_data.order.care}}</p>
					<p style="font-size:14px;color:#303133;"><span>身份特征：</span>{{detail_data.order.physical_signs}}</p>
					<p style="font-size:14px;color:#303133;"><span>受教育程度：</span>{{detail_data.order.education}}</p>
					<p style="font-size:14px;color:#303133;"><span>改造地址：</span>{{detail_data.order.province_id + detail_data.order.city_id + detail_data.order.county_id + detail_data.order.street_id}}</p>
					<p style="font-size:14px;color:#303133;"><span>申请备注：</span>{{detail_data.order.content}}</p>
					<p style="font-size:14px;color:#303133;"><span>当前申请进度：</span>{{detail_data.order.status}}</p>
					<p style="font-size:14px;color:#303133;"><span>订单完成时间：</span>{{detail_data.order.construction_end_time}}</p>
				</div>
			</div>
			<div class="content2" style="background:#fff;">
				<div class="head">评估信息</div>
				<div style="padding: 0 20px;">
					<p style="font-size:14px;color:#303133;"><span>评估人员：</span>{{detail_data.order.evaluation_user_name}}</p>
					<p style="font-size:14px;color:#303133;"><span>开始评估时间：</span>{{detail_data.order.create_time}}</p>
					<p style="font-size:14px;color:#303133;display:flex;align-items:top;">
						<span>用户签字图片：</span>
<!--						<img class="signPic" src="../../../img/1.png" />-->
						<img class="signPic" :src="domain+detail_data.order.construction_user_sign" />
					</p>
				</div>
			</div>
			<div class="content2" style="background:#fff;">
				<div class="head">施工信息</div>
				<div style="padding: 0 20px;">
					<p style="font-size:14px;color:#303133;"><span>方案总报价：</span>{{detail_data.order.total_price}}</p>
					<p style="font-size:14px;color:#303133;"><span>施工人员：</span>{{detail_data.order.construction_user_name}}</p>
					<p style="font-size:14px;color:#303133;"><span>开始施工时间：</span>{{detail_data.order.total_price}}</p>
					<p style="font-size:14px;color:#303133;display:flex;align-items:top;">
						<span>施工方案照片存证：</span>
						<img class="signPic" src="../../../img/1.png" />
						<img class="signPic" src="../../../img/1.png" />
					</p>
					<p style="font-size:14px;color:#303133;display:flex;align-items:top;">
						<span>签字图片：</span>
						<img class="signPic" src="../../../img/1.png" />
						<img class="signPic" src="../../../img/1.png" />
					</p>
				</div>
			</div>
			<div class="content2" style="background:#fff;">
				<div class="head">详细改造方案-客厅</div>
				<div style="padding: 0 20px;">
					<p style="font-size:14px;color:#303133;display:flex;align-items:top;">
						<span>客厅图片：</span>
						<img class="floorPic" src="../../../img/1.png" />
						<img class="floorPic" src="../../../img/1.png" />
						<img class="floorPic" src="../../../img/1.png" />
						<img class="floorPic" src="../../../img/1.png" />
						<img class="floorPic" src="../../../img/1.png" />
						<img class="floorPic" src="../../../img/1.png" />
					</p>
					<p style="font-size:14px;color:#303133;"><span>区域评估概况：</span>{{detail_data.order.total_price}}</p>
					<div class="dianwei_modual">
						<div class="title">输入点位</div>
						<div class="dianwei_content">
							<p class="dianwei1">
								<span class="small_title">点位1：</span>
								<el-input class="inputTxt w300" placeholder="请输入点位数据"></el-input>
								<span class="pre_txt">cm</span>
							</p>
							<p class="dianwei1">
								<span class="small_title">点位2：</span>
								<el-input class="inputTxt w300" placeholder="请输入点位数据"></el-input>
								<span class="pre_txt">cm</span>
							</p>
						</div>
					</div>
					<div class="dianwei_modual">
						<div class="title">助具适配方案</div>
						<div class="zhuju_modual">
							<img class="zhujuPic" src="../../../img/1.png" />
							<div>
								<p>我是助具名称（默认规格）</p>
								<p>单价：</p>
								<p><span class="mar_R_20">数量：20</span><span>总价：</span></p>
							</div>
						</div>
					</div>
					<div class="dianwei_modual">
						<div class="title">助具安装方案描述：</div>
					</div>
				</div>

			</div>
			<el-button class="backABtn" @click="back">返回</el-button>
		</div>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/order_detail.js"></script>

<style>
.signPic{
	width: 200px;
	height: 100px;
	margin-right: 10px;
}
.floorPic{
	width: 100px;
	height: 100px;
	margin-right: 40px;
}
.dianwei_modual{
	font-size: 14px;
}
/* .dianwei_modual .dianwei_content{
	display: flex;
	justify-content: space-between;
} */
.dianwei_modual .title{
	margin: 32px 0;
}
#order_detail .dianwei_modual .dianwei_content .dianwei1{
	margin: 32px 0 0 0;
}
#order_detail .dianwei_modual .dianwei_content .dianwei1 .pre_txt{
	margin-left: 10px;
}
#order_detail .dianwei_modual .zhujuPic{
	width: 100px;
	height: 100px;
	margin-right: 20px;
}
#order_detail .dianwei_modual .zhuju_modual{
	display: flex;
	align-items: center;
}
#order_detail .dianwei_modual .zhuju_modual p{
	line-height: 32px;
	margin: 0;
}
#order_detail .backABtn{
	margin: 30px 0 0 20px;
}
</style>
