export default {
  data() {
    return {
      tableData: [],
      count: 0,
      page: 1,
      limit: 10,
      keys: '',
      type: '',
      
      goods_title: '',
      goods_id: '',
    }
  },
  //进入页面加载
  mounted: function () {
    var that = this;
    
    var query = that.$route.query;

    that.goods_id = query.id;
    that.goods_title = query.title;

    that.getList();
  },

  //方法
  methods: {

    /**
     * 获取列表
     */
    getList() {
      let that = this;
      let formData = {};

      formData.page = that.page;
      formData.type = that.type;
      formData.token = that.token;
      formData.limit = that.limit;
      formData.key = that.keys;
      formData.goods_id = that.goods_id;

      that.axios.post("/evaluate/index", formData, {
        emulateJSON: true
      }).then(res => {
        var data = res;
        if (data) {

          that.tableData = data.list;
          that.count = data.count;
        }
      });
    },

    /**
     * 搜索
     */
    search() {
      const that = this;
      that.page = 1;
      that.getList();
    },

    /**
     * 查看 审核
     */
    to_detail(e) {
      var that = this;
      that.$router.push({
        path: '/nav1/comment_detail',
        query: {
          id: e,
          goods_title: that.goods_title,
          goods_id: that.goods_id,
        }
      })
    },

    /**
     * 删除
     */
    del_ele: function (e) {
      var that = this;

      that.$confirm('此操作将永久删除该项, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.axios.post("/evaluate/del_evaluate", {
          token: that.token,
          id: e
        }, {
          emulateJSON: true
        }).then(
          function (res) {
            that.$message.success('删除成功');
            that.getList();
          })
      }).catch(res => {});
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
