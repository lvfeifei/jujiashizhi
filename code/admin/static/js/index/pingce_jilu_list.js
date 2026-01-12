import commJS from '../common.js';
export default {
	data() {
		return {
			img_url: this.adminApi.img_url, 
			sex_id:'0', 
			sex_list:[ 
				{
					id:'L1',
					name:'男'
				},
				{
					id:'L2',
					name:'女'
				}
			],
			xueli_id:'0',
			xueli_list:[ 
				{
				  id: 'N1',
				  name:'未上过学/不识字',
				},
				{
				  id: 'N2',
				  name:'小学',
				},
				{
				  id: 'N3',
				  name: '初中', 
				},
				{
				  id: 'N4',
				  name:'高中/中专'
				},
				{
				  id: 'N5',
				  name:'本科及以上'
				} 
			],
			bqcd_id:"0",
			bqcd_list:[ 
				{
					id:'P1',
					name:'轻度'
				},
				{
					id:'P2',
					name:'中度'
				},
				{
					id:'P3',
					name:'重度'
				} 
			],
			jblx_id:"0",
			jblx_list:[ 
				{
					id:'O1',
					name:'阿尔茨海默病'
				},
				{
					id:'O2',
					name:'血管性痴呆'
				},
				{
					id:'O3',
					name:'混合性痴呆'
				},
				{
					id:'O4',
					name:'其他'
				}
			],
			xznl_id:"0",
			xznl_list:[ 
				{
					id:'R1',
					name:'可以正常行走'
				},
				{
					id:'R2',
					name:'自行使用拐杖'
				} ,
				{
					id:'R3',
					name:'使用轮椅且需帮助'
				},
				{
					id:'R4',
					name:'完全卧床'
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
			is_show_bead_house:true,
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

		var that = this;
		that.getList(); 
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
			formData.status = that.status; 

			if(that.bead_house_id){
				formData.bead_house_id = that.bead_house_id 
			}

			// 性别
			if(that.sex_id != 0){
				formData.gender = that.sex_id; 
			}
			 
			// 学历
			if(that.xueli_id != 0){
				formData.education = that.xueli_id 
			}

			// 疾病类型
			if(that.jblx_id != 0){
				formData.disease_type = that.jblx_id 
			}

			// 行走能力
			if(that.xznl_id != 0){
				formData.walk = that.xznl_id 
			}

		    // 严重程度
			if (that.bqcd_id != 0) {
				formData.illness = that.bqcd_id;
			} 
 
			// 关键字搜索
			if (that.key) {
				formData.hobby = that.key;
			} 
  
			that.axios.post("/order/index", formData, {
				emulateJSON: true
			}).then(
				function (res) {
					let data = res.data 
					if (data) {  
						console.log(data.list)
						that.tableData = data.list; 
						that.count = data.count;
						that.page = curr_page ? curr_page : that.page;
						curr_page ? sessionStorage.removeItem('curr_page') : '';
					}
			}).catch(err => {that.$message.error(err.msg);});
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
		edit: function (row) {
			let that = this;
			commJS.save_page(that) 
			that.$router.push({
				path: '/index/pingce_jilu_detail',
				query: {
					order_id:row.id,
					user_id:row.user_id,
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