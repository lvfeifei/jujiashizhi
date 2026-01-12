import commJS from './common.js';

export default {
  data() {
    return {
      // 相关推荐
      recommend_list: [],
      // 暂存数组
      temp_Recommend: [],
      page: 1,
      limit: 10,
      count: '',
      type: '',
      keys: '',

      //  表单
      formData: {
        name: '',
        picture: [],
        url: '',
        sort: '',
        // 相关推荐
        recommend_list: [],
        status: '1'
      },

      rules: {
        name: [{
          required: true,
          message: '请输入标签名称',
          trigger: 'blur',
        }],
        picture: [{
          required: true,
          message: '请上传图片',
          trigger: 'blur',
          type: 'array',
          min: 1,
        }],
        recommend_list: [{
          required: true,
          message: '请添加相关推荐',
          trigger: 'blur',
          type: 'array',
          min: 1,
        }],
      },

      // 弹窗
      dialogVisible: false,
      loading: false,

      // 七牛云地址
      upload_img_url: this.adminApi.upload_url,
      postData: {},
      //图片域名
      domain: '',

      // 分类列表
      goods_classify: [],
      floor_id: ''

    }
  },

  /**
   * 进入页面加载
   */
  mounted: function () {
    var that = this;
    
    commJS.getQiNiuToken(that);
    that.getGoodsList();
    that.get_goods_type_list();

    var query = that.$route.query;
    that.floor_id = query.floor_id;

    if (that.floor_id) {
      that.getDetail();
    }
  },

  //方法
  methods: {

    /**
     * 推荐商品列表
     */
    getGoodsList() {
      var that = this;

      that.axios.post("/lable/label_goods", {
        token: that.token,
        class: that.type,
        key: that.keys,
        page: that.page,
        limit: that.limit,
      }, {
        emulateJSON: true
      }).then(res => {
        var data = res;
        if (data) {
          that.recommend_list = data.list;
          that.count = data.count;
        }
      });
    },

    /**
     * 分类列表
     */
    get_goods_type_list() {
      var that = this;

      that.axios.post("/classif/index", {
        token: that.token,
        class: that.type,
        key: that.keys,
      }, {
        emulateJSON: true
      }).then(res => {
        var data = res;
        if (data) {
          that.goods_classify = data.list;
        }
      });
    },

    /**
     * 获取详情
     */
    getDetail() {
      var that = this;

      that.axios.post("/lable/edit", {
        token: that.token,
        id: that.floor_id,
      }, {
        emulateJSON: true
      }).then(res => {
        var data = res;
        if (data) {

          that.formData.name = data.name;
          that.formData.picture = [{
            url: data.picture
          }];
          that.formData.url = data.url;
          that.formData.sort = data.sort;
          that.formData.recommend_list = data.label_goods;
          that.formData.status = data.status.toString();
        }
      });
    },

    /**
     * 保存预览
     */
    save() {
      const that = this;

      that.$refs.formData.validate((valid) => {
        if (!valid) return that.$message.warning('请完整填写内容!');
        that.loading = true;

        var formData = {};


        formData.name = that.formData.name;
        formData.picture = that.formData.picture[0].url;
        formData.url = that.formData.url;
        formData.sort = that.formData.sort;

        var arr = [];
        that.formData.recommend_list.forEach(ele => {
          arr.push(ele.id);
        })
        formData.goods_id = arr;
        formData.status = that.formData.status;

        var url = '/lable/create';
        if (that.floor_id) {
          formData.id = that.floor_id;
          url = '/lable/update';
        }

        that.axios.post(url, formData, {
          emulateJSON: true
        }).then((res) => {
          var data = res;
          that.loading = false;
          that.$message.success(that.floor_id ? '修改成功' : '添加成功');
          that.$router.go(-1);
        }).catch(res => {
          that.loading = false;
        });
      })
    },

    /**
     * 图片上传成功
     */
    img_succ(res) {
      const that = this;
      that.formData.picture.push({
        url: that.domain + res.key,
      })
    },

    /**
     *图片移除
     */
    del_img(file, fileList) {
      this.formData.picture = fileList
    },

    /**
     * 文件超出个数限制时的钩子
     */
    descExceed(files, fileList) {
      this.$message.warning('只能上传一张图片哦!');
    },

    /**
     * 筛选相关推荐
     */
    search_recommend() {
      var that = this;
      that.getGoodsList();
    },

    /**
     * 返回
     */
    back() {
      this.$router.go(-1);
    },

    /**
     * 显示对话框
     */
    show_dialog() {
      const that = this;
      that.dialogVisible = true;
    },

    /**
     * type改变
     */
    recommend_type_chane(e) {
      const that = this;
      that.getGoodsList();
    },

    /**
     * 表格选中改变
     */
    handleSelectionChange(e) {
      const that = this;
      that.temp_Recommend = e;
    },

    /**
     * 确定选中
     */
    Confirm_selection() {
      const that = this;
      if (!that.temp_Recommend.length) return that.$message.warning('请勾选内容!');

      var flag = false;
      that.temp_Recommend.forEach(ele => {
        that.formData.recommend_list.forEach(item => {
          if (ele.id == item.id) {
            flag = true;
            return;
          }
        })
      })

      if (flag) {
        return that.$message.warning('请勿重复添加内容!');
      } else {
        that.temp_Recommend.forEach(ele => {
          that.formData.recommend_list.push(ele);
        })
      }

      that.dialogVisible = false;
      that.$message({
        type: 'success',
        message: '关联成功!'
      });
    },

    /**
     * 删除相关推荐
     */
    del_recommend(e) {
      const that = this;
      this.$confirm('确认删除此项吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.formData.recommend_list.splice(e, 1)
        this.$message({
          type: 'success',
          message: '删除成功!'
        });
      }).catch(() => {});
    },

    /**
     * 删除老师
     */
    del_teacher(e, id) {
      var that = this;

      this.$confirm('确认删除此项吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.select_teacher.splice(e, 1);
        that.formData.adviser_ids.splice(e, 1);
        this.$message({
          type: 'success',
          message: '删除成功!'
        });
      }).catch(() => {});
    },

    /**
     * 推荐老师
     */
    adviser_change(e) {
      var that = this;
      that.select_teacher = [];

      e.forEach(item => {
        that.teacher_list.forEach(ele => {
          if (item == ele.id) {
            that.select_teacher.push(ele)
          }
        })
      })
    },

    /**
     * 下一页
     */
    handleCurrentChange: function (currentPage) {
      var that = this;
      that.page = currentPage;
      commJS.get_Recommend(that);
    },
  }
}
