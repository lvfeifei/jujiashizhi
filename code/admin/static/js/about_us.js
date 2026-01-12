import richText from "../../src/page/common/richText";

export default {
  components: {
    richText
  },
  data() {
    return {
      //  表单
      form_data: {
        content: ''
      },

      rules: {
        content: [{
          required: true,
          message: '请填写内容',
          trigger: 'blur'
        }],
      },
    };
  },

  /**
   * 进入页面加载
   */
  mounted: function () {
    var that = this;
    
    that.getDetail();
  },

  //方法
  methods: {
    /**
     * 获取详情
     */
    getDetail() {
      var that = this;
      //请求的数据
      var formData = {};
      formData.token = that.token;
      //请求
      that.axios.post("/Config/getAboutUs", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          that.form_data.content = res.AboutUs;
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
     * 保存预览
     */
    save() {
      var that = this;
      //请求的数据
      var formData = {};
      formData.token = that.token;
      formData.AboutUs = that.form_data.content;
      //请求邀请者列表
      that.axios.post("/Config/addAboutUs", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          // 处理成功的结果
          that.$message({
            type: 'success',
            message: `操作提示: ${ '添加成功' }`
          });
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
     * 富文本改变时
     */
    editor_change(e) {
      this.form_data.content = e;
    },
  }
};
