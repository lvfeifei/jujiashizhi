import commJS from '../common.js';
export default {
	data() {
		return {
			// 表单
			form_data: {
				picture:[],
				checked: true, 
				time: '09:00', 
			},
			rules: {
				picture: [{
					required: true,
					message: '请上传图片',
					trigger: 'blur',
					type: 'array',
					min: 1,
				}] 
			}, 
			upload_img_url: this.adminApi.upload_url,  
			upload_header:{
				token:this.cookie.get('token')
			},
			postData: {
				folder:"ceping"
			},
			id: ''
		}
	},

	/**
	 * 进入页面加载
	 */
	mounted: function () {
		this.getDetail();
	},

	//方法
	methods: {
		/**
		 * 获取详情
		 */
		getDetail() {
			var that = this; 
			that.axios.post("/setting/expert_intervene", '', {
				emulateJSON: true
			}).then(
				function (res) {
					console.log(res)
					let {data} = res
					if (data) {  
						that.form_data.checked = data.carePlan == 1 ? true : false
						that.form_data.time = data.sendTime 
						that.form_data.picture = [
							{
								name:'img',
								url: data.expertAvatar
							} 
						]
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
                    
				var formData = {}; 
				formData.expertAvatar = that.form_data.picture[0].url;
				formData.carePlan  = that.form_data.checked ? 1 : 0
				formData.sendTime  = that.form_data.time 
				var url = '/setting/save'; 
				that.axios.post(url, formData, {
					emulateJSON: true
				}).then(
					function () {
						that.$message.success('保存成功'); 
						that.getDetail()
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
		 img_succ(file) {
			const that = this; 
			that.form_data.picture = [{
				name: 'img',
				url:   file.data.imgurl
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