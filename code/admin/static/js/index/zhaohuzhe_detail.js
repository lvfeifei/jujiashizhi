import commJS from '../common.js';
export default {
  data() {
    return {
      detail_data: {},
      tableData: [],
      count: 0,
      page: 1,
      limit: 10,
    }
  },

  //进入页面加载
  mounted: function () {
    var that = this;
    if (that.$route.query.order_id) {
      that.order_id = that.$route.query.order_id;
      that.detail(that.order_id);
      that.getList()
    }
  },

  methods: {

    /**
   * 下一页
   */
    handleCurrentChange(currentPage) {
      var that = this;
      that.page = currentPage;
      that.getList();
    },

    /**
   * 获取列表
   */
    getList() {
      let that = this;
      let formData = {};
      formData.page = that.page;
      formData.limit = that.limit;
      formData.id = that.order_id;
      that.axios.post("/user/patient_list", formData, {
        emulateJSON: true,
      }).then(
        function (res) {
          let data = res.data
          if (data) {
            // console.log(data.list)
            that.tableData = data.list;
            that.count = data.count;
            that.page = that.page;
          }
        }).catch(err => { that.$message.error(err); });
    },

    //查询详情
    detail: function (id) {
      var that = this;
      //请求登陆接口
      that.axios.post("/user/details", {
        token: that.token, id,
      }, {
        emulateJSON: true
      }).then(
        function (res) {
          that.detail_data = res;
        },
        function () {
          // 处理失败的结果
          that.$message({
            type: 'error',
            message: `操作提示: ${'处理异常'}`
          });
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
          order_id: row.id,
          user_id: row.user_id
        }
      });
    },


    /**
 * 返回
 */
    back() {
      this.$router.go(-1);
    },
  }
}
