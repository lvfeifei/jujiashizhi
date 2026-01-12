import commJS from '../common.js';
export default {
	data() {
		return { 
			tableData: [],
			evaluationclass_list:[],
			// 表单
			form_data: {
				name: '', 
				sort: '100',
				sn:'',
				type:2,
				classify_id:'', 
				status: 1
			},
			form_data_dialog:{
				name: '', 
				sn:'',
				sort: '0', 
				type:2, 
				status: 1,
				picture:[]
			},
			rules: {
				name: [{
					required: true,
					message: '请输入测评问题名称',
					trigger: 'blur'
				}],
				sn: [{
					required: true,
					message: '请输入测评问题编号',
					trigger: 'blur'
				}],
				classify_id: [{
					required: true,
					message: '请选择测评问题分类',
					trigger: 'blur'
				}],
			}, 
			rules_dialog: {
				name: [{
					required: true,
					message: '请输入选项内容',
					trigger: 'blur'
				}],
				sn: [{
					required: true,
					message: '请输入选项编号',
					trigger: 'blur'
				}], 
				picture: [{
					required: true,
					message: '请上传图片',
					trigger: 'blur',
					type: 'array',
					min: 1,
				}] 
			}, 
			// 七牛云信息
			upload_img_url: this.adminApi.upload_url,  
			upload_header:{
				token:this.cookie.get('token')
			},
			postData: {
				folder:"ceping"
			},
			id: '',
			dialogVisible:false, 
			current_item_my_id:'', 
			current_item_id:''
		}
	},

	/**
	 * 进入页面加载
	 */
	mounted: function () {
		var that = this; 
		var query = that.$route.query;
		that.getTypeList(); 
 
		if (query.id) {
			that.id = query.id;
			that.getDetail();
		}else{
			let tableData =  localStorage.getItem('tableData') ? JSON.parse(localStorage.getItem('tableData')) : []
			if(tableData.length){
				tableData.sort((a,b) => {
					a = a.sort
					b = b.sort 
					return b - a
				});
				that.tableData = tableData
			}
		} 
 
	},

	//方法
	methods: {

		/**
		 * 修改
		 */
		edit: function (row) {
			 let that = this 
			 if(row.my_id){
				that.current_item_my_id = row.my_id
			 }else{
				that.current_item_id = row.id
			 } 
			 that.form_data_dialog = row
			 if(row.picture){
				that.form_data_dialog.picture = [{
					url: row.picture
				}];
			 }else{
				that.form_data_dialog.picture = []
			 }
			
			that.dialogVisible = true
  
		},
  
		/**
		 * 删除
		 */
		del_item: function (row) {
			var that = this; 
			that.$confirm('此操作将永久删除该项, 是否继续?', '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				Category: 'warning'
			}).then(() => { 
				if(row.my_id){
					let new_table_data = that.tableData.filter(item => item.my_id != row.my_id)
					localStorage.setItem('tableData',JSON.stringify(new_table_data))
					that.tableData = new_table_data
				}else{
					that.axios.post("/evaluation_capability/optiondel", {
						option_id: row.id,
						evaluation_capability_id:that.id
					}).then(res => {
						that.$message({
							Category: 'success',
							message: `操作提示: ${'删除成功'}`
						}); 
						that.getDetail();
					})
				}
				
			}).catch();
		},

 
		// 清空选项
		clear_form_data_dialog(){
			this.form_data_dialog = {
				name: '', 
				sn:'',
				sort: '0', 
				type:2, 
				status: 1,
				picture:[]
			}
		},

		/**
		 * 图片超限制
		 */
		 descExceed: function (t, e) {
			this.$message.warning("只能上传一张图片哦!")
		},
 
		/**
		 *图片移除
		 */
		 del_img(file, fileList) {
			this.form_data_dialog.picture = fileList
		},

		/**
		 * 图片上传成功
		 */
		 img_succ(file, fileList) {
			const that = this;
			// console.log(file);
			that.form_data_dialog.picture = [{
				name: 'img',
				url:   file.data.imgurl
			}]; 
		},


		// 打开弹窗
		open_dialog(){ 
			this.dialogVisible = true
		},

		// 关闭弹窗
		close_dialog(){ 
			this.clear_form_data_dialog() 
			if(this.current_item_my_id){
				let tableData =  JSON.parse(localStorage.getItem('tableData')) || []
				if(tableData.length){
					tableData.sort((a,b) => {
						a = a.sort
						b = b.sort 
						return b - a
					});
					this.tableData = tableData
				}
			}
			 
			this.dialogVisible = false
		},

		// 保存弹窗内容
		confirm_add(){ 
			const that = this;  
			that.$refs.form_data_dialog.validate((valid) => { 
				if (!valid) return that.$message.warning('请完整填写内容!');  
				var formData = that.form_data_dialog; 
				if(that.form_data_dialog.picture.length){
					formData.picture = that.form_data_dialog.picture[0].url 
				}else{
					formData.picture = ''
				}
			     
				if(!that.id){
					formData.my_id = that.current_item_my_id ?  that.current_item_my_id : parseInt(Math.random()*100000)
				
					// 第一次添加 
					let tableData = localStorage.getItem('tableData') ? JSON.parse(localStorage.getItem('tableData')) : []
					if(tableData.length){                 
						if(that.current_item_my_id){ 
							tableData.forEach(element => {
								if(element.my_id == that.current_item_my_id) {
									element.name = formData.name
									element.sn = formData.sn
									element.sort = formData.sort
									element.type = formData.type
									element.status = formData.status
									element.picture = formData.picture
								} 
							});

						}else{
							tableData.push(formData) 
						}
						
						tableData.sort((a,b) => {
							a = a.sort
							b = b.sort 
							return b - a
						});
						that.tableData = tableData
					}else{
						that.tableData.push(formData) 
					}
 
					localStorage.setItem('tableData',JSON.stringify(that.tableData))
					that.dialogVisible = false
					that.clear_form_data_dialog();
					that.$message.success('保存成功');  
				}else{
					// 接口操作
					var url = '/evaluation_capability/optionsave';
					formData.evaluation_capability_id = that.id; 
					if (that.current_item_id) { 
						formData.option_id = that.current_item_id; 
					}   
					that.axios.post(url, formData, {
						emulateJSON: true
					}).then(
						function (data) {
							that.$message.success(that.current_item_id ? '修改成功' : '添加成功');
							that.dialogVisible = false
							that.getDetail();
						}).catch(err => {
						that.$message.error(err);
					});
				} 	

			})
			
		},

		// 获取分类列表
		getTypeList(){
			let that = this 
			that.axios.post("/evaluation_class/getlist", '', {
				emulateJSON: true
			}).then(
				function (res) {  
					that.evaluationclass_list = res.data;    
			}).catch(err => {that.$message.error(err);});
		},

		/**
		 * 获取详情
		 */
		getDetail() {
			var that = this;
			var formData = {}
			formData.evaluation_capability_id = that.id 
			that.axios.post("/evaluation_capability/show", formData, {
				emulateJSON: true
			}).then(
				function (res) { 
					let {data} = res
					if (data) { 
						that.form_data = data
						that.tableData = data.option
					} 
				}).catch(err => {that.$message.error(err);});
		},

		/**
		 * 保存预览
		 */
		save() {
			const that = this;
			that.$refs.form_data.validate((valid) => {
				if (!valid) return that.$message.warning('请完整填写内容!');
 
				var formData = that.form_data;  
				let option = that.tableData
				if(!that.id){
				   option = localStorage.getItem('tableData') ? JSON.parse(localStorage.getItem('tableData')) : []
			  
				}
				if(!option.length){
					return that.$message.error('选项内容不能为空');
				}

				formData.option = option
				var url = '/evaluation_capability/save';
				if (that.id) {
					formData.evaluation_capability_id = that.id; 
				}

				that.axios.post(url, formData, {
					emulateJSON: true
				}).then(
					function (data) {
						that.$message.success(that.id ? '修改成功' : '添加成功');
						localStorage.setItem('tableData','')
						that.$router.go(-1)
					}).catch(err => {
					that.$message.error(err);
				});

			})
		},

		  
		/**
		 * 返回
		 */
		back() {
			this.$router.go(-1);
		}
	}
}