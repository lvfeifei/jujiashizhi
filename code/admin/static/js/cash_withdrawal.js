// import commJS from '../common.js';
export default {
	data() {
		return {
			tableData: [],
			count: 0,
			page: 1,
			limit: 10,
			type: '',
			date: '',
			is_handle:false,
			id:0,
			status:'1',
			describe:''
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

			var curr_page = sessionStorage.getItem('curr_page');
			that.page = curr_page ? curr_page - 0 : that.page;

			formData.page = that.page;
			formData.limit = that.limit;
			if (that.type) {
				formData.type = that.type;
			}
			if (that.date) {
				formData.start_time = that.date[0];
				formData.end_time = that.date[1];
			}
			that.axios.post("/User_distribution/user_balance_list", formData, {
				emulateJSON: true
			}).then(res => {
				var data = res;
				if (data) {
					//   data.list.forEach(ele => {
					//     ele.create_time = commJS.formatTime(new Date(ele.create_time * 1000));
					//     ele.pay_time = commJS.formatTime(new Date(ele.pay_time * 1000));
					//   })
					that.tableData = data.list;
					that.count = data.count;
					that.page = curr_page ? curr_page : that.page;
					curr_page ? sessionStorage.removeItem('curr_page') : '';
				}
			});
		},

		/**
		 * 导出表格
		 */
		export_file() {
			let that = this;
			if (!that.tableData.length) return that.$message.warning('暂无数据!');
			var start_time = that.date.length ? commJS.formatTime(that.date[0], 1) : '';
			var end_time = that.date.length ? commJS.formatTime(that.date[1], 1) : '';

			window.location.href = that.adminApi.admin_api + "/experience/order_export?token=" + that.token + '&type=' + that.type + '&sn=' + that.key + '&nickname=' + that.nickname + '&start_time=' + start_time + '&end_time=' + end_time;
		},

		/**
		 * 搜索
		 */
		search() {
			let that = this;
			that.page = 1;
			that.getList();
		},

		/**
		 * 下一页
		 */
		handleCurrentChange: function (currentPage) {
			var that = this;
			that.page = currentPage;
			that.getList();
		},

		/**
		 * 处理
		 */
		give_price(e) {
			var that = this;
			that.is_handle = true;
			that.id = e;
		},

		/**
		 * 关闭弹窗
		 */
		close_peice(){
			var that = this;
			that.is_handle = false;
			that.status = '1';
			that.describe = '';
			that.id = 0;
		},


		/**
		 * 确定处理
		 */
		commit_prcie(){
			var that = this;
			that.$confirm('确定'+(that.status == 1?'操作打款吗?':'驳回'), '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {
				that.axios.post("/User_distribution/review", {
					token: that.token,
					id: that.id,
					status:that.status,
					describe:that.describe
				}, {
					emulateJSON: true
				}).then(
					function (res) {
						that.$message.success('处理成功');
						that.getList();
						that.close_peice();
					})
			}).catch(res => { });
		}

	}
}
