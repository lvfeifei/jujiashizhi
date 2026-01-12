import commJS from '../common.js';
export default {
	data() {
		return {
			img_url: this.adminApi.img_url, 
			sex_id: '0', 
			
			sex_list:[ 
				{
					id:'S1',
					name:'男'
				},
				{
					id:'S2',
					name:'女'
				}
			],
			xueli_id:'0',
			xueli_list:[ 
				{
				  id: 'U1',
				  name:'未上过学/不识字',
				},
				{
				  id: 'U2',
				  name:'小学',
				},
				{
				  id: 'U3',
				  name: '初中', 
				},
				{
				  id: 'U4',
				  name:'高中/中专'
				},
				{
				  id: 'U5',
				  name:'本科及以上'
				} 
			],
			year_id:"0",
			year_list:[
				{
					id:'V1',
					name:'<1年'
				},
				{
					id:'V2',
					name:'1-2年'
				},
				{
					id:'V3',
					name:'2–4年'
				},
				{
					id:'V4',
					name:'>4年'
				}
			],
			guanxi_id:"0",
			guanxi_list:[
				{
					id:'W1',
					name:'配偶'
				},
				{
					id:'W2',
					name:'子女'
				},
				{
					id:'W3',
					name:'媳婿'
				},
				{
					id:'W4',
					name:'其他'
				}
			],
			room_id:"0",
			room_list:[
				{
					id:'X1',
					name:'是'
				},
				{
					id:'X2',
					name:'否'
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
			bead_house_list:[],
			bead_house_id:"0",
			is_show_bead_house:true
		}
	},
	//进入页面加载
	mounted: function () { 
		let arr = document.cookie.split('; ');
		for(let i=0;i<arr.length;i++){
			let index = arr[i].indexOf("=") //返回第一个“=”所在的位置 
			if(arr[i].substring(0,index)=="role_id"){ 
				this.is_show_bead_house = arr[i].split('=')[1] == 11 ? false : true 
			}
		} 

		this.getList(); 
		this.get_bead_house_list()
	},

	//方法
	methods: {
 
		addstatus(e) {
			this.status = e
            this.page = 1;
			this.getList()
		},
		// 获取养老院列表
		get_bead_house_list() {
            let that = this; 
            that.axios.get("/bead_house/beadhouselist", {}, {
                emulateJSON: true
            }).then(
                function (res) {
                    let data = res.data
                    if (data) {  
                        that.bead_house_list = data;
						console.log(that.bead_house_list) 
                    }
                }).catch(err => { that.$message.error(err); });
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

			if(that.sex_id){
				formData.gender = that.sex_id; 
			}
			
			if(that.xueli_id){
				formData.education = that.xueli_id 
			}

			if(that.year_id){
				formData.care_years = that.year_id 
			}

			if(that.guanxi_id){
				formData.relation = that.guanxi_id 
			}

			if(that.room_id){
				formData.live = that.room_id 
			} 
			if(that.bead_house_id){
				formData.bead_house_id = that.bead_house_id 
			}
			 
			if (that.key) {
				formData.key = that.key;
			} 
  
			that.axios.post("/user/index", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					let data = res.data 
					if (data) { 
						  
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
				path: '/index/zhaohuzhe_detail',
				query: {
					order_id:id,
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