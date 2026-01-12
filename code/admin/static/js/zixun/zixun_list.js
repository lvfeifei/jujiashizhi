import commJS from '../common.js';
export default {
	data() {
		return {
			img_url: this.adminApi.img_url, 
			type_id:'0', 
			type:[ 
				{
					id:1,
					name:'典型案例'
				},
				{
					id:2,
					name:'共性问题解答'
				}
			],
 
			status: 0,
			date: '',
			tableData: [],
			count: 0,
			page: 1,
			limit: 10,
			key: '',
			
			prevList: [],
			multipleSelection: [],
			
			 
			project_type_list:[],

			helpType:1, // 展示模式
			dialogVisible:false,
		}
	},
	//进入页面加载
	mounted: function () {
		var that = this;
		that.getList();  
		// 获取展示模式
		that.get_show_model() 
	},

	//方法
	methods: {
 
		// 设置展示模式
		set_show_model(){
			var that = this;
			that.axios.post("/config/sethelpway", {
				help_type:that.helpType
			}, {
				emulateJSON: true
			}).then(function (res) {  
				if(res.status == 1){
					that.$message.success(res.msg)
					that.dialogVisible = false
					// 获取展示模式
					that.get_show_model() 
				}
			}).catch(err => {that.$message.error(err);});
		},
 
		// 获取展示模式
		get_show_model(){ 
			var that = this;
			that.axios.post("/config/gethelpway", '', {
				emulateJSON: true
			}).then(function (res) { 
				that.helpType = res.data.helpType 
			}).catch(err => {that.$message.error(err);});

		},

		addstatus(e) {
			this.status = e
            this.page = 1;
			this.getList()
		},

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

			if(that.status > 0){
				formData.send_status = that.status; 
			}
			
			if(that.type_id > 0){
				formData.help_class_id = that.type_id 
			}
            
			if (that.key) {
				formData.key = that.key;
			} 

			// if(that.date){
			// 	formData.start_time = that.date[0]
			// 	formData.end_time = that.date[1]
			// }
 
			that.axios.post("/help/index", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					let data = res.data 
					if (data) { 
						// data.list.forEach(element => {
						// 	element.bigimage = that.img_url + element.bigimage
						// 	element.image = that.img_url  + element.image
						// });

						// console.log(data.list)
						that.tableData = data.list; 
						that.count = data.count;
						that.page = curr_page ? curr_page : that.page;
						curr_page ? sessionStorage.removeItem('curr_page') : '';
					}
			}).catch(err => {that.$message.error(err);});
		},

		/**
		 * 添加
		 */
		add: function () {
			let that = this;
			commJS.save_page(that) 
			that.$router.push({
				path: '/zixun/add_zixun'
			});
		},

		/**
		 * 修改
		 */
		edit: function (id) {
			let that = this;
			commJS.save_page(that) 
			that.$router.push({
				path: '/zixun/add_zixun',
				query: {
					banner_id: id,
				}
			});
		},


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
				type: 'warning'
			}).then(() => { 
				let ids = this.multipleSelection.map(item => item.id) 
				that.axios.post("/Content/del_all", {ids}).then(res => {
					that.$message({
						type: 'success',
						message: `操作提示: ${'删除成功'}`
					});
					if (that.tableData.length == 1 && that.page > 1) {
						sessionStorage.setItem('curr_page', that.page - 1)
					}
					that.getList();
				})
			}).catch();
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
				var formData = {}
				formData.help_id = e;
				that.axios.post('/help/del', formData, {
					emulateJSON: true
				}).then(
					function (res) {
						if(!res.status){
							return that.$message.error(res.msg);
						}
						that.$message({
							type: 'success',
							message: `操作提示: ${res.msg}`
						});
						if (that.tableData.length == 1 && that.page > 1) {
							sessionStorage.setItem('curr_page', that.page - 1)
						}
						that.getList();
					}).catch(err => {that.$message.error(err);});
			})
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
		}

	}
}