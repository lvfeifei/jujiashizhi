<template>
	<div id="invitation_set">
		<div id="content">
			<div id="head">邀请返利设置</div>
			<div id="rebate">
				<template>
					<span class="label">邀请返利开关：&nbsp;&nbsp;</span>
					<el-radio v-model="invite_rebate_switch" label="1">开启</el-radio>
					<el-radio v-model="invite_rebate_switch" label="2">关闭</el-radio>
					<p class="tagging">邀请返利开关关闭后，前端将不能再显示我的好友和结算中心入口。</p>
				</template>
				<template>
					<span class="label">有效期设置：&nbsp;&nbsp;</span>
					<el-radio v-model="invite_rebate_status" label="1">永久</el-radio>
					<el-radio v-model="invite_rebate_status" label="2">自定义时间</el-radio>
					<div class="input_model" v-if="invite_rebate_status === '2'">
						<span class="character">保护期时间：</span>
						<el-input placeholder="请输入有效期时间" v-model="invite_rebate_day" clearable></el-input>&nbsp;&nbsp;天
					</div>
					<p class="tagging">永久：被邀请者发生的购买将永久计入邀请者的业绩中，不受有效期时间限制<br/>自定义时间：被邀请者通过分享链接进入后，超出有效期时间，被邀请者发生的购买将不再计入邀请者的业绩中。</p>
				</template>
				<template>
					<span class="label">邀请者保护期设置：&nbsp;&nbsp;</span>
					<el-radio v-model="invite_protect_status" label="1">永久</el-radio>
					<el-radio v-model="invite_protect_status" label="2">自定义时间</el-radio>
					<div class="input_model" v-if="invite_protect_status === '2'">
						<span class="character">保护期时间：</span>
						<el-input placeholder="请输入保护期时间" v-model="protect_day" clearable></el-input>&nbsp;&nbsp;天
					</div>
					<p class="tagging">永久：被邀请者将永远不能变更邀请者。<br/>自定义时间：超出保护期时间，被邀请者将可以变更邀请者。</p>
				</template>
				<template>
					<div class="input_model">
						<span class="label">佣金比例设置：&nbsp;&nbsp;</span>
						<el-input placeholder="请输入佣金比例" v-model="invite_commission_rate" clearable></el-input>&nbsp;&nbsp;%
					</div>
					<p class="tagging">被邀请者每笔订单实付金额 * 佣金比例 = 邀请者单笔佣金收益<br/>系统将根据你设置的佣金比例自动为邀请者结算佣金。</p>
				</template>
				<template>
					<div class="input_model">
						<span class="label">优惠比例设置：&nbsp;&nbsp;</span>
						<el-input placeholder="请输入优惠比例" v-model="discount_rate" clearable></el-input>&nbsp;&nbsp;%
					</div>
					<p class="tagging">消费者自己购买的每笔订单实付金额 * 优惠比例 = 消费者自己的单笔VIP优惠返利<br/>系统将根据你设置的佣金比例自动为邀请者结算佣金。</p>
				</template>
				<template>
					<span class="label">自动结算时间：&nbsp;&nbsp;</span>
					<el-radio v-model="auto_balance_type" label="1">交易完成后</el-radio>
					<el-radio v-model="auto_balance_type" label="2">退款期过后</el-radio>
					<div class="input_model" v-if="auto_balance_type === '2'">
						<span class="character">自动结算时间：</span>
						<el-input placeholder="请输入自动结算时间" v-model="auto_balance_day" clearable></el-input>&nbsp;&nbsp;天
					</div>
					<p class="tagging">交易完成后：被邀请者主动确认收货后或系统自动确认收货后将为邀请者发放佣金，如交易完成后发生<br/>退款，已发放的佣金不能追回。<br/>退款期过后：交易完成后再过自定义天数才会为邀请者发放佣金，防止被邀请者退款。</p>
				</template>
				<div id="operation">
					<el-button size="mini" @click="save">保存</el-button>
					<el-button size="mini" @click="cancel">返回</el-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script  type="text/javascript" src="../../../static/js/invitation_set.js">

</script>

<style scoped>
	@import '../../../static/css/global.css';/*引入公共样式*/
</style>