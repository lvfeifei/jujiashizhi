import commJS from '../common.js';
export default {
	data() {
		return {
			tableData: [],
			count: 0,
			page: 1,
			limit: 20,
			key: '',
			sort: 1,
			status: 0,

			multipleSelection: []
		}
	},
	//进入页面加载
	mounted: function () {
		var that = this;
		that.getList();
	},

	//方法
	methods: {
 
		handleSelectionChange(val) {
			this.multipleSelection = val;
		}, 
		
		//  批量删除
		delete_all(){ 
			if(this.multipleSelection.length === 0){
				return this.$message.error('请选择要删除的内容~');
			} 
			var that = this; 
			that.$confirm('此操作将永久删除该项, 是否继续?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				Category: 'warning'
			}).then(() => { 
				let ids = this.multipleSelection.map(item => item.id) 
				that.axios.post("/Category/del_all", {ids}).then(res => {
					that.$message({
						Category: 'success',
						message: `操作提示: ${'删除成功'}`
					});
					if (that.tableData.length == 1 && that.page > 1) {
						sessionStorage.setItem('curr_page', that.page - 1)
					}
					that.getList();
				})
			}).catch();
		},

		// 导出数据分析文件
		export_file(){
			let url =  this.adminApi.api_url + '/export/export_analyze'
			const el = document.createElement('a');
			el.style.display = 'none';
			el.setAttribute('target', '_blank'); 
			el.setAttribute('download', 'analyze_file');
			el.href = url;
			console.log(el);
			document.body.appendChild(el);
			el.click();
			document.body.removeChild(el);
		},

		/**
		 * 获取列表
		 */
		getList() {
			let that = this; 
			let formData = {}; 
			that.axios.post("/analyze/analyze", formData).then(res => {
				let {data} = res
				if (data) { 
					console.log(data)
					that.tableData = data; 
					
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
				path: '/cepingguanli/add_ceping_type'
			});
		},

		/**
		 * 修改
		 */
		edit: function (id) {
			let that = this;
			commJS.save_page(that) 
			that.$router.push({
				path: '/cepingguanli/add_ceping_type',
				query: {
					id: id,
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
				Category: 'warning'
			}).then(() => {
				that.axios.post("/evaluation_class/del", {
					evaluationclass_id: e
				}).then(res => {
					that.$message({
						Category: 'success',
						message: `操作提示: ${'删除成功'}`
					});
					// if (that.tableData.length == 1 && that.page > 1) {
					// 	sessionStorage.setItem('curr_page', that.page - 1)
					// }
					that.getList();
				})
			}).catch();
		},

		/**
		 * 下一页
		 */
		handleCurrentChange(currentPage) {
			var that = this;
			that.page = currentPage;
			that.getList();
		},
		/**
		 * 搜索
		 */
		search() {
			var that = this;
			that.page = 1;
			that.getList();
		},

	}
}