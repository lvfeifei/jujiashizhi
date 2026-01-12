import richText from "../../../src/page/common/richText";

export default {
  components: {
    richText
  },
  data() {
    return {
      // 表单
      form_data: {
        title:'',
        content:'',
        class_id:'',
        picture: [],
        describe: [],
        price: null,
      },
      rules: {
        title: [{
          required: true,
          message: '请填写助具名称',
          trigger: 'blur'
        }],
        content: [{
          required: true,
          message: '请填写助具简介',
          trigger: 'blur'
        }],
        class_id: [{
          required: true,
          message: '请选择关联类目',
          trigger: 'blur'
        }],
        price: [{
          required: true,
          message: '请填写助具单价',
          trigger: 'blur'
        }],
        describe: [{
          required: true,
          message: '请填写助具描述',
          trigger: 'blur'
        }],
        /*picture: [{
          required: true,
          message: '请上传助具图片',
          trigger: 'blur'
        }],*/
      },
      selectedClass: '',
      classData: [],
      dialogVisible: false, //添加规格弹窗
      dialogVisible2: false, //修改规格弹窗
      class_name: '',
      class_price: '',
      class_model_url: '',
      class_stock: '',
      cost: '',
      class_table: [],
      class_table_index: 0,
      goods_desc: '',
      dialogImageUrl: '', //轮播图
      banner: [],
      goods_banner: false,
      postData: {},
      descImageUrl: '', //商品简介图片
      desc_img: false,
      desc: '',
      domain: '', //图片域名
      goods_production: '', //产地

      // 商品分类
      goods_type_list: [],
      class_id: '',
      // 市场价
      market_price: '',
      status: '1',

      // 七牛云信息
      upload_img_url: this.adminApi.upload_url,
      picture:[]
    };
  },

  //进入页面加载
  mounted: function () {
    var that = this;

    that.getClassList();
    var query = that.$route.query;
    if (query.banner_id) {
      that.banner_id = query.banner_id;
      that.getDetail();
    }
    // that.getQiNiuToken();
    // that.get_goods_type();
  },
  methods: {
    /**
     * 获取详情
     */
    getDetail() {
      var that = this;

      that.axios.post("/goods/goods_show_edit", {
        token: that.token,
        id: that.banner_id
      }, {
        emulateJSON: true
      }).then(res => {
        var data = res.data;
        if (data) {
          that.form_data.class_id = data.class_id;
          that.form_data.content = data.content;
          that.form_data.describe = data.describe;
          that.form_data.price = data.price;
          that.form_data.title = data.title;
          that.class_table = data.spec_info;
        }
      });
    },

    getClassList() {
      let that = this;
      let formData = {};
      formData.token = that.token;
      formData.page = that.page;
      formData.limit = 10000;
      that.axios.post("/classif/index", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res.data;
          if (data) {
            that.classData = data.list;
          }
        });
    },

    save() {
      const that = this;
      that.$refs.form_data.validate((valid) => {
        if (!valid) return that.$message.warning('请完整填写内容!');
        var formData = {};
        console.log(that.checkList);

        formData.token = that.token;
        formData.title = that.form_data.title;
        formData.price = that.form_data.price;
        formData.content = that.form_data.content;
        formData.describe = that.form_data.describe;
        formData.class_id = that.form_data.class_id;
        formData.picture = that.form_data.picture.toString();
        formData.goods_spec = that.class_table;

        var url = '/goods/goodsadd';
        if (that.banner_id) {
          formData.goods_id = that.banner_id;
          url = '/goods/goodsedit';
        }

        console.log("formData");
        console.log(formData);
        that.axios.post(url, formData, {
          emulateJSON: true
        }).then(() => {
          that.$message.success(that.banner_id ? '修改成功' : '添加成功');
          that.$router.go(-1)
        });
      })
    },

    /**
     * 返回
     */
    back() {
      this.$router.go(-1);
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
     * 分类列表
     */
    get_goods_type() {
      let that = this;

      let formData = {};
      formData.token = that.token;

      that.axios.post("/classif/index", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          var data = res;
          if (data) {

            that.goods_type_list = data.list;
          }
        });
    },

    //上传轮播
    handleRemove(file, fileList) {
    },

    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.goods_banner = true;
    },

    //显示错误
    handleError(res) {
    },

    //上传成功后在图片框显示图片
    handleAvatarSuccess(res, file) {
      var that = this;
      var bannerUrl = that.domain + res.key
      that.banner.push(bannerUrl);
    },

    //文件超出个数限制时的钩子
    handleExceed(files, fileList) {
      this.$message.error('最多上传10张图片');
    },

    //获取七牛云token
    getQiNiuToken: function () {
      var that = this;
      //请求登陆接口
      that.axios.post("/Qiniu/getToken", {
        token: that.token,
      }, {
        emulateJSON: true
      }).then(
        function (res) {
          // 处理成功的结果
          that.postData = {
            token: res.upToken,
          }
          that.domain = res.domain;
        },
        function () {
          // 处理失败的结果
          that.$message({
            type: 'warning',
            message: `操作提示: ${ '处理异常' }`
          });
        });
    },


    //添加规格弹窗
    confirm() {
      var that = this;
      that.dialogVisible = false;
      that.class_table.push({
        name: that.class_name,
        price: that.class_price,
        model_url: that.class_model_url,
        picture: that.class_stock,
        cost_price: that.cost
      });
      that.class_name = '';
      that.class_price = '';
      that.class_stock = '';
      that.class_model_url = '';
      that.cost = '';
    },

    //移除规格
    deleteRow(index, rows) {
      rows.splice(index, 1);
    },

    //修改规格
    edit: function (indx) {
      var that = this;
      that.class_table_index = indx;
      that.dialogVisible2 = true;
      that.class_name = that.class_table[indx].name;
      that.class_price = that.class_table[indx].price;
      that.class_model_url = that.class_table[indx].model_url;
      that.class_stock = that.class_table[indx].picture;
      that.cost = that.class_table[indx].cost_price;
    },

    confirm2() {
      var that = this;
      that.dialogVisible2 = false;
      that.class_table[that.class_table_index].name = that.class_name;
      that.class_table[that.class_table_index].price = that.class_price;
      that.class_table[that.class_table_index].picture = that.class_stock;
      that.class_table[that.class_table_index].cost_price = that.cost;

      // 清空值
      that.class_name = '';
      that.class_price = '';
      that.class_stock = '';
      that.class_model_url = '';
      that.cost = '';
    },

    //添加商品
    addData: function () {
      var that = this;
      if (!that.goods_name) {
        that.$message({
          type: 'warning',
          message: `操作提示: ${ '请输入商品名称!' }`
        });
        return false;
      }
      if (!that.place_origin) {
        that.$message({
          type: 'warning',
          message: `操作提示: ${ '请输入商品产地!' }`
        });
        return false;
      }

      if ((that.class_table === '') || (that.class_table.length === 0)) {
        that.$message({
          type: 'warning',
          message: `操作提示: ${ '至少添加一个规格!' }`
        });
        return false;
      }
      if ((that.banner === '') || (that.banner.length === 0)) {
        that.$message({
          type: 'warning',
          message: `操作提示: ${ '至少添加一张轮播图!' }`
        });
        return false;
      }
      if ((that.desc === '') || (that.desc.length === 0)) {
        that.$message({
          type: 'warning',
          message: `操作提示: ${ '至少添加一张详情图!' }`
        });
        return false;
      }

      if (!that.class_id) {
          that.$message.warning('请选择商品分类!');
        return false;
      }

      if (!that.market_price) {
          that.$message.warning('请填写市场价!');
        return false;
      }

      var formData = {};
      formData.title = that.goods_name;
      formData.place_origin = that.place_origin;
      formData.market_price = that.market_price;
      formData.token = that.token;
      formData.class_id = that.class_id;
      formData.goods_spec = that.class_table;
      formData.goods_picture = that.banner;
      formData.goods_imgs = that.desc;
      formData.picture = that.banner[0];
      formData.sort = that.goods_sort;
      formData.status = that.status;

      that.axios.post("/Goods/addGoods", formData, {
        emulateJSON: true
      }).then(
        function (res) {
          // 处理成功的结果
          that.$message({
            type: 'success',
            message: `操作提示: ${ '发布成功' }`
          });
          that.$router.push('/nav1/shop_list');
        },
        function () {
          // 处理失败的结果
          that.$message({
            type: 'warning',
            message: `操作提示: ${ '处理异常' }`
          });
        });
    },

    //放弃编辑
    cancel: function () {
      var that = this;
      that.$router.push('/nav1/shop_list');
    },

    //关闭规格弹窗
    close_table: function () {
      var that = this;
      that.dialogVisible = false;
      that.dialogVisible2 = false;
    },
    // 富文本内容输入
    editor_change(e){
      this.form_data.describe = e;
    },
  }
}
