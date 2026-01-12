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

      that.axios.post("/classif/index", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res;
          if (data) {

            that.tableData = data.list;
          }
        });
    },

    /**
     * 添加
     */
    add: function () {
      let that = this;
      commJS.save_page(that)

      that.$router.push({
        path: '/nav1/add_goods_type'
      });
    },

    /**
     * 修改
     */
    edit: function (id) {
      let that = this;
      commJS.save_page(that)

      that.$router.push({
        path: '/nav1/add_goods_type',
        query: {
          type_id: id,
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
        that.axios.post("/classif/del", {
          token: that.token,
          id: e
        }, {
          emulateJSON: true
        }).then(
          function (res) {
            that.$message({
              type: 'success',
              message: `操作提示: ${ '删除成功' }`
            });
            that.getList();
          })
      }).catch(res => {});
    },

    /**
     * 查看地址
     */
    see_url(id) {
      var that = this;

      that.$confirm('/pages/all/menu?class_id=' + id, '链接地址', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'none'
      }).then(() => {}).catch();
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
