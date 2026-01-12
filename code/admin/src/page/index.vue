<template>
  <div id="index">
    <el-container>
      <!--导航栏-->
      <el-menu  :unique-opened="true" default-active="1-4-1" class="el-menu-vertical-demo" :collapse="isCollapse" router>
        <el-menu-item index="1" class="logo" disabled>
          <img src="../../static/login/logo.png" />
          <span slot="title">智护相随</span>
        </el-menu-item>

        <el-submenu  v-for="(item,k) in menuList" :key="item.id" :index="''+ k +''">
          <div slot="title">
            <img :src="item.icon" class="icon" />
            <span slot="title" class="menu-title">{{ item.name }}</span>
          </div>
          <el-menu-item-group v-for="(v) in item.child" :key="v.id">
            <el-menu-item :index="v.url">{{ v.name }}</el-menu-item>
          </el-menu-item-group>
        </el-submenu>
      </el-menu>

      <!--主体-->
      <el-main>
        <el-col class="main_haed">
          <el-radio-group v-model="isCollapse">
            <el-radio-button :label="false">
              <img src="../../static/index/open.png" class="open_retract" />
            </el-radio-button>
            <el-radio-button :label="true">
              <img src="../../static/index/retract.png" class="open_retract" />
            </el-radio-button>
          </el-radio-group>
          <el-dropdown trigger="hover">
            <span class="el-dropdown-link userinfo-inner">
              {{ user_name }}
              <i class="el-icon-caret-bottom"></i>
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item>
                <el-button type="text" @click="dialogVisible = true">修改密码</el-button>
              </el-dropdown-item>
              <el-dropdown-item divided @click.native="logout">退出登陆</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </el-col>
        <section class="content-container">
          <el-breadcrumb separator="/">
            <el-breadcrumb-item v-if="oneMenuName">{{ oneMenuName }}</el-breadcrumb-item>
            <el-breadcrumb-item v-if="twoMenuName">{{ twoMenuName }}</el-breadcrumb-item>
            <el-breadcrumb-item v-if="threeMenuName">{{ threeMenuName }}</el-breadcrumb-item>
          </el-breadcrumb>
          <transition name="fade" mode="out-in">
            <router-view></router-view>
          </transition>
        </section>
      </el-main>
    </el-container>

    <!--修改密码-->
    <el-dialog title="修改密码" :visible.sync="dialogVisible" width="30%" :before-close="handleClose">
      <el-form :model="ruleForm2" status-icon :rules="rules2" ref="ruleForm2" label-width="100px" class="demo-ruleForm">
        <el-form-item label="原密码:" prop="OldPass">
          <el-input v-model.number="ruleForm2.OldPass" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="密码:" prop="pass">
          <el-input type="password" v-model="ruleForm2.pass" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="确认密码:" prop="checkPass">
          <el-input type="password" v-model="ruleForm2.checkPass" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm2')">提交</el-button>
          <el-button @click="resetForm('ruleForm2')">重置</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>
<script>
import Vue from 'vue';
export default {
  name: "index",
  data() {
    var validOldPass = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请输入原密码"));
      } else {
        if (this.ruleForm2.checkOldPass !== "") {
          this.$refs.ruleForm2.validateField("checkOldPass");
        }
        callback();
      }
    };
    var validatePass = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请输入密码"));
      } else {
        if (this.ruleForm2.checkPass !== "") {
          this.$refs.ruleForm2.validateField("checkPass");
        }
        callback();
      }
    };
    var validatePass2 = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请再次输入密码"));
      } else if (value !== this.ruleForm2.pass) {
        callback(new Error("两次输入密码不一致!"));
      } else {
        callback();
      }
    };
    return {
      isCollapse: false,
      user_name: "",
      user_id: "",
      token: "",
      menuList: [],
      oneMenuName: "",
      twoMenuName: "",
      threeMenuName: "",
      key: 0,
      ruleForm2: {
        pass: "",
        checkPass: "",
        OldPass: ""
      },
      rules2: {
        checkOldPass: [
          {
            validator: validOldPass,
            trigger: "blur"
          }
        ],
        pass: [
          {
            validator: validatePass,
            trigger: "blur"
          }
        ],
        checkPass: [
          {
            validator: validatePass2,
            trigger: "blur"
          }
        ]
      },
      dialogVisible: false,
      role_id: ""
    };
  },

  //进入页面加载
  mounted: function() {
    var that = this;
    //在缓存中获取值
    that.user_name = that.cookie.get("user_name");
    that.user_id = that.cookie.get("user_id");
    that.token = that.cookie.get("token");
    that.role_id = that.cookie.get("role_id");

    //获取菜单列表
    that.getMenuList();
  },

  //要执行的方法
  methods: {
    //退出登陆
    logout: function() {
      var _this = this;
      this.$confirm("确认退出吗?", "提示", {
        type: "warning"
      })
        .then(() => {
            _this.cookie.remove("token");
            _this.$router.push("/login");
        })
        .catch(() => {});
    },

    //请求菜单api
    getMenuList: function() {
      var that = this;
      //请求登陆接口
      that.axios
        .post("/System/show", {role_id: that.role_id}).then(
          function(res) {
            if (res.status == 1) {
              // 处理成功的结果
              for (var i in res.data) {
                res.data[i].img = res.data.icon;
              }
              that.menuList = res.data;
            }
          },
          function() {}
        );

    },

    //提交
    submitForm(formName) {
      var that = this;
      that.$refs[formName].validate(valid => {
        if (valid) {
          that.editPass();
          // alert('submit!');
        } else {
          return false;
        }
      });
    },
    //重置
    resetForm(formName) {
      this.$refs[formName].resetFields();
    },
    //关闭
    handleClose(done) {
      this.$confirm("确认关闭？")
        .then(_ => {
          done();
          this.ruleForm2 = {
            pass: "",
            checkPass: "",
            checkOldPass: ""
          };
        })
        .catch(_ => {});
    },

    //修改密码api
    editPass: function() {
      var that = this;

      // 正则验证
      let reg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[^]{0,20}$/;
      if(!reg.test(that.ruleForm2.pass)){
        return that.$message({
          type: 'error',
          message: '密码需要同时包含英文大小写和数字'
        });
      }

      that.axios
        .post("/Manager/edit_pass_word",{
            id: that.user_id,
            token: that.token,
            old_password: that.ruleForm2.OldPass,
            newPassword: that.ruleForm2.pass,
            newConfirmPassword: that.ruleForm2.checkPass
          },{emulateJSON: true}
        )
        .then(
          function(res) {
            if(res.status){
               // 处理成功的结果
              that.$message({
                type: "success",
                message: `操作提示: ${"修改成功"}`
              });
              that.ruleForm2 = {
                pass: "",
                checkPass: "",
                checkOldPass: ""
              };
              that.dialogVisible = false;
            }else{
                that.$message({
                type: "error",
                message: `操作提示: ${res.msg}`
              });
            }

          },
          function(res) {
            // 处理失败的结果
            that.$message({
              type: "error",
              message: `操作提示: ${res.msg}`
            });
          }
        );
    }
  }
};
</script>

<style>
@import "../../static/css/global.css";
@import "../../static/css/all.css";
@import "../../static/css/global_all.css";
@import "../../static/css/index.css";
/* @import "../../static/css/shop_global.css"; */
/*引入公共样式*/
</style>
