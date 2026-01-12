<template>
  <div id="login">
      <div style="width:100%;height:135px;"></div>
    <div class="logo">
      <img src="../../static/login/logo.png" />
    </div>
    <p class="title">智护相随</p>
     
    <div id="form">
      <div class="tip" style="color:red;font-size:13px;margin:10px 0;">提示:请先确认VPN处于连接状态再登录 </div>
     
      <!-- 隐藏登录防止账号密码回显 -->
      <div style="opacity: 0;height: 1px;">
        <input  name="username" type="text"  placeholder="请输入您的用户名">
        <input  name="password" type="password"  placeholder="输入登录密码"> 
      </div>
      <div class="model">
        <img src="../../static/login/user.png" class="icon-user" />
        <el-input v-model="user_name"  autocomplete="off" v-on:input="checkNumber(user_name)" placeholder="账号" prefix-icon=" " clearable>
        </el-input>
      </div>
      <div class="model">
        <img src="../../static/login/password.png" class="icon-password" />
        <el-input v-model="passWord" autocomplete="off" v-on:input="checkPwd(passWord)" @keyup.enter.native="Login" type="password"
          placeholder="请输入密码" prefix-icon=" " clearable></el-input>
      </div>
       
      <el-button v-on:click="Login" type="primary">立即登录</el-button>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      user_name: "",
      passWord: ""
    };
  },
  methods: {
    //设置账号
    checkNumber: function(val) {
      this.user_name = val;
    },
    //设置密码
    checkPwd: function(val) {
      this.passWord = val;
    },
    //登陆
    Login: function() {
      var that = this;
      if (that.user_name == "") {
        that.$message({
          type: "error",
          message: `操作提示: ${"账号不能为空"}`
        });
        return;
      }
      if (that.passWord == "") {
        that.$message({
          type: "error",
          message: `操作提示: ${"请您输入密码"}`
        });
        return;
      }

      //请求登陆接口
      that.axios.post("/login/login",
          {
                username: that.user_name,
                password: that.passWord
          },
        )
        .then((data)=>{


           if(!data.status){
                return that.$message({
                    type: 'warning',
                    message: `操作提示: ${data.msg}`
                });
            }
  
            //设置到缓存
            that.cookie.set("token", data.token);
            that.cookie.set("user_name", data.user_name);
            that.cookie.set("user_id", data.user_id);
            that.cookie.set("role_id", data.role_id);

            that.$message({
                type: "success",
                message: `操作提示: ${"登陆成功"}`
            });

            //登录成功，把用户信息保存在sessionStorage中
            that.$router.push("/index");
        }).catch((res) => {
            // 处理失败的结果
            that.$message({
                type: "error",
                message: `操作提示: ${res}`
            });
        })
    }
  }
};
</script>

<style scoped>
@import "../../static/css/global.css";
/*引入公共样式*/
</style>
