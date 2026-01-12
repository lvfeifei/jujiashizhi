<template>
	<div id="batch">
		<div id="content">
			<div id="head">批量发货
				<a v-bind:href="src"><el-button class="btn3" size="mini">下载批量发货模板</el-button></a>
			</div>
			<div id="rebate">
				<el-upload
						class="upload-demo"
						action="http://up-z2.qiniup.com/"
						:before-remove="beforeRemove"
					 	multiple
						:limit="1"
						:on-exceed="handleExceed"
						:file-list="fileList"
						:data="postData"
						:before-upload="beforeAvatarUpload"
						:on-success="handleAvatarSuccess"
						:on-error="handleError">
					<el-button size="small" type="primary">点击上传</el-button>
					<div slot="tip" class="el-upload__tip">只能上传xsl文件，且不超过1M</div>
				</el-upload>
				<el-button class="btn2" size="mini" @click="confirmSend">确定批量发货</el-button>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript" src="../../../static/js/batch.js"></script>

<style scoped>
	@import '../../../static/css/global.css';/*引入公共样式*/
</style>