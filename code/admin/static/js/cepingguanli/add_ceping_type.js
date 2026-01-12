import commJS from '../common.js';
export default {
	data() {
		return {
			// 表单
			form_data: {
				name: '', 
				sort: '100',
				content:'',
				status: 1
			},
			rules: {
				name: [{
					required: true,
					message: '请输入分类名称',
					trigger: 'blur'
				}],
				content: [{
					required: true,
					message: '请输入分类描述',
					trigger: 'blur'
				}]
			}, 
			postData: {},
			id: ''
		}
	},

	/**
	 * 进入页面加载
	 */
	mounted: function () {
		var that = this; 
		var query = that.$route.query;
		if (query.id) {
			that.id = query.id;
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
			var formData = {}
			formData.classify_id = that.id 
			that.axios.post("/evaluation_class/show", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					let {data} = res
					if (data) { 
						that.form_data = data
					}
				}).catch(err => {that.$message.error(err);});
		},

		/**
		 * 保存预览
		 */
		save() {
			const that = this;
			that.$refs.form_data.validate((valid) => {
				if (!valid) return that.$message.warning('请完整填写内容!');
                    
				var formData = that.form_data; 
				var url = '/evaluation_class/save';
				if (that.id) {
					formData.classify_id = that.id; 
				}

				that.axios.post(url, formData, {
					emulateJSON: true
				}).then(
					function (data) {
						that.$message.success(that.id ? '修改成功' : '添加成功');
						that.$router.go(-1)
					}).catch(err => {
					that.$message.error(err);
				});

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
			console.log(res)
			const that = this;
			that.form_data.picture = [{
				name: 'img',
				url: res
			}];
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
		}
	}
}