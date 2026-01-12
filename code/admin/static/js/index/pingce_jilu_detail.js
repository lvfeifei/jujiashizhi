export default {
    data() {
        return { 
          order_id:'',
          user_id:'',
          detail_data: {} ,
          order_detail_data:{},
          question_data:[],
          program_data:[],  // 照护方案详情
          research_family:{}, // 关爱调查数据
          scenes:{},  // 场景语音
          is_show_bead_house:true
        }
    },

    //进入页面加载
    mounted: function() {

      let arr = document.cookie.split('; ');
      for(let i=0;i<arr.length;i++){
        let index = arr[i].indexOf("=") //返回第一个“=”所在的位置 
        if(arr[i].substring(0,index)=="role_id"){ 
          this.is_show_bead_house = arr[i].split('=')[1] == 11 ? false : true 
        }
      } 

        var that = this; 
        if(that.$route.query.order_id) {
            that.order_id = that.$route.query.order_id; 
            that.user_id = that.$route.query.user_id; 
            that.detail(that.user_id)
            that.order_detail(that.order_id)
            that.get_question(that.order_id) 
            // 获取关爱调查 态度
            that.get_research_family(that.order_id)

            // 获取照护方案
            that.get_program_details(that.order_id)
        }
    },
 
    methods: {

      // 导出数据分析文件
		export_file(){
      let url =  this.adminApi.api_url + '/export/export_excel?id=' + this.order_id
			const el = document.createElement('a');
			el.style.display = 'none';
			el.setAttribute('target', '_blank'); 
			el.setAttribute('download', 'analyze_file');
			el.href = url;
			console.log(el);
			document.body.appendChild(el);
			el.click();
			document.body.removeChild(el);
		},


      // 下载音频
      download_file(){
        // console.log(this.order_id)
        let url =  this.adminApi.api_url + '/export/export_scenes?id=' + this.order_id
        const el = document.createElement('a');
        el.style.display = 'none';
        el.setAttribute('target', '_blank'); 
        el.setAttribute('download', 'audio_file');
        el.href = url;
        console.log(el);
        document.body.appendChild(el);
        el.click();
        document.body.removeChild(el);

      },
       // 获取关爱调查 态度
       get_research_family(id){
        var that = this;
        //请求登陆接口
        that.axios.post("/order/research_family", {
          token: that.token,  id,
        }, {
          emulateJSON: true
        }).then(
          function (res) {  
            that.research_family = res.data;
            that.scenes = that.research_family.scenes
          },
          function () {
            // 处理失败的结果
            that.$message({
              type: 'error',
              message: `操作提示: ${ '处理异常' }`
            });
          });
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
            // console.log(res.data)
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

      // 跳转修改记录页面
      go_edit(){
        this.$router.push({
          path: '/index/edit_jilu',
          query: {
            id:this.order_id
          }
        });
      },

      // 重新请求数据
      request_on(){
        var that = this;
        //请求登陆接口
        that.axios.post("/order/send_program", {
          id:that.order_id,
        }, {
          emulateJSON: true
        }).then(
          function (res) {  
            console.log(res)
            if(!res.status){
              that.$message({
                type: 'error',
                message:res.msg
              });
            }else{
              that.order_detail(that.order_id);
              // 获取照护方案
              that.get_program_details(that.order_id)
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

      //查询详情
      detail: function (id) {
        var that = this;
        //请求登陆接口
        that.axios.post("/user/details", {
          token: that.token,  id,
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
              message: `操作提示: ${ '处理异常' }`
            });
          });
      },


       //查询详情
       order_detail: function (id) {
        var that = this;
        //请求登陆接口
        that.axios.post("/order/details", {
          token: that.token,  id,
        }, {
          emulateJSON: true
        }).then(
          function (res) {   
            that.order_detail_data = res.data;
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
