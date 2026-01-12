import commJS from './common.js';
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
     * 获取列表
     */
    getList() {
      let that = this;

      let formData = {};
      formData.token = that.token;
      formData.page = that.page;
      formData.limit = that.limit;

      that.axios.post("/lable/index", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res;
          if (data) {
            data.forEach(ele => {
              ele.status = ele.status == 1 ? '开启' : '关闭';
            })

            that.tableData = data;
            that.count = data.count;
          }
        });
    },

    /**
     * 添加
     */
    add: function () {
      let that = this;

      that.$router.push({
        path: '/nav1/add_goods_classify'
      });
    },

    /**
     * 修改
     */
    edit: function (id) {
      let that = this;

      that.$router.push({
        path: '/nav1/add_goods_classify',
        query: {
          floor_id: id,
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
        type: 'warning'
      }).then(() => {
        that.axios.post("/lable/delete", {
          token: that.token,
          id: e
        }, {
          emulateJSON: true
        }).then(
          function (res) {
            that.$message.success('删除成功');
            that.getList();

          }).catch(res => {
          that.$message.warning(res);
        });
      })
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
