export default {
    data() {
        return {
          domain: 'https://shilaohua-1258884793.cos.ap-beijing.myqcloud.com/',
          detail_data: {
            order: {},
            order_region: {}
          },
          statusList: [{
            text: '待审核',
            name: '1'
          }, {
            text: '待评估',
            name: '2'
          }, {
            text: '已评估',
            name: '3'
          }, {
            text: '待施工',
            name: '4'
          }, {
            text: '已完成',
            name: '5'
          },{
            text: '取消审核',
            name: '6'
          },{
            text: '已取消',
            name: '7'
          },{
            text: '已评价',
            name: '8'
          },
          ],
        }
    },

    //进入页面加载
    mounted: function() {
        var that = this;

        if(that.$route.query.order_id) {
            that.order_id = that.$route.query.order_id;
            that.detail(that.order_id);
        }
    },

    methods: {
      //查询单个分类值
      detail: function (id) {
        var that = this;
        //请求登陆接口
        that.axios.post("/order/orderdetails", {
          token: that.token,
          order_id: id,
        }, {
          emulateJSON: true
        }).then(
          function (res) {
            that.statusList.map(item => {
              if (item.name == res.data.order.status){
                res.data.order.status =  item.text;
              }
            })
            that.detail_data = res.data;
          },
          function () {
            // 处理失败的结果
            that.$message({
              type: 'error',
              message: `操作提示: ${ '处理异常' }`
            });
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
