export default {
  data() {
    return {
      tableData: [],
      count: 0,
      page: 1,
      limit: 10,
    }
  },
  //进入页面加载
  mounted: function () {
    var that = this;

    that.getList();
  },

  //方法
  methods: {
    /**
     * 保存页数
     */
    save_page() {
        var that = this;
        // 记录当前页
        sessionStorage.setItem('curr_page', that.page)
    },

    /**
     * 获取列表
     */
    getList() {
      let that = this;

      let formData = {};
      formData.token = that.token;
      formData.id = that.user_id;

      var curr_page = sessionStorage.getItem('curr_page');
      that.page = curr_page ? curr_page : that.page;

      formData.page = that.page;
      formData.limit = that.limit;

      //请求邀请者列表
      that.axios.post("/Manager/index", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res;
          if (data) {
            that.tableData = data.list;
            that.count = data.count;

            that.page = curr_page ? curr_page - 0 : that.page;
            curr_page ? sessionStorage.removeItem('curr_page') : '';
          }
        });
    },

    /**
     * 添加
     */
    addRegion: function () {
      let that = this;
      that.save_page()
      
      that.$router.push({
        path: '/set_up/addStaff'
      });
    },


    /**
     * 修改
     * @param id
     */
    edit: function (id) {
      let that = this;
      that.save_page()
      
      that.$router.push({
        path: '/set_up/addStaff',
        query: {
          id: id
        }
      });
    },


    /**
     * 删除
     * @param e
     */
    deleteRow: function (e) {
      var that = this;
      that.$confirm('此操作将永久删除该 员工, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.doDelete(e);
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        });
      });
    },

    //执行删除
    doDelete: function (e) {
      var that = this;
      //请求登陆接口
      that.axios.post("/Manager/del", {
        token: that.token,
        id: e
      }, {
        emulateJSON: true
      }).then(
        function (res) {
          // 处理成功的结果
          that.$message({
            type: 'success',
            message: `操作提示: ${ '删除成功' }`
          });
          that.getList();
        });
    },


    /**
     * 下一页
     * @param currentPage
     */
    handleCurrentChange: function (currentPage) {
      var that = this;
      that.page = currentPage;
      that.getList();
    },
  }
}
