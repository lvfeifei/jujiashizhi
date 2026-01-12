export default {
    data() {
        return { 
          confirm_dialog:false,
          order_id:'',
          detail_data: {} ,
          question_data:[],
          dialogVisible:false, 
          type:'',
          form_data_dialog:{
            text: '',  
            picture:[]
          },
          // 七牛云信息
          upload_img_url: this.adminApi.upload_url,  
          upload_header:{
            token:this.cookie.get('token')
          },
          postData: {
            folder:"auther"
          },
          current_index:0,
          current_list:[],
          program_data:[],  // 照护方案详情
          // content:[
          //   {
          //     "type":'text',
          //     "con": '测试内容'
          //   },
          //   {
          //     "type":'text',
          //     "con": '测试内容'
          //   },
          //   {
          //     "type":'img',
          //     "con": 'https://jujiashizhi-1258884793.cos.ap-beijing.myqcloud.com/auther/8451659770174.jpg'
          //   },
          //   {
          //     "type":'text',
          //     "con": '测试内容'
          //   },
          //   {
          //     "type":'img',
          //     "con": 'https://jujiashizhi-1258884793.cos.ap-beijing.myqcloud.com/auther/8451659770174.jpg'
          //   },
          // ]
        }
    },

    //进入页面加载
    mounted: function() {
        var that = this; 
        if(that.$route.query.id) {
            that.id = that.$route.query.id; 
            that.detail(that.id);
            // 获取问题
            that.get_question(that.id)
            // 获取照护方案
            that.get_program_details(that.id)

        }
    },

    methods: {
 
      // 保存添加的内容
      confirm_add(){
        console.log(666666666)
        let {type,current_index,current_list} = this 
        console.log(type)
        let obj = {
          type
        }
        if(type == 'text'){
          obj.con = this.form_data_dialog.text
        }else if(type == 'image'){
          obj.con = this.form_data_dialog.picture[0].url
        }

        console.log(obj)

        current_list.splice(current_index+1,0,obj)
        this.close_dialog()
      },

      del_item(index,list){
        list.splice(index,1)
        // console.log(index,list)
      },

    /**
		 * 图片上传成功
		 */
    img_succ(file, fileList) {
      const that = this;
      // console.log(file);
      that.form_data_dialog.picture = [{
        name: 'image',
        url:   file.data.imgurl
      }]; 
    },


    // 打开弹窗
    open_dialog(type,index,current_list){   
      this.type = type
      this.current_index = index
      this.current_list = current_list
			this.dialogVisible = true
      this.form_data_dialog = {
        text: '',  
        picture:[]
      }
		},

    close_dialog(){
      this.type = ''
      this.dialogVisible = false
    },

      /**
		 * 图片超限制
		 */
		 descExceed: function (t, e) {
        this.$message.warning("每次只能上传一张图片哦!")
      },

    /**
		 *图片移除
		 */
		 del_img(file, fileList) {
			this.form_data_dialog.picture = fileList
		},
      /**
		 * 修改
		 */
		edit: function (row) {
      let that = this 
      if(row.my_id){
       that.current_item_my_id = row.my_id
      }else{
       that.current_item_id = row.id
      } 
      that.form_data_dialog = row
      that.form_data_dialog.picture = [{
       url: row.picture
     }];
     that.dialogVisible = true
 
   },

      // 清空选项
      clear_form_data_dialog(){
        this.form_data_dialog = {
          name: '', 
          sn:'',
          sort: '0', 
          type:2, 
          status: 1,
          picture:[]
        }
      },

      //查询详情
      detail: function (id) {
        var that = this;
        //请求登陆接口
        that.axios.post("/order/details", {
          token: that.token,  id,
        }, {
          emulateJSON: true
        }).then(
          function (res) {  
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

      

      // 确认发送
      confirm_send(){
        let that = this 
        that.$confirm('发送方案后，次日照护者就会收到照护方案通知, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => { 
          that.axios.post("/order/program_save", {
            id:that.id,
            program:that.program_data
        }, {
          emulateJSON: true
        }).then(
          function (res) { 
            if(res.status == 0){
              that.$message({
                type: 'error',
                message: res.msg
              });
            }else{
              // that.$router.push({
              //   path: '/index/pingce_jilu_list'
              // });
              that.back() 
            } 
          },
          function () {
            // 处理失败的结果
            that.$message({
              type: 'error',
              message: `操作提示: ${ '处理异常' }`
            });
          });
        }).catch();
         
      },

      // 获取照护方案
      get_program_details(id){
        var that = this;
        //请求登陆接口
        that.axios.post("/order/program_details", {
          token: that.token,  id,
        }, {
          emulateJSON: true
        }).then(
          function (res) { 
            console.log(res.data)
            that.program_data = res.data;
          },
          function () {
            // 处理失败的结果
            that.$message({
              type: 'error',
              message: `操作提示: ${ '处理异常' }`
            });
          });
      },

      // 查询选中的问题
      get_question(id){
        var that = this;
        //请求登陆接口
        that.axios.post("/order/evaluationdetails", {
          token: that.token,  id,
        }, {
          emulateJSON: true
        }).then(
          function (res) { 
            // console.log(res.data)
            that.question_data = res.data;
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
