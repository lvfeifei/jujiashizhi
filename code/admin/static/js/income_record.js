// import commJS from '../common.js';
export default {
	data() {
		return {
			tableData: [],
			count: 0,
			page: 1,
			limit: 10,
			status: '',
			// 模糊搜索
			date: '',
			key: ''
		}
	},
	//进入页面加载
	mounted: function () {
		var that = this;
		that.getList();
	},

	//方法
	methods: {

		/**
		 * 获取列表
		 */
		getList() {
			let that = this;
			let formData = {};
			formData.page = that.page;
			formData.token = that.token;
			formData.limit = that.limit;
			if(that.keys){
				formData.key = that.keys;
			}
			if(that.status){
				formData.status = that.status;
			}
			if(that.date){
				formData.start_time = that.date[0];
				formData.end_time = that.date[0];
			}

			that.axios.post("/User_Distribution/index", formData, {
				emulateJSON: true
			}).then(res => {
				var data = res;
				if (data) {
					that.tableData = data.list;
					that.count = data.count;
				}
			});
		},

		/**
		 * 导出表格
		 */
		// export_file() {
		// 	let that = this;
		// 	if (!that.tableData.length) return that.$message.warning('暂无数据!');
		// 	var start_time = that.date.length ? commJS.formatTime(that.date[0], 1) : '';
		// 	var end_time = that.date.length ? commJS.formatTime(that.date[1], 1) : '';

		// 	window.location.href = that.adminApi.admin_api + "/experience/order_export?token=" + that.token + '&type=' + that.type + '&sn=' + that.key + '&nickname=' + that.nickname + '&start_time=' + start_time + '&end_time=' + end_time;
		// },

		/**
		 * 搜索
		 */
		search() {
			let that = this;
			that.page = 1;
			that.getList();
		},

		/**
		 * 查看详情
		 */
		detail: function (e) {
			let that = this;
			//   commJS.save_page(that)

			that.$router.push({
				path: '/distribution/income_details',
				query: {
					record_id: e
				}
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
