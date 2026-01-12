export default {
  data() {
    return {
      staff_id: 0,
      formData: {
        username: '',
        password: '',
        name: '',
        phone_num: '',
        auth: 1
      },
      rules: {
        username: [{
          required: true,
          message: '请输入输手机号码',
          trigger: 'blur'
        }, ],
        name: [{
          required: true,
          message: '请输入姓名',
          trigger: 'blur'
        }, ],

        phone_num: [{
          required: true,
          message: '请输入手机号码',
          trigger: 'blur'
        }, ],

        // password: [{
        //   required: true,
        //   message: '请输入密码',
        //   trigger: 'blur'
        // }, ],

      },

      // 权限列表
      Role_list: []
    };
  },
  //进入页面加载
  mounted: function () {
    var that = this;

    that.get_Role_list();

    if (that.$route.query.id) {
      that.staff_id = that.$route.query.id;
      that.detail();
    }
  },

  methods: {

    /**
     * 获取数据
     */
    detail: function () {
      var that = this;
      //请求登陆接口
      that.axios.post("/Manager/show_edit", {
        token: that.token,
        id: that.staff_id,
      }, {
        emulateJSON: true
      }).then(

        function (res) {
           res = res.data
          that.formData.username = res.username;
          that.formData.phone_num = res.mobile;
          that.formData.name = res.truename;
          that.formData.auth = res.identity;

        });
    },

    /**
     * 保存
     */
    submitForm: function (formName) {
      const that = this;
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if (that.staff_id == 0) {
            that.add();
          } else {
            that.edit();
          }
        } else {
          return false;
        }
      });
    },

    /**
     * 添加员工
     */
    add: function () {
      const that = this;

      //请求的数据
      var formData = {};
      formData.token = that.token;
      formData.mobile = that.formData.phone_num;
      formData.username = that.formData.username;
      formData.truename = that.formData.name;
      formData.password = that.formData.password;
      formData.identity = that.formData.auth;

      if(formData.password){
           // 正则验证
           let reg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[^]{0,20}$/;
           if(!reg.test(formData.password)){
              return that.$message({
                type: 'error',
                message: '密码需要同时包含英文大小写和数字'
              });
           }
      }

      //请求api
      that.axios.post("/Manager/add", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          if(res.status === 1){
            // 处理成功的结果
            that.$message({
              type: 'success',
              message: `操作提示: ${ '添加成功' }`
            });
            that.$router.back(-1);
          }else{
            that.$message({
              type: 'error',
              message: res.msg
            });
          }

        });
    },

    /**
     * 权限列表
     */
    get_Role_list() {
      const that = this;

      that.axios.post("/Role/index", {
        token: that.token,
        page: 1,
        limit: 99999,
      }, {
        emulateJSON: true
      }).then(
        function (res) {
          that.Role_list = res.data.list;
        });
    },

    /**
     * 返回
     */
    back() {
      this.$router.go(-1);
    },

    /**
     * 修改员工
     */
    edit: function () {
      const that = this;

      //请求的数据
      var formData = {};
      formData.token = that.token;
      formData.mobile = that.formData.phone_num;
      formData.truename = that.formData.name;
      formData.username = that.formData.username;
      formData.password = that.formData.password;
      formData.identity = that.formData.auth;
      formData.id = that.staff_id;

      if(formData.password){
          // 正则验证
          let reg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[^]{0,20}$/;
          if(!reg.test(formData.password)){
            return that.$message({
              type: 'error',
              message: '密码需要同时包含英文大小写和数字'
            });
          }
      }

      //请求api
      that.axios.post("/Manager/edit", formData, {
        emulateJSON: true
      }).then(
        function (res) {

          if(res.status === 1){
            // 处理成功的结果
            that.$message({
              type: 'success',
              message: `操作提示: 修改成功`
            });
            that.$router.back(-1);
          }else{
            that.$message({
              type: 'error',
              message: res.msg
            });
          }

        });
    },

  }
}
