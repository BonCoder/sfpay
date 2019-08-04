<template>
  <i-form>
    <Form-item label="新密码">
      <i-input :value.sync="formItem.input" placeholder="请输入密码"></i-input>
    </Form-item>
    <Form-item label="新密码">
      <i-input :value.sync="formItem.input" placeholder="请输入密码"></i-input>
    </Form-item>
    <Form-item label="重复密码">
      <i-input :value.sync="formItem.input" placeholder="请输入"></i-input>
    </Form-item>
     <i-button type="primary" :loading="loading2">提交</i-button>
    <i-button type="error">重置</i-button>
  </i-form>
</template>
<script>
export default {
  data() {
    return {
      formItem: {},
      loading2:false,
      isLogin:false
    };
  },
  created(){
      console.log(this.$route)
      if(this.$route.params.name=="修改登陆密码"){
          this.isLogin =true
      }
  }

};
</script>
<style scoped>
</style>


