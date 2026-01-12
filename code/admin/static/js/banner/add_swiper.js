import commJS from '../common.js';
export default {
	data() {
		return {
			// 表单
			form_data: {
				title: '',
				picture: [],
				sort: '',
				url_type: '1',
				url: '',
				status: '1',
			},
			header: {
				Authorization: this.token,
			},
			rules: {
				picture: [{
					required: true,
					message: '请上传图片',
					trigger: 'blur',
					type: 'array',
					min: 1,
				}],
				sort: [{
					required: true,
					message: '请填写排序',
					trigger: 'blur'
				}],
				url: [{
					required: true,
					message: '请填写链接地址',
					trigger: 'blur'
				}],
			},

		// 七牛云信息
		upload_img_url: this.adminApi.upload_url,
		postData: {folder:'banner'},
		domain: 'https://shilaohua-1258884793.cos.ap-beijing.myqcloud.com/',
			banner_id: ''
	}
},

/**
 * 进入页面加载
 */
mounted: function () {
	var that = this;
	
	// commJS.getQiNiuToken(that);
	var query = that.$route.query;
	if (query.banner_id) {
		that.banner_id = query.banner_id;
		that.getDetail();
	}
},

//方法
methods: {
	/**
	 * 获取详情
	 */
	getDetail() {
		var that = this;
		that.axios.post("/bannerimg/banner_show_edit", {
			token: that.token,
			banner_id: that.banner_id
		}, {
			emulateJSON: true
		}).then(res => {
			let data = res.data;
			if (res.status == 1) {
				that.form_data.title = data.title;
				that.form_data.picture.push({
					url: data.picture
				});
				that.form_data.sort = data.sort;
				that.form_data.url = data.url;
				that.form_data.status = data.status.toString();
			}
		});
	},

	/**
	 * 保存预览
	 */
	save() {
		const that = this;
		that.$refs.form_data.validate((valid) => {
			if (!valid) return that.$message.warning('请完整填写内容!');
			var formData = {};
			formData.token = that.token;
			formData.title = that.form_data.title;
			formData.picture = that.form_data.picture[0].url;
			formData.sort = that.form_data.sort;
			formData.url = that.form_data.url;
			formData.type = that.form_data.url_type;
			formData.status = that.form_data.status;
			var url = '/bannerimg/add';
			if (that.banner_id) {
				formData.banner_id = that.banner_id;
				url = '/bannerimg/banneredit';
			}
			that.axios.post(url, formData, {
				emulateJSON: true
			}).then(() => {
				that.$message.success(that.banner_id ? '修改成功' : '添加成功');
				that.$router.go(-1)
			}).catch(err=>{
				that.$message.error(err);
			})
		})
	},

	/**
	 * 图片超限制
	 */
	descExceed: function (t, e) {
		this.$message.warning("只能上传一张图片哦!")
	},

	/**
	 * 图片上传成功
	 */
	img_succ(res) {
		const that = this;
		that.form_data.picture.push({
			url: that.domain + res.data.imgurl,
		});
    that.form_data.title = res.data.imgurl;
	},

	/**
	 *图片移除
	 */
	del_img(file, fileList) {
		this.form_data.picture = fileList
	},

	/**
	 * 返回
	 */
	back() {
		this.$router.go(-1);
	},
}
}
