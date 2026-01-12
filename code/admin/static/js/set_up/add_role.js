export default {
	data() {
		return {
			ruleForm: {
				name: '',
				sort: '',
				status: '1',
			},

			reles: {
				name: [{
					required: true,
					message: '请输入角色名称',
					trigger: 'blur'
				}],
				sort: [{
					required: true,
					message: '请输入角色名称',
					trigger: 'blur'
				}],
			},

			data2: [],
			defaultProps: {
				children: 'child',
				label: 'name'
			},

			menu_ids: '',

			// 默认选中
			default_select: [],
			role_id: ''
		}
	},
	//进入页面加载
	mounted: function () {
		var that = this;
		if (that.$route.query.role_id) {
			that.role_id = that.$route.query.role_id;
			that.getDetail();
    } else{
      that.getOptionData(); 
    }
   
	},

	//方法
	methods: {
		/**
		 * 获取列表
		 */
		getOptionData() {
			let that = this;
			that.axios.post("/Role/can_use_menu",{
        token: that.token
      }).then(res => {
        let data = res.data
				if (data) {
					that.data2 = data ;
					console.log(that.data2,123)
				}
			});
		},

		/**
		 * 获取详情
		 */
		getDetail() {
			let that = this;
			that.axios.post("/Role/show_edit", {
        token: that.token,
        role_id: that.role_id
      }).then(res => {
        let data = res.data
				if (data) {
					console.log(data,456)
					that.ruleForm.name = data.name;
					that.ruleForm.sort = data.sort;
          that.ruleForm.status = data.status.toString();
					that.default_select = data.menu_id; 
					that.menu_ids = data.menu_id;
				}
			}).then(res=>{
          that.getOptionData();
      });
		},

		/**
		 * 属性控件
		 */
		handleNodeClick(e, data) {
     	 this.menu_ids = data.checkedKeys;
    //   if(data.halfCheckedKeys){
    //       this.menu_ids = data.checkedKeys.concat(data.halfCheckedKeys);
    //   }
			//confirm(this.menu_ids);
		},

		/**
		 * 保存
		 */
		save(formName) {
			let that = this;

			this.$refs[formName].validate((valid) => {
				if (valid) {
					var formData = {
						name: that.ruleForm.name,
						sort: that.ruleForm.sort,
						status: that.ruleForm.status,
						menu_ids: that.menu_ids,
					}

					if (that.role_id) {
						formData.role_id = that.role_id;
					}

					var url = that.role_id ? '/Role/edit' : "/Role/add";
					that.axios.post(url, formData).then(res => {
						that.$message.success(res.msg);
						that.$router.push('/set_up/role_list')
					}).catch(res => {
						that.$message.warning(res.data);
					});
				}
			});


		},



	}
}
