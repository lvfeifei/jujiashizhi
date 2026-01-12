export default {
	data() {
		return {
			tableData: [],

			count: 0,
			page: 1,
			limit: 10,
		}
	},
	//进入页面加载
	mounted: function () {
		var that = this;
		that.getListData();
	},

	//方法
	methods: {
		/**
		 * 保存页数
		 */
		save_page() {
			var that = this;
			// 记录当前页
			sessionStorage.setItem('curr_page', that.page)
		},

		/**
		 * 获取列表
		 */
		getListData() {
			let that = this;

      let formData = {};
      formData.token = that.token;
      var curr_page = sessionStorage.getItem('curr_page');
      that.page = curr_page ? curr_page : that.page;

      formData.page = that.page;
      formData.limit = that.limit;
			that.axios.post("/Role/index", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					var data = res.data;
					if (data) {
						data.list.forEach(ele => {
							ele.status = ele.status == 1 ? true : false;
						})
						that.count = data.count;
						that.tableData = data.list;
						console.log(that.tableData)
					}
				});
		},

		/**
		 * 下一页
		 */
		handleCurrentChange: function (currentPage) {
			var that = this;
			that.page = currentPage;
			that.getListData();
		},

		/**
		 * 添加角色
		 */
		addRegion() {
			this.$router.push('/set_up/add_role')
		},

		/**
		 * 修改
		 */
		edit(e) {
			this.$router.push({
				path: '/set_up/add_role',
				query: {
					role_id: e
				}
			})
		},

		/**
		 * 删除
		 */
		del(e) {
			var that = this;
			that.$confirm('是否删除该角色 ?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {

				that.axios.post("/Role/delete_role", {
					token: that.token,
					role_id: e
				}, {
					emulateJSON: true
				}).then(
					function (res) {
						var data = res;
						if (data) {
							that.$message({
								type: 'success',
								message: '删除成功!'
							});
							that.getListData();
						}
					});
			})
		}
	}
}
