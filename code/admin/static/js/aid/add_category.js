import commJS from '../common.js';
const cityOptions = ['上海', '北京', '广州', '深圳'];
export default {
  data() {
    return {
      // 表单
      form_data: {
        name:''
      },

      rules: {
        name: [{
          required: true,
          message: '请填写类目名称',
          trigger: 'blur'
        }],
      },

      // 七牛云信息
      upload_img_url: this.adminApi.upload_url,
      postData: {},
      domain: '',

      checkList: [],
      banner_id: '',
      labelData: [],
    }
  },

  /**
   * 进入页面加载
   */
  mounted: function () {
    var that = this;
    
    // commJS.getQiNiuToken(that);
    that.getLabelList();

    var query = that.$route.query;
    if (query.banner_id) {
      that.banner_id = query.banner_id;
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

      that.axios.post("/classif/classif_show_edit", {
        token: that.token,
        classif_id: that.banner_id
      }, {
        emulateJSON: true
      }).then(res => {
        var data = res.data;
        if (data) {
          that.form_data.name = data.name;
          data.label_id.map(ele => {
            that.checkList.push(ele.label_id);
          })
          console.log(that.checkList);
        }
      });
    },

    /**
     * 获取标签列表
     */
    getLabelList() {
      let that = this;

      let formData = {};
      formData.token = that.token;
      formData.page = that.page;
      formData.limit = 10000;
      that.axios.post("/label/index", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res.data;
          if (data) {
            that.labelData = data.list;
          }
        });
    },
    checkboxClick(value){
      console.log(value);
      console.log(this.checkList);
    },

    /**
     * 保存预览
     */
    save() {
      const that = this;
      that.$refs.form_data.validate((valid) => {
        if (!valid) return that.$message.warning('请完整填写内容!');
        var formData = {};
        console.log(that.checkList);

        formData.token = that.token;
        formData.name = that.form_data.name;
        formData.label_id = that.checkList;

        var url = '/classif/classifadd';
        if (that.banner_id) {
          formData.classif_id = that.banner_id;
          url = '/classif/classifedit';
        }

        that.axios.post(url, formData, {
          emulateJSON: true
        }).then(() => {
          that.$message.success(that.banner_id ? '修改成功' : '添加成功');
          that.$router.go(-1)
        });
      })
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
