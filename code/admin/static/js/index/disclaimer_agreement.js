import richText from "../../../src/page/common/richText_1"; 
import commJS from '../common.js';
export default {
	components: {
		richText,
	},
	data() {
		return {
			// 表单
			form_data: { 
				content:'', 
			}, 
			// 七牛云信息
			upload_img_url: this.adminApi.upload_url, 
			img_url: this.adminApi.img_url,  
			upload_header:{
				token:this.cookie.get('token')
			},
			postData: {
				folder:"xieyi"
			},
			domain: '',
			banner_id: ''
		}
	},

	/**
	 * 进入页面加载
	 */
	async mounted() {
		var that = this;  
		that.getDetail();  
	},

	//方法
	methods: { 
		/**
		* 富文本改变时
		*/
		editor_change(e) {
			this.form_data.content = e;
		},

		/**
		 * 获取详情
		 */
		getDetail() {
			var that = this;
			var formData = {}
			formData.key = 'disclaimer'
			that.axios.post("/config/index", formData, {
				emulateJSON: true
			}).then(
				function (res) { 
					that.form_data.content = res.value;  
				}).catch(err => {});
		},

		/**
		 * 保存预览
		 */
		save() { 
			const that = this;
			that.$refs.form_data.validate(() => { 
				let formData = {}
				formData.value = that.form_data.content;   
				formData.key = 'disclaimer';   
				that.axios.post('/config/save', formData, {
					emulateJSON: true
				}).then(
					function () {
						that.$message.success('保存成功'); 
					}).catch(err => {}); 
			})
		},

		/**
		 * 图片超限制
		 */
		descExceed: function (t, e) {
			this.$message.warning("只能上传一张图片哦!")
		},

		/**
		 * 图片超限制
		 */
		 descExceed_bigimage: function (t, e) {
			this.$message.warning("只能上传一张图片哦!")
		},

		/**
		 * 图片上传成功
		 */
		img_succ(file, fileList) {
			const that = this;
			// console.log(file);
			that.form_data.picture = [{
				name: 'img',
				url:   file.data.imgurl
			}]; 
		},

		/**
		 * 图片上传成功
		 */
		 img_succ_bigimage(file, fileList) {
			const that = this;
			// console.log(file);
			that.form_data.bigimage = [{
				name: 'img',
				// url: that.domain + '/' +  file.key
				url:  file.data.imgurl
			}]; 
		},


		/**
		 *图片移除
		 */
		del_img(file, fileList) {
			this.form_data.picture = fileList
		},

		/**
		 *图片移除
		 */
		 del_bigimage(file, fileList) {
			this.form_data.bigimage = fileList
		},

		/**
		 * 返回
		 */
		back() {
			this.$router.go(-1);
		}
	}
}