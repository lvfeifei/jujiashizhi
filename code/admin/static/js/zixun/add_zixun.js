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
				title: '',
				picture: [],
				bigimage: [],
				video_arr:[],
				video:'',
				sort: 100,
				send_time:'',
				content:'',
				send_status: '1',  
				help_class_id:'1'
			},
			rules: {
				picture: [{
					required: true,
					message: '请上传图片',
					trigger: 'blur',
					type: 'array',
					min: 1,
				}],
				bigimage: [{
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
				title: [{
					required: true,
					message: '请填写标题',
					trigger: 'blur'
				}],
				content: [{
					required: true,
					message: '请填写资讯内容',
					trigger: 'blur'
				}],
			},
			// 七牛云信息
			upload_img_url: this.adminApi.upload_url, 
			upload_video_url: this.adminApi.upload_video_url, 
			img_url: this.adminApi.img_url, 

			upload_header:{
				token:this.cookie.get('token')
			},
			postData: {
				folder:"zixun"
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
		var query = that.$route.query;
		if (query.banner_id) {
			that.banner_id = query.banner_id;
			that.getDetail();
		}
		
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
			formData.help_id = that.banner_id

			that.axios.post("/help/helpshow", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					let {data} = res
					console.log(data)
					if (data) {
						that.form_data.title = data.title;
						that.form_data.picture = [{
							url:   data.image
						}];
						that.form_data.bigimage = [{
							url:    data.bigimage
						}];
						that.form_data.sort = data.sort;
						that.form_data.video = data.video;
					 
						if(data.send_status == 2){
							that.form_data.send_time = data.create_time;
						}

						that.form_data.content = data.content;
						that.form_data.help_class_id = data.help_class_id.toString();
						that.form_data.send_status = data.send_status.toString();
					 
					}
				}).catch(err => {});
		},

		/**
		 * 保存预览
		 */
		save() {
 
			const that = this;
			that.$refs.form_data.validate((valid) => {
				if (!valid) return that.$message.warning('请完整填写内容!');
				var formData = {};
  
				if (that.form_data.picture.length == 0) {
					return that.$message.warning('请上传小图!')
				}

				if (that.form_data.bigimage.length == 0) {
					return that.$message.warning('请上传大图!')
				}

				formData.send_time = that.form_data.send_time; 
				if (that.form_data.send_status == 2) {
					if (!that.form_data.send_time) {
						return that.$message.warning('请选择发布时间!')
					}  
				}else{
					formData.send_time = ''
				}

				formData.title = that.form_data.title; 
				formData.sort = that.form_data.sort; 
				formData.content = that.form_data.content; 
				formData.help_class_id = that.form_data.help_class_id; 
				formData.small_img = that.form_data.picture[0].url ;
				formData.big_img = that.form_data.bigimage[0].url ;
				formData.video = that.form_data.video ;
				formData.send_status = that.form_data.send_status;
				var url = '/help/save';
				if (that.banner_id) {
					formData.help_id = that.banner_id; 
				}

				that.axios.post(url, formData, {
					emulateJSON: true
				}).then(
					function (data) {
						that.$message.success(that.banner_id ? '修改成功' : '添加成功');
						that.$router.go(-1) 
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

		// 视频限制
		video_descExceed: function (t, e) {
			this.$message.warning("只能上传一个视频!")
		},

		/**
		 * 视频上传成功
		 */
		 h5_img_succ(file) {
			const that = this;
			that.form_data.video_arr = [{
				name: 'img',
				url:   file.data.imgurl
			}]; 
			that.form_data.video = file.data.imgurl 
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
		 *h5图片移除
		 */
		 del_h5_img(file, fileList) {
			let that = this
			that.form_data.video_arr = []
			that.form_data.video = ''
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