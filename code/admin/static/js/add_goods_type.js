import commJS from './common.js';
export default {
  data() {
    return {
      // 表单
      form_data: {
        name: '',
        picture: [],
        sort: '',
        url: '',
        status: '1',
      },

      // 七牛云信息
      upload_img_url: this.adminApi.upload_url,
      postData: {},
      domain: '',

      type_id: ''
    }
  },

  /**
   * 进入页面加载
   */
  mounted: function () {
    var that = this;
    
    commJS.getQiNiuToken(that);

    var query = that.$route.query;
    if (query.type_id) {
      that.type_id = query.type_id;
      that.getDetail();
    }
  },

  //方法
  methods: {
    /**
     * 获取详情
     */
    getDetail() {
      var that = this;

      that.axios.post("/classif/show_edit", {
        token: that.token,
        id: that.type_id
      }, {
        emulateJSON: true
      }).then(res => {
        var data = res;
        if (data) {
          that.form_data.name = data.name;
          that.form_data.picture = [{
            url: data.icon
          }];
          that.form_data.sort = data.sort;
          that.form_data.url = data.url;
          that.form_data.status = data.status.toString();
        }
      });
    },

    /**
     * 保存预览
     */
    save() {
      const that = this;
        var formData = {};

        formData.token = that.token;
        formData.name = that.form_data.name;
        formData.icon = that.form_data.picture[0].url;
        formData.sort = that.form_data.sort;
        formData.sort = that.form_data.sort;
        formData.status = that.form_data.status;

        var url = '/classif/add';
        if (that.type_id) {
          formData.id = that.type_id;
          url = '/classif/edit';
        }

        that.axios.post(url, formData, {
          emulateJSON: true
        }).then(() => {
          that.$message.success(that.type_id ? '修改成功' : '添加成功');
          that.$router.go(-1)
        });
    },

    /**
     * 图片超限制
     */
    descExceed: function (t, e) {
      this.$message.warning("只能上传一张图片哦!")
    },

    /**
     * 图片上传成功
     */
    img_succ(res) {
      const that = this;
      that.form_data.picture.push({
        url: that.domain + res.key,
      })
    },

    /**
     *图片移除
     */
    del_img(file, fileList) {
      this.form_data.picture = fileList
    },

    /**
     * 返回
     */
    back() {
      this.$router.go(-1);
    },
  }
}
