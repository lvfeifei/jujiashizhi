export default {
	data() {
		return {
			tableData: [],
			page: 1,
			limit: 10,
			count: 0

		}
	},

	//进入页面加载
	mounted: function () {
		var that = this;

		//获取邀请者
		that.getList();
	},

	methods: {
		//请求api
		getList: function () {
			var that = this;

			//请求的数据
			var formData = {};
			formData.token = that.token;
			formData.page = that.page;
			formData.limit = that.limit;

			that.axios.post("/user/index", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					var data = res;
					that.tableData = data.list;
					that.count = data.count;
				});
		},

		/**
		 * 下一页
		 */
		handleCurrentChange: function (currentPage) {
			var that = this;
			that.page = currentPage;
			that.getList();
		},
	}
}
