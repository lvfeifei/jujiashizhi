import commJS from '../common.js';
export default {
	data() {
		return {
            form_data: {
                url_type: '1',
            },
			tableData: [],
			page: 1,
			limit: 10,
			count: 0,
            dialogVisible:false,
            
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

			that.axios.post("/user/index", formData).then(
				function (res) {
					that.tableData = res.list;
					that.count = res.count;
				});
		},
        /**
		 * 编辑
		 */
		edit: function (id) {
			let that = this;
			commJS.save_page(that)

			that.$router.push({
				path: '/user/edit_user',
				query: {
					banner_id: id,
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
