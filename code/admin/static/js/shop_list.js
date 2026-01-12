export default {
  data() {
    return {
      activeName: 'first',
      tableData: [], //商品列表
      class_list: [],
      class_id: '',
      key: '',

      goods_name: '',
      create_time: [{
        text: '最新',
        type: 1,
      }, {
        text: '权重',
        type: 2,
      }],
      sort_type: '',
      count: 0,
      type: 0,
      label: [{
        text: '全部',
        name: 'first'
      }, {
        text: '出售中',
        name: 'second'
      }, {
        text: '已售罄',
        name: 'third'
      }, {
        text: '已下架',
        name: 'fourth'
      }],
      page: 1,
      limit: 10,
    }
  },

  //进入页面加载
  mounted: function () {
    var that = this;

    //获取菜单列表
    that.getGoodsList();
    that.gettypeList();
  },

  methods: {
    /**
     * 获取列表
     */
    gettypeList() {
      let that = this;

      let formData = {};
      formData.token = that.token;

      that.axios.post("/classif/index", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res;
          if (data) {
            that.typeList = data.list;
          }
        });
    },

    handleClick(tab) {
      var that = this;
      if (tab.name == 'first') {
        that.type = 0;
      }
      if (tab.name == 'second') {
        that.type = 1;
      }
      if (tab.name == 'third') {
        that.type = 3;
      }
      if (tab.name == 'fourth') {
        that.type = 2;
      }
      that.page = 1;
      that.getGoodsList();
    },

    /**
     * 保存页数
     */
    save_page() {
      const that = this;
      sessionStorage.setItem('curr_page', that.page)
    },

    /**
     * 评论列表
     */
    to_comm_list(e) {
      const that = this;
      that.save_page();

      that.$router.push({
        path: '/nav1/comment_list',
        query: {
          id: e.id,
          title: e.title,
        }
      })
    },

    //请求商品列表api
    getGoodsList: function () {
      var that = this;
      //初始化数据
      that.tableData = [];

      //请求的参数
      var formData = {};
      if (that.type != 0) {
        formData.type = that.type;
      }
      var curr_page = sessionStorage.getItem('curr_page');
      if (curr_page) {
        that.page = curr_page - 0;
      }

      formData.token = that.token;
      formData.page = that.page;
      formData.limit = that.limit;
      formData.key = that.key;

      if (that.class_id) {
        formData.class_id = that.class_id;
      }

      if (that.goods_name) {
        formData.name = that.goods_name;
      }
      if (that.sort_type) {
        if (that.sort_type === 1) {
          formData.order_by = that.sort_type;
        }
      }

      //请求登陆接口
      that.axios.post("/Goods/index", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          // 处理成功的结果
          if (res.list) {

            that.tableData = res.list;
            that.count = res.count;
            if (curr_page) {
              that.page = curr_page;
              sessionStorage.removeItem('curr_page');
            }

          } else {
            that.count = 0;
          }
        },
        function () {
          // 处理失败的结果
          that.$message({
            type: 'error',
            message: `操作提示: ${ '处理异常' }`
          });
        });
    },

    //跳转至发布商品
    toAdd: function () {
      const that = this;
      that.save_page();
      that.$router.push('/nav1/add_shop');
    },

    /**
     * 链接地址
     */
    see_link(id) {
      var that = this;
      that.$confirm('/pages/index/goods?goods_id=' + id, '链接地址', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'success'
      }).then(() => {}).catch();
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
        that.axios.post("/Goods/editStatus", {
          token: that.token,
          goods_id: e.id,
          status: e.status
        }, {
          emulateJSON: true
        }).then(
          function (res) {
            that.$message.success('删除成功');
            that.getGoodsList();
          })
      }).catch(res => {});
    },

    /**
     * 下架
     */
    lowershelf: function (e) {
      var that = this;

      that.$confirm('是否要' + (e.status == 1 ? '下架' : '上架') + '该商品', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.axios.post("/goods/Merchandise", {
          token: that.token,
          id: e.id,
          status: e.status
        }, {
          emulateJSON: true
        }).then(
          function (res) {
            that.$message.success('提示：' + (e.status == 1 ? '下架' : '上架') + '成功');
            that.getGoodsList();
          })
      }).catch(res => {});
    },

    //下一页
    handleCurrentChange: function (currentPage) {
      var that = this;
      that.page = currentPage;
      that.getGoodsList();
    },

    //跳转至发布商品
    toEdit: function (e) {
      const that = this;
      that.save_page();
      that.$router.push({
        path: '/nav1/edit',
        query: {
          goods_id: e
        }
      });
    },

    //筛选
    search: function () {
      var that = this;
      that.getGoodsList();
    }
  }
}
