import commJS from '../common.js';
export default {
	data() {
		return {
			tableData: [],
			key:'',//搜索
			count: 0,
			page: 1,
			limit: 10,
		}
	},
	//进入页面加载
	mounted: function () {
		var that = this;
		
		that.getList();
	},

	//方法
	methods: {
    search(){
      this.getList();
    },

		/**
		 * 获取列表
		 */
		getList() {
			let that = this;

			let formData = {};
			formData.token = that.token;
			formData.page = that.page;
			formData.limit = that.limit;
			formData.keyword = that.key;

			that.axios.post("/capability/index", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					var data = res.data;
					if (data) {
						data.list.forEach(ele => {
							ele.status = ele.status == 1 ? '开启' : '关闭';
						})
						that.tableData = data.list;
						that.count = data.count;
					}
				});
		},

		/**
		 * 添加
		 */
		add: function () {
			let that = this;
			commJS.save_page(that)

			that.$router.push({
				path: '/base/add_problem'
			});
		},

		/**
		 * 编辑
		 */
		edit: function (id) {
			let that = this;
			commJS.save_page(that)

			that.$router.push({
				path: '/base/add_problem',
				query: {
					banner_id: id,
				}
			});
		},

		/**
		 * 删除
		 */
		del_item: function (e) {
			var that = this;

			that.$confirm('此操作将永久删除该项, 是否继续?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {
				that.axios.post("/bannerimg/del", {
					token: that.token,
					id: e
				}, {
					emulateJSON: true
				}).then(
					function (res) {
						that.$message({
							type: 'success',
							message: `操作提示: ${'删除成功'}`
						});
						that.getList();
					})
			}).catch(res => { });
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
