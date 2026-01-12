export default {
  data() {
    return {
      // 评论详情
      detail: {},
      radio: "1",
      reason: '',

      id: '',
      title: '',
      g_id: '',
    }
  },

  /**
   * 进入页面加载
   */                                                                                                                     
  mounted: function () {
    var that = this;
    

    var query = that.$route.query;
    that.id = query.id;
    that.title = query.goods_title;
    that.g_id = query.goods_id;

    that.getDetail();
  },

  //方法
  methods: {
    /**
     * 获取详情
     */
    getDetail() {
      var that = this;

      that.axios.post("/evaluate/read", {
        id: that.id,    
        token: that.token,
      }).then(res => {
        var data = res;
        if (data) {
          that.detail = data;
        }
      });
    },

    /**
     * 返回
     */
    back() {
      var that = this;
      that.$router.push({
        path: '/nav1/comment_list',
        query: {
          id: that.g_id,
          title: that.title,
        }
      })
    },

    /**
     * 处理
     */
    save() {
      var that = this;
      var fromData = {};
      fromData.token = that.token;
      fromData.id = that.id;

      var url = "/evaluate/update_yes";
      if (that.radio == 2) {
        url = "/evaluate/update_no";
        fromData.reason = that.reason;
      }

      that.axios.post(url, fromData).then(data => {
        if (data) {
            that.$router.push({
              path: '/nav1/comment_list',
              query: {
                id: that.g_id,
                title: that.title,
              }
            })
        }
      });
    }
  }
}
