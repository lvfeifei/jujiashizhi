export default {
	data() {
		return {
			list_data: [],
			checked: ''
		}
	},
	//进入页面加载
	mounted: function () {
		var that = this;
		
		that.getlist();
	},

	//方法
	methods: {
		/**
		 * 获取列表
		 */
		getlist() {
			let that = this;
			//请求的数据
			let formData = {};
			formData.token = that.token;

			that.axios.post("/system/index", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					var data = res.data;
					if (data) {
						data.forEach(ele => {
							ele.status = ele.status == 1 ? true : false;

							if (ele.child) {
								ele.child.forEach(item => {
									item.status = item.status == 1 ? true : false;
								})
							}
						});

						that.list_data = data;
					}
				});
		},

		/**
		 * 添加菜单
		 */
		addRegion() {
			this.$router.push('/set_up/add_menu')
		},

		/**
		 * 修改菜单
		 */
		edit(e) {
			this.$router.push({
				path: '/set_up/add_menu',
				query: {
					id: e
				}
			})
		},

		/**
		 * 
		 */
		del(e) {
			var that = this;
			that.$confirm('是否删除该菜单?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(() => {

				that.axios.post("/system/delete", {
					token: that.token,
					id: e
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
							that.getlist();
						}
					});
			})
		}

	}
}
