// import commJS from '../common.js';
export default {
	data() {
		return {
			user_id: '',
			avatar_url: '',
			nickname: '',
			gender: '',
			timestamp_phone: '',
			my_reward_count:'',
			profit_total_price:'',
			can_extract:'',
			freeze_price:'',
			my_friend_count:'',
			invite_people: [],
		}
	},

	/**
	 * 进入页面加载
	 */
	mounted: function () {
		var that = this;
		var query = that.$route.query;
		if (query.user_id) {
		  that.user_id = query.user_id;
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
			that.axios.post("/user/distribution_info", {
				id: that.user_id
			}, {
				emulateJSON: true
			}).then(res => {
				var data = res;
				if (data) {
					that.avatar_url = data.avatar_url;
					that.nickname = data.nickname;
					that.gender = data.gender == 1 ? '男' : '女';
					that.timestamp_phone = data.timestamp_phone ? data.timestamp_phone : '--';
					that.my_reward_count = data.my_reward_count;
					that.profit_total_price = data.profit_total_price;
					that.can_extract = data.can_extract;
					that.freeze_price = data.freeze_price;
					that.my_friend_count = data.my_friend_count;
					that.invite_people = data.invite_people;
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
