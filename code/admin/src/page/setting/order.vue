<template>
	<div id="setting_order">
		<div id="content">
			<div id="head">邀请返利设置</div>
			<div id="rebate">
				<template>
					<div class="input_model">
						<span class="label">自动取消待付款订单时间设置 (分钟)：&nbsp;&nbsp;</span>
						<el-input placeholder="请输入取消待付款订单时间" v-model="automatic_cancel_pay_time" clearable></el-input>
					</div>
					<p class="tagging">买家下单N分钟后仍未支付，则系统自动取消订单。</p>
				</template>
				<template>
					<div class="input_model">
						<span class="label">发货后自动确认收货时间设置 (天)：&nbsp;&nbsp;</span>
						<el-input placeholder="请输入自动确认收货时间" v-model="automatic_delivery_time" clearable></el-input>
					</div>
					<p class="tagging">发货N天后，如买家还没有主动确认收货，系统将自动确认收货。</p>
				</template>
				<div id="operation">
					<el-button size="mini" @click="save">保存</el-button>
					<el-button size="mini" @click="cancel">返回</el-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script  type="text/javascript" src="../../../static/js/order_set.js">

</script>

<style scoped>
	@import '../../../static/css/global.css';/*引入公共样式*/
</style>