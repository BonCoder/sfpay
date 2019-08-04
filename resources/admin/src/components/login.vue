<template>
  <i-form>
    <img src="../assets/logo.png" alt srcset />
    <h2>欢迎使用申孚通后台管理</h2>
    <Form-item label="账号">
      <i-input placeholder="请输入账号" v-model="ac"></i-input>
    </Form-item>
    <Form-item label="密码">
      <i-input type="password" v-model="psw" placeholder="请输入密码"></i-input>
    </Form-item>
    <Form-item>
      <a href="/">忘记密码？</a>
      <!-- <a href="/">注册</a> -->
    </Form-item>
    <Form-item>
      <i-button type="primary" @click="login">确定</i-button>
      <i-button type="error">取消</i-button>
    </Form-item>
  </i-form>
</template>
<script>
export default {
    data(){
        return{
          ac:"",
          psw:""
        }
    },
    methods:{
        login(){
            
            let that =this
            this.$ajax({
              url:"login/admin_login",
              data:{
                username:this.ac,
                password:this.psw
              },
              method:"POST",
              then:r=>{
                if(r.code==0){
                  this.$Message.info(r.msg);
                  return false
                }
                localStorage.setItem('user',JSON.stringify(r))
                location.reload()
              }
            })
        }
    }
};
</script>
<style>
.ivu-form {
  padding: 0 20px;
}
h2{
    margin-bottom: 10px
}
</style>
