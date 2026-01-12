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
    
    that.getOptionData();

    if (that.$route.query.role_id) {
      that.role_id = that.$route.query.role_id;
      that.getDetail();
    }
  },

  //方法
  methods: {
    /**
     * 获取列表
     */
    getOptionData() {
      let that = this;

      // that.axios.post("/Role/getAllMenu", {
        that.axios.post("/Role/can_use_menu", {
        token: that.token
      }, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res.data;
          if (data) {
            that.data2 = data;
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
      }, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res.data;
          if (data) {
            that.ruleForm.name = data.name;
            that.ruleForm.sort = data.sort;
            that.ruleForm.status = data.status.toString();
            that.default_select = data.menu_id; 
            that.menu_ids = data.menu_id;
          }
        });
    },

    /**
     * 属性控件
     */
    handleNodeClick(e, data) {
      // console.log(e,data)
      this.menu_ids = data.checkedKeys
      // if(!this.menu_ids.findIndex(item => data.halfCheckedKeys[0])){
      //   this.menu_ids.push(data.halfCheckedKeys[0])
      // } 
      if(data.halfCheckedKeys){
        this.menu_ids = data.checkedKeys.concat(data.halfCheckedKeys);
      }
    },

    /**
     * 保存
     */
    save(formName) {
      let that = this;

      this.$refs[formName].validate((valid) => {
        if (valid) {
          var formData = {
            token: that.token,
            name: that.ruleForm.name,
            sort: that.ruleForm.sort,
            status: that.ruleForm.status,
            menu_ids: that.menu_ids,
          }

          if (that.role_id) {
            formData.role_id = that.role_id;
          }
 
          var url = that.role_id ? '/Role/edit' : "/Role/add";
          that.axios.post(url, formData, {
            emulateJSON: true
          }).then(
            function (res) {
              that.$message.success('添加成功');
              that.$router.push('/set_up/role_list')
            }).catch(res => {
            that.$message.warning(res);
          });
        }
      });


    },



  }
}
