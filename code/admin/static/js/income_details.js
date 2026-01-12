// import commJS from '../common.js';
export default {
	data() {
		return {
			record_id: '',
			distribution_list: [],
			nickname:'',
			avatar_url:'',
			mobile:'',
			contribution_avatar_url:'',
			contribution_nickname:'',
			contribution_mobile:'',
			distribution_avatar_url:[],
		}
	},

	/**
	 * 进入页面加载
	 */
	mounted: function () {
		var that = this;

		var query = that.$route.query;
		if (query.record_id) {
			that.record_id = query.record_id;
			that.getDetail();
		}


	},

	//方法
	methods: {
		/**
		 * 获取详情
		 */
		getDetail() {
			let that = this;
			let formData = {};
			that.axios.post("/User_Distribution/info", {
				id:that.record_id
			}, {
				emulateJSON: true
			}).then(res => {
				var data = res;
				if (data) {
					that.nickname = data.nickname;
					that.avatar_url = data.avatar_url;
					that.mobile = data.mobile;
					that.contribution_avatar_url = data.contribution_avatar_url;
					that.contribution_nickname = data.contribution_nickname;
					that.contribution_mobile = data.contribution_mobile;
					that.distribution_list = data.distribution_list;
					that.distribution_avatar_url = data.distribution_avatar_url;
				}
			});
		},
		/**
		 * 返回
		 */
		back() {
			this.$router.go(-1);
		},
	}
}
